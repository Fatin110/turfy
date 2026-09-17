<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Enums\TournamentStatus;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Services\Payments\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function create(Tournament $tournament): Response|RedirectResponse
    {
        $user = auth()->user();

        $eligibleTeams = Team::manageableBy($user)
            ->where('sport', $tournament->sport->value)
            ->whereDoesntHave('registrations', fn ($q) => $q->where('tournament_id', $tournament->id))
            ->get(['teams.id', 'teams.name', 'teams.slug', 'teams.city']);

        return Inertia::render('Tournaments/Register', [
            'tournament' => $tournament->load('venue:id,name,city'),
            'eligibleTeams' => $eligibleTeams,
        ]);
    }

    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        abort_unless(
            in_array($tournament->status, [TournamentStatus::RegistrationOpen, TournamentStatus::Upcoming], true),
            403,
            'Registration is not open for this tournament.'
        );
        abort_if($tournament->isFull(), 403, 'This tournament is full.');

        $data = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::manageableBy($request->user())->findOrFail($data['team_id']);

        abort_unless($team->sport === $tournament->sport, 422, 'This team plays a different sport.');

        $registration = TournamentRegistration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'registered_by' => $request->user()->id,
            'status' => RegistrationStatus::PendingPayment,
        ]);

        if ($tournament->isFree()) {
            $this->payments->completeFreeRegistration($registration);

            return to_route('tournaments.show', $tournament)
                ->with('success', "{$team->name} is registered for {$tournament->name}!");
        }

        $payment = $this->payments->initiateBkashPayment($registration, (float) $tournament->entry_fee);

        return to_route('payments.checkout', $payment);
    }
}
