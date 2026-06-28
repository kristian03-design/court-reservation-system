<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $court->court_name }} — {{ $court->court_type }} court available for booking at ₱{{ number_format($court->hourly_rate, 0) }}/hr.">
    <title>{{ $court->court_name }} | CourtConnect</title>
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

        {{-- ══════════════ HERO BANNER ══════════════ --}}
        <div class="cc-detail-banner">
            <img src="{{ $court->image ?: asset('images/courtconnect-multisport-hero.png') }}"
                 alt="{{ $court->court_name }}">
            <div class="cc-detail-banner-copy site-container scroll-reveal reveal-fade-up">
                <div class="cc-breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span class="sep">/</span>
                    <a href="{{ route('courts.index') }}">Courts</a>
                    <span class="sep">/</span>
                    <span class="current">{{ $court->court_name }}</span>
                </div>
                <span class="sport-badge" style="position:static;margin-bottom:12px;">{{ $court->court_type }}</span>
                <h1>{{ $court->court_name }}</h1>
            </div>
        </div>

        {{-- ══════════════ TWO-COLUMN LAYOUT ══════════════ --}}
        <div style="background:var(--bg);">
            <div class="site-container cc-detail-layout">

                {{-- Left: Court Details --}}
                <div class="cc-detail-main">

                    {{-- Stats strip --}}
                    <div class="cc-detail-stats">
                        <div class="cc-stat-card scroll-reveal reveal-fade-up stagger-1">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="label">Capacity</span>
                            <span class="value">{{ $court->capacity }}</span>
                        </div>
                        <div class="cc-stat-card scroll-reveal reveal-fade-up stagger-2">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="label">Duration</span>
                            <span class="value">60 min</span>
                        </div>
                        <div class="cc-stat-card scroll-reveal reveal-fade-up stagger-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="label">Rate</span>
                            <span class="value">₱{{ number_format($court->hourly_rate, 0) }}/hr</span>
                        </div>
                        <div class="cc-stat-card scroll-reveal reveal-fade-up stagger-4" id="court-status-card">
                            <div id="court-status-icon-container">
                                @if ($court->status === 'available')
                                    @if (collect($slots)->where('available', true)->isEmpty())
                                        <svg fill="none" stroke="var(--coral)" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--coral); width: 22px; height: 22px; margin: 0 auto 10px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    @else
                                        <svg fill="none" stroke="var(--lime)" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--lime); width: 22px; height: 22px; margin: 0 auto 10px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                @elseif ($court->status === 'maintenance')
                                    <svg fill="none" stroke="var(--coral)" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--coral); width: 22px; height: 22px; margin: 0 auto 10px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                @else
                                    <svg fill="none" stroke="var(--coral)" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--coral); width: 22px; height: 22px; margin: 0 auto 10px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="label">Status</span>
                            <span class="value" id="court-status-value" style="color:{{ ($court->status === 'available' && !collect($slots)->where('available', true)->isEmpty()) ? 'var(--lime)' : 'var(--coral)' }}">
                                @if ($court->status === 'available' && collect($slots)->where('available', true)->isEmpty())
                                    Fully Booked
                                @else
                                    {{ ucfirst($court->status) }}
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="cc-detail-section scroll-reveal reveal-fade-up stagger-1">
                        <h2>About This Court</h2>
                        <p>{{ $court->description ?: 'Premium court equipped with top-tier amenities for both professional training and casual play. Maintained to international standards with proper lighting, ventilation, and surface quality.' }}</p>
                    </div>

                    {{-- Court Gallery --}}
                    @php
                        // Load gallery config from the public JSON file
                        $galleryConfig = [];
                        if (file_exists(public_path('gallery.json'))) {
                            $galleryConfig = json_decode(file_get_contents(public_path('gallery.json')), true) ?: [];
                        }
                        
                        $typeSlug = strtolower($court->court_type);
                        
                        // Parse database gallery if stored as JSON string, otherwise use array cast
                        $dbGallery = $court->gallery;
                        if (is_string($dbGallery)) {
                            $dbGallery = json_decode($dbGallery, true);
                        }
                        
                        // Fallback to type-specific JSON config, or general default
                        $galleryImages = $dbGallery ?: ($galleryConfig[$typeSlug] ?? [
                            $court->image ?: 'images/courtconnect-multisport-hero.png',
                            'images/courtconnect-club-courts.png'
                        ]);
                        
                        // Ensure all local paths point to existing files
                        $galleryImages = array_filter($galleryImages, function($path) {
                            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                                return true;
                            }
                            return file_exists(public_path(ltrim($path, '/')));
                        });
                        
                        // Fallback if all images are filtered out
                        if (empty($galleryImages)) {
                            $galleryImages = [
                                $court->image ?: ''
                            ];
                        }
                        
                        // Reset array keys and resolve URLs
                        $galleryImages = array_map(function($path) {
                            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                                return $path;
                            }
                            return asset(ltrim($path, '/'));
                        }, array_values($galleryImages));
                    @endphp
                    <div class="cc-detail-section">
                        <h2>Court Views &amp; Gallery</h2>
                        <div class="cc-gallery-container">
                            <div class="cc-gallery-main">
                                <img id="active-gallery-image" src="{{ $galleryImages[0] }}" alt="{{ $court->court_name }} - View 1">
                            </div>
                            <div class="cc-gallery-thumbnails">
                                @foreach ($galleryImages as $index => $img)
                                    <div class="cc-gallery-thumb {{ $index === 0 ? 'is-active' : '' }}" 
                                         onclick="changeGalleryImage('{{ $img }}', this)"
                                         role="button"
                                         tabindex="0">
                                        <img src="{{ $img }}" alt="View {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Amenities --}}
                    <div class="cc-detail-section">
                        <h2>Amenities</h2>
                        <div class="cc-amenity-list">
                            <span class="cc-amenity-pill">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Professional Lighting
                            </span>
                            <span class="cc-amenity-pill">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Locker Rooms
                            </span>
                            <span class="cc-amenity-pill">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Bleacher Seating
                            </span>
                            <span class="cc-amenity-pill">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Free Parking
                            </span>
                            <span class="cc-amenity-pill">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Equipment Available
                            </span>
                            <span class="cc-amenity-pill">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Air Conditioned
                            </span>
                        </div>
                    </div>

                    {{-- Rules --}}
                    <div class="cc-detail-section">
                        <h2>Court Rules</h2>
                        <div style="display:grid;gap:10px;">
                            @foreach ([
                                'Arrive 10 minutes before your scheduled session.',
                                'Proper sports attire and non-marking shoes required.',
                                'No food or drinks on the court surface.',
                                'Cancel at least 2 hours in advance to avoid charges.',
                                'Maximum of ' . $court->capacity . ' players per session.',
                            ] as $rule)
                                <div style="display:flex;gap:10px;align-items:flex-start;">
                                    <span style="color:var(--lime);font-size:14px;margin-top:1px;flex-shrink:0;">—</span>
                                    <span style="color:var(--muted-mid);font-size:14px;line-height:1.6;">{{ $rule }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Right: Sticky Booking Panel --}}
                <div>
                    <div class="cc-booking-panel">

                        {{-- Progress indicator --}}
                        <div class="booking-progress">
                            <div class="booking-progress-step is-active">
                                <span class="num">1</span>
                                <span>Pick date &amp; time</span>
                            </div>
                            <div class="booking-progress-divider"></div>
                            <div class="booking-progress-step">
                                <span class="num">2</span>
                                <span>Confirm</span>
                            </div>
                            <div class="booking-progress-divider"></div>
                            <div class="booking-progress-step">
                                <span class="num">3</span>
                                <span>Done</span>
                            </div>
                        </div>

                        <style>
                        .cc-date-pill.is-active {
                            border-color: var(--lime) !important;
                            background: rgba(191, 255, 0, 0.08) !important;
                        }
                        .cc-date-pill.is-active span {
                            color: var(--lime) !important;
                        }
                        .cc-date-pill.is-active strong {
                            color: var(--lime) !important;
                        }
                        #custom-date-btn.is-active {
                            border-color: var(--lime) !important;
                            background: rgba(191, 255, 0, 0.08) !important;
                        }
                        #custom-date-btn.is-active svg {
                            color: var(--lime) !important;
                        }
                        #court-date-pills {
                            -ms-overflow-style: none;  /* IE and Edge */
                            scrollbar-width: none;  /* Firefox */
                        }
                        #court-date-pills::-webkit-scrollbar {
                            display: none;  /* Chrome, Safari and Opera */
                        }
                        </style>

                        <div class="cc-booking-panel-head">
                            <h3>Book This Court</h3>
                            <p id="booking-display-date">{{ now()->format('l, F j, Y') }}</p>
                        </div>

                        {{-- Date selector section --}}
                        <div class="cc-date-selector-section" style="padding: 12px 22px 0;">
                            <p class="cc-slot-label" style="margin-bottom: 8px;">Select Date</p>
                            
                            {{-- Day Pills Grid --}}
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;" id="court-date-pills">
                                @foreach(range(0, 6) as $i)
                                    @php $d = now()->addDays($i); @endphp
                                    <button type="button" 
                                            class="cc-date-pill {{ $i === 0 ? 'is-active' : '' }}" 
                                            data-date="{{ $d->toDateString() }}"
                                            data-display="{{ $d->format('l, F j, Y') }}"
                                            style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 52px; border: 1px solid var(--border); border-radius: var(--radius); background: rgba(255, 255, 255, 0.02); color: var(--muted); cursor: pointer; transition: all var(--transition); font-family: inherit;">
                                        <span style="font-size: 10px; font-weight: 500; text-transform: uppercase; color: var(--text-secondary); transition: color var(--transition);">{{ $d->format('D') }}</span>
                                        <strong style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-top: 2px; transition: color var(--transition);">{{ $d->format('d') }}</strong>
                                    </button>
                                @endforeach
                                
                                {{-- Custom date picker trigger --}}
                                <div style="position: relative; width: 100%;">
                                    <input type="date" 
                                           id="custom-date-picker" 
                                           min="{{ now()->toDateString() }}" 
                                           max="{{ now()->addDays(30)->toDateString() }}"
                                           style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;">
                                    <button type="button" 
                                            id="custom-date-btn"
                                            style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 52px; border: 1px solid var(--border); border-radius: var(--radius); background: rgba(255, 255, 255, 0.02); color: var(--muted); cursor: pointer; transition: all var(--transition); font-family: inherit;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-secondary); margin-bottom: 2px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: var(--text-secondary);">More</span>
                                    </button>
                                </div>
                            </div>
                            
                            <input type="hidden" id="selected-booking-date" value="{{ now()->toDateString() }}">
                        </div>

                        {{-- Availability slots --}}
                        <div class="cc-slot-section">
                            <p class="cc-slot-label" id="availability-label">Available Slots</p>

                            {{-- THE SIGNATURE SLOT RAIL --}}
                            <div class="cc-slot-rail-wrap">
                                <div class="cc-slot-rail" id="slot-rail">
                                    @if ($court->status !== 'available')
                                        <div style="color: var(--coral); text-align: center; width: 100%; padding: 12px 0; font-size: 13px; font-weight: 600;">
                                            This court is currently {{ $court->status }}.
                                        </div>
                                    @else
                                        @foreach ($slots as $slot)
                                            @php
                                                $stateClass = $slot['available'] ? 'is-available' : 'is-booked';
                                                $timeDisplay = \Carbon\Carbon::parse($slot['start'])->format('g A');
                                            @endphp
                                            <div class="cc-slot {{ $stateClass }}"
                                                 data-time="{{ $slot['start'] }}"
                                                 data-available="{{ $slot['available'] ? '1' : '0' }}"
                                                 title="{{ $slot['available'] ? 'Available' : 'Booked' }} — {{ $timeDisplay }}"
                                                 role="button"
                                                 tabindex="{{ $slot['available'] ? '0' : '-1' }}"
                                                 aria-label="{{ $timeDisplay }} — {{ $slot['available'] ? 'Available' : 'Booked' }}">
                                                <span class="slot-time">{{ $timeDisplay }}</span>
                                                <span class="slot-price">
                                                    @if ($slot['available'])
                                                        ₱{{ number_format($court->hourly_rate, 0) }}
                                                    @else
                                                        Booked
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Booking summary (live update placeholder) --}}
                        <div class="cc-booking-summary">
                            <div class="cc-booking-summary-row">
                                <span class="key">Court</span>
                                <span class="val">{{ $court->court_name }}</span>
                            </div>
                            <div class="cc-booking-summary-row">
                                <span class="key">Date</span>
                                <span class="val" id="summary-date">{{ now()->format('M d, Y') }}</span>
                            </div>
                            <div class="cc-booking-summary-row">
                                <span class="key">Time</span>
                                <span class="val" id="summary-time">Select a slot above</span>
                            </div>
                            <div class="cc-booking-summary-row total" style="padding-top:10px;border-top:1px solid var(--border);margin-top:4px;">
                                <span class="key">Rate</span>
                                <span class="val">₱{{ number_format($court->hourly_rate, 0) }}/hr</span>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="cc-booking-panel-cta">
                            <a href="{{ route('booking.create', ['court' => $court->id]) }}"
                               id="book-cta"
                               class="cc-confirm-btn"
                               style="display:flex;align-items:center;justify-content:center;text-decoration:none;">
                                Confirm Booking
                            </a>
                            <p style="text-align:center;color:var(--muted);font-size:11px;margin:10px 0 0;font-weight:500;">
                                You'll select the exact time on the next step.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </main>

    @include('partials.public-footer')

    <script>
    function changeGalleryImage(src, element) {
        const activeImage = document.getElementById('active-gallery-image');
        if (activeImage) {
            activeImage.style.opacity = '0.3';
            setTimeout(() => {
                activeImage.src = src;
                activeImage.style.opacity = '1';
            }, 100);
        }
        
        const thumbnails = document.querySelectorAll('.cc-gallery-thumb');
        thumbnails.forEach(thumb => thumb.classList.remove('is-active'));
        element.classList.add('is-active');
    }

    (function () {
        const rail = document.getElementById('slot-rail');
        if (!rail) return;

        const summaryTime = document.getElementById('summary-time');
        const bookCta = document.getElementById('book-cta');
        const displayDate = document.getElementById('booking-display-date');
        const dateInput = document.getElementById('selected-booking-date');
        const quickPills = document.querySelectorAll('.cc-date-pill');
        const datePicker = document.getElementById('custom-date-picker');
        const customDateBtn = document.getElementById('custom-date-btn');

        const courtId = "{{ $court->id }}";
        const hourlyRate = {{ $court->hourly_rate }};
        const availabilityUrlPattern = "{{ route('courts.availability', ['court' => $court->id]) }}";
        const courtStatus = "{{ $court->status }}";

        function updateCtaNoSlots() {
            if (bookCta) {
                if (courtStatus === 'maintenance') {
                    bookCta.textContent = "Unavailable - Maintenance";
                } else if (courtStatus === 'closed') {
                    bookCta.textContent = "Unavailable - Closed";
                } else {
                    bookCta.textContent = "Fully Booked for Selected Date";
                }
                bookCta.style.pointerEvents = "none";
                bookCta.style.background = "var(--border)";
                bookCta.style.color = "var(--muted)";
                bookCta.style.borderColor = "var(--border)";
            }
            if (summaryTime) {
                summaryTime.textContent = "None available";
            }
        }

        function bindSlotEvents() {
            const slots = rail.querySelectorAll('.cc-slot.is-available');
            const date = dateInput.value;

            // Update date text in summary
            const summaryDate = document.getElementById('summary-date');
            if (summaryDate) {
                const parsedDate = new Date(date);
                summaryDate.textContent = parsedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }

            slots.forEach(function (slot) {
                slot.addEventListener('click', function () {
                    // Deselect others
                    rail.querySelectorAll('.cc-slot').forEach(function (s) { s.classList.remove('is-selected'); });
                    slot.classList.add('is-selected');

                    const time = slot.dataset.time;
                    if (summaryTime) {
                        const [hoursStr, minutesStr] = time.split(':');
                        const hours = parseInt(hoursStr);
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        const displayHour = hours % 12 || 12;
                        summaryTime.textContent = `${displayHour}:${minutesStr} ${ampm}`;
                    }

                    // Update CTA link
                    if (bookCta) {
                        const url = new URL(bookCta.href, window.location.origin);
                        url.searchParams.set('start_time', time);
                        url.searchParams.set('reservation_date', date);
                        bookCta.href = url.toString();
                        
                        // Reset CTA styles in case it was disabled
                        bookCta.textContent = "Confirm Booking";
                        bookCta.style.pointerEvents = "auto";
                        bookCta.style.background = "var(--lime)";
                        bookCta.style.color = "var(--bg)";
                        bookCta.style.borderColor = "var(--lime)";
                    }
                });

                // Keyboard nav
                slot.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        slot.click();
                    }
                });
            });

            // Auto-select first available slot on load
            if (slots.length > 0) {
                slots[0].click();
            } else {
                updateCtaNoSlots();
            }
        }

        function fetchAvailability(date) {
            if (courtStatus !== 'available') {
                rail.innerHTML = `<div style="color: var(--coral); text-align: center; width: 100%; padding: 12px 0; font-size: 13px; font-weight: 600;">This court is currently ${courtStatus}.</div>`;
                updateCtaNoSlots();
                return;
            }

            // Update UI to show loading state with shimmer skeletons
            let skeletons = '';
            for (let i = 0; i < 8; i++) {
                skeletons += `<div class="skeleton-shimmer" style="height: 80px; border-radius: var(--radius); opacity: 0.15;"></div>`;
            }
            rail.innerHTML = `<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; width: 100%;">${skeletons}</div>`;
            
            fetch(`${availabilityUrlPattern}?date=${date}`)
                .then(response => response.json())
                .then(slotsData => {
                    rail.style.opacity = '1';
                    rail.innerHTML = ''; // Clear

                    const hasAvailableSlots = slotsData.some(slot => slot.available);
                    
                    // Update court status card dynamically based on slot availability
                    const statusVal = document.getElementById('court-status-value');
                    const statusIconContainer = document.getElementById('court-status-icon-container');
                    
                    if (statusVal && statusIconContainer) {
                        if (!hasAvailableSlots) {
                            statusVal.textContent = "Fully Booked";
                            statusVal.style.color = "var(--coral)";
                            statusIconContainer.innerHTML = `<svg fill="none" stroke="var(--coral)" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--coral); width: 22px; height: 22px; margin: 0 auto 10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
                        } else {
                            statusVal.textContent = "Available";
                            statusVal.style.color = "var(--lime)";
                            statusIconContainer.innerHTML = `<svg fill="none" stroke="var(--lime)" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--lime); width: 22px; height: 22px; margin: 0 auto 10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                        }
                    }

                    if (slotsData.length === 0) {
                        rail.innerHTML = '<div style="color: var(--muted); text-align: center; width: 100%; padding: 12px 0; font-size: 13px;">No slots available.</div>';
                        updateCtaNoSlots();
                        return;
                    }

                    slotsData.forEach(slot => {
                        const stateClass = slot.available ? 'is-available' : 'is-booked';
                        
                        // Parse time for display (e.g. "08:00" -> "8 AM")
                        const [hoursStr, minutesStr] = slot.start.split(':');
                        const hours = parseInt(hoursStr);
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        const displayHour = hours % 12 || 12;
                        const timeDisplay = `${displayHour} ${ampm}`;

                        const slotEl = document.createElement('div');
                        slotEl.className = `cc-slot ${stateClass}`;
                        slotEl.dataset.time = slot.start;
                        slotEl.dataset.available = slot.available ? '1' : '0';
                        slotEl.title = `${slot.available ? 'Available' : 'Booked'} — ${timeDisplay}`;
                        slotEl.setAttribute('role', 'button');
                        slotEl.setAttribute('tabindex', slot.available ? '0' : '-1');
                        slotEl.setAttribute('aria-label', `${timeDisplay} — ${slot.available ? 'Available' : 'Booked'}`);

                        const priceHtml = slot.available 
                            ? `₱${hourlyRate.toLocaleString()}`
                            : 'Booked';

                        slotEl.innerHTML = `
                            <span class="slot-time">${timeDisplay}</span>
                            <span class="slot-price">${priceHtml}</span>
                        `;

                        rail.appendChild(slotEl);
                    });

                    // Wire up events for the newly rendered slots
                    bindSlotEvents();
                })
                .catch(error => {
                    console.error('Error fetching availability:', error);
                    rail.style.opacity = '1';
                });
        }

        // Quick date pills selection events
        quickPills.forEach(pill => {
            pill.addEventListener('click', () => {
                quickPills.forEach(p => p.classList.remove('is-active'));
                customDateBtn.classList.remove('is-active');
                pill.classList.add('is-active');
                
                const date = pill.dataset.date;
                const display = pill.dataset.display;
                
                dateInput.value = date;
                displayDate.textContent = display;
                
                fetchAvailability(date);
            });
        });

        // Custom date selector input events
        if (datePicker) {
            datePicker.addEventListener('change', (e) => {
                const date = e.target.value;
                if (!date) return;

                quickPills.forEach(p => p.classList.remove('is-active'));
                customDateBtn.classList.add('is-active');
                
                dateInput.value = date;
                
                const parsed = new Date(date);
                const display = parsed.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
                displayDate.textContent = display;
                
                fetchAvailability(date);
            });
        }

        // Initial setup for default (today's) slots
        bindSlotEvents();
    })();
    </script>
</body>
</html>
