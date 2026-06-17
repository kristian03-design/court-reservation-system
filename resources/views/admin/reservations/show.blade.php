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
    <title>Reservation | CourtConnect</title>
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
            <section class="card mx-auto max-w-3xl">
        <h1 class="text-3xl font-bold text-[#0F2447]">{{ $reservation->reservation_number }}</h1>
        <dl class="mt-6 grid gap-4 md:grid-cols-2">
            <div><dt class="text-sm text-[#64748B]">Customer</dt><dd class="font-semibold">{{ $reservation->user->name }}</dd></div>
            <div><dt class="text-sm text-[#64748B]">Court</dt><dd class="font-semibold">{{ $reservation->court->court_name }}</dd></div>
            <div><dt class="text-sm text-[#64748B]">Schedule</dt><dd class="font-semibold">{{ $reservation->reservation_date->format('M d, Y') }} {{ substr($reservation->start_time, 0, 5) }}-{{ substr($reservation->end_time, 0, 5) }}</dd></div>
            <div><dt class="text-sm text-[#64748B]">Total</dt><dd class="font-semibold">PHP {{ number_format($reservation->total_amount, 2) }}</dd></div>
        </dl>
        <form class="mt-8 flex flex-wrap items-end gap-3" method="POST" action="{{ route('admin.reservations.update', $reservation) }}">
            @csrf
            @method('PUT')
            <label class="grid gap-2 text-sm font-semibold">Status
                <select class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="status">
                    @foreach (['pending', 'approved', 'rejected', 'cancelled', 'completed'] as $status)
                        <option value="{{ $status }}" @selected($reservation->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="btn btn-secondary">Update Status</button>
        </form>
    </section>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
