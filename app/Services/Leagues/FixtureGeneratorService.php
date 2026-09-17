<?php

namespace App\Services\Leagues;

use App\Enums\MatchStatus;
use App\Models\League;
use App\Models\MatchGame;
use Illuminate\Support\Collection;

/**
 * Generates a round-robin (circle method) fixture list for a league, with
 * an optional reverse "away leg" second half — the same format used by
 * competitions like the Premier League / La Liga.
 */
class FixtureGeneratorService
{
    /**
     * Build and persist matches for every team currently attached to the league.
     *
     * @return Collection<int, MatchGame>
     */
    public function generate(League $league): Collection
    {
        $league->matches()->delete();

        $teamIds = $league->teams()->pluck('teams.id')->all();

        $firstLeg = $this->roundRobinPairs($teamIds);
        $rounds = $firstLeg;

        if ($league->double_round_robin) {
            foreach ($firstLeg as $round) {
                $rounds[] = $round->map(fn (array $pair) => [$pair[1], $pair[0]]);
            }
        }

        $matches = collect();
        $matchday = 1;
        $scheduledAt = $league->start_date?->copy();

        foreach ($rounds as $round) {
            foreach ($round as [$homeTeamId, $awayTeamId]) {
                $matches->push(MatchGame::create([
                    'league_id' => $league->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'matchday' => $matchday,
                    'round' => "Matchday {$matchday}",
                    'scheduled_at' => $scheduledAt,
                    'status' => MatchStatus::Scheduled,
                ]));
            }

            $matchday++;
            $scheduledAt = $scheduledAt?->copy()->addWeek();
        }

        return $matches;
    }

    /**
     * Circle method: fix team 0, rotate the rest across n-1 (or n) rounds.
     * A null "bye" slot is used to pad an odd number of teams.
     *
     * @param  array<int, int>  $teamIds
     * @return Collection<int, Collection<int, array{0:int,1:int}>>
     */
    private function roundRobinPairs(array $teamIds): Collection
    {
        $teams = $teamIds;

        if (count($teams) % 2 !== 0) {
            $teams[] = null; // bye
        }

        $teamCount = count($teams);
        $roundsCount = $teamCount - 1;
        $half = intdiv($teamCount, 2);

        $rounds = collect();

        for ($round = 0; $round < $roundsCount; $round++) {
            $pairs = collect();

            for ($i = 0; $i < $half; $i++) {
                $home = $teams[$i];
                $away = $teams[$teamCount - 1 - $i];

                if ($home !== null && $away !== null) {
                    // Alternate home/away by round so the fixed team doesn't
                    // always play at home.
                    $pairs->push($round % 2 === 0 ? [$home, $away] : [$away, $home]);
                }
            }

            $rounds->push($pairs);

            // Rotate all teams except the fixed first one.
            $fixed = $teams[0];
            $rest = array_slice($teams, 1);
            array_unshift($rest, array_pop($rest));
            $teams = array_merge([$fixed], $rest);
        }

        return $rounds;
    }
}
