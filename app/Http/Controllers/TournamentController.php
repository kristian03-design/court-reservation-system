<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(): View
    {
        $tournaments = \Illuminate\Support\Facades\Cache::remember('tournaments_list', 3600, function () {
            return [
                'upcoming' => Tournament::whereIn('status', ['published', 'registration_open', 'registration_closed'])
                    ->orderBy('start_date', 'asc')
                    ->get(),
                'active' => Tournament::where('status', 'active')
                    ->orderBy('start_date', 'asc')
                    ->get(),
                'completed' => Tournament::where('status', 'completed')
                    ->latest('end_date')
                    ->take(5)
                    ->get(),
            ];
        });

        $upcoming = $tournaments['upcoming'];
        $active = $tournaments['active'];
        $completed = $tournaments['completed'];

        // My registered tournaments (for logged-in users)
        $joinedTournaments = collect();
        if (auth()->check()) {
            $joinedTournaments = \App\Models\TournamentParticipant::where('user_id', auth()->id())
                ->with('tournament')
                ->get()
                ->pluck('tournament')
                ->filter();
        }

        return view('guest.tournaments.index', compact('upcoming', 'active', 'completed', 'joinedTournaments'));
    }

    public function show(Tournament $tournament): View
    {
        $cachedData = \Illuminate\Support\Facades\Cache::remember('tournament_show_' . $tournament->id, 3600, function () use ($tournament) {
            $tournament->load([
                'participants.user',
                'matches.participant1',
                'matches.participant2',
                'matches.winner',
                'matches.sets',
                'matches.schedule.court',
            ]);

            $matchesByRound = $tournament->matches->groupBy('round_number')->sortKeys();

            return [
                'tournament' => $tournament,
                'matchesByRound' => $matchesByRound,
            ];
        });

        $tournament = $cachedData['tournament'];
        $matchesByRound = $cachedData['matchesByRound'];

        return view('guest.tournaments.show', compact('tournament', 'matchesByRound'));
    }

    public function participants(Tournament $tournament): View
    {
        $stats = \Illuminate\Support\Facades\Cache::remember('tournament_stats_' . $tournament->id, 300, function () use ($tournament) {
            $totalParticipants = $tournament->participants()->count();
            
            $teamsCount = $tournament->participants()
                ->whereNotNull('team_name')
                ->where('team_name', '!=', '')
                ->distinct('team_name')
                ->count('team_name');
            
            if ($teamsCount === 0) {
                $teamsCount = $tournament->participants()
                    ->where('participant_type', 'team')
                    ->count();
            }

            $matchesCount = $tournament->matches()->count();
            
            $prizePool = $totalParticipants * $tournament->entry_fee;

            $courtsCount = \DB::table('tournament_schedules')
                ->join('tournament_matches', 'tournament_schedules.match_id', '=', 'tournament_matches.id')
                ->where('tournament_matches.tournament_id', $tournament->id)
                ->distinct('court_id')
                ->count('court_id');

            if ($courtsCount === 0 && $matchesCount > 0) {
                $courtsCount = 1;
            }

            $bracketStatus = $matchesCount > 0 ? 'Generated' : 'Pending';

            return [
                'total_participants' => $totalParticipants,
                'teams_count' => $teamsCount,
                'matches_count' => $matchesCount,
                'prize_pool' => $prizePool,
                'courts_count' => $courtsCount,
                'bracket_status' => $bracketStatus,
            ];
        });

        return view('tournaments.participants', [
            'tournament' => $tournament,
            'stats' => $stats
        ]);
    }
}
