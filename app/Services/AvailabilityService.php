<?php

namespace App\Services;

use App\Models\Court;
use App\Models\CourtSchedule;
use App\Models\Reservation;
use Carbon\Carbon;

class AvailabilityService
{
    public function isSlotAvailable(Court $court, string $date, string $startTime, string $endTime, ?int $ignoreReservationId = null): bool
    {
        if ($court->status !== 'available') {
            return false;
        }

        $blockedSchedule = CourtSchedule::query()
            ->where('court_id', $court->id)
            ->whereDate('schedule_date', $date)
            ->whereIn('availability_status', ['reserved', 'maintenance', 'closed'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        if ($blockedSchedule) {
            return false;
        }

        return ! Reservation::query()
            ->where('court_id', $court->id)
            ->whereDate('reservation_date', $date)
            ->active()
            ->when($ignoreReservationId, fn ($query) => $query->whereKeyNot($ignoreReservationId))
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();
    }

    public function dailySlots(Court $court, string $date): array
    {
        $slots = [];
        $cursor = Carbon::parse($date.' 08:00');
        $close = Carbon::parse($date.' 22:00');

        while ($cursor->lt($close)) {
            $end = $cursor->copy()->addHour();
            $slots[] = [
                'start' => $cursor->format('H:i'),
                'end' => $end->format('H:i'),
                'label' => $cursor->format('g:i A').' - '.$end->format('g:i A'),
                'available' => $this->isSlotAvailable($court, $date, $cursor->format('H:i:s'), $end->format('H:i:s')),
            ];
            $cursor->addHour();
        }

        return $slots;
    }
}
