@extends('user.layouts.shell', ['pageTitle' => 'Player Dashboard'])

@section('content')
    @php
        $nextReservation = $upcoming->first();
        $completionTotal = max(1, $stats['history']);
        $completionRate = min(100, round(($stats['completed'] / $completionTotal) * 100));
        $firstName = explode(' ', auth()->user()->name)[0];
    @endphp

    {{-- ═══════════ MAIN CONTENT AREA ═══════════ --}}
    <div class="dash-layout">
        <!-- Left Column -->
        <div style="display:flex; flex-direction:column; gap:24px; min-width:0;">
            {{-- HERO COPY --}}
            <div class="dashboard-hero-copy scroll-reveal reveal-fade-up">
                <p class="dash-kicker">Member Dashboard</p>
                <h1>Hey, {{ $firstName }}.</h1>
                <p>
                    @if ($stats['upcoming'] > 0)
                        You have <strong style="color:var(--lime);">{{ $stats['upcoming'] }}</strong> upcoming {{ $stats['upcoming'] === 1 ? 'reservation' : 'reservations' }}.
                    @else
                        Your schedule is clear. Time to book a court.
                    @endif
                </p>
                <div class="dashboard-hero-actions">
                    <a href="{{ route('booking.create') }}" class="btn btn-primary">
                        <i data-lucide="calendar-plus"></i>
                        Reserve a Court
                    </a>
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline">
                        <i data-lucide="clipboard-list"></i>
                        My Reservations
                    </a>
                </div>
            </div>

            {{-- STATS BAR --}}
            <section class="metric-grid" aria-label="Reservation stats">
                <article class="metric-card scroll-reveal reveal-fade-up stagger-1">
                    <span>Upcoming</span>
                    <strong>{{ $stats['upcoming'] }}</strong>
                    <small>Pending and approved slots</small>
                    <i data-lucide="calendar-days"></i>
                </article>
                <article class="metric-card scroll-reveal reveal-fade-up stagger-2">
                    <span>Completed</span>
                    <strong>{{ $stats['completed'] }}</strong>
                    <small>{{ $completionRate }}% of your history</small>
                    <i data-lucide="check-circle-2"></i>
                </article>
                <article class="metric-card scroll-reveal reveal-fade-up stagger-3">
                    <span>Cancelled</span>
                    <strong>{{ $stats['cancelled'] }}</strong>
                    <small>Slots removed from play</small>
                    <i data-lucide="alert-triangle"></i>
                </article>
                <article class="metric-card scroll-reveal reveal-fade-up stagger-4">
                    <span>All Time</span>
                    <strong>{{ $stats['history'] }}</strong>
                    <small>Total reservations created</small>
                    <i data-lucide="clipboard-list"></i>
                </article>
            </section>

            {{-- Upcoming reservations panel --}}
            <section class="dashboard-panel scroll-reveal reveal-fade-up stagger-1">
                <div class="section-title-row">
                    <div>
                        <p class="dash-kicker">Up next</p>
                        <h2>Upcoming Reservations</h2>
                    </div>
                    <a href="{{ route('reservations.index') }}">View all →</a>
                </div>

                @if ($upcoming->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-lucide="clipboard-list"></i>
                        </div>
                        <h3>No Upcoming Reservations</h3>
                        <p>Your schedule is clear. Book your next match and it will appear here.</p>
                        <a href="{{ route('booking.create') }}" class="btn btn-primary">Book a Court</a>
                    </div>
                @else
                    <div class="reservation-card-list">
                        @foreach ($upcoming as $reservation)
                            <article class="reservation-card">
                                <div class="reservation-date-tile">
                                    <span>{{ $reservation->reservation_date->format('M') }}</span>
                                    <strong>{{ $reservation->reservation_date->format('d') }}</strong>
                                </div>
                                <div class="reservation-main">
                                    <div class="reservation-topline">
                                        <span>{{ $reservation->reservation_number }}</span>
                                        <span class="status status-{{ $reservation->status }}">{{ $reservation->status }}</span>
                                    </div>
                                    <h3>{{ $reservation->court->court_name }}</h3>
                                    <p>{{ substr($reservation->start_time, 0, 5) }} – {{ substr($reservation->end_time, 0, 5) }} &middot; {{ $reservation->players }} {{ \Illuminate\Support\Str::plural('player', $reservation->players) }}</p>
                                </div>
                                <div class="reservation-side">
                                    <span class="status status-{{ $reservation->payment?->payment_status ?? 'unpaid' }}">
                                        {{ str_replace('_', ' ', $reservation->payment?->payment_status ?? 'unpaid') }}
                                    </span>
                                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline btn-sm">Details</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>

        {{-- Side console --}}
        <aside class="side-console">
            {{-- Next play pass card --}}
            <aside class="next-pass-card scroll-reveal reveal-fade-left stagger-1" aria-label="Next reservation">
                <span class="pass-label">Next Play Pass</span>
                @if ($nextReservation)
                    <div class="pass-status-row">
                        <span class="status status-{{ $nextReservation->status }}">{{ $nextReservation->status }}</span>
                        <span>{{ $nextReservation->reservation_number }}</span>
                    </div>
                    <h2>{{ $nextReservation->court->court_name }}</h2>
                    <div class="pass-meta-grid">
                        <div>
                            <span>Date</span>
                            <strong>{{ $nextReservation->reservation_date->format('M d, Y') }}</strong>
                        </div>
                        <div>
                            <span>Time</span>
                            <strong>{{ substr($nextReservation->start_time, 0, 5) }} – {{ substr($nextReservation->end_time, 0, 5) }}</strong>
                        </div>
                    </div>
                    <a href="{{ route('reservations.show', $nextReservation) }}" class="pass-link">Open reservation</a>
                @else
                    <div class="pass-empty-icon">
                        <i data-lucide="calendar-plus"></i>
                    </div>
                    <h2>No court queued yet</h2>
                    <p style="color:var(--muted);font-size:13px;margin:0 0 16px;">Pick a court and time slot to create your next play pass.</p>
                    <a href="{{ route('booking.create') }}" class="pass-link">Start a booking</a>
                @endif
            </aside>

            {{-- Quick book card --}}
            <section class="card quick-book-card scroll-reveal reveal-fade-left stagger-2">
                <p class="dash-kicker">Quick Reserve</p>
                <h2>Book from the court list.</h2>
                <p>Compare sport type, capacity, and hourly rate before locking a time.</p>
                <div class="quick-action-stack">
                    <a href="{{ route('booking.create') }}">
                        <span><i data-lucide="calendar-plus"></i> New reservation</span>
                        <i data-lucide="chevron-right"></i>
                    </a>
                    <a href="{{ route('courts.index') }}">
                        <span><i data-lucide="grid-3x3"></i> Browse courts</span>
                        <i data-lucide="chevron-right"></i>
                    </a>
                    <a href="{{ route('contact') }}">
                        <span><i data-lucide="phone-call"></i> Contact facility</span>
                        <i data-lucide="chevron-right"></i>
                    </a>
                </div>
            </section>

            {{-- Upcoming Events Card --}}
            @if($upcomingEvents->isNotEmpty())
                <section class="card scroll-reveal reveal-fade-left stagger-3" style="background: linear-gradient(135deg, rgba(191,255,0,0.04) 0%, rgba(0,0,0,0) 100%);">
                    <p class="dash-kicker" style="color: var(--lime);">Recommended For You</p>
                    <h2>Upcoming Events</h2>
                    <p style="font-size:12px; color:var(--muted); margin:0 0 16px;">New sessions you might be interested to join:</p>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach($upcomingEvents as $evt)
                            <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:8px; padding:12px; display:flex; gap:12px; align-items:center; justify-content:space-between;">
                                <div style="display:flex; gap:12px; align-items:center; min-width:0; flex:1;">
                                    <img src="{{ $evt->image ? asset($evt->image) : asset('images/courtconnect-multisport-hero.png') }}" style="width:40px; height:40px; object-fit:cover; border-radius:6px; flex-shrink:0;" alt="event image">
                                    <div style="min-width:0; flex:1;">
                                        <span style="font-size:8px; font-weight:700; color:var(--lime); text-transform:uppercase; display:block;">{{ $evt->sport }}</span>
                                        <strong style="color:#fff; font-size:12px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $evt->title }}</strong>
                                        <span style="font-size:10px; color:var(--muted);">{{ $evt->start_date->format('M d') }} · {{ $evt->price > 0 ? '₱' . number_format($evt->price) : 'Free' }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('events.show', $evt->slug) }}" class="btn btn-outline btn-sm" style="padding:4px 8px; font-size:11px; flex-shrink:0; text-decoration:none; height:auto; line-height:1.2;">Join</a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Activity timeline --}}
            <section class="card scroll-reveal reveal-fade-left stagger-3">
                <div class="section-title-row compact">
                    <div>
                        <p class="dash-kicker">Timeline</p>
                        <h2>Recent Activity</h2>
                    </div>
                </div>
                <div class="activity-timeline">
                    @forelse($upcoming->take(3) as $res)
                        <div class="timeline-item {{ $res->status === 'approved' ? 'success' : 'active' }}">
                            <div class="timeline-badge">
                                <i data-lucide="{{ $res->status === 'approved' ? 'check-circle-2' : 'info' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">{{ ucfirst($res->status) }} reservation</div>
                                <div class="timeline-desc">{{ $res->court->court_name }} · {{ $res->reservation_date->format('M d') }}</div>
                                <div class="timeline-time">{{ $res->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="timeline-empty">No recent updates yet.</div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
@endsection
