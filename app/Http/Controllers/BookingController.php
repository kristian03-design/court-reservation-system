<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Court;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request, AvailabilityService $availabilityService): View
    {
        $court = Court::find($request->query('court'));

        return view('pages.booking', [
            'courts' => Court::available()->orderBy('court_name')->get(),
            'court' => $court,
            'slots' => $court ? $availabilityService->dailySlots($court, now()->toDateString()) : [],
        ]);
    }

    public function store(StoreReservationRequest $request, ReservationService $reservationService): RedirectResponse
    {
        $court = Court::findOrFail($request->validated('court_id'));
        $reservation = $reservationService->create($request->user(), $court, $request->validated());

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Reservation created and awaiting approval.');
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);

        return view('user.reservations.show', [
            'reservation' => $reservation->load(['court', 'payment']),
        ]);
    }

    public function cancel(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservation cancelled.');
    }
}
