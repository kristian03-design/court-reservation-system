@extends('admin.layouts.shell', ['pageTitle' => 'Reports', 'active' => 'reports'])

@section('content')
    <div class="reports-page">

        {{-- Header --}}
        <header class="reports-header">
            <div>
                <p class="rpt-eyebrow">Analytics</p>
                <h1>System Reports</h1>
                <p class="rpt-subtitle">Performance overview for {{ now()->format('F Y') }}</p>
            </div>
            <div class="rpt-header-meta">
                <span class="rpt-badge"><i class="ti ti-calendar"></i> Last updated: {{ now()->format('M d, Y · h:i A') }}</span>
            </div>
        </header>

        {{-- ─── KPI Strip ─────────────────────────────────────────── --}}
        <section class="rpt-kpi-strip" aria-label="Key performance indicators">

            {{-- Total Revenue --}}
            <article class="rpt-kpi-card rpt-kpi-lime">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-icon"><i class="ti ti-cash"></i></span>
                    <span class="rpt-kpi-label">Total Revenue</span>
                </div>
                <strong class="rpt-kpi-value">₱{{ number_format($stats['revenue'], 0) }}</strong>
                <div class="rpt-kpi-foot">
                    <span class="rpt-kpi-sub">This month: ₱{{ number_format($revenueThisMonth, 0) }}</span>
                    @if ($revenueGrowth !== null)
                        <span class="rpt-kpi-delta {{ $revenueGrowth >= 0 ? 'delta-up' : 'delta-down' }}">
                            <i class="ti {{ $revenueGrowth >= 0 ? 'ti-trending-up' : 'ti-trending-down' }}"></i>
                            {{ abs($revenueGrowth) }}% vs last month
                        </span>
                    @endif
                </div>
            </article>

            {{-- Total Bookings --}}
            <article class="rpt-kpi-card rpt-kpi-blue">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-icon"><i class="ti ti-calendar-event"></i></span>
                    <span class="rpt-kpi-label">Total Bookings</span>
                </div>
                <strong class="rpt-kpi-value">{{ number_format($stats['total_reservations']) }}</strong>
                <div class="rpt-kpi-foot">
                    <span class="rpt-kpi-sub">This month: {{ $bookingsThisMonth }}</span>
                    @if ($bookingsGrowth !== null)
                        <span class="rpt-kpi-delta {{ $bookingsGrowth >= 0 ? 'delta-up' : 'delta-down' }}">
                            <i class="ti {{ $bookingsGrowth >= 0 ? 'ti-trending-up' : 'ti-trending-down' }}"></i>
                            {{ abs($bookingsGrowth) }}% vs last month
                        </span>
                    @endif
                </div>
            </article>

            {{-- Avg Booking Value --}}
            <article class="rpt-kpi-card rpt-kpi-purple">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-icon"><i class="ti ti-receipt"></i></span>
                    <span class="rpt-kpi-label">Avg Value</span>
                </div>
                <strong class="rpt-kpi-value">₱{{ number_format($avgBookingValue, 0) }}</strong>
                <div class="rpt-kpi-foot">
                    <span class="rpt-kpi-sub">Per paid reservation</span>
                </div>
            </article>

            {{-- Active Courts --}}
            <article class="rpt-kpi-card rpt-kpi-orange">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-icon"><i class="ti ti-layout-grid"></i></span>
                    <span class="rpt-kpi-label">Active Courts</span>
                </div>
                <strong class="rpt-kpi-value">{{ $stats['active_courts'] }}</strong>
                <div class="rpt-kpi-foot">
                    <span class="rpt-kpi-sub">Online &amp; available</span>
                </div>
            </article>

            {{-- Cancellation Rate --}}
            <article class="rpt-kpi-card rpt-kpi-red">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-icon"><i class="ti ti-x"></i></span>
                    <span class="rpt-kpi-label">Cancel Rate</span>
                </div>
                <strong class="rpt-kpi-value">{{ $cancellationRate }}%</strong>
                <div class="rpt-kpi-foot">
                    <span class="rpt-kpi-sub">{{ $cancelledCount }} cancelled / rejected</span>
                </div>
            </article>

            {{-- New Users --}}
            <article class="rpt-kpi-card rpt-kpi-teal">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-icon"><i class="ti ti-user-plus"></i></span>
                    <span class="rpt-kpi-label">New Users</span>
                </div>
                <strong class="rpt-kpi-value">{{ $newUsersThisMonth }}</strong>
                <div class="rpt-kpi-foot">
                    <span class="rpt-kpi-sub">Joined this month</span>
                </div>
            </article>

        </section>

        {{-- ─── Charts Row ─────────────────────────────────────────── --}}
        <div class="rpt-charts-row">

            {{-- Monthly Revenue Bar Chart --}}
            <section class="rpt-panel">
                <div class="rpt-panel-head">
                    <div>
                        <h2>Monthly Revenue</h2>
                        <p>Last 6 months · paid bookings only</p>
                    </div>
                </div>
                <div class="rpt-chart-wrap">
                    @php
                        $hasRevenue = $monthlyRevenue->sum('revenue') > 0;
                        $maxRev = $monthlyRevenue->max('revenue') ?: 1;
                    @endphp
                    @if ($hasRevenue)
                        <div class="rpt-bar-chart">
                            @foreach ($monthlyRevenue as $m)
                                @php $pct = ($m['revenue'] / $maxRev) * 100; @endphp
                                <div class="rpt-bar-col">
                                    <span class="rpt-bar-val">₱{{ number_format($m['revenue'], 0) }}</span>
                                    <div class="rpt-bar-track">
                                        <div class="rpt-bar-fill rpt-bar-lime" style="height: {{ $pct }}%;"></div>
                                    </div>
                                    <span class="rpt-bar-label">{{ $m['month'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rpt-chart-empty">
                            <i class="ti ti-chart-bar"></i>
                            <p>No revenue data yet for the last 6 months.</p>
                        </div>
                        {{-- Still show month labels below --}}
                        <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; border-top: 1px solid var(--border); padding-top: 10px; padding-bottom: 12px;">
                            @foreach ($monthlyRevenue as $m)
                                <div style="text-align:center; color: var(--muted); font-size: 10px; font-weight: 700;">{{ $m['month'] }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            {{-- Booking Status Breakdown --}}
            <section class="rpt-panel">
                <div class="rpt-panel-head">
                    <div>
                        <h2>Booking Status</h2>
                        <p>All-time breakdown by status</p>
                    </div>
                </div>
                <div class="rpt-status-list">
                    @php
                        $statusTotal = array_sum($statusBreakdown);
                        $statusColors = [
                            'approved'  => ['#BFFF00', 'rgba(191,255,0,0.12)'],
                            'completed' => ['#60a5fa', 'rgba(59,130,246,0.12)'],
                            'pending'   => ['#FFC800', 'rgba(255,200,0,0.12)'],
                            'cancelled' => ['#FF5C3A', 'rgba(255,92,58,0.12)'],
                            'rejected'  => ['#f87171', 'rgba(248,113,113,0.12)'],
                        ];
                    @endphp
                    @foreach ($statusBreakdown as $status => $count)
                        @php
                            $pct = $statusTotal > 0 ? round(($count / $statusTotal) * 100) : 0;
                            $col = $statusColors[$status] ?? ['#6B6B6B', 'rgba(107,107,107,0.12)'];
                        @endphp
                        <div class="rpt-status-row">
                            <div class="rpt-status-meta">
                                <span class="status status-{{ $status }}">{{ str_replace('_', ' ', $status) }}</span>
                                <span class="rpt-status-count">{{ number_format($count) }}</span>
                            </div>
                            <div class="rpt-status-track">
                                <div class="rpt-status-fill" style="width: {{ $pct }}%; background: {{ $col[0] }};"></div>
                            </div>
                            <span class="rpt-status-pct">{{ $pct }}%</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- ─── Bottom Row ─────────────────────────────────────────── --}}
        <div class="rpt-bottom-row">

            {{-- Popular Courts --}}
            <section class="rpt-panel">
                <div class="rpt-panel-head">
                    <div>
                        <h2>Top Courts</h2>
                        <p>Most booked courts of all time</p>
                    </div>
                </div>
                <div class="rpt-court-list">
                    @php $maxBookings = $popularCourts->max('reservations_count') ?: 1; @endphp
                    @forelse ($popularCourts as $i => $court)
                        @php $pct = $maxBookings > 0 ? ($court->reservations_count / $maxBookings) * 100 : 0; @endphp
                        <div class="rpt-court-row">
                            <span class="rpt-court-rank">{{ $i + 1 }}</span>
                            <div class="rpt-court-info">
                                <strong>{{ $court->court_name }}</strong>
                                <small>{{ $court->court_type }}</small>
                            </div>
                            <div class="rpt-court-bar-wrap">
                                <div class="rpt-court-bar" style="width: {{ $pct }}%;"></div>
                            </div>
                            <span class="rpt-court-count">{{ number_format($court->reservations_count) }}</span>
                        </div>
                    @empty
                        <p class="rpt-empty">No court data yet.</p>
                    @endforelse
                </div>
            </section>

            {{-- Monthly Bookings Table --}}
            <section class="rpt-panel">
                <div class="rpt-panel-head">
                    <div>
                        <h2>Monthly Bookings</h2>
                        <p>Reservation count per month</p>
                    </div>
                </div>
                <div class="rpt-table-wrap">
                    <table class="rpt-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th style="text-align:right;">Bookings</th>
                                <th style="text-align:right;">Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthlyReservations->sortByDesc('period')->take(8) as $row)
                                @php
                                    $prev = $monthlyReservations->firstWhere('period', \Carbon\Carbon::createFromFormat('Y-m', $row->period)->subMonth()->format('Y-m'));
                                    $delta = $prev ? $row->total - $prev->total : null;
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $row->period)->format('F Y') }}</td>
                                    <td style="text-align:right; font-weight: 700; color: var(--text);">{{ number_format($row->total) }}</td>
                                    <td style="text-align:right;">
                                        @if ($delta !== null)
                                            <span style="color: {{ $delta >= 0 ? 'var(--lime)' : 'var(--coral)' }}; font-size: 11px; font-weight: 700;">
                                                {{ $delta >= 0 ? '+' : '' }}{{ $delta }}
                                            </span>
                                        @else
                                            <span style="color: var(--muted); font-size: 11px;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center; color: var(--muted);">No booking data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

    </div>

    <style>
        /* ── Reports Page ───────────────────────────── */
        .reports-page { display: grid; gap: 24px; }

        .reports-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .rpt-eyebrow {
            margin: 0 0 4px;
            color: var(--lime);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .reports-header h1 {
            margin: 0 0 4px;
            color: var(--text);
            font-family: var(--display-font);
            font-size: clamp(26px, 3vw, 40px);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            line-height: 1;
        }

        .rpt-subtitle {
            margin: 0;
            color: var(--muted-mid);
            font-size: 13px;
        }

        .rpt-header-meta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rpt-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: var(--surface-2);
            color: var(--muted-mid);
            font-size: 11px;
            font-weight: 600;
        }

        .rpt-badge .ti { color: var(--lime); }

        /* ── KPI Strip ──────────────────────────────── */
        .rpt-kpi-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
        }

        .rpt-kpi-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--surface-2);
            padding: 20px 18px;
            display: grid;
            gap: 10px;
            position: relative;
            overflow: hidden;
            transition: border-color var(--transition), transform var(--transition);
        }

        .rpt-kpi-card:hover { transform: translateY(-2px); border-color: var(--border-hover); }

        .rpt-kpi-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 2px;
        }

        .rpt-kpi-lime::before  { background: linear-gradient(90deg, transparent, var(--lime) 50%, transparent); }
        .rpt-kpi-blue::before  { background: linear-gradient(90deg, transparent, #60a5fa 50%, transparent); }
        .rpt-kpi-purple::before{ background: linear-gradient(90deg, transparent, #c084fc 50%, transparent); }
        .rpt-kpi-orange::before{ background: linear-gradient(90deg, transparent, #fb923c 50%, transparent); }
        .rpt-kpi-red::before   { background: linear-gradient(90deg, transparent, var(--coral) 50%, transparent); }
        .rpt-kpi-teal::before  { background: linear-gradient(90deg, transparent, #2dd4bf 50%, transparent); }

        .rpt-kpi-top {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .rpt-kpi-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .rpt-kpi-icon .ti { font-size: 16px; }

        .rpt-kpi-lime  .rpt-kpi-icon { background: var(--lime-dim); color: var(--lime); border: 1px solid rgba(191,255,0,0.15); }
        .rpt-kpi-blue  .rpt-kpi-icon { background: rgba(59,130,246,0.1); color: #60a5fa; border: 1px solid rgba(59,130,246,0.15); }
        .rpt-kpi-purple.rpt-kpi-icon,
        .rpt-kpi-purple .rpt-kpi-icon { background: rgba(168,85,247,0.1); color: #c084fc; border: 1px solid rgba(168,85,247,0.15); }
        .rpt-kpi-orange .rpt-kpi-icon { background: rgba(249,115,22,0.1); color: #fb923c; border: 1px solid rgba(249,115,22,0.15); }
        .rpt-kpi-red    .rpt-kpi-icon { background: rgba(255,92,58,0.1); color: var(--coral); border: 1px solid rgba(255,92,58,0.15); }
        .rpt-kpi-teal   .rpt-kpi-icon { background: rgba(45,212,191,0.1); color: #2dd4bf; border: 1px solid rgba(45,212,191,0.15); }

        .rpt-kpi-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rpt-kpi-value {
            display: block;
            color: var(--text);
            font-family: var(--display-font);
            font-size: clamp(36px, 3.5vw, 48px);
            letter-spacing: 0.03em;
            line-height: 1;
        }

        .rpt-kpi-lime   .rpt-kpi-value { color: var(--lime); }

        .rpt-kpi-foot {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .rpt-kpi-sub {
            color: var(--muted);
            font-size: 11px;
            font-weight: 500;
        }

        .rpt-kpi-delta {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
        }

        .rpt-kpi-delta .ti { font-size: 12px; }
        .delta-up   { color: var(--lime); }
        .delta-down { color: var(--coral); }

        /* ── Panels ─────────────────────────────────── */
        .rpt-panel {
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            background: var(--surface-2);
            overflow: hidden;
        }

        .rpt-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
        }

        .rpt-panel-head h2 {
            margin: 0 0 3px;
            color: var(--text);
            font-family: var(--display-font);
            font-size: 18px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .rpt-panel-head p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        /* ── Charts Row ─────────────────────────────── */
        .rpt-charts-row {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .rpt-charts-row,
            .rpt-bottom-row { grid-template-columns: 1fr; }
        }

        /* Bar Chart */
        .rpt-chart-wrap { padding: 20px 20px 0; }

        .rpt-chart-empty {
            height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--muted);
        }

        .rpt-chart-empty .ti { font-size: 36px; color: var(--border); }
        .rpt-chart-empty p { font-size: 13px; margin: 0; }

        .rpt-bar-chart {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            height: 220px;
            align-items: end;
        }

        .rpt-bar-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            height: 100%;
        }

        .rpt-bar-val {
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
        }

        .rpt-bar-track {
            flex: 1;
            width: 100%;
            display: flex;
            align-items: flex-end;
            background: var(--surface-3);
            border-radius: 6px 6px 0 0;
            overflow: hidden;
        }

        .rpt-bar-fill {
            width: 100%;
            border-radius: 6px 6px 0 0;
            transition: height 0.6s cubic-bezier(0.34,1.56,0.64,1);
        }

        .rpt-bar-lime { background: var(--lime); }

        .rpt-bar-label {
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
            padding-bottom: 12px;
        }

        /* Status List */
        .rpt-status-list {
            padding: 16px 20px;
            display: grid;
            gap: 12px;
        }

        .rpt-status-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: center;
            gap: 10px;
        }

        .rpt-status-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rpt-status-count {
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .rpt-status-track {
            flex: 1;
            height: 6px;
            background: var(--surface-3);
            border-radius: 999px;
            overflow: hidden;
            min-width: 80px;
        }

        .rpt-status-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.5s ease;
        }

        .rpt-status-pct {
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            min-width: 30px;
            text-align: right;
        }

        /* ── Bottom Row ─────────────────────────────── */
        .rpt-bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

        /* Top Courts */
        .rpt-court-list {
            padding: 16px 20px;
            display: grid;
            gap: 12px;
        }

        .rpt-court-row {
            display: grid;
            grid-template-columns: 24px 1fr auto auto;
            align-items: center;
            gap: 12px;
        }

        .rpt-court-rank {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: var(--surface-3);
            border: 1px solid var(--border);
            display: grid;
            place-items: center;
            font-size: 10px;
            font-weight: 800;
            color: var(--muted);
        }

        .rpt-court-info strong {
            display: block;
            color: var(--text);
            font-size: 12px;
            font-weight: 700;
        }

        .rpt-court-info small {
            color: var(--muted);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .rpt-court-bar-wrap {
            flex: 1;
            height: 6px;
            background: var(--surface-3);
            border-radius: 999px;
            overflow: hidden;
            min-width: 60px;
        }

        .rpt-court-bar {
            height: 100%;
            background: var(--lime);
            border-radius: 999px;
            transition: width 0.6s ease;
        }

        .rpt-court-count {
            color: var(--lime);
            font-family: var(--display-font);
            font-size: 18px;
            font-weight: 400;
            letter-spacing: 0.04em;
            min-width: 32px;
            text-align: right;
        }

        /* Table */
        .rpt-table-wrap { overflow-x: auto; }

        .rpt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .rpt-table thead tr {
            background: var(--surface-3);
            border-bottom: 1px solid var(--border);
        }

        .rpt-table th {
            padding: 10px 16px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-align: left;
        }

        .rpt-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background var(--transition);
        }

        .rpt-table tbody tr:last-child { border-bottom: none; }

        .rpt-table tbody tr:hover { background: rgba(255,255,255,0.025); }

        .rpt-table td {
            padding: 11px 16px;
            color: var(--muted-mid);
        }

        .rpt-empty {
            padding: 20px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }
    </style>
@endsection
