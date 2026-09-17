<?php

namespace App\Http\Controllers;

use App\Enums\LeagueStatus;
use App\Enums\TournamentStatus;
use App\Enums\VenueStatus;
use App\Models\League;
use App\Models\Tournament;
use App\Models\Venue;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home', [
            'liveTournaments' => Tournament::query()
                ->where('status', TournamentStatus::Live)
                ->withCount('confirmedRegistrations')
                ->with('venue:id,name,city,slug')
                ->latest('start_date')
                ->limit(6)
                ->get(),
            'upcomingTournaments' => Tournament::query()
                ->whereIn('status', [TournamentStatus::RegistrationOpen, TournamentStatus::Upcoming])
                ->withCount('confirmedRegistrations')
                ->with('venue:id,name,city,slug')
                ->orderBy('start_date')
                ->limit(6)
                ->get(),
            'ongoingLeagues' => League::query()
                ->whereIn('status', [LeagueStatus::Ongoing, LeagueStatus::Upcoming])
                ->withCount('teams')
                ->latest('start_date')
                ->limit(4)
                ->get(),
            'featuredVenues' => Venue::query()
                ->where('status', VenueStatus::Approved)
                ->orderByDesc('rating_avg')
                ->limit(8)
                ->get(),
            'stats' => [
                'venues' => Venue::where('status', VenueStatus::Approved)->count(),
                'tournaments' => Tournament::count(),
                'leagues' => League::count(),
            ],
        ]);
    }
}
