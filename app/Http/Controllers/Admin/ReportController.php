<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Reservation;
use App\Services\ReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(ReportService $reportService): View
    {
        return view('admin.reports.index', [
            'stats' => $reportService->dashboardStats(),
            'statusBreakdown' => $reportService->bookingStatus(),
            'popularCourts' => Court::withCount('reservations')->orderByDesc('reservations_count')->take(5)->get(),
            'monthlyReservations' => Reservation::selectRaw("strftime('%Y-%m', reservation_date) as period, count(*) as total")
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
        ]);
    }
}
