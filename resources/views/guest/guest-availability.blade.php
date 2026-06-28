<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Court Availability | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); min-height: calc(100vh - 120px);">
        {{-- Hero Header --}}
        <section class="cc-section" style="padding-bottom: 32px;">
            <div class="site-container scroll-reveal reveal-fade-up" style="max-width: 1000px; margin-inline: auto; text-align: center;">
                <p class="cc-kicker" style="justify-content: center;">Slot Radar</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(48px, 9vw, 96px); text-transform: uppercase; line-height: 0.95; margin: 0 0 24px; font-weight: 400;">
                    FIND AN OPEN COURT. <span style="color: var(--lime);">GET TO PLAYING.</span>
                </h1>
                <p class="cc-lead" style="margin: 0 auto 36px; max-width: 620px;">
                    Select your preferred date and sport to scan real-time availability across all our premium courts. Tap any open slot to reserve it instantly.
                </p>
            </div>
        </section>

        {{-- Filters & Availability Section --}}
        <section class="cc-section cc-section-alt" style="padding-top: 48px; padding-bottom: 96px;">
            <div class="site-container">
                {{-- Date selection and filters --}}
                <div class="scroll-reveal reveal-fade-up stagger-1" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 24px; margin-bottom: 40px;">
                    <form method="GET" action="{{ route('availability') }}" id="availability-form" style="display: grid; grid-template-columns: 1fr; gap: 20px; align-items: end;">
                        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                            {{-- Date picker --}}
                            <div style="display: grid; gap: 8px;">
                                <label for="date-picker" style="color: var(--muted); font-family: var(--ui-font); font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Select Date</label>
                                <input type="date" name="date" id="date-picker" value="{{ $date }}" min="{{ now()->toDateString() }}" style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; color: var(--text); font-family: var(--ui-font); font-weight: 600;" onchange="document.getElementById('availability-form').submit();">
                            </div>
                        </div>

                        {{-- Sport Type filter pills --}}
                        <div style="border-top: 1px solid var(--border); padding-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                            <label style="color: var(--muted); font-family: var(--ui-font); font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Filter by Sport</label>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('availability', ['date' => $date]) }}" class="cc-filter-pill {{ !$selectedType ? 'is-active' : '' }}">All Sports</a>
                                @foreach ($types as $type)
                                    <a href="{{ route('availability', ['date' => $date, 'type' => $type]) }}" class="cc-filter-pill {{ $selectedType === $type ? 'is-active' : '' }}">{{ $type }}</a>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Courts Listing with Slots --}}
                @if ($courtsWithSlots->isNotEmpty())
                    <div style="display: grid; gap: 32px;">
                        @foreach ($courtsWithSlots as $item)
                            @php
                                $court = $item['court'];
                                $slots = $item['slots'];
                            @endphp
                            <div class="scroll-reveal reveal-fade-up stagger-{{ $loop->iteration }}" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); overflow: hidden; display: grid; grid-template-columns: 1fr; border-top: 2px solid var(--border);">
                                {{-- Court Info Bar --}}
                                <div style="display: grid; grid-template-columns: 1fr; padding: 24px; border-bottom: 1px solid var(--border); gap: 16px; background: var(--surface-alt);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                                        <div>
                                            <span class="sport-badge" style="position: static; margin-bottom: 8px; display: inline-flex;">{{ $court->court_type }}</span>
                                            <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); margin: 4px 0 0; line-height: 1.1;">{{ $court->court_name }}</h3>
                                        </div>
                                        <div style="text-align: right; display: flex; align-items: center; gap: 20px;">
                                            <div>
                                                <span style="color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; display: block;">Hourly Rate</span>
                                                <span style="font-size: 20px; font-weight: 700; color: var(--lime);">₱{{ number_format($court->hourly_rate, 0) }}<span style="font-size: 13px; color: var(--muted); font-weight: 500;">/hr</span></span>
                                            </div>
                                            <div>
                                                <span style="color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; display: block;">Capacity</span>
                                                <span style="font-size: 16px; font-weight: 600; color: var(--text);">{{ $court->capacity }} Players</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hourly Slots Rail --}}
                                <div style="padding: 24px; background: var(--surface);">
                                    <p style="color: var(--muted); font-family: var(--ui-font); font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; margin: 0 0 16px;">Daily time slots (8:00 AM - 10:00 PM)</p>
                                    
                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px;">
                                        @foreach ($slots as $slot)
                                            @if ($slot['available'])
                                                <a href="{{ route('booking.create', ['court' => $court->id, 'reservation_date' => $date, 'start_time' => $slot['start']]) }}" class="cc-slot is-available" style="width: 100%; min-height: 80px; text-decoration: none; padding: 12px 6px;">
                                                    <span class="slot-time" style="color: var(--text); font-size: 13px;">{{ explode(' - ', $slot['label'])[0] }}</span>
                                                    <span style="color: var(--lime); font-size: 10px; font-weight: 700; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.04em;">Available</span>
                                                </a>
                                            @else
                                                <div class="cc-slot is-booked" style="width: 100%; min-height: 80px; padding: 12px 6px;">
                                                    <span class="slot-time" style="font-size: 13px;">{{ explode(' - ', $slot['label'])[0] }}</span>
                                                    <span style="color: var(--coral); font-size: 10px; font-weight: 700; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.04em;">Booked</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="cc-empty-state">
                        <p class="cc-kicker">No Courts Found</p>
                        <h2>No active courts found</h2>
                        <p>We couldn't find any courts matching your criteria. Try changing the date or adjusting the sport type filter.</p>
                        <a href="{{ route('availability') }}" class="btn btn-primary" style="margin-top: 12px;">Reset Filters</a>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
