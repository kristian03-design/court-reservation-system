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
    <title>Reports | CourtConnect</title>
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
            <h1 class="text-3xl font-bold text-[#0F2447]">Reports</h1>
    <div class="mt-6 grid gap-4 md:grid-cols-4">
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Revenue</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">PHP {{ number_format($stats['revenue'], 2) }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Total Reservations</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['total_reservations'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Active Courts</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['active_courts'] }}</p></section>
        <section class="card"><p class="text-sm font-medium text-[#64748B]">Pending</p><p class="mt-3 text-3xl font-bold text-[#0F2447]">{{ $stats['pending_reservations'] }}</p></section>
    </div>
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="card">
            <h2 class="text-xl font-semibold text-[#0F2447]">Popular Courts</h2>
            <div class="mt-4 grid gap-3">
                @foreach ($popularCourts as $court)
                    <div class="flex justify-between rounded-lg border border-[#E5E7EB] px-3 py-2"><span>{{ $court->court_name }}</span><strong>{{ $court->reservations_count }}</strong></div>
                @endforeach
            </div>
        </section>
        <section class="card">
            <h2 class="text-xl font-semibold text-[#0F2447]">Exports</h2>
            <p class="mt-3 text-sm text-[#64748B]">PDF, Excel, and CSV export hooks are represented here for production integration.</p>
            <div class="mt-5 flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline">PDF</button>
                <button type="button" class="btn btn-outline">Excel</button>
                <button type="button" class="btn btn-outline">CSV</button>
            </div>
        </section>
    </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
