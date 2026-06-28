<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ReportService $reportService): View
    {
        $courts = \App\Models\Court::with(['reservations' => function ($q) {
            $q->whereDate('reservation_date', today())
              ->whereIn('status', ['approved', 'completed', 'pending']);
        }])->get();

        return view('admin.admin-dashboard', [
            'stats' => $reportService->dashboardStats(),
            'statusBreakdown' => $reportService->bookingStatus(),
            'recentReservations' => Reservation::with(['court', 'user', 'payment'])->latest()->take(8)->get(),
            'courts' => $courts,
        ]);
    }
}
