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
    <title>Reservation {{ $reservation->reservation_number }} | CourtConnect</title>
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
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-[#57C271]">Reservation Confirmation</p>
                    <h1 class="mt-2 text-3xl font-bold text-[#0F2447]">{{ $reservation->reservation_number }}</h1>
                </div>
                <span class="status status-{{ $reservation->status }}">{{ str_replace('_', ' ', $reservation->status) }}</span>
            </div>
            <dl class="mt-8 grid gap-4 md:grid-cols-2">
                <div><dt class="text-sm text-[#64748B]">Court</dt><dd class="mt-1 font-semibold">{{ $reservation->court->court_name }}</dd></div>
                <div><dt class="text-sm text-[#64748B]">Date</dt><dd class="mt-1 font-semibold">{{ $reservation->reservation_date->format('F d, Y') }}</dd></div>
                <div><dt class="text-sm text-[#64748B]">Time</dt><dd class="mt-1 font-semibold">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</dd></div>
                <div><dt class="text-sm text-[#64748B]">Players</dt><dd class="mt-1 font-semibold">{{ $reservation->players }}</dd></div>
                <div><dt class="text-sm text-[#64748B]">Total</dt><dd class="mt-1 font-semibold">PHP {{ number_format($reservation->total_amount, 2) }}</dd></div>
                <div><dt class="text-sm text-[#64748B]">Payment</dt><dd class="mt-1"><span class="status status-{{ $reservation->payment->payment_status }}">{{ str_replace('_', ' ', $reservation->payment->payment_status) }}</span></dd></div>
            </dl>
            @if ($reservation->status === 'pending')
                <form class="mt-8" method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Cancel Reservation</button>
                </form>
            @endif
        </section>
        <section class="card">
            <h2 class="text-xl font-semibold text-[#0F2447]">Upload Payment Proof</h2>
            <p class="mt-3 text-sm leading-6 text-[#64748B]">Use this for GCash/manual receipt payments. PayMongo can be connected with live credentials later.</p>
            <form class="mt-5 grid gap-4" method="POST" action="{{ route('payments.proof', $reservation->payment) }}" enctype="multipart/form-data">
                @csrf
                <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 text-sm" type="file" name="proof_image" accept="image/*" required>
                <button type="submit" class="btn btn-secondary">Upload Receipt</button>
            </form>
        </section>
    </div></div>
        </div>
    </main>
    @stack('scripts')
</body>
</html>
