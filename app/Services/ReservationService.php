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
        return DB::transaction(function () use ($user, $court, $data) {
            // Apply lockForUpdate to prevent race conditions on availability
            $exists = Reservation::query()
                ->where('court_id', $court->id)
                ->whereDate('reservation_date', $data['reservation_date'])
                ->active()
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'start_time' => 'That court is no longer available for the selected time slot.',
                ]);
            }

            // Also check blocked schedules
            $blockedSchedule = \App\Models\CourtSchedule::query()
                ->where('court_id', $court->id)
                ->whereDate('schedule_date', $data['reservation_date'])
                ->whereIn('availability_status', ['reserved', 'maintenance', 'closed'])
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->lockForUpdate()
                ->exists();

            if ($blockedSchedule) {
                throw ValidationException::withMessages([
                    'start_time' => 'That court is no longer available for the selected time slot.',
                ]);
            }

            $reservation = Reservation::create([
                'reservation_number' => 'CC-'.now()->format('Ymd').'-'.strtoupper(str()->random(6)),
                'user_id' => $user->id,
                'court_id' => $court->id,
                'reservation_date' => $data['reservation_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'players' => $data['players'],
                'total_amount' => $this->calculateTotal($court, $data['start_time'], $data['end_time']),
                'status' => 'held',
                'notes' => $data['notes'] ?? null,
            ]);

            $reservation->payment()->create([
                'amount' => $reservation->total_amount,
                'payment_method' => $data['payment_method'] ?? 'pay_at_venue',
                'payment_status' => 'unpaid',
            ]);

            $user->systemNotifications()->create([
                'title' => 'Reservation created',
                'message' => "Reservation {$reservation->reservation_number} is held for 10 minutes. Please complete payment.",
            ]);

            \App\Services\AuditLogService::log('reservation_created', $reservation, [
                'reservation_number' => $reservation->reservation_number,
                'court_name' => $court->court_name,
                'amount' => $reservation->total_amount,
            ]);

            \App\Models\AdminNotification::create([
                'type'       => 'reservation',
                'title'      => 'New Reservation',
                'message'    => "{$user->name} booked {$court->court_name} on " . \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y') . " ({$reservation->start_time} – {$reservation->end_time}).",
                'action_url' => route('admin.reservations.show', $reservation),
            ]);

            return $reservation->load(['court', 'payment']);
        });
    }

    public function cancel(Reservation $reservation): void
    {
        $reservation->update(['status' => 'cancelled']);

        \App\Services\AuditLogService::log('reservation_cancelled', $reservation, [
            'reservation_number' => $reservation->reservation_number,
        ]);

        \App\Models\AdminNotification::create([
            'type'       => 'reservation',
            'title'      => 'Reservation Cancelled',
            'message'    => "{$reservation->user->name} cancelled reservation {$reservation->reservation_number}.",
            'action_url' => route('admin.reservations.show', $reservation),
        ]);
    }

    public function calculateTotal(Court $court, string $startTime, string $endTime): float
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $hours = max(1, ($end - $start) / 3600);

        return (float) $court->hourly_rate * $hours;
    }
}
