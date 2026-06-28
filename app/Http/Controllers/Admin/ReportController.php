<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(ReportService $reportService): View
    {
        $now        = Carbon::now();
        $thisMonth  = Carbon::now()->startOfMonth();
        $lastMonth  = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Revenue this month vs last month
        $revenueThisMonth = Payment::where('payment_status', 'paid')
            ->whereBetween('created_at', [$thisMonth, $now])
            ->sum('amount');

        $revenueLastMonth = Payment::where('payment_status', 'paid')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->sum('amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : null;

        // Bookings this month vs last month
        $bookingsThisMonth = Reservation::whereBetween('reservation_date', [$thisMonth, $now])->count();
        $bookingsLastMonth = Reservation::whereBetween('reservation_date', [$lastMonth, $lastMonthEnd])->count();
        $bookingsGrowth = $bookingsLastMonth > 0
            ? round((($bookingsThisMonth - $bookingsLastMonth) / $bookingsLastMonth) * 100, 1)
            : null;

        // Average booking value
        $avgBookingValue = Payment::where('payment_status', 'paid')->avg('amount') ?? 0;

        // Cancellation rate
        $totalReservations = Reservation::count();
        $cancelledCount    = Reservation::whereIn('status', ['cancelled', 'rejected'])->count();
        $cancellationRate  = $totalReservations > 0
            ? round(($cancelledCount / $totalReservations) * 100, 1)
            : 0;

        // New users this month
        $newUsersThisMonth = User::whereBetween('created_at', [$thisMonth, $now])->count();

        // Monthly revenue (last 6 months)
        $monthlyRevenue = collect(range(5, 0))->map(function ($i) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end   = Carbon::now()->subMonths($i)->endOfMonth();
            return [
                'month'   => $start->format('M Y'),
                'revenue' => (float) Payment::where('payment_status', 'paid')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('amount'),
                'bookings' => Reservation::whereBetween('reservation_date', [$start, $end])->count(),
            ];
        });

        // Monthly bookings raw (for controller — used by blade)
        $monthlyReservations = Reservation::selectRaw("strftime('%Y-%m', reservation_date) as period, count(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return view('admin.reports.admin-reports', [
            'stats'              => $reportService->dashboardStats(),
            'statusBreakdown'    => $reportService->bookingStatus(),
            'popularCourts'      => Court::withCount('reservations')->orderByDesc('reservations_count')->take(5)->get(),
            'monthlyReservations'=> $monthlyReservations,
            // KPI extras
            'revenueThisMonth'   => $revenueThisMonth,
            'revenueLastMonth'   => $revenueLastMonth,
            'revenueGrowth'      => $revenueGrowth,
            'bookingsThisMonth'  => $bookingsThisMonth,
            'bookingsLastMonth'  => $bookingsLastMonth,
            'bookingsGrowth'     => $bookingsGrowth,
            'avgBookingValue'    => $avgBookingValue,
            'cancellationRate'   => $cancellationRate,
            'cancelledCount'     => $cancelledCount,
            'newUsersThisMonth'  => $newUsersThisMonth,
            'monthlyRevenue'     => $monthlyRevenue,
        ]);
    }
}
