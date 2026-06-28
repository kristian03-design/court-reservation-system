<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\CourtSchedule;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourtScheduleController extends Controller
{
    public function index(Court $court): View
    {
        $schedules = $court->schedules()
            ->orderBy('schedule_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(15);

        return view('admin.courts.admin-court-schedules', [
            'court' => $court,
            'schedules' => $schedules,
        ]);
    }

    public function store(Request $request, Court $court): RedirectResponse
    {
        $data = $request->validate([
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'availability_status' => ['required', 'in:maintenance,closed,reserved'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        // Check for overlapping blocks
        $overlap = $court->schedules()
            ->whereDate('schedule_date', $data['schedule_date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors(['start_time' => 'A schedule block already overlaps with this time window.']);
        }

        $schedule = $court->schedules()->create($data);

        // Write Audit Log
        if (class_exists(AuditLogService::class)) {
            AuditLogService::log('schedule_created', $schedule, [
                'court_name' => $court->court_name,
                'date' => $schedule->schedule_date->toDateString(),
                'time' => "{$schedule->start_time} - {$schedule->end_time}",
                'status' => $schedule->availability_status,
            ]);
        }

        return back()->with('success', 'Court schedule block added successfully.');
    }

    public function destroy(Court $court, CourtSchedule $schedule): RedirectResponse
    {
        // Ensure this schedule belongs to the court
        if ($schedule->court_id !== $court->id) {
            abort(404);
        }

        $schedule->delete();

        // Write Audit Log
        if (class_exists(AuditLogService::class)) {
            AuditLogService::log('schedule_deleted', $court, [
                'court_name' => $court->court_name,
                'date' => $schedule->schedule_date->toDateString(),
                'time' => "{$schedule->start_time} - {$schedule->end_time}",
            ]);
        }

        return back()->with('success', 'Court schedule block removed successfully.');
    }
}
