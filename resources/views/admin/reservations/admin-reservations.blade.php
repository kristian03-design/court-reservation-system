@extends('admin.layouts.shell', ['pageTitle' => 'Reservations', 'active' => 'reservations'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Booking Management</p>
            <h1>Reservations</h1>
        </div>
    </div>

    <section class="mt-6">
        <!-- Filters Panel -->
        <div class="card" style="margin-bottom: 20px; padding: 16px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);">
            <form method="GET" action="{{ route('admin.reservations.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) auto; gap: 14px; align-items: end;">
                <!-- Keyword Search -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Res # or Customer Name" style="width: 100%; height: 38px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg); color: var(--text); padding: 8px 12px; font-size: 13px; outline: none; transition: border-color var(--transition);">
                </div>

                <!-- Status Filter -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Status</label>
                    <div style="position: relative;">
                        <select name="status" style="width: 100%; height: 38px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg); color: var(--text); padding: 8px 30px 8px 12px; font-size: 13px; outline: none; appearance: none; cursor: pointer; transition: border-color var(--transition);">
                            <option value="">All Statuses</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                        </select>
                        <i class="ti ti-chevron-down" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--muted); font-size: 12px;"></i>
                    </div>
                </div>

                <!-- Court Filter -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Court Space</label>
                    <div style="position: relative;">
                        <select name="court_id" style="width: 100%; height: 38px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg); color: var(--text); padding: 8px 30px 8px 12px; font-size: 13px; outline: none; appearance: none; cursor: pointer; transition: border-color var(--transition);">
                            <option value="">All Courts</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" @selected(request('court_id') == $court->id)>{{ $court->court_name }}</option>
                            @endforeach
                        </select>
                        <i class="ti ti-chevron-down" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--muted); font-size: 12px;"></i>
                    </div>
                </div>

                <!-- Date Filter -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" style="width: 100%; height: 38px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg); color: var(--text); padding: 8px 12px; font-size: 13px; outline: none; color-scheme: dark; transition: border-color var(--transition);">
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline" style="height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: var(--radius);">Reset</a>
                    <button type="submit" class="btn" style="background: var(--lime); color: var(--bg); font-weight: 700; border: none; height: 38px; padding: 0 20px; border-radius: var(--radius); transition: opacity var(--transition);">Filter</button>
                </div>
            </form>
        </div>

        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reservation #</th>
                            <th>Customer</th>
                            <th>Court</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $reservation)
                            <tr>
                                <td class="font-semibold" data-label="Reservation #">{{ $reservation->reservation_number }}</td>
                                <td data-label="Customer">{{ $reservation->user->name ?? 'N/A' }}</td>
                                <td data-label="Court">{{ $reservation->court->court_name }}</td>
                                <td data-label="Date &amp; Time">{{ $reservation->reservation_date->format('M d, Y') }}<br><span style="font-size:11px;color:var(--muted)">{{ substr($reservation->start_time,0,5) }}–{{ substr($reservation->end_time,0,5) }}</span></td>
                                <td data-label="Status"><span class="status status-{{ $reservation->status }}">{{ $reservation->status }}</span></td>
                                <td data-label="Payment"><span class="status status-{{ $reservation->payment?->payment_status ?? 'unpaid' }}">{{ str_replace('_',' ',$reservation->payment?->payment_status ?? 'unpaid') }}</span></td>
                                <td style="text-align: right;" data-label="Action">
                                    <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-outline btn-sm">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;padding:32px;">No reservations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($reservations->hasPages())
                <div style="padding: 16px; border-top: 1px solid var(--border);">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
