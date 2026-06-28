<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Court;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reservations = $request->user()->reservations()
            ->with(['court', 'payment'])
            ->orderBy('id')
            ->cursorPaginate(15);

        return response()->json($reservations);
    }

    public function store(StoreReservationRequest $request, ReservationService $reservationService): JsonResponse
    {
        $court = Court::findOrFail($request->validated('court_id'));
        $reservation = $reservationService->create($request->user(), $court, $request->validated());

        return response()->json([
            'message' => 'Reservation created successfully.',
            'reservation' => $reservation,
        ], 201);
    }

    public function show(Reservation $reservation, Request $request): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($reservation->load(['court', 'payment']));
    }
}
