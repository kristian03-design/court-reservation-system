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

        return view('guest.tournaments.index', compact('upcoming', 'active', 'completed'));
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
}
