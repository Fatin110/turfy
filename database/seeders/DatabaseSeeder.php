<?php

namespace Database\Seeders;

use App\Enums\LeagueStatus;
use App\Enums\MatchStatus;
use App\Enums\RegistrationStatus;
use App\Enums\Sport;
use App\Enums\TeamType;
use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use App\Enums\UserRole;
use App\Enums\VenueStatus;
use App\Models\League;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Models\User;
use App\Models\Venue;
use App\Services\Leagues\FixtureGeneratorService;
use App\Services\Payments\PaymentService;
use App\Support\Slug;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Turfy Admin',
            'email' => 'admin@turfy.app',
            'role' => UserRole::Admin,
            'city' => 'Dhaka',
        ]);

        $venues = $this->seedVenuesWithOwners();
        $players = $this->seedPlayers();
        $openTeams = $this->seedOpenTeams($players);

        $this->seedPaidFootballTournament($venues['dhanmondi'], $openTeams, $venues);
        $this->seedFreeCricketTournament($venues['mirpur'], $openTeams);
        $this->seedLeague($admin, $venues);
        $this->seedReviews($venues, $players);

        $this->command?->info('Seeded: 1 admin, ' . count($venues) . ' venues, ' . count($players) . ' players, demo tournaments, a 5-team league, payments and reviews.');
    }

    /**
     * @return array<string, Venue>
     */
    private function seedVenuesWithOwners(): array
    {
        $definitions = [
            'dhanmondi' => ['name' => 'Dhanmondi Arena', 'city' => 'Dhanmondi', 'owner' => 'Rafiq Hasan', 'sports' => ['cricket', 'football'], 'status' => VenueStatus::Approved],
            'gulshan' => ['name' => 'Gulshan Turf Zone', 'city' => 'Gulshan', 'owner' => 'Kamal Ahmed', 'sports' => ['football'], 'status' => VenueStatus::Approved],
            'mirpur' => ['name' => 'Mirpur Sports Hub', 'city' => 'Mirpur', 'owner' => 'Nasir Uddin', 'sports' => ['cricket', 'football'], 'status' => VenueStatus::Approved],
            'uttara' => ['name' => 'Uttara Champions Turf', 'city' => 'Uttara', 'owner' => 'Shafiul Islam', 'sports' => ['football'], 'status' => VenueStatus::Approved],
            'bashundhara' => ['name' => 'Bashundhara Indoor Arena', 'city' => 'Bashundhara', 'owner' => 'Tanvir Rahman', 'sports' => ['cricket', 'football'], 'status' => VenueStatus::Approved],
            'banani' => ['name' => 'Banani Play Zone', 'city' => 'Banani', 'owner' => 'Imran Kabir', 'sports' => ['football'], 'status' => VenueStatus::Pending],
        ];

        $venues = [];

        foreach ($definitions as $key => $def) {
            $owner = User::factory()->create([
                'name' => $def['owner'],
                'email' => strtolower(str_replace(' ', '.', $def['owner'])) . '@turfy.app',
                'role' => UserRole::VenueOwner,
                'city' => $def['city'],
                'phone' => '01' . fake()->numerify('#########'),
            ]);

            $venue = Venue::create([
                'owner_id' => $owner->id,
                'name' => $def['name'],
                'slug' => Slug::unique($def['name'], Venue::class),
                'description' => "A premium indoor turf in {$def['city']}, Dhaka — book a slot or join a tournament.",
                'city' => $def['city'],
                'address' => "House 12, Road 5, {$def['city']}, Dhaka",
                'sports' => $def['sports'],
                'phone' => '01' . fake()->numerify('#########'),
                'status' => $def['status'],
                'rating_avg' => 0,
            ]);

            if ($venue->status === VenueStatus::Approved) {
                $venue->team()->create([
                    'name' => $venue->name,
                    'slug' => Slug::unique($venue->name, Team::class),
                    'type' => TeamType::Venue,
                    'sport' => $def['sports'][0],
                    'city' => $venue->city,
                ]);
            }

            $venues[$key] = $venue;
        }

        return $venues;
    }

    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function seedPlayers()
    {
        $names = [
            'Arif Chowdhury', 'Bappy Islam', 'Rakib Hasan', 'Tamim Ahmed', 'Riad Hossain',
            'Mashrur Alam', 'Shakil Rahman', 'Naeem Uddin', 'Rubel Khan', 'Anisur Karim',
            'Litton Sarker', 'Mehedi Molla', 'Soumya Talukder', 'Farhan Siddique',
        ];

        return collect($names)->map(fn (string $name) => User::factory()->create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'role' => UserRole::Player,
            'city' => 'Dhaka',
            'phone' => '01' . fake()->numerify('#########'),
        ]));
    }

    /**
     * @return array<string, Team>
     */
    private function seedOpenTeams($players): array
    {
        $definitions = [
            'thunder' => ['name' => 'Thunder Strikers', 'sport' => Sport::Football, 'captain' => 0],
            'warriors' => ['name' => 'Dhaka Warriors CC', 'sport' => Sport::Cricket, 'captain' => 3],
            'kickers' => ['name' => 'City Kickers', 'sport' => Sport::Football, 'captain' => 6],
            'gladiators' => ['name' => 'Old Dhaka Gladiators', 'sport' => Sport::Cricket, 'captain' => 9],
        ];

        $teams = [];

        foreach ($definitions as $key => $def) {
            $captain = $players[$def['captain']];

            $team = Team::create([
                'name' => $def['name'],
                'slug' => Slug::unique($def['name'], Team::class),
                'type' => TeamType::Open,
                'sport' => $def['sport'],
                'city' => 'Dhaka',
                'captain_id' => $captain->id,
            ]);

            $roster = $players->slice($def['captain'], 5)->values();

            foreach ($roster as $i => $player) {
                $team->players()->attach($player->id, [
                    'role' => $player->id === $captain->id ? 'captain' : 'player',
                    'jersey_number' => $i + 1,
                ]);
            }

            $teams[$key] = $team;
        }

        return $teams;
    }

    private function seedPaidFootballTournament(Venue $host, array $openTeams, array $venues): void
    {
        $tournament = Tournament::create([
            'name' => 'Dhanmondi Winter Football Cup',
            'slug' => Slug::unique('Dhanmondi Winter Football Cup', Tournament::class),
            'description' => 'An 8-a-side knockout football cup open to every registered team in Dhaka.',
            'rules' => '7-a-side, 2x20 minute halves, FIFA small-sided rules apply.',
            'sport' => Sport::Football,
            'format' => TournamentFormat::Knockout,
            'venue_id' => $host->id,
            'created_by' => $host->owner_id,
            'city' => $host->city,
            'start_date' => now()->addWeeks(3),
            'end_date' => now()->addWeeks(4),
            'registration_deadline' => now()->addWeeks(2),
            'max_teams' => 8,
            'entry_fee' => 1500,
            'prize_info' => '৳30,000 winner prize + trophy',
            'status' => TournamentStatus::RegistrationOpen,
        ]);

        $payments = app(PaymentService::class);

        $entrants = [
            [$openTeams['thunder'], $openTeams['thunder']->captain_id, true],
            [$openTeams['kickers'], $openTeams['kickers']->captain_id, true],
            [$venues['gulshan']->team()->first(), $venues['gulshan']->owner_id, true],
            [$venues['uttara']->team()->first(), $venues['uttara']->owner_id, false],
        ];

        foreach ($entrants as [$team, $registeredBy, $confirmPayment]) {
            $registration = TournamentRegistration::create([
                'tournament_id' => $tournament->id,
                'team_id' => $team->id,
                'registered_by' => $registeredBy,
                'status' => RegistrationStatus::PendingPayment,
            ]);

            $payment = $payments->initiateBkashPayment($registration, (float) $tournament->entry_fee);

            if ($confirmPayment) {
                $payments->confirmBkashPayment($payment);
            }
        }
    }

    private function seedFreeCricketTournament(Venue $host, array $openTeams): void
    {
        $tournament = Tournament::create([
            'name' => 'Free Cricket Championship 2026',
            'slug' => Slug::unique('Free Cricket Championship 2026', Tournament::class),
            'description' => 'A free-entry round robin cricket championship to celebrate our launch.',
            'rules' => '8 overs per side, tennis ball cricket.',
            'sport' => Sport::Cricket,
            'format' => TournamentFormat::RoundRobin,
            'venue_id' => $host->id,
            'created_by' => $host->owner_id,
            'city' => $host->city,
            'start_date' => now()->subDays(3),
            'end_date' => now()->addWeek(),
            'registration_deadline' => now()->subDay(),
            'max_teams' => 6,
            'entry_fee' => 0,
            'prize_info' => 'Champions trophy',
            'status' => TournamentStatus::Live,
        ]);

        $payments = app(PaymentService::class);
        $mirpurTeam = $host->team()->first();

        $teams = [$openTeams['warriors'], $openTeams['gladiators'], $mirpurTeam];

        $registrations = collect($teams)->map(function (Team $team) use ($tournament, $payments) {
            $registration = TournamentRegistration::create([
                'tournament_id' => $tournament->id,
                'team_id' => $team->id,
                'registered_by' => $team->captain_id ?? $team->venue->owner_id,
                'status' => RegistrationStatus::PendingPayment,
            ]);

            $payments->completeFreeRegistration($registration);

            return $registration->fresh();
        });

        $tournament->matches()->create([
            'home_team_id' => $registrations[0]->team_id,
            'away_team_id' => $registrations[1]->team_id,
            'venue_id' => $host->id,
            'round' => 'Round 1',
            'scheduled_at' => now()->subDay(),
            'home_score' => 142,
            'away_score' => 128,
            'winner_team_id' => $registrations[0]->team_id,
            'status' => MatchStatus::Completed,
        ]);

        $tournament->matches()->create([
            'home_team_id' => $registrations[1]->team_id,
            'away_team_id' => $registrations[2]->team_id,
            'venue_id' => $host->id,
            'round' => 'Round 2',
            'scheduled_at' => now()->addHours(2),
            'status' => MatchStatus::Live,
        ]);

        $tournament->matches()->create([
            'home_team_id' => $registrations[2]->team_id,
            'away_team_id' => $registrations[0]->team_id,
            'venue_id' => $host->id,
            'round' => 'Round 3',
            'scheduled_at' => now()->addDays(2),
            'status' => MatchStatus::Scheduled,
        ]);
    }

    private function seedLeague(User $admin, array $venues): void
    {
        $league = League::create([
            'name' => 'Dhaka Football Premier League',
            'slug' => Slug::unique('Dhaka Football Premier League', League::class),
            'description' => 'Five of Dhaka\'s top indoor arenas face off home and away, La Liga style — one team per venue.',
            'sport' => Sport::Football,
            'season' => '2026',
            'city' => 'Dhaka',
            'created_by' => $admin->id,
            'start_date' => now()->subWeeks(3),
            'end_date' => now()->addWeeks(5),
            'points_win' => 3,
            'points_draw' => 1,
            'points_loss' => 0,
            'double_round_robin' => true,
            'status' => LeagueStatus::Ongoing,
        ]);

        $teamKeys = ['dhanmondi', 'gulshan', 'mirpur', 'uttara', 'bashundhara'];

        foreach ($teamKeys as $key) {
            $league->teams()->attach($venues[$key]->team()->first()->id);
        }

        $matches = app(FixtureGeneratorService::class)->generate($league->fresh());

        // Play out the first three matchdays so the standings table has real results.
        $matches->where('matchday', '<=', 3)->each(function ($match) {
            $homeScore = random_int(0, 4);
            $awayScore = random_int(0, 4);

            $match->update([
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'status' => MatchStatus::Completed,
                'winner_team_id' => match (true) {
                    $homeScore > $awayScore => $match->home_team_id,
                    $awayScore > $homeScore => $match->away_team_id,
                    default => null,
                },
            ]);
        });
    }

    private function seedReviews(array $venues, $players): void
    {
        $reviews = [
            ['venue' => 'dhanmondi', 'player' => 0, 'rating' => 5, 'comment' => 'Excellent turf quality and great lighting for night games.'],
            ['venue' => 'dhanmondi', 'player' => 1, 'rating' => 4, 'comment' => 'Good facilities, parking can be tricky on weekends.'],
            ['venue' => 'mirpur', 'player' => 3, 'rating' => 5, 'comment' => 'Best cricket nets in the area, very well maintained.'],
            ['venue' => 'gulshan', 'player' => 6, 'rating' => 4, 'comment' => 'Solid 5-a-side pitch, friendly staff.'],
            ['venue' => 'bashundhara', 'player' => 9, 'rating' => 3, 'comment' => 'Decent but gets booked out fast — plan ahead.'],
        ];

        foreach ($reviews as $r) {
            $venues[$r['venue']]->reviews()->create([
                'user_id' => $players[$r['player']]->id,
                'rating' => $r['rating'],
                'comment' => $r['comment'],
            ]);
        }
    }
}
