<?php

namespace App\Services\Leagues;

use App\Enums\MatchStatus;
use App\Models\League;
use Illuminate\Support\Collection;

class StandingsService
{
    /**
     * Compute the league table from completed matches.
     *
     * @return Collection<int, array{
     *     team: \App\Models\Team, played: int, won: int, drawn: int, lost: int,
     *     scored_for: int, scored_against: int, goal_difference: int, points: int
     * }>
     */
    public function table(League $league): Collection
    {
        $teams = $league->teams()->get();

        // Plain array, not a Collection: rows are mutated by reference below,
        // which Collection's ArrayAccess does not support.
        $rows = $teams->mapWithKeys(fn ($team) => [$team->id => [
            'team' => $team,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'scored_for' => 0,
            'scored_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
        ]])->all();

        $completed = $league->matches()
            ->whereIn('status', [MatchStatus::Completed, MatchStatus::Walkover])
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->get();

        foreach ($completed as $match) {
            if (! isset($rows[$match->home_team_id]) || ! isset($rows[$match->away_team_id])) {
                continue;
            }

            $home = &$rows[$match->home_team_id];
            $away = &$rows[$match->away_team_id];

            $home['played']++;
            $away['played']++;
            $home['scored_for'] += $match->home_score;
            $home['scored_against'] += $match->away_score;
            $away['scored_for'] += $match->away_score;
            $away['scored_against'] += $match->home_score;

            if ($match->home_score > $match->away_score) {
                $home['won']++;
                $home['points'] += $league->points_win;
                $away['lost']++;
                $away['points'] += $league->points_loss;
            } elseif ($match->home_score < $match->away_score) {
                $away['won']++;
                $away['points'] += $league->points_win;
                $home['lost']++;
                $home['points'] += $league->points_loss;
            } else {
                $home['drawn']++;
                $away['drawn']++;
                $home['points'] += $league->points_draw;
                $away['points'] += $league->points_draw;
            }

            $home['goal_difference'] = $home['scored_for'] - $home['scored_against'];
            $away['goal_difference'] = $away['scored_for'] - $away['scored_against'];
            unset($home, $away);
        }

        return collect($rows)->values()
            ->sortBy([
                ['points', 'desc'],
                ['goal_difference', 'desc'],
                ['scored_for', 'desc'],
            ])
            ->values();
    }
}
