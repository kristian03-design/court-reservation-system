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
        $courts = Court::query()
            ->when($request->search, fn ($query, $search) => $query->where('court_name', 'like', "%{$search}%"))
            ->when($request->type, fn ($query, $type) => $query->where('court_type', $type))
            ->when($request->sort === 'rate_low', fn ($query) => $query->orderBy('hourly_rate'))
            ->when($request->sort === 'rate_high', fn ($query) => $query->orderByDesc('hourly_rate'))
            ->when(! $request->sort, fn ($query) => $query->latest())
            ->paginate(9)
            ->withQueryString();

        return view('pages.courts', [
            'courts' => $courts,
            'types' => ['Basketball', 'Volleyball', 'Badminton', 'Tennis', 'Futsal'],
        ]);
    }

    public function show(Court $court, AvailabilityService $availabilityService): View
    {
        return view('pages.court-details', [
            'court' => $court,
            'slots' => $availabilityService->dailySlots($court, now()->toDateString()),
        ]);
    }

    public function availability(Court $court, Request $request, AvailabilityService $availabilityService): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        return response()->json($availabilityService->dailySlots($court, $date));
    }
}
