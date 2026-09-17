<?php

namespace App\Http\Controllers;

use App\Enums\Sport;
use App\Enums\TeamType;
use App\Models\Team;
use App\Models\User;
use App\Support\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function show(Team $team): Response
    {
        $team->load([
            'venue:id,name,slug,city',
            'captain:id,name',
            'players' => fn ($q) => $q->select('users.id', 'users.name'),
            'homeMatches' => fn ($q) => $q->with('awayTeam:id,name,slug')->latest('scheduled_at')->limit(10),
            'awayMatches' => fn ($q) => $q->with('homeTeam:id,name,slug')->latest('scheduled_at')->limit(10),
        ]);

        return Inertia::render('Teams/Show', [
            'team' => $team,
            'canManage' => auth()->user()?->can('update', $team) ?? false,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Team::class);

        return Inertia::render('Teams/Form', [
            'sports' => collect(Sport::cases())->map->value,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Team::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sport' => ['required', 'string', 'in:cricket,football'],
            'city' => ['nullable', 'string', 'max:120'],
        ]);

        $team = Team::create([
            ...$data,
            'slug' => Slug::unique($data['name'], Team::class),
            'type' => TeamType::Open,
            'captain_id' => $request->user()->id,
        ]);

        $team->players()->attach($request->user()->id, ['role' => 'captain']);

        return to_route('teams.show', $team)->with('success', 'Team created! Add your players next.');
    }

    public function edit(Team $team): Response
    {
        $this->authorize('update', $team);

        return Inertia::render('Teams/Form', [
            'team' => $team,
            'sports' => collect(Sport::cases())->map->value,
        ]);
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        $team->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
        ]));

        return to_route('teams.show', $team)->with('success', 'Team updated.');
    }

    public function addPlayer(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'jersey_number' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        if ($team->players()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'That player is already on the roster.');
        }

        $team->players()->attach($user->id, [
            'role' => 'player',
            'jersey_number' => $data['jersey_number'] ?? null,
        ]);

        return back()->with('success', "{$user->name} added to the roster.");
    }

    public function removePlayer(Team $team, User $user): RedirectResponse
    {
        $this->authorize('update', $team);

        $team->players()->detach($user->id);

        return back()->with('success', 'Player removed from the roster.');
    }
}
