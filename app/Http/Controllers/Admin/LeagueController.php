<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeagueStatus;
use App\Enums\Sport;
use App\Enums\TeamType;
use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Team;
use App\Services\Leagues\FixtureGeneratorService;
use App\Services\Leagues\StandingsService;
use App\Support\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeagueController extends Controller
{
    public function create(): Response
    {
        $this->authorize('create', League::class);

        return Inertia::render('Admin/Leagues/Form', [
            'sports' => collect(Sport::cases())->map->value,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', League::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sport' => ['required', 'string', 'in:cricket,football'],
            'season' => ['required', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'double_round_robin' => ['boolean'],
        ]);

        $league = League::create([
            ...$data,
            'slug' => Slug::unique($data['name'], League::class),
            'created_by' => $request->user()->id,
            'status' => LeagueStatus::Draft,
        ]);

        return to_route('admin.leagues.manage', $league)->with('success', 'League created. Now add participating venue teams.');
    }

    public function manage(League $league, StandingsService $standings): Response
    {
        $this->authorize('update', $league);

        $league->load(['teams:id,name,slug,city,venue_id', 'matches.homeTeam:id,name,slug', 'matches.awayTeam:id,name,slug']);

        return Inertia::render('Admin/Leagues/Manage', [
            'league' => $league,
            'standings' => $standings->table($league)->map(fn ($row) => [
                ...$row,
                'team' => $row['team']->only(['id', 'name', 'slug']),
            ])->values(),
            'availableTeams' => Team::where('type', TeamType::Venue)
                ->where('sport', $league->sport->value)
                ->whereDoesntHave('leagues', fn ($q) => $q->where('leagues.id', $league->id))
                ->with('venue:id,name')
                ->get(['id', 'name', 'slug', 'venue_id']),
        ]);
    }

    public function addTeam(Request $request, League $league): RedirectResponse
    {
        $this->authorize('update', $league);

        $data = $request->validate(['team_id' => ['required', 'exists:teams,id']]);

        $league->teams()->syncWithoutDetaching([$data['team_id']]);

        return back()->with('success', 'Team added to the league.');
    }

    public function removeTeam(League $league, Team $team): RedirectResponse
    {
        $this->authorize('update', $league);

        $league->teams()->detach($team->id);

        return back()->with('success', 'Team removed from the league.');
    }

    public function generateFixtures(League $league, FixtureGeneratorService $generator): RedirectResponse
    {
        $this->authorize('update', $league);

        abort_if($league->teams()->count() < 2, 422, 'Add at least two teams before generating fixtures.');

        $generator->generate($league);

        $league->update(['status' => LeagueStatus::Upcoming]);

        return back()->with('success', 'Fixtures generated for the full home & away schedule.');
    }

    public function updateStatus(Request $request, League $league): RedirectResponse
    {
        $this->authorize('update', $league);

        $data = $request->validate(['status' => ['required', 'string', 'in:draft,upcoming,ongoing,completed']]);

        $league->update(['status' => $data['status']]);

        return back()->with('success', 'League status updated.');
    }
}
