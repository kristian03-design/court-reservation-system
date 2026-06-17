@php
    $logoCandidates = ['images/courtconnect-mark.png', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.png', 'courtconnect-logo.svg', 'courtconnect-logo.png'];
    $logo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CourtConnect</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a href="{{ route('home') }}" class="brand">
                @if ($logo)
                    <span class="brand-mark">
                        <img src="{{ asset($logo) }}" alt="CourtConnect logo">
                    </span>
                @else
                    <span class="brand-fallback">CC</span>
                @endif
                <span>
                    <span class="brand-name">CourtConnect</span>
                    <span class="brand-tagline">Reserve. Play. Connect.</span>
                </span>
            </a>

            <nav class="main-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('courts.index') }}">Courts</a>
                <a href="{{ route('home') }}#pricing">Pricing</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
            </nav>

            <div class="nav-actions">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="button button-outline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="login-link">Login</a>
                    <a href="{{ route('register') }}" class="button button-secondary">Register</a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('success') || $errors->any())
        <div class="container alert-wrap">
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

    <main>
        <section class="hero">
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1519861531473-9200262188bf?auto=format&fit=crop&w=1800&q=80" alt="Indoor court">
            </div>
            <div class="container hero-content">
                <div class="hero-text">
                    <p class="eyebrow">Premium sports reservations</p>
                    <h1 class="hero-title">Reserve. Play. <span class="accent">Connect.</span></h1>
                    <p class="hero-copy">Premium courts, real-time availability, and seamless booking for athletes, teams, and modern sports facilities.</p>
                    <div class="hero-actions">
                        <a href="{{ route('booking.create') }}" class="button button-secondary">Book a Court</a>
                        <a href="{{ route('courts.index') }}" class="button button-outline">Explore Courts</a>
                    </div>
                    <div class="hero-stats" aria-label="CourtConnect highlights">
                        <div class="hero-stat"><strong>12+</strong><span>Active Courts</span></div>
                        <div class="hero-stat"><strong>1.5K+</strong><span>Bookings</span></div>
                        <div class="hero-stat"><strong>98%</strong><span>Satisfaction</span></div>
                        <div class="hero-stat"><strong>Live</strong><span>Availability</span></div>
                    </div>
                </div>

                <aside class="booking-widget" aria-label="Book your court">
                    <h2>Book your court</h2>
                    <p>Select your preferred court, date, and play time.</p>
                    <form class="booking-form" method="GET" action="{{ route('booking.create') }}">
                        <label class="booking-field">
                            Court Type
                            <select name="court">
                                <option value="">All Courts</option>
                                @foreach ($courts as $court)
                                    <option value="{{ $court->id }}">{{ $court->court_type }} - {{ $court->court_name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="booking-field">
                            Date
                            <input type="date" name="reservation_date" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}">
                        </label>
                        <label class="booking-field">
                            Time
                            <input type="time" name="start_time" value="10:00">
                        </label>
                        <label class="booking-field">
                            Players
                            <select name="players">
                                <option value="2">2 Players</option>
                                <option value="4">4 Players</option>
                                <option value="8">8 Players</option>
                                <option value="10">10 Players</option>
                            </select>
                        </label>
                        <button class="button button-secondary" type="submit">Check Availability</button>
                    </form>
                </aside>
            </div>
        </section>

        <section class="section">
            <div class="container feature-grid">
                @foreach (['Real-Time Availability', 'Easy Booking', 'Secure Reservations', 'Email Notifications'] as $feature)
                    <section class="feature-card">
                        <h2>{{ $feature }}</h2>
                        <p>Reliable tools for players and facility teams to move from search to confirmed play time.</p>
                    </section>
                @endforeach
            </div>
        </section>

        <section id="pricing" class="section section-white">
            <div class="container">
                <div class="section-head">
                    <div>
                        <p class="section-kicker">Court Showcase</p>
                        <h2 class="section-title">Find the right court</h2>
                    </div>
                    <a href="{{ route('courts.index') }}" class="button button-outline">Browse All</a>
                </div>

                <div class="court-grid">
                    @foreach ($courts as $court)
                        <article class="court-card">
                            <img class="court-image" src="{{ $court->image ?: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $court->court_name }}">
                            <div class="court-body">
                                <div class="court-top">
                                    <div>
                                        <p class="court-type">{{ $court->court_type }}</p>
                                        <h3 class="court-name">{{ $court->court_name }}</h3>
                                    </div>
                                    <span class="status status-{{ $court->status }}">{{ str_replace('_', ' ', $court->status) }}</span>
                                </div>

                                <div class="court-meta">
                                    <span>Capacity: {{ $court->capacity }}</span>
                                    <span>PHP {{ number_format($court->hourly_rate, 2) }}/hr</span>
                                </div>

                                <div class="court-actions">
                                    <a href="{{ route('courts.show', $court) }}" class="button button-outline">Details</a>
                                    <a href="{{ route('booking.create', ['court' => $court->id]) }}" class="button button-secondary">Book</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2 class="section-title">How It Works</h2>
                <div class="steps-grid">
                    @foreach (['Choose Court', 'Select Schedule', 'Confirm Reservation', 'Play'] as $index => $step)
                        <div class="step-card">
                            <span class="step-number">Step {{ $index + 1 }}</span>
                            <h3>{{ $step }}</h3>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section section-muted">
            <div class="container">
                <p class="section-kicker">Testimonials</p>
                <h2 class="section-title">Customer Reviews</h2>
                <div class="review-grid">
                    @foreach ($testimonials as $testimonial)
                        <figure class="review-card">
                            <p>{{ $testimonial->comment }}</p>
                            <figcaption>{{ $testimonial->name }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="brand">
                    @if ($logo)
                        <span class="brand-mark">
                            <img src="{{ asset($logo) }}" alt="CourtConnect logo">
                        </span>
                    @else
                        <span class="brand-fallback">CC</span>
                    @endif
                    <span>
                        <span class="brand-name">CourtConnect</span>
                        <span class="brand-tagline">Reserve. Play. Connect.</span>
                    </span>
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
</body>
</html>
