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
    <title>Courts | CourtConnect</title>
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
            <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-[#57C271]">Court Management</p>
            <h1 class="mt-2 text-3xl font-bold text-[#0F2447]">Courts</h1>
        </div>
        <a href="{{ route('admin.courts.create') }}" class="btn btn-secondary">Add Court</a>
    </div>
    <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($courts as $court)
            <section class="card">
                <img class="h-40 w-full rounded-lg object-cover" src="{{ $court->image }}" alt="{{ $court->court_name }}">
                <div class="mt-4 flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-[#0F2447]">{{ $court->court_name }}</h2>
                        <p class="text-sm text-[#64748B]">{{ $court->court_type }} · PHP {{ number_format($court->hourly_rate, 2) }}</p>
                    </div>
                    <span class="status status-{{ $court->status }}">{{ str_replace('_', ' ', $court->status) }}</span>
                </div>
                <div class="mt-5 flex gap-2">
                    <a href="{{ route('admin.courts.edit', $court) }}" class="btn btn-outline flex-1">Edit</a>
                    <form method="POST" action="{{ route('admin.courts.destroy', $court) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-full">Delete</button>
                    </form>
                </div>
            </section>
        @endforeach
    </div>
    <div class="mt-6">{{ $courts->links() }}</div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
