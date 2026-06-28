<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Browse available courts — badminton, tennis, basketball, futsal. Filter by sport type, price, and availability.">
    <title>Courts | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main>

        @php
            $courtTotal = method_exists($courts, 'total') ? $courts->total() : $courts->count();
            $activeFilters = collect([request('type')])->filter()->count();
        @endphp

        {{-- Page header --}}
        <div style="background:var(--bg); padding-top: 48px; border-bottom: 1px solid var(--border);">
            <div class="site-container">
                <div class="cc-listing-header scroll-reveal reveal-fade-up" style="padding-bottom: 24px; margin-bottom: 0;">
                    <div>
                        <p class="cc-kicker">{{ request('type') ?: 'All Sports' }}</p>
                        <h1 style="margin: 0;">Find Your Court.</h1>
                    </div>
                    <div class="cc-listing-meta" style="text-align: right;">
                        <span style="font-size: 20px; font-weight: 700; color: var(--lime); font-family: var(--display-font); letter-spacing: 0.02em;">{{ $courtTotal }}</span> courts listed<br>
                        @if ($activeFilters)
                            <span style="color: var(--muted-mid); font-size: 11px;">{{ $activeFilters }} filter active</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky filter bar --}}
        <div class="cc-filter-bar scroll-reveal reveal-fade-up stagger-1" style="border-bottom: 1px solid var(--border); background: rgba(15, 15, 15, 0.85); -webkit-backdrop-filter: blur(20px); backdrop-filter: blur(20px); position: sticky; top: 68px; z-index: 100;">
            <div class="site-container">
                <form class="cc-filter-bar-inner" method="GET" id="filter-form">
                    <span style="color: var(--muted); font-family: var(--ui-font); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-right: 8px;">Sport Type:</span>
                    <button type="submit" name="type" value="" class="cc-filter-pill {{ !request('type') ? 'is-active' : '' }}">All</button>
                    @php $sportTypes = ['Badminton', 'Tennis', 'Basketball', 'Futsal', 'Volleyball']; @endphp
                    @foreach ($sportTypes as $sport)
                        <button type="submit" name="type" value="{{ $sport }}"
                            class="cc-filter-pill {{ request('type') === $sport ? 'is-active' : '' }}">
                            {{ $sport }}
                        </button>
                    @endforeach
                </form>
            </div>
        </div>

        <div style="background:var(--bg); min-height:calc(100vh - 240px); padding-bottom:80px; padding-top: 48px;">
            <div class="site-container">

                {{-- Court Grid --}}
                @if ($courts->count())
                    <div class="cc-courts-grid">
                        @foreach ($courts as $court)
                            @php $isUnavailable = $court->status !== 'available'; @endphp
                            <article class="cc-court-card scroll-reveal reveal-fade-up stagger-{{ $loop->iteration }} {{ $isUnavailable ? 'is-unavailable' : '' }}">
                                <a href="{{ route('courts.show', $court) }}" class="cc-court-image">
                                    <img src="{{ $court->image ?: asset('images/courtconnect-multisport-hero.png') }}"
                                         alt="{{ $court->court_name }}">
                                    @if ($isUnavailable)
                                        <span class="badge-fully-booked">Fully Booked</span>
                                    @else
                                        <span class="sport-badge">{{ $court->court_type }}</span>
                                    @endif
                                </a>
                                <div class="cc-court-body">
                                    <div>
                                        <h3>{{ $court->court_name }}</h3>
                                        <p style="margin:4px 0 0;color:var(--muted);font-size:12px;font-weight:500;">
                                            Up to {{ $court->capacity }} players
                                        </p>
                                        @if (!$isUnavailable)
                                            <p class="cc-next-slot" style="margin:8px 0 0;">
                                                Next slot: {{ now()->addHour()->format('g A') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:14px;border-top:1px solid var(--border);">
                                        <div class="cc-price">
                                            ₱{{ number_format($court->hourly_rate, 0) }}<span>/hr</span>
                                        </div>
                                        @if (!$isUnavailable)
                                            <div class="cc-card-actions">
                                                <a href="{{ route('courts.show', $court) }}" class="btn-card">Details</a>
                                                <a href="{{ route('booking.create', ['court' => $court->id]) }}" class="btn-card btn-card-primary">Book Now</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <section class="cc-empty-state">
                        <p class="cc-kicker">No results</p>
                        <h2>No courts match your filters.</h2>
                        <p>Try adjusting your search or clearing the active filters to see all available courts.</p>
                        <a href="{{ route('courts.index') }}" class="btn btn-primary">
                            Clear filters &amp; browse all
                        </a>
                    </section>
                @endif

                {{-- Pagination --}}
                <div style="margin-top:12px;">
                    {{ $courts->links() }}
                </div>

            </div>
        </div>

    </main>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
