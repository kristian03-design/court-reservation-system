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
    <title>Court Form | CourtConnect</title>
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
        <h1 class="text-3xl font-bold text-[#0F2447]">{{ $court->exists ? 'Edit Court' : 'Add Court' }}</h1>
        <form class="mt-6 grid gap-5" method="POST" action="{{ $court->exists ? route('admin.courts.update', $court) : route('admin.courts.store') }}">
            @csrf
            @if ($court->exists)
                @method('PUT')
            @endif
            <label class="grid gap-2 text-sm font-semibold">Court Name
                <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="court_name" value="{{ old('court_name', $court->court_name) }}" required>
            </label>
            <div class="grid gap-4 md:grid-cols-3">
                <label class="grid gap-2 text-sm font-semibold">Type
                    <select class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="court_type">
                        @foreach (['Basketball', 'Volleyball', 'Badminton', 'Tennis', 'Futsal'] as $type)
                            <option value="{{ $type }}" @selected(old('court_type', $court->court_type) === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-2 text-sm font-semibold">Capacity
                    <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="number" name="capacity" value="{{ old('capacity', $court->capacity ?? 4) }}" required>
                </label>
                <label class="grid gap-2 text-sm font-semibold">Hourly Rate
                    <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $court->hourly_rate ?? 0) }}" required>
                </label>
            </div>
            <label class="grid gap-2 text-sm font-semibold">Image URL
                <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="image" value="{{ old('image', $court->image) }}">
            </label>
            <label class="grid gap-2 text-sm font-semibold">Status
                <select class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="status">
                    @foreach (['available', 'maintenance', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $court->status ?: 'available') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-2 text-sm font-semibold">Description
                <textarea class="min-h-32 rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="description">{{ old('description', $court->description) }}</textarea>
            </label>
            <button type="submit" class="btn btn-secondary">{{ $court->exists ? 'Save Changes' : 'Create Court' }}</button>
        </form>
    </section>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
