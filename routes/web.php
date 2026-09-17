<?php

use App\Http\Controllers\Admin\LeagueController as AdminLeagueController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VenueController as AdminVenueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/{venue}', [VenueController::class, 'show'])->name('venues.show');

Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');

Route::get('/leagues', [LeagueController::class, 'index'])->name('leagues.index');
Route::get('/leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');

Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Teams — created and managed by players ("my teams" namespace avoids
    // colliding with the public /teams/{team} show route).
    Route::get('/my/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/my/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/my/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/my/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::post('/my/teams/{team}/players', [TeamController::class, 'addPlayer'])->name('teams.players.add');
    Route::delete('/my/teams/{team}/players/{user}', [TeamController::class, 'removePlayer'])->name('teams.players.remove');

    // Tournament registration + bKash checkout
    Route::get('/tournaments/{tournament}/register', [RegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/tournaments/{tournament}/register', [RegistrationController::class, 'store'])->name('registrations.store');

    Route::get('/payments/{payment}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/{payment}/pay', [PaymentController::class, 'pay'])->name('payments.pay');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'success'])->name('payments.success');

    // Venue reviews
    Route::post('/venues/{venue}/reviews', [VenueController::class, 'storeReview'])->name('venues.reviews.store');

    // Venue owner + admin
    Route::middleware('role:venue_owner,admin')->group(function () {
        Route::get('/owner/venues/create', [VenueController::class, 'create'])->name('venues.create');
        Route::post('/owner/venues', [VenueController::class, 'store'])->name('venues.store');
        Route::get('/owner/venues/{venue}/edit', [VenueController::class, 'edit'])->name('venues.edit');
        Route::put('/owner/venues/{venue}', [VenueController::class, 'update'])->name('venues.update');

        Route::get('/owner/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
        Route::post('/owner/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
        Route::get('/owner/tournaments/{tournament}/edit', [TournamentController::class, 'edit'])->name('tournaments.edit');
        Route::put('/owner/tournaments/{tournament}', [TournamentController::class, 'update'])->name('tournaments.update');
        Route::get('/owner/tournaments/{tournament}/registrations', [TournamentController::class, 'registrations'])->name('tournaments.registrations');

        Route::patch('/matches/{match}/result', [MatchController::class, 'update'])->name('matches.update');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/venues', [AdminVenueController::class, 'index'])->name('venues.index');
        Route::patch('/venues/{venue:id}/approve', [AdminVenueController::class, 'approve'])->name('venues.approve');
        Route::patch('/venues/{venue:id}/reject', [AdminVenueController::class, 'reject'])->name('venues.reject');

        Route::get('/leagues/create', [AdminLeagueController::class, 'create'])->name('leagues.create');
        Route::post('/leagues', [AdminLeagueController::class, 'store'])->name('leagues.store');
        Route::get('/leagues/{league}/manage', [AdminLeagueController::class, 'manage'])->name('leagues.manage');
        Route::post('/leagues/{league}/teams', [AdminLeagueController::class, 'addTeam'])->name('leagues.teams.add');
        Route::delete('/leagues/{league}/teams/{team:id}', [AdminLeagueController::class, 'removeTeam'])->name('leagues.teams.remove');
        Route::post('/leagues/{league}/fixtures', [AdminLeagueController::class, 'generateFixtures'])->name('leagues.fixtures.generate');
        Route::patch('/leagues/{league}/status', [AdminLeagueController::class, 'updateStatus'])->name('leagues.status');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    });
});

require __DIR__.'/auth.php';
