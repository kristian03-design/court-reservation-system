<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiCourtController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $courts = Court::available()
            ->orderBy('id')
            ->cursorPaginate(10);

        return response()->json($courts);
    }

    public function availability(Court $court, Request $request, AvailabilityService $availabilityService): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());
        $slots = $availabilityService->dailySlots($court, $date);

        return response()->json([
            'court' => $court->only(['id', 'court_name', 'court_type', 'hourly_rate']),
            'date' => $date,
            'slots' => $slots,
        ]);
    }
}
