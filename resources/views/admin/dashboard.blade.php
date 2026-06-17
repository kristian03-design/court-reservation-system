@php
    $logoCandidates = ['images/courtconnect-mark.png', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.png', 'courtconnect-logo.svg', 'courtconnect-logo.png'];
    $layoutLogo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | CourtConnect</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/admin.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                @if ($layoutLogo)
                    <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                @else
                    <span class="brand-fallback">CC</span>
                @endif
                <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
            </a>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.courts.index') }}">Courts</a>
                <a href="{{ route('admin.reservations.index') }}">Reservations</a>
                <a href="{{ route('admin.payments.index') }}">Payments</a>
                <a href="{{ route('admin.users.index') }}">Users</a>
                <a href="{{ route('admin.reports.index') }}">Reports</a>
                <a href="{{ route('admin.settings.index') }}">Settings</a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-topbar">
                <a href="{{ route('home') }}" class="brand">
                    @if ($layoutLogo)
                        <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                    @else
                        <span class="brand-fallback">CC</span>
                    @endif
                    <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline">Logout</button>
                </form>
            </div>
            @if (session('success') || $errors->any())
                <div class="alert {{ session('success') ? 'alert-success' : 'alert-error' }}">
                    @if (session('success'))
                        {{ session('success') }}
                    @else
                        <strong>Please check the highlighted fields.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-[#57C271]">Admin Dashboard</p>
            <h1 class="mt-2 text-3xl font-bold text-[#0F2447]">Facility operations overview</h1>
        </div>
        <a href="{{ route('admin.courts.create') }}" class="btn btn-secondary">Add Court</a>
    </div>
    <div class="mt-8 grid gap-4 md:grid-cols-5">
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Total Reservations</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['total_reservations'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Today's Reservations</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['todays_reservations'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Active Courts</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['active_courts'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Revenue</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">PHP {{ number_format($stats['revenue'], 2) }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Pending</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['pending_reservations'] }}</p></section>
    </div>
    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_380px]">
        <section class="card">
            <h2 class="text-xl font-semibold text-[#0F2447]">Reservation Calendar</h2>
            <div id="reservation-calendar" class="mt-4" data-events-url="{{ route('admin.reservations.calendar') }}"></div>
        </section>
        <section class="card">
            <h2 class="text-xl font-semibold text-[#0F2447]">Booking Status</h2>
            <div class="mt-5 grid gap-3">
                @foreach ($statusBreakdown as $status => $total)
                    <div class="flex items-center justify-between rounded-lg border border-[#E5E7EB] px-3 py-2">
                        <span class="status status-{{ $status }}">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="font-semibold">{{ $total }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
    <section class="mt-8">
        <h2 class="text-xl font-semibold text-[#0F2447]">Recent Reservations</h2>
        <div class="mt-4 table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>Reservation</th><th>Court</th><th>Schedule</th><th>Status</th><th>Payment</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($recentReservations as $reservation)
                            <tr>
                                <td>{{ $reservation->reservation_number }}</td>
                                <td>{{ $reservation->court->court_name }}</td>
                                <td>{{ $reservation->reservation_date->format('M d, Y') }} {{ substr($reservation->start_time, 0, 5) }}-{{ substr($reservation->end_time, 0, 5) }}</td>
                                <td><span class="status status-{{ $reservation->status }}">{{ $reservation->status }}</span></td>
                                <td><span class="status status-{{ $reservation->payment?->payment_status ?? 'unpaid' }}">{{ str_replace('_', ' ', $reservation->payment?->payment_status ?? 'unpaid') }}</span></td>
                                <td><a href="{{ route('admin.reservations.show', $reservation) }}">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No reservations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    @endpush
        </main>
    </div>
    @stack('scripts')
</body>
</html>
