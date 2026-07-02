<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourtController extends Controller
{
    public function index(Request $request): View
    {
        $isDefault = !$request->search && !$request->type && !$request->sort;

        if ($isDefault) {
            $page = $request->input('page', 1);
            $courts = \Illuminate\Support\Facades\Cache::remember("courts_list_page_{$page}", 3600, function () {
                return Court::latest()->paginate(9);
            });
        } else {
            $courts = Court::query()
                ->when($request->search, fn ($query, $search) => $query->where('court_name', 'like', "%{$search}%"))
                ->when($request->type, fn ($query, $type) => $query->where('court_type', $type))
                ->when($request->sort === 'rate_low', fn ($query) => $query->orderBy('hourly_rate'))
                ->when($request->sort === 'rate_high', fn ($query) => $query->orderByDesc('hourly_rate'))
                ->when(! $request->sort, fn ($query) => $query->latest())
                ->paginate(9)
                ->withQueryString();
        }

        return view('guest.guest-courts', [
            'courts' => $courts,
            'types' => ['Basketball', 'Volleyball', 'Badminton', 'Tennis', 'Futsal'],
        ]);
    }

    public function show(Court $court, AvailabilityService $availabilityService): View
    {
        return view('guest.guest-courtdetails', [
            'court' => $court,
            'slots' => $availabilityService->dailySlots($court, now()->toDateString()),
        ]);
    }

    public function availability(Court $court, Request $request, AvailabilityService $availabilityService): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        return response()->json($availabilityService->dailySlots($court, $date));
    }

    public function globalAvailability(Request $request, AvailabilityService $availabilityService): View
    {
        $date = $request->query('date', now()->toDateString());

        $type = $request->query('type');

        $courts = Court::query()
            ->when($type, fn ($query, $type) => $query->where('court_type', $type))
            ->where('status', 'available')
            ->get();

        $courtIds = $courts->pluck('id');

        // Fetch all reservations in a single query
        $allReservations = \App\Models\Reservation::query()
            ->whereIn('court_id', $courtIds)
            ->whereDate('reservation_date', $date)
            ->active()
            ->get()
            ->groupBy('court_id');

        // Fetch all blocked schedules in a single query
        $allBlockedSchedules = \App\Models\CourtSchedule::query()
            ->whereIn('court_id', $courtIds)
            ->whereDate('schedule_date', $date)
            ->whereIn('availability_status', ['reserved', 'maintenance', 'closed'])
            ->get()
            ->groupBy('court_id');

        $courtsWithSlots = $courts->map(function (Court $court) use ($date, $availabilityService, $allReservations, $allBlockedSchedules) {
            $courtReservations = $allReservations->get($court->id, collect());
            $courtSchedules = $allBlockedSchedules->get($court->id, collect());

            return [
                'court' => $court,
                'slots' => $availabilityService->dailySlots($court, $date, $courtReservations, $courtSchedules),
            ];
        });

        return view('guest.guest-availability', [
            'date' => $date,
            'selectedType' => $type,
            'courtsWithSlots' => $courtsWithSlots,
            'types' => ['Basketball', 'Volleyball', 'Badminton', 'Tennis', 'Futsal'],
        ]);
    }
}
