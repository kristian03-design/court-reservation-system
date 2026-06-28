<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentParticipant;
use App\Models\Court;
use App\Services\TournamentGeneratorService;
use App\Services\TournamentSchedulerService;
use App\Services\TournamentScoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(): View
    {
        $tournaments = Tournament::withCount('participants')->latest()->paginate(10);
        
        $stats = [
            'total' => Tournament::count(),
            'active' => Tournament::where('status', 'active')->count(),
            'completed' => Tournament::where('status', 'completed')->count(),
            'participants' => TournamentParticipant::count(),
        ];

        return view('admin.tournaments.index', compact('tournaments', 'stats'));
    }

    public function create(): View
    {
        return view('admin.tournaments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:single,double,round_robin',
            'registration_start' => 'required|date',
            'registration_end' => 'required|date|after:registration_start',
            'start_date' => 'required|date|after_or_equal:registration_end',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_participants' => 'required|integer|min:2',
            'entry_fee' => 'required|numeric|min:0',
            'auto_schedule' => 'nullable|boolean',
        ]);

        $validated['auto_schedule'] = $request->has('auto_schedule');

        $tournament = Tournament::create($validated);

        return redirect()->route('admin.tournaments.show', $tournament)
            ->with('success', 'Tournament created successfully in draft mode.');
    }

    public function show(Tournament $tournament): View
    {
        $tournament->load([
            'participants.user',
            'matches.participant1',
            'matches.participant2',
            'matches.winner',
            'matches.sets',
            'matches.schedule.court',
            'activityLogs.admin',
        ]);

        $courts = Court::where('status', 'available')->get();
        
        // Group matches by round
        $matchesByRound = $tournament->matches->groupBy('round_number')->sortKeys();

        return view('admin.tournaments.show', compact('tournament', 'matchesByRound', 'courts'));
    }

    public function edit(Tournament $tournament): View
    {
        return view('admin.tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:single,double,round_robin',
            'registration_start' => 'required|date',
            'registration_end' => 'required|date|after:registration_start',
            'start_date' => 'required|date|after_or_equal:registration_end',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_participants' => 'required|integer|min:2',
            'entry_fee' => 'required|numeric|min:0',
            'auto_schedule' => 'nullable|boolean',
        ]);

        $validated['auto_schedule'] = $request->has('auto_schedule');

        $tournament->update($validated);

        return redirect()->route('admin.tournaments.show', $tournament)
            ->with('success', 'Tournament settings updated successfully.');
    }

    public function destroy(Tournament $tournament): RedirectResponse
    {
        $tournament->delete();
        return redirect()->route('admin.tournaments.index')
            ->with('success', 'Tournament deleted successfully.');
    }

    public function publish(Tournament $tournament): RedirectResponse
    {
        if ($tournament->status !== 'draft') {
            return back()->with('error', 'Only draft tournaments can be published.');
        }

        $tournament->update(['status' => 'registration_open']);

        return back()->with('success', 'Tournament registration is now live!');
    }

    public function generateBracket(Tournament $tournament, TournamentGeneratorService $generator): RedirectResponse
    {
        try {
            $generator->generate($tournament);
            return back()->with('success', 'Brackets generated successfully. Status set to seeding.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate bracket: ' . $e->getMessage());
        }
    }

    public function scheduleMatches(Tournament $tournament, TournamentSchedulerService $scheduler): RedirectResponse
    {
        try {
            $scheduler->schedule($tournament);
            return back()->with('success', 'Matches auto-scheduled onto courts successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to auto-schedule matches: ' . $e->getMessage());
        }
    }

    public function updateScore(Request $request, TournamentMatch $match, TournamentScoreService $scoreService): RedirectResponse
    {
        $request->validate([
            'sets' => 'required|array|min:1',
            'sets.*.p1' => 'required|integer|min:0',
            'sets.*.p2' => 'required|integer|min:0',
        ]);

        try {
            $scoreService->submitScore($match, $request->sets);
            return back()->with('success', 'Scores submitted and bracket updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update score: ' . $e->getMessage());
        }
    }

    public function checkIn(Request $request, TournamentParticipant $participant): RedirectResponse
    {
        $participant->update([
            'checked_in' => !$participant->checked_in,
            'checked_in_at' => !$participant->checked_in ? now() : null,
        ]);

        $status = $participant->checked_in ? 'checked in' : 'checked out';
        return back()->with('success', "Participant successfully {$status}.");
    }
}
