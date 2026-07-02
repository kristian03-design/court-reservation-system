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

        // Pre-fetch active reservations for this court on this date
        $reservations = Reservation::query()
            ->where('court_id', $court->id)
            ->whereDate('reservation_date', $date)
            ->active()
            ->get();

        // Pre-fetch blocked schedules
        $blockedSchedules = CourtSchedule::query()
            ->where('court_id', $court->id)
            ->whereDate('schedule_date', $date)
            ->whereIn('availability_status', ['reserved', 'maintenance', 'closed'])
            ->get();

        while ($cursor->lt($close)) {
            $end = $cursor->copy()->addHour();
            $startTime = $cursor->format('H:i:s');
            $endTime = $end->format('H:i:s');

            // Check overlap in memory
            $isBlocked = $blockedSchedules->contains(function ($schedule) use ($startTime, $endTime) {
                return $schedule->start_time < $endTime && $schedule->end_time > $startTime;
            });

            $hasReservation = !$isBlocked && $reservations->contains(function ($res) use ($startTime, $endTime) {
                return $res->start_time < $endTime && $res->end_time > $startTime;
            });

            $available = !$isBlocked && !$hasReservation && $court->status === 'available';

            $slots[] = [
                'start' => $cursor->format('H:i'),
                'end' => $end->format('H:i'),
                'label' => $cursor->format('g:i A').' - '.$end->format('g:i A'),
                'available' => $available,
            ];
            $cursor->addHour();
        }

        return $slots;
    }
}
