<?php

namespace App\Services;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;

class ReportService
{
    public function dashboardStats(): array
    {
        return [
            'total_reservations' => Reservation::count(),
            'todays_reservations' => Reservation::whereDate('reservation_date', today())->count(),
            'active_courts' => Court::available()->count(),
            'revenue' => Payment::where('payment_status', 'paid')->sum('amount'),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];
    }

    public function bookingStatus(): array
    {
        return Reservation::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }
}
