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
            ->when($request->court_id, fn ($query, $courtId) => $query->where('court_id', $courtId))
            ->when($request->date, fn ($query, $date) => $query->whereDate('reservation_date', $date))
            ->when($request->search, function ($query, $search) {
                $query->where('reservation_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            })
            ->latest('reservation_date')
            ->paginate(15)
            ->withQueryString();

        $courts = \App\Models\Court::orderBy('court_name')->get();

        return view('admin.reservations.admin-reservations', [
            'reservations' => $reservations,
            'courts' => $courts,
        ]);
    }

    public function show(Reservation $reservation): View
    {
        return view('admin.reservations.admin-reservation-show', [
            'reservation' => $reservation->load(['court', 'user', 'payment']),
        ]);
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:held,pending_payment,confirmed,completed,cancelled,expired'],
        ]);

        $reservation->update($data);

        \App\Services\AuditLogService::log('reservation_status_updated', $reservation, [
            'reservation_number' => $reservation->reservation_number,
            'status' => $data['status'],
        ]);

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
                    'confirmed' => '#22C55E',
                    'cancelled', 'expired' => '#EF4444',
                    'completed' => '#3B82F6',
                    default => '#F59E0B',
                },
            ])
        );
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $resNum = $reservation->reservation_number;
        $reservation->delete();

        \App\Services\AuditLogService::log('reservation_deleted', null, [
            'reservation_number' => $resNum,
        ]);

        return back()->with('success', 'Reservation deleted.');
    }
}
