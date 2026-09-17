<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Enums\Sport;
use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Venue;
use App\Support\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TournamentController extends Controller
{
    public function index(Request $request): Response
    {
        $tournaments = Tournament::query()
            ->whereNot('status', TournamentStatus::Draft)
            ->when($request->string('sport')->toString(), fn ($q, $sport) => $q->where('sport', $sport))
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->when($request->string('city')->toString(), fn ($q, $city) => $q->where('city', $city))
            ->withCount('confirmedRegistrations')
            ->with('venue:id,name,city,slug')
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'registration_open' THEN 1 WHEN status = 'upcoming' THEN 2 ELSE 3 END")
            ->orderBy('start_date')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Tournaments/Index', [
            'tournaments' => $tournaments,
            'sports' => collect(Sport::cases())->map->value,
            'statuses' => collect(TournamentStatus::cases())->map->value,
            'filters' => $request->only(['sport', 'status', 'city']),
        ]);
    }

    public function show(Tournament $tournament): Response
    {
        $tournament->load([
            'venue',
            'creator:id,name',
            'registrations' => fn ($q) => $q->where('status', RegistrationStatus::Confirmed)->with('team:id,name,slug,logo,city'),
            'matches' => fn ($q) => $q->with(['homeTeam:id,name,slug', 'awayTeam:id,name,slug'])->orderBy('scheduled_at'),
        ]);

        $user = auth()->user();
        $myTeamIds = $user ? $user->teams()->pluck('teams.id') : collect();

        return Inertia::render('Tournaments/Show', [
            'tournament' => $tournament,
            'canManage' => $user?->can('update', $tournament) ?? false,
            'alreadyRegisteredTeamIds' => $tournament->registrations->pluck('team_id'),
            'myEligibleTeams' => $user
                ? Team::manageableBy($user)->where('sport', $tournament->sport->value)->get(['teams.id', 'teams.name', 'teams.slug'])
                : [],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Tournament::class);

        $user = auth()->user();

        return Inertia::render('Tournaments/Form', [
            'sports' => collect(Sport::cases())->map->value,
            'formats' => collect(TournamentFormat::cases())->map->value,
            'venues' => $user->isAdmin()
                ? Venue::approved()->get(['id', 'name'])
                : $user->venues()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Tournament::class);

        $data = $this->validated($request);

        $tournament = Tournament::create([
            ...$data,
            'slug' => Slug::unique($data['name'], Tournament::class),
            'created_by' => $request->user()->id,
            'status' => TournamentStatus::RegistrationOpen,
        ]);

        return to_route('tournaments.show', $tournament)->with('success', 'Tournament created!');
    }

    public function edit(Tournament $tournament): Response
    {
        $this->authorize('update', $tournament);

        $user = auth()->user();

        return Inertia::render('Tournaments/Form', [
            'tournament' => $tournament,
            'sports' => collect(Sport::cases())->map->value,
            'formats' => collect(TournamentFormat::cases())->map->value,
            'statuses' => collect(TournamentStatus::cases())->map->value,
            'venues' => $user->isAdmin()
                ? Venue::approved()->get(['id', 'name'])
                : $user->venues()->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Tournament $tournament): RedirectResponse
    {
        $this->authorize('update', $tournament);

        $data = $this->validated($request);

        if ($request->filled('status')) {
            $data['status'] = $request->enum('status', TournamentStatus::class);
        }

        $tournament->update($data);

        return to_route('tournaments.show', $tournament)->with('success', 'Tournament updated.');
    }

    public function registrations(Tournament $tournament): Response
    {
        $this->authorize('manageRegistrations', $tournament);

        $tournament->load(['registrations.team:id,name,slug,city', 'registrations.registeredBy:id,name,email', 'registrations.payment']);

        return Inertia::render('Tournaments/Registrations', [
            'tournament' => $tournament,
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rules' => ['nullable', 'string'],
            'sport' => ['required', 'string', 'in:cricket,football'],
            'format' => ['required', 'string', 'in:knockout,round_robin'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'city' => ['nullable', 'string', 'max:120'],
            'banner_image' => ['nullable', 'string', 'max:2048'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'registration_deadline' => ['nullable', 'date'],
            'max_teams' => ['required', 'integer', 'min:2', 'max:64'],
            'entry_fee' => ['required', 'numeric', 'min:0'],
            'prize_info' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
