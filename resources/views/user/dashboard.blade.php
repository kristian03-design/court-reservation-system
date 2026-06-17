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
    <title>Dashboard | CourtConnect</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/user.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="site-container site-nav">
            <a href="{{ route('home') }}" class="brand">
                @if ($layoutLogo)
                    <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                @else
                    <span class="brand-fallback">CC</span>
                @endif
                <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
            </a>
            <nav class="site-menu">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('courts.index') }}">Courts</a>
                <a href="{{ route('booking.create') }}">Book</a>
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline" type="submit">Logout</button>
            </form>
        </div>
    </header>
    <main class="main-shell">
        <div class="site-container">
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
            <div class="content-space"><div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-[#57C271]">Player Dashboard</p>
            <h1 class="mt-2 text-3xl font-bold text-[#0F2447]">Welcome, {{ auth()->user()->name }}</h1>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline">Logout</button>
        </form>
    </div>
    <div class="mt-8 grid gap-4 md:grid-cols-4">
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Upcoming Reservations</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['upcoming'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Completed Reservations</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['completed'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Cancelled Reservations</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['cancelled'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Reservation History</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['history'] }}</p></section>
    </div>
    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">
        <section>
            <h2 class="text-xl font-semibold text-[#0F2447]">Upcoming Reservations</h2>
            <div class="mt-4">
                <div class="table-wrap">
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead><tr><th>Reservation</th><th>Court</th><th>Schedule</th><th>Status</th><th>Payment</th><th>Action</th></tr></thead>
                            <tbody>
                                @forelse ($upcoming as $reservation)
                                    <tr>
                                        <td>{{ $reservation->reservation_number }}</td>
                                        <td>{{ $reservation->court->court_name }}</td>
                                        <td>{{ $reservation->reservation_date->format('M d, Y') }} {{ substr($reservation->start_time, 0, 5) }}-{{ substr($reservation->end_time, 0, 5) }}</td>
                                        <td><span class="status status-{{ $reservation->status }}">{{ $reservation->status }}</span></td>
                                        <td><span class="status status-{{ $reservation->payment?->payment_status ?? 'unpaid' }}">{{ str_replace('_', ' ', $reservation->payment?->payment_status ?? 'unpaid') }}</span></td>
                                        <td><a href="{{ route('reservations.show', $reservation) }}">View</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">No reservations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <section class="card">
            <h2 class="text-xl font-semibold text-[#0F2447]">Quick Actions</h2>
            <div class="mt-5 grid gap-3">
                <a href="{{ route('booking.create') }}" class="btn btn-secondary">Book Court</a>
                <a href="{{ route('courts.index') }}" class="btn btn-outline">View Courts</a>
                <a href="{{ route('contact') }}" class="btn btn-outline">Contact Facility</a>
            </div>
        </section>
    </div></div>
        </div>
    </main>
    @stack('scripts')
</body>
</html>
