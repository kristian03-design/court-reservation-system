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
    public function index(Request $request): View
    {
        $query = $request->user()->reservations()
            ->with(['court', 'payment']);

        $status = $request->query('status');
        if ($status && in_array($status, ['held', 'pending_payment', 'confirmed', 'completed', 'cancelled', 'expired'])) {
            $query->where('status', $status);
        }

        $reservations = $query->latest('reservation_date')->paginate(15);

        return view('user.reservations.user-reservations', [
            'reservations' => $reservations,
        ]);
    }

    public function create(Request $request, AvailabilityService $availabilityService): View
    {
        $court = Court::find($request->query('court'));
        $date = $request->query('reservation_date', now()->toDateString());

        return view('user.reservations.user-booking', [
            'courts' => Court::available()->orderBy('court_name')->get(),
            'court' => $court,
            'slots' => $court ? $availabilityService->dailySlots($court, $date) : [],
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

        return view('user.reservations.user-reservation-show', [
            'reservation' => $reservation->load(['court', 'payment']),
        ]);
    }

    public function cancel(Reservation $reservation, ReservationService $reservationService): RedirectResponse
    {
        $this->authorize('update', $reservation);

        $reservationService->cancel($reservation);

        return back()->with('success', 'Reservation cancelled.');
    }
}
