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
    <title>Reservations | CourtConnect</title>
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
            <h1 class="text-3xl font-bold text-[#0F2447]">Reservations</h1>
    <form class="mt-5 grid gap-3 md:grid-cols-[1fr_220px_120px]" method="GET">
        <input class="rounded-lg border border-[#E5E7EB] px-4 py-3" name="search" value="{{ request('search') }}" placeholder="Search number or customer">
        <select class="rounded-lg border border-[#E5E7EB] px-4 py-3" name="status">
            <option value="">All statuses</option>
            @foreach (['pending', 'approved', 'rejected', 'cancelled', 'completed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>
    <div class="mt-6 overflow-hidden rounded-lg border border-[#E5E7EB] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E5E7EB] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-[#64748B]"><tr><th class="px-4 py-3">Number</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Court</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Action</th></tr></thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @foreach ($reservations as $reservation)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $reservation->reservation_number }}</td>
                            <td class="px-4 py-3">{{ $reservation->user->name }}</td>
                            <td class="px-4 py-3">{{ $reservation->court->court_name }}</td>
                            <td class="px-4 py-3"><span class="status status-{{ $reservation->status }}">{{ str_replace('_', ' ', $reservation->status) }}</span></td>
                            <td class="px-4 py-3"><a class="font-semibold text-[#0F2447]" href="{{ route('admin.reservations.show', $reservation) }}">Manage</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $reservations->links() }}</div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
