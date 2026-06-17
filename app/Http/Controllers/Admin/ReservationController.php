<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = Reservation::with(['court', 'user', 'payment'])
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->search, function ($query, $search) {
                $query->where('reservation_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            })
            ->latest('reservation_date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reservations.index', ['reservations' => $reservations]);
    }

    public function show(Reservation $reservation): View
    {
        return view('admin.reservations.show', [
            'reservation' => $reservation->load(['court', 'user', 'payment']),
        ]);
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,cancelled,completed'],
        ]);

        $reservation->update($data);
        $reservation->user->systemNotifications()->create([
            'title' => 'Reservation '.$data['status'],
            'message' => "Reservation {$reservation->reservation_number} was marked {$data['status']}.",
        ]);

        return back()->with('success', 'Reservation status updated.');
    }

    public function calendar(): JsonResponse
    {
        return response()->json(
            Reservation::with('court')->get()->map(fn ($reservation) => [
                'id' => $reservation->id,
                'title' => $reservation->court->court_name.' - '.$reservation->status,
                'start' => $reservation->reservation_date->toDateString().'T'.$reservation->start_time,
                'end' => $reservation->reservation_date->toDateString().'T'.$reservation->end_time,
                'color' => match ($reservation->status) {
                    'approved' => '#22C55E',
                    'rejected', 'cancelled' => '#EF4444',
                    'completed' => '#3B82F6',
                    default => '#F59E0B',
                },
            ])
        );
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $reservation->delete();

        return back()->with('success', 'Reservation deleted.');
    }
}
