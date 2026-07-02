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
        $cachedData = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 3600, function () use ($reportService) {
            $courts = \App\Models\Court::with(['reservations' => function ($q) {
                $q->whereDate('reservation_date', today())
                  ->whereIn('status', ['confirmed', 'completed', 'pending_payment']);
            }])->get();

            return [
                'stats' => $reportService->dashboardStats(),
                'statusBreakdown' => $reportService->bookingStatus(),
                'courts' => $courts,
            ];
        });

        $recentReservations = Reservation::with(['court', 'user', 'payment'])->latest()->take(8)->get();

        return view('admin.admin-dashboard', array_merge($cachedData, [
            'recentReservations' => $recentReservations,
        ]));
    }
}
