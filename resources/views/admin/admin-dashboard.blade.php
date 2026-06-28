@extends('admin.layouts.shell', ['pageTitle' => 'Dashboard', 'active' => 'dashboard'])

@section('content')
    @php
        $upcomingReservations = $recentReservations
            ->filter(fn ($reservation) => $reservation->reservation_date->isFuture() || $reservation->reservation_date->isToday())
            ->take(4);
    @endphp

    <div class="admin-dashboard-grid">
        <div class="admin-dashboard-main">
            <header class="admin-welcome">
                <div>
                    <h1>Welcome back, {{ explode(' ', auth('admin')->user()->name)[0] }}</h1>
                    <p>Track occupancy, payments, and verify court bookings.</p>
                </div>
            </header>

            <!-- 4 Stat Cards Grid -->
            <section class="admin-stat-strip" aria-label="Dashboard statistics">
                <article class="admin-stat-card stat-green">
                    <span class="stat-icon"><i class="ti ti-calendar-check" aria-hidden="true"></i></span>
                    <div>
                        <p>Today's Bookings</p>
                        <strong>{{ $stats['todays_reservations'] }}</strong>
                        <small>Active slots today</small>
                    </div>
                </article>
                <article class="admin-stat-card stat-blue">
                    <span class="stat-icon"><i class="ti ti-cash" aria-hidden="true"></i></span>
                    <div>
                        <p>Revenue</p>
                        <strong>₱{{ number_format($stats['revenue'], 0) }}</strong>
                        <small>Total paid to date</small>
                    </div>
                </article>
                <article class="admin-stat-card stat-orange">
                    <span class="stat-icon"><i class="ti ti-layout-grid" aria-hidden="true"></i></span>
                    <div>
                        <p>Active Courts</p>
                        <strong>{{ $stats['active_courts'] }}</strong>
                        <small>Online spaces</small>
                    </div>
                </article>
                <article class="admin-stat-card stat-red">
                    <span class="stat-icon"><i class="ti ti-alert-circle" aria-hidden="true"></i></span>
                    <div>
                        <p>Pending Review</p>
                        <strong>{{ $stats['pending_reservations'] }}</strong>
                        <small>Awaiting confirmation</small>
                    </div>
                </article>
            </section>

            <!-- Court Utilization Grid Heatmap -->
            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h2>Today's Court Utilization</h2>
                        <p>Hourly occupancy tracking for all courts today.</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                <span style="width: 10px; height: 10px; background: var(--border); border-radius: 2px; display: inline-block;"></span> Available
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                <span style="width: 10px; height: 10px; background: #FFC800; border-radius: 2px; display: inline-block;"></span> Pending
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                <span style="width: 10px; height: 10px; background: var(--lime); border-radius: 2px; display: inline-block;"></span> Approved
                            </span>
                        </div>
                    </div>
                </div>
                
                <div style="padding: 20px; overflow-x: auto;">
                    <div style="min-width: 900px; display: grid; gap: 10px;">
                        <!-- Hour headers row -->
                        <div style="display: grid; grid-template-columns: 200px repeat(16, 1fr); gap: 6px; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                            <div style="font-weight: 700; color: var(--muted); font-size: 13px; text-transform: uppercase; letter-spacing: 0.06em;">Court Space</div>
                            @foreach (range(6, 21) as $hour)
                                @php
                                    $formattedHour = $hour > 12 ? ($hour - 12) . ' PM' : ($hour === 12 ? '12 PM' : $hour . ' AM');
                                @endphp
                                <div style="text-align: center; font-weight: 700; color: var(--muted); font-size: 11px; white-space: nowrap;">{{ $formattedHour }}</div>
                            @endforeach
                        </div>

                        <!-- Court rows -->
                        @forelse ($courts as $court)
                            <div style="display: grid; grid-template-columns: 200px repeat(16, 1fr); gap: 6px; align-items: center;">
                                <!-- Court Name -->
                                <div style="font-weight: 700; color: var(--text); font-size: 15px; padding-right: 8px;">
                                    {{ $court->court_name }}
                                    <span style="display: block; font-size: 12px; color: var(--muted); font-weight: 500; margin-top: 2px;">{{ $court->court_type }}</span>
                                </div>

                                <!-- Hour cells -->
                                @foreach (range(6, 21) as $hour)
                                    @php
                                        $slotStart = sprintf('%02d:00:00', $hour);
                                        $slotEnd = sprintf('%02d:00:00', $hour + 1);
                                        
                                        // Find reservation overlapping this hour
                                        $res = $court->reservations->first(function ($r) use ($slotStart) {
                                            return $r->start_time <= $slotStart && $r->end_time > $slotStart;
                                        });

                                        $status = $res ? $res->status : null;
                                        $bg = 'var(--surface-3)';
                                        $border = '1px solid var(--border)';
                                        $title = 'Available (' . $court->court_name . ' @ ' . $hour . ':00)';
                                        
                                        if ($status === 'approved' || $status === 'completed') {
                                            $bg = 'var(--lime)';
                                            $border = '1px solid var(--lime)';
                                            $title = 'Booked: #' . $res->reservation_number . ' (' . $res->user->name . ')';
                                        } elseif ($status === 'pending') {
                                            $bg = '#FFC800';
                                            $border = '1px solid #FFC800';
                                            $title = 'Pending: #' . $res->reservation_number . ' (' . $res->user->name . ')';
                                        }
                                    @endphp
                                    
                                    <div style="height: 52px; background: {{ $bg }}; border: {{ $border }}; border-radius: 8px; transition: opacity var(--transition), transform var(--transition); cursor: {{ $res ? 'pointer' : 'default' }};"
                                         title="{{ $title }}"
                                         @if($res) onclick="window.location.href='{{ route('admin.reservations.show', $res) }}'" @endif
                                         onmouseover="this.style.opacity='0.75'; this.style.transform='scaleY(0.95)';"
                                         onmouseout="this.style.opacity='1'; this.style.transform='scaleY(1)';"
                                    ></div>
                                @endforeach
                            </div>
                        @empty
                            <div style="text-align: center; color: var(--muted); padding: 32px 0; font-size: 14px;">No courts configured.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- Recent Bookings Table -->
            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h2>Recent Reservations</h2>
                        <p>Latest booking activity across all courts.</p>
                    </div>
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline btn-sm">View All</a>
                </div>
                <div class="table-wrap">
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Reservation</th>
                                    <th>Court</th>
                                    <th>User</th>
                                    <th>Schedule</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentReservations as $reservation)
                                    <tr>
                                        <td data-label="Reservation" style="font-weight: 700; color: var(--text);">{{ $reservation->reservation_number }}</td>
                                        <td data-label="Court">{{ $reservation->court->court_name }}</td>
                                        <td data-label="User">{{ $reservation->user->name }}</td>
                                        <td data-label="Schedule">{{ $reservation->reservation_date->format('M d, Y') }} {{ substr($reservation->start_time, 0, 5) }}-{{ substr($reservation->end_time, 0, 5) }}</td>
                                        <td data-label="Status"><span class="status status-{{ $reservation->status }}">{{ $reservation->status }}</span></td>
                                        <td data-label="Payment"><span class="status status-{{ $reservation->payment?->payment_status ?? 'unpaid' }}">{{ str_replace('_', ' ', $reservation->payment?->payment_status ?? 'unpaid') }}</span></td>
                                        <td data-label="Action" style="text-align:right;"><a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-outline btn-sm">Review</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">No reservations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <!-- Sidebar Panel Options -->
        <aside class="admin-dashboard-aside">
            <section class="admin-side-card">
                <h2>Quick Actions</h2>
                <div class="admin-action-list">
                    <a href="{{ route('admin.courts.create') }}"><i class="ti ti-plus" aria-hidden="true"></i> Add Court</a>
                    <a href="{{ route('admin.courts.index') }}"><i class="ti ti-layout-grid" aria-hidden="true"></i> View Courts</a>
                    <a href="{{ route('admin.reservations.index') }}"><i class="ti ti-calendar-event" aria-hidden="true"></i> Reservations</a>
                </div>
            </section>

            <section class="admin-side-card">
                <div class="admin-side-head">
                    <h2>Upcoming Bookings</h2>
                    <a href="{{ route('admin.reservations.index') }}">View All</a>
                </div>
                <div class="admin-booking-list">
                    @forelse ($upcomingReservations as $reservation)
                        <a href="{{ route('admin.reservations.show', $reservation) }}" class="admin-booking-item {{ $reservation->status === 'approved' ? 'is-approved' : 'is-pending' }}">
                            <span><i class="ti ti-calendar-event" aria-hidden="true"></i></span>
                            <div>
                                <strong>{{ $reservation->court->court_name }}</strong>
                                <small>{{ $reservation->reservation_date->format('M d, Y') }}</small>
                            </div>
                            <em>{{ substr($reservation->start_time, 0, 5) }}-{{ substr($reservation->end_time, 0, 5) }}</em>
                        </a>
                    @empty
                        <p class="admin-empty-note">No upcoming bookings yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="admin-side-card">
                <h2>Booking Status</h2>
                <div class="status-breakdown-list">
                    @foreach ($statusBreakdown as $status => $total)
                        <div>
                            <span class="status status-{{ $status }}">{{ str_replace('_', ' ', $status) }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
@endsection
