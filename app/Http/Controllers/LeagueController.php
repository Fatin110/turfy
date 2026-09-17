<?php

namespace App\Http\Controllers;

use App\Enums\LeagueStatus;
use App\Models\League;
use App\Services\Leagues\StandingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeagueController extends Controller
{
    public function index(Request $request): Response
    {
        $leagues = League::query()
            ->whereNot('status', LeagueStatus::Draft)
            ->when($request->string('sport')->toString(), fn ($q, $sport) => $q->where('sport', $sport))
            ->withCount('teams')
            ->orderByDesc('start_date')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Leagues/Index', [
            'leagues' => $leagues,
            'filters' => $request->only(['sport']),
        ]);
    }

    public function show(League $league, StandingsService $standings): Response
    {
        $league->load(['teams:id,name,slug,logo,city', 'matches.homeTeam:id,name,slug,logo', 'matches.awayTeam:id,name,slug,logo']);

        return Inertia::render('Leagues/Show', [
            'league' => $league,
            'standings' => $standings->table($league)->map(fn ($row) => [
                ...$row,
                'team' => $row['team']->only(['id', 'name', 'slug', 'logo']),
            ])->values(),
        ]);
    }
}
