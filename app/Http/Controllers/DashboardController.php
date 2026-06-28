<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();

        $joinedTournaments = \App\Models\TournamentParticipant::where('user_id', $user->id)
            ->with('tournament')
            ->get()
            ->pluck('tournament')
            ->filter();

        return view('user.user-dashboard', [
            'upcoming' => $user->reservations()
                ->with(['court', 'payment'])
                ->whereDate('reservation_date', '>=', today())
                ->latest('reservation_date')
                ->take(5)
                ->get(),
            'stats' => [
                'upcoming' => $user->reservations()->whereIn('status', ['held', 'pending_payment', 'confirmed'])->count(),
                'completed' => $user->reservations()->where('status', 'completed')->count(),
                'cancelled' => $user->reservations()->where('status', 'cancelled')->count(),
                'history' => $user->reservations()->count(),
            ],
            'joinedTournaments' => $joinedTournaments,
        ]);
    }

    public function notifications(Request $request): View
    {
        $notifications = $request->user()->systemNotifications()
            ->latest()
            ->paginate(10);

        return view('user.user-notifications', [
            'notifications' => $notifications,
        ]);
    }

    public function markNotificationRead(SystemNotification $notification): RedirectResponse
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead(Request $request): RedirectResponse
    {
        $request->user()->systemNotifications()->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function billing(Request $request): View
    {
        $payments = \App\Models\Payment::whereHas('reservation', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->with(['reservation.court'])->latest()->paginate(10);

        return view('user.payments.user-payments', [
            'payments' => $payments,
        ]);
    }

    public function tournamentShow(\App\Models\Tournament $tournament): View
    {
        $user = auth()->user();

        // Ensure user is actually in this tournament
        $isParticipant = \App\Models\TournamentParticipant::where('tournament_id', $tournament->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            abort(403, 'You are not registered in this tournament.');
        }

        $tournament->load([
            'participants.user',
            'matches.participant1',
            'matches.participant2',
            'matches.winner',
            'matches.sets',
            'matches.schedule.court',
        ]);

        $matchesByRound = $tournament->matches->groupBy('round_number')->sortKeys();

        return view('user.tournaments.show', compact('tournament', 'matchesByRound'));
    }
}
