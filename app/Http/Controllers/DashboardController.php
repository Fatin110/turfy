<?php

namespace App\Http\Controllers;

use App\Enums\MatchStatus;
use App\Enums\PaymentStatus;
use App\Enums\VenueStatus;
use App\Models\League;
use App\Models\MatchGame;
use App\Models\Payment;
use App\Models\User;
use App\Models\Venue;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('Dashboard', match (true) {
            $user->isAdmin() => ['role' => 'admin', ...$this->adminData()],
            $user->isVenueOwner() => ['role' => 'venue_owner', ...$this->venueOwnerData($user)],
            default => ['role' => 'player', ...$this->playerData($user)],
        });
    }

    private function playerData(User $user): array
    {
        $teamIds = $user->teams()->pluck('teams.id');

        return [
            'myTeams' => $user->teams()->withCount('players')->get(['teams.id', 'teams.name', 'teams.slug', 'teams.sport', 'teams.city']),
            'myRegistrations' => $user->teams()
                ->with(['registrations' => fn ($q) => $q->with(['tournament:id,name,slug,start_date,status', 'payment'])])
                ->get()
                ->pluck('registrations')
                ->flatten()
                ->unique('id')
                ->values(),
            'upcomingMatches' => MatchGame::query()
                ->where(fn ($q) => $q->whereIn('home_team_id', $teamIds)->orWhereIn('away_team_id', $teamIds))
                ->where('status', MatchStatus::Scheduled)
                ->with(['homeTeam:id,name,slug', 'awayTeam:id,name,slug', 'venue:id,name,city'])
                ->orderBy('scheduled_at')
                ->limit(10)
                ->get(),
        ];
    }

    private function venueOwnerData(User $user): array
    {
        $venueIds = $user->venues()->pluck('id');
        $tournamentIds = $user->tournamentsCreated()->pluck('id');

        return [
            'myVenues' => $user->venues()->withCount('tournaments')->get(),
            'myTournaments' => $user->tournamentsCreated()
                ->withCount('confirmedRegistrations')
                ->orderByDesc('start_date')
                ->get(),
            'revenueCollected' => Payment::whereHas(
                'registration',
                fn ($q) => $q->whereIn('tournament_id', $tournamentIds)
            )->where('status', PaymentStatus::Completed)->sum('amount'),
            'matchesNeedingResults' => MatchGame::query()
                ->whereIn('tournament_id', $tournamentIds)
                ->where('status', MatchStatus::Scheduled)
                ->with(['homeTeam:id,name', 'awayTeam:id,name', 'tournament:id,name,slug'])
                ->orderBy('scheduled_at')
                ->limit(10)
                ->get(),
        ];
    }

    private function adminData(): array
    {
        return [
            'stats' => [
                'users' => User::count(),
                'venues' => Venue::count(),
                'pendingVenues' => Venue::where('status', VenueStatus::Pending)->count(),
                'leagues' => League::count(),
                'revenue' => Payment::where('status', PaymentStatus::Completed)->sum('amount'),
            ],
            'pendingVenuesList' => Venue::where('status', VenueStatus::Pending)->latest()->limit(5)->get(),
            'recentPayments' => Payment::with(['user:id,name', 'registration.tournament:id,name'])
                ->where('status', PaymentStatus::Completed)
                ->latest('paid_at')
                ->limit(8)
                ->get(),
        ];
    }
}
