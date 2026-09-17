<?php

namespace App\Http\Controllers;

use App\Enums\MatchStatus;
use App\Models\MatchGame;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function update(Request $request, MatchGame $match): RedirectResponse
    {
        $this->authorizeScoreEntry($match);

        $data = $request->validate([
            'home_score' => ['required', 'integer', 'min:0'],
            'away_score' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:completed,walkover'],
        ]);

        $winnerId = match (true) {
            $data['home_score'] > $data['away_score'] => $match->home_team_id,
            $data['away_score'] > $data['home_score'] => $match->away_team_id,
            default => null,
        };

        $match->update([
            ...$data,
            'status' => MatchStatus::from($data['status']),
            'winner_team_id' => $winnerId,
        ]);

        return back()->with('success', 'Result saved.');
    }

    private function authorizeScoreEntry(MatchGame $match): void
    {
        $user = auth()->user();

        abort_unless($user, 403);

        if ($user->isAdmin()) {
            return;
        }

        if ($match->tournament_id && $match->tournament?->created_by === $user->id) {
            return;
        }

        abort(403, 'You are not authorized to enter this result.');
    }
}
