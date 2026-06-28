@extends('user.layouts.shell', ['pageTitle' => 'My Reservations'])

@section('content')
    <div class="scroll-reveal reveal-fade-up" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <p class="dash-kicker" style="margin: 0 0 4px;">Booking History</p>
            <h1 style="font-family: var(--font-display); font-size: 28px; font-weight: 800; color: var(--text-primary); margin: 0;">Reservations</h1>
        </div>
        <a href="{{ route('booking.create') }}" class="btn btn-primary">Book a Court</a>
    </div>

    @php
        $reservations = $reservations ?? collect();
        $currentStatus = request('status', 'all');
    @endphp

    <!-- Filter Tabs -->
    <div class="filter-tabs scroll-reveal reveal-fade-up stagger-1" style="margin-bottom: 24px;">
        <a href="{{ route('reservations.index') }}" class="{{ $currentStatus === 'all' ? 'active' : '' }}">All</a>
        <a href="{{ route('reservations.index', ['status' => 'pending']) }}" class="{{ $currentStatus === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('reservations.index', ['status' => 'approved']) }}" class="{{ $currentStatus === 'approved' ? 'active' : '' }}">Approved</a>
        <a href="{{ route('reservations.index', ['status' => 'completed']) }}" class="{{ $currentStatus === 'completed' ? 'active' : '' }}">Completed</a>
        <a href="{{ route('reservations.index', ['status' => 'cancelled']) }}" class="{{ $currentStatus === 'cancelled' ? 'active' : '' }}">Cancelled</a>
    </div>

    @if ($reservations->isEmpty())
        <div class="empty-state scroll-reveal reveal-fade-up stagger-2">
            <div class="empty-state-icon">
                <i data-lucide="clipboard-list"></i>
            </div>
            <h3>No reservations found</h3>
            <p>We couldn't find any reservations matching "{{ $currentStatus }}" status. Try changing filters or start a new booking.</p>
            <a href="{{ route('booking.create') }}" class="btn btn-primary" style="margin-top: 8px;">Book a Court</a>
        </div>
    @else
        <div class="table-wrap scroll-reveal reveal-fade-up stagger-2">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reservation</th>
                            <th>Court Space</th>
                            <th>Schedule</th>
                            <th>Players</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td data-label="Reservation" style="font-weight: 700; color: var(--text-primary);">{{ $reservation->reservation_number }}</td>
                                <td data-label="Court" style="color: var(--text-primary); font-weight: 500;">{{ $reservation->court->court_name }}</td>
                                <td data-label="Schedule">
                                    {{ $reservation->reservation_date->format('M d, Y') }}
                                    &nbsp;&middot;&nbsp;
                                    <span style="color: var(--text-primary);">{{ substr($reservation->start_time, 0, 5) }}&ndash;{{ substr($reservation->end_time, 0, 5) }}</span>
                                </td>
                                <td data-label="Players">{{ $reservation->players }} Players</td>
                                <td data-label="Status">
                                    <span class="status status-{{ $reservation->status }}">{{ $reservation->status }}</span>
                                </td>
                                <td data-label="Payment">
                                    <span class="status status-{{ $reservation->payment?->payment_status ?? 'unpaid' }}">
                                        {{ str_replace('_', ' ', $reservation->payment?->payment_status ?? 'unpaid') }}
                                    </span>
                                </td>
                                <td data-label="Action" style="text-align:right;">
                                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; border-radius: var(--radius-sm);">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($reservations, 'links'))
            <div class="pagination-row">
                {{ $reservations->links() }}
            </div>
        @endif
    @endif
@endsection