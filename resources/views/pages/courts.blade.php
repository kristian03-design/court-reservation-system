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
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
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
                <a href="{{ route('home') }}#pricing">Pricing</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
            </nav>
            <div class="site-actions">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn btn-outline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                @endauth
            </div>
        </div>
    </header>
    <main>
        @if (session('success') || $errors->any())
            <div class="site-container alert-wrap">
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
            </div>
        @endif
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#57C271]">Available Courts</p>
                <h1 class="mt-2 text-4xl font-bold text-[#0F2447]">Browse courts and schedules</h1>
            </div>
            <form class="grid gap-3 sm:grid-cols-3" method="GET">
                <input class="rounded-lg border border-[#E5E7EB] px-4 py-3" name="search" value="{{ request('search') }}" placeholder="Search courts">
                <select class="rounded-lg border border-[#E5E7EB] px-4 py-3" name="type">
                    <option value="">All categories</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
                <select class="rounded-lg border border-[#E5E7EB] px-4 py-3" name="sort" onchange="this.form.submit()">
                    <option value="">Newest</option>
                    <option value="rate_low" @selected(request('sort') === 'rate_low')>Rate: Low to High</option>
                    <option value="rate_high" @selected(request('sort') === 'rate_high')>Rate: High to Low</option>
                </select>
            </form>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($courts as $court)
                <article class="card">
                    <img class="h-52 w-full rounded-lg object-cover" src="{{ $court->image ?: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $court->court_name }}">
                    <div class="mt-5 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-[#57C271]">{{ $court->court_type }}</p>
                            <h3 class="mt-1 text-lg font-semibold text-[#0F2447]">{{ $court->court_name }}</h3>
                        </div>
                        <span class="status status-{{ $court->status }}">{{ str_replace('_', ' ', $court->status) }}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-[#64748B]">
                        <span>Capacity: {{ $court->capacity }}</span>
                        <span>PHP {{ number_format($court->hourly_rate, 2) }}/hr</span>
                    </div>
                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('courts.show', $court) }}" class="btn btn-outline flex-1">Details</a>
                        <a href="{{ route('booking.create', ['court' => $court->id]) }}" class="btn btn-secondary flex-1">Book</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $courts->links() }}</div>
    </section>
    </main>
    <footer class="site-footer">
        <div class="site-container footer-grid">
            <div>
                <a href="{{ route('home') }}" class="brand">
                    @if ($layoutLogo)
                        <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                    @else
                        <span class="brand-fallback">CC</span>
                    @endif
                    <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
                </a>
                <p class="footer-copy">Reserve. Play. Connect. A modern reservation platform for sports facilities and recreational centers.</p>
            </div>
            <div>
                <h3 class="footer-title">Quick Links</h3>
                <div class="footer-links">
                    <a href="{{ route('courts.index') }}">Courts</a>
                    <a href="{{ route('about') }}">About</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
            </div>
            <div>
                <h3 class="footer-title">Contact</h3>
                <p class="footer-contact">hello@courtconnect.test<br>+63 900 123 4567</p>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
