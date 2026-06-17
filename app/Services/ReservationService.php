<?php

namespace App\Services;

use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(private AvailabilityService $availabilityService)
    {
    }

    public function create(User $user, Court $court, array $data): Reservation
    {
        if (! $this->availabilityService->isSlotAvailable($court, $data['reservation_date'], $data['start_time'], $data['end_time'])) {
            throw ValidationException::withMessages([
                'start_time' => 'That court is no longer available for the selected time slot.',
            ]);
        }

        return DB::transaction(function () use ($user, $court, $data) {
            $reservation = Reservation::create([
                'reservation_number' => 'CC-'.now()->format('Ymd').'-'.strtoupper(str()->random(6)),
                'user_id' => $user->id,
                'court_id' => $court->id,
                'reservation_date' => $data['reservation_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'players' => $data['players'],
                'total_amount' => $this->calculateTotal($court, $data['start_time'], $data['end_time']),
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $reservation->payment()->create([
                'amount' => $reservation->total_amount,
                'payment_method' => $data['payment_method'] ?? 'pay_at_venue',
                'payment_status' => 'unpaid',
            ]);

            $user->systemNotifications()->create([
                'title' => 'Reservation created',
                'message' => "Reservation {$reservation->reservation_number} is pending approval.",
            ]);

            return $reservation->load(['court', 'payment']);
        });
    }

    public function calculateTotal(Court $court, string $startTime, string $endTime): float
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $hours = max(1, ($end - $start) / 3600);

        return (float) $court->hourly_rate * $hours;
    }
}
