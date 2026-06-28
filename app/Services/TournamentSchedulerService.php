<?php

namespace App\Services;

use App\Models\Court;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentSchedule;
use Carbon\Carbon;

class TournamentSchedulerService
{
    protected AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Automatically schedule unscheduled matches for a tournament.
     */
    public function schedule(Tournament $tournament): void
    {
        $matches = $tournament->matches()
            ->whereIn('status', ['ready', 'waiting', 'scheduled'])
            ->orderBy('round_number', 'asc')
            ->orderBy('match_number', 'asc')
            ->get();

        $courts = Court::where('status', 'available')->get();
        if ($courts->isEmpty()) {
            throw new \Exception('No available courts found to schedule tournament matches.');
        }

        $startDate = Carbon::parse($tournament->start_date);
        $durationHours = 1; // 1 hour match duration
        $bufferMinutes = 15; // 15 minutes buffer

        foreach ($matches as $match) {
            // If already scheduled, skip or re-schedule
            if ($match->schedule) {
                continue;
            }

            // Find a slot for this match
            $scheduled = false;
            $currentDate = $startDate->copy();
            
            // Try up to 30 days out to find slots
            for ($day = 0; $day < 30; $day++) {
                // Check hourly slots from 08:00 to 21:00
                $cursor = Carbon::parse($currentDate->format('Y-m-d') . ' 08:00');
                $endOfDay = Carbon::parse($currentDate->format('Y-m-d') . ' 21:00');

                while ($cursor->lt($endOfDay)) {
                    $slotStart = $cursor->format('Y-m-d H:i:s');
                    $slotEnd = $cursor->copy()->addHours($durationHours)->format('Y-m-d H:i:s');

                    foreach ($courts as $court) {
                        if ($this->isSlotFreeForMatch($court, $match, $slotStart, $slotEnd)) {
                            // Create schedule
                            TournamentSchedule::create([
                                'match_id' => $match->id,
                                'court_id' => $court->id,
                                'start_time' => $slotStart,
                                'end_time' => $slotEnd,
                                'buffer_minutes' => $bufferMinutes,
                                'status' => 'scheduled',
                            ]);

                            $match->update(['status' => 'scheduled']);
                            $scheduled = true;
                            break 2; // Slot found, break out of courts & hours loops
                        }
                    }
                    // Add 1 hour + buffer to cursor
                    $cursor->addHours($durationHours)->addMinutes($bufferMinutes);
                }

                $currentDate->addDay();
            }

            if (!$scheduled) {
                // If we couldn't schedule, we continue but don't crash
                continue;
            }
        }

        $tournament->update(['status' => 'scheduled']);
    }

    /**
     * Verify if a court slot is free and participants have no conflicts.
     */
    protected function isSlotFreeForMatch(Court $court, TournamentMatch $match, string $start, string $end): bool
    {
        $carbonStart = Carbon::parse($start);
        $carbonEnd = Carbon::parse($end);
        $dateStr = $carbonStart->format('Y-m-d');
        $timeStartStr = $carbonStart->format('H:i:s');
        $timeEndStr = $carbonEnd->format('H:i:s');

        // 1. Check client reservations & maintenance blocks
        if (!$this->availabilityService->isSlotAvailable($court, $dateStr, $timeStartStr, $timeEndStr)) {
            return false;
        }

        // 2. Check other tournament schedules on this court
        $courtConflict = TournamentSchedule::where('court_id', $court->id)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();

        if ($courtConflict) {
            return false;
        }

        // 3. Check participant conflicts (neither participant should be playing elsewhere at this time)
        $participantIds = array_filter([$match->participant1_id, $match->participant2_id]);
        if (!empty($participantIds)) {
            $participantConflict = TournamentSchedule::whereHas('match', function ($q) use ($participantIds) {
                $q->whereIn('participant1_id', $participantIds)
                  ->orWhereIn('participant2_id', $participantIds);
            })
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();

            if ($participantConflict) {
                return false;
            }
        }

        return true;
    }
}
