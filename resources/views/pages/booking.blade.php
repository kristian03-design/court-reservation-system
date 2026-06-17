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
    <title>Book Court | CourtConnect</title>
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
            <div class="content-space"><div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <section class="card">
            <h1 class="text-3xl font-bold text-[#0F2447]">Reserve a court</h1>
            <form class="mt-6 grid gap-5" method="POST" action="{{ route('booking.store') }}">
                @csrf
                <label class="grid gap-2 text-sm font-semibold">Court
                    <select class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="court_id" required>
                        @foreach ($courts as $item)
                            <option value="{{ $item->id }}" @selected(old('court_id', $court?->id) == $item->id)>{{ $item->court_name }} - PHP {{ number_format($item->hourly_rate) }}/hr</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid gap-4 md:grid-cols-3">
                    <label class="grid gap-2 text-sm font-semibold">Date
                        <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="date" name="reservation_date" value="{{ old('reservation_date', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                    </label>
                    <label class="grid gap-2 text-sm font-semibold">Start
                        <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="time" name="start_time" value="{{ old('start_time', '18:00') }}" required>
                    </label>
                    <label class="grid gap-2 text-sm font-semibold">End
                        <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="time" name="end_time" value="{{ old('end_time', '19:00') }}" required>
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="grid gap-2 text-sm font-semibold">Number of Players
                        <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="number" name="players" min="1" value="{{ old('players', 2) }}" required>
                    </label>
                    <label class="grid gap-2 text-sm font-semibold">Payment Method
                        <select class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="payment_method">
                            <option value="pay_at_venue">Pay at Venue</option>
                            <option value="gcash_upload">GCash Upload</option>
                            <option value="paymongo">PayMongo</option>
                        </select>
                    </label>
                </div>
                <label class="grid gap-2 text-sm font-semibold">Notes
                    <textarea class="min-h-28 rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="notes" placeholder="Optional request or team details">{{ old('notes') }}</textarea>
                </label>
                <button type="submit" class="btn btn-secondary w-full md:w-auto">Confirm Reservation</button>
            </form>
        </section>
        <aside class="card sticky top-24">
            <h2 class="text-xl font-semibold text-[#0F2447]">Booking Summary</h2>
            @if ($court)
                <img class="mt-4 h-40 w-full rounded-lg object-cover" src="{{ $court->image }}" alt="{{ $court->court_name }}">
                <dl class="mt-4 grid gap-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-[#64748B]">Court</dt><dd class="font-semibold">{{ $court->court_name }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[#64748B]">Type</dt><dd>{{ $court->court_type }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[#64748B]">Rate</dt><dd>PHP {{ number_format($court->hourly_rate, 2) }}/hr</dd></div>
                </dl>
            @else
                <p class="mt-4 text-sm text-[#64748B]">Select a court to view pricing and capacity.</p>
            @endif
        </aside>
    </div></div>
        </div>
    </main>
    @stack('scripts')
</body>
</html>
