<?php

namespace App\Services;

use App\Models\MatchSet;
use App\Models\TournamentMatch;

class TournamentScoreService
{
    protected TournamentGeneratorService $generatorService;

    public function __construct(TournamentGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * Submit scores for a match and advance participants.
     *
     * @param TournamentMatch $match
     * @param array $setsArray Array of sets: [['p1' => 6, 'p2' => 4], ['p1' => 3, 'p2' => 6]]
     */
    public function submitScore(TournamentMatch $match, array $setsArray): void
    {
        // 1. Delete existing sets
        $match->sets()->delete();

        $p1SetsWon = 0;
        $p2SetsWon = 0;

        // 2. Save new sets and tally sets won
        foreach ($setsArray as $index => $setData) {
            $setNum = $index + 1;
            $s1 = (int) ($setData['p1'] ?? 0);
            $s2 = (int) ($setData['p2'] ?? 0);

            MatchSet::create([
                'match_id' => $match->id,
                'set_number' => $setNum,
                'participant1_score' => $s1,
                'participant2_score' => $s2,
            ]);

            if ($s1 > $s2) {
                $p1SetsWon++;
            } elseif ($s2 > $s1) {
                $p2SetsWon++;
            }
        }

        // 3. Determine winner and loser
        if ($p1SetsWon > $p2SetsWon) {
            $winnerId = $match->participant1_id;
            $loserId = $match->participant2_id;
        } elseif ($p2SetsWon > $p1SetsWon) {
            $winnerId = $match->participant2_id;
            $loserId = $match->participant1_id;
        } else {
            // Tie breaker or draw. In elimination, we must have a winner.
            // If scores are equal, we choose participant 1 as fallback or require administrative resolution.
            // Let's default to participant 1 if they drew.
            $winnerId = $match->participant1_id;
            $loserId = $match->participant2_id;
        }

        $match->update([
            'winner_id' => $winnerId,
            'loser_id' => $loserId,
            'status' => 'finished',
        ]);

        // 4. Advance winner
        if ($winnerId) {
            $this->generatorService->advanceWinner($match, $winnerId);
        }

        // 5. Advance loser if double elimination
        if ($loserId && $match->loser_next_match_id) {
            $loserNextMatch = TournamentMatch::find($match->loser_next_match_id);
            if ($loserNextMatch) {
                if (!$loserNextMatch->participant1_id) {
                    $loserNextMatch->update(['participant1_id' => $loserId]);
                } else {
                    $loserNextMatch->update(['participant2_id' => $loserId]);
                }

                if ($loserNextMatch->participant1_id && $loserNextMatch->participant2_id) {
                    $loserNextMatch->update(['status' => 'ready']);
                }
            }
        }

        // 6. Check if entire tournament is finished
        $tournament = $match->tournament;
        $totalUnfinished = $tournament->matches()
            ->whereNotIn('status', ['finished', 'bye', 'cancelled'])
            ->count();

        if ($totalUnfinished === 0) {
            $tournament->update(['status' => 'completed']);
        }

        $this->recalculateParticipantStats($tournament);
    }

    /**
     * Recalculate all participant stats based on match outcomes.
     */
    public function recalculateParticipantStats(\App\Models\Tournament $tournament): void
    {
        // Reset all stats for this tournament's participants
        $tournament->participants()->update([
            'wins' => 0,
            'losses' => 0,
            'matches_played' => 0,
            'is_eliminated' => false,
            'status' => 'active',
        ]);

        // Load finished matches for this tournament
        $matches = $tournament->matches()
            ->where('status', 'finished')
            ->get();

        foreach ($matches as $match) {
            if ($match->winner_id) {
                $winner = $tournament->participants()->find($match->winner_id);
                if ($winner) {
                    $winner->increment('wins');
                    $winner->increment('matches_played');
                }
            }

            if ($match->loser_id) {
                $loser = $tournament->participants()->find($match->loser_id);
                if ($loser) {
                    $loser->increment('losses');
                    $loser->increment('matches_played');
                }
            }
        }

        // Determine elimination state based on tournament format
        $participants = $tournament->participants()->get();
        foreach ($participants as $p) {
            if ($tournament->type === 'single') {
                if ($p->losses >= 1) {
                    $p->update(['is_eliminated' => true, 'status' => 'eliminated']);
                }
            } elseif ($tournament->type === 'double') {
                if ($p->losses >= 2) {
                    $p->update(['is_eliminated' => true, 'status' => 'eliminated']);
                }
            }
        }

        // Update champion if tournament is completed
        if ($tournament->status === 'completed') {
            $championshipMatch = $tournament->matches()
                ->where('round_number', $tournament->matches()->max('round_number'))
                ->first();
            if ($championshipMatch && $championshipMatch->winner_id) {
                $champion = $tournament->participants()->find($championshipMatch->winner_id);
                if ($champion) {
                    $champion->update(['status' => 'champion']);
                }
            }
        }
    }
}
