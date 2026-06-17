<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();

        return view('user.dashboard', [
            'upcoming' => $user->reservations()
                ->with(['court', 'payment'])
                ->whereDate('reservation_date', '>=', today())
                ->latest('reservation_date')
                ->take(5)
                ->get(),
            'stats' => [
                'upcoming' => $user->reservations()->whereIn('status', ['pending', 'approved'])->count(),
                'completed' => $user->reservations()->where('status', 'completed')->count(),
                'cancelled' => $user->reservations()->where('status', 'cancelled')->count(),
                'history' => $user->reservations()->count(),
            ],
        ]);
    }
}
