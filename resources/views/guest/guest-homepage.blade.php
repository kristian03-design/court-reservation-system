<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CourtConnect — Book badminton, tennis, basketball and futsal courts online. Browse live availability and reserve your slot instantly.">
    <title>CourtConnect — Book Your Court. Own Your Game.</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home">
    @include('partials.public-header')
    @include('partials.toast')

    <main>

        {{-- ═══════════════════════ HERO ═══════════════════════ --}}
        <section class="cc-hero" id="availability">
            <div class="cc-hero-bg">
                <img src="{{ asset('images/courtconnect-badminton-court.webp') }}" alt="Premium indoor court">
            </div>
            <div class="site-container cc-hero-grid">
                <div class="scroll-reveal reveal-fade-up">
                    <p class="cc-kicker">Badminton · Tennis · Basketball · Futsal · Volleyball</p>
                    <h1>
                        Book your court.<br>
                        <span class="accent">Own your game.</span>
                    </h1>
                    <p class="cc-lead">Browse live court availability, reserve a slot, and keep every game organized from one clean schedule.</p>
                    <div class="cc-hero-actions">
                        <a href="{{ route('courts.index') }}" class="btn btn-primary">Browse Courts</a>
                        <a href="{{ route('booking.create') }}" class="btn btn-outline">Book a Court</a>
                    </div>
                    {{-- Equinox-style Active Player Badging --}}
                    <div style="display: flex; align-items: center; gap: 16px; margin-top: 36px; padding: 12px 18px; border: 1px solid var(--border); border-radius: 99px; background: rgba(255,255,255,0.02); width: fit-content;">
                        <div style="display: flex; align-items: center; margin-right: -4px;">
                            <div style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid var(--bg); background: #3B82F6; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #FFF; margin-right: -10px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">JD</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid var(--bg); background: #10B981; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #FFF; margin-right: -10px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">MR</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid var(--bg); background: #F59E0B; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #FFF; margin-right: -10px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">AL</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid var(--bg); background: var(--surface); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; color: var(--lime); box-shadow: 0 4px 10px rgba(0,0,0,0.3);">+500</div>
                        </div>

                        <div style="display: grid; gap: 2px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="display: flex; gap: 1px; color: var(--lime);">
                                    <span style="font-size: 13px;">★</span>
                                    <span style="font-size: 13px;">★</span>
                                    <span style="font-size: 13px;">★</span>
                                    <span style="font-size: 13px;">★</span>
                                    <span style="font-size: 13px;">★</span>
                                </div>
                                <span style="font-family: var(--ui-font); font-size: 12px; font-weight: 700; color: var(--text);">4.9/5</span>
                            </div>
                            <span style="font-family: var(--ui-font); font-size: 10px; color: var(--muted-mid); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Joined by 500+ active players</span>
                        </div>
                    </div>
                </div>

                {{-- Live Court Radar / Status Ticker --}}
                <aside class="cc-booking-console scroll-reveal reveal-fade-left stagger-1" aria-label="Live Court Scanner" style="display: flex; flex-direction: column; min-height: 480px; justify-content: space-between;">
                    <style>
                        @keyframes pulse {
                            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(191, 255, 0, 0.7); }
                            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(191, 255, 0, 0); }
                            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(191, 255, 0, 0); }
                        }
                    </style>
                    <div class="console-top" style="border-bottom: 1px solid var(--border); padding-bottom: 16px;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--lime); display: inline-block; animation: pulse 1.8s infinite;"></span>
                            LIVE RADAR
                        </span>
                        <strong id="live-radar-datetime" style="font-family: var(--display-font); font-size: 16px; color: var(--text); letter-spacing: 0.02em;">{{ now()->format('M d, Y • g:i:s A') }}</strong>
                    </div>

                    @php
                        $occupiedCount = $courts->filter(fn($c) => $c->isOccupiedNow())->count();
                        $totalCount = $courts->count();
                        $occupancyRate = $totalCount > 0 ? round(($occupiedCount / $totalCount) * 100) : 0;
                    @endphp

                    <div style="padding: 20px 22px 14px; display: grid; gap: 6px; border-bottom: 1px solid var(--border);">
                        <div style="display: flex; justify-content: space-between; font-family: var(--ui-font); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                            <span style="color: var(--muted-mid);">Occupancy Rate</span>
                            <span style="color: var(--lime);">{{ $occupancyRate }}% ({{ $occupiedCount }}/{{ $totalCount }} courts)</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: var(--border); border-radius: 99px; overflow: hidden;">
                            <div style="width: <?php echo $occupancyRate; ?>%; height: 100%; background: var(--lime); border-radius: 99px;"></div>
                        </div>
                    </div>

                    <div style="display: grid; gap: 1px; background: var(--border); flex: 1;">
                        @foreach ($courts->take(4) as $court)
                            @php
                                $isOccupied = $court->isOccupiedNow();
                            @endphp
                            <div style="background: var(--surface); padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                <div>
                                    <h4 style="margin: 0; font-family: var(--ui-font); font-size: 14px; font-weight: 700; color: var(--text);">{{ $court->court_name }}</h4>
                                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600;">{{ $court->court_type }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if ($isOccupied)
                                        <span class="status status-unavailable" style="font-size: 10px; padding: 2px 8px;">BUSY</span>
                                        <a href="{{ route('courts.show', $court) }}" class="btn-card" style="padding: 4px 8px; font-size: 11px; min-height: 28px;">Details</a>
                                    @else
                                        <span class="status status-available" style="font-size: 10px; padding: 2px 8px;">FREE</span>
                                        <a href="{{ route('booking.create', ['court' => $court->id]) }}" class="btn-card btn-card-primary" style="padding: 4px 8px; font-size: 11px; min-height: 28px;">Book</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="padding: 16px 20px; border-top: 1px solid var(--border); text-align: center; background: var(--surface-alt);">
                        <a href="{{ route('availability') }}" class="btn btn-outline btn-sm" style="width: 100%; font-family: var(--display-font); font-size: 14px; letter-spacing: 0.04em;">View Live Radar Schedule</a>
                    </div>
                </aside>
            </div>
        </section>

        {{-- ═══════════════════════ FEATURE BAND ═══════════════════════ --}}
        <section class="cc-band" id="events">
            <div class="site-container cc-band-grid scroll-reveal reveal-fade-up">
                <div>
                    <p class="cc-kicker">How It Works</p>
                    <h2>Built for players and court managers.</h2>
                    <p class="cc-lead">CourtConnect keeps public browsing simple while giving admins a separate workspace for courts, schedules, reservations, and payments.</p>
                </div>
                <div class="cc-feature-row">
                    <article><strong>01</strong><span>Discover available courts</span></article>
                    <article><strong>02</strong><span>Reserve a play slot</span></article>
                    <article><strong>03</strong><span>Track booking status</span></article>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ FEATURED COURTS ═══════════════════════ --}}
        <section class="cc-section" id="featured-courts">
            <div class="site-container">
                <div class="cc-section-head scroll-reveal reveal-fade-up">
                    <div>
                        <p class="cc-kicker">Featured Courts</p>
                        <h2>Spaces that feel <span class="accent">worth reserving.</span></h2>
                    </div>
                    <a href="{{ route('courts.index') }}" class="btn btn-outline cc-view-all-btn">View All Courts</a>
                </div>

                {{-- Sport type filter pills --}}
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:32px;" class="scroll-reveal reveal-fade-up stagger-1">
                    <a href="{{ route('courts.index') }}" class="cc-filter-pill {{ !request('type') ? 'is-active' : '' }}">All Sports</a>
                    <a href="{{ route('courts.index', ['type' => 'Badminton']) }}" class="cc-filter-pill {{ request('type') === 'Badminton' ? 'is-active' : '' }}">Badminton</a>
                    <a href="{{ route('courts.index', ['type' => 'Tennis']) }}" class="cc-filter-pill {{ request('type') === 'Tennis' ? 'is-active' : '' }}">Tennis</a>
                    <a href="{{ route('courts.index', ['type' => 'Basketball']) }}" class="cc-filter-pill {{ request('type') === 'Basketball' ? 'is-active' : '' }}">Basketball</a>
                    <a href="{{ route('courts.index', ['type' => 'Futsal']) }}" class="cc-filter-pill {{ request('type') === 'Futsal' ? 'is-active' : '' }}">Futsal</a>
                </div>

                <div class="cc-court-grid">
                    @foreach ($courts->take(3) as $court)
                        <article class="cc-court-card scroll-reveal reveal-fade-up stagger-{{ $loop->iteration }}">
                            <a href="{{ route('courts.show', $court) }}" class="cc-court-image">
                                <img src="{{ $court->image ?: asset('images/courtconnect-multisport-hero.webp') }}" alt="{{ $court->court_name }}">
                                @if ($court->status === 'available')
                                    <span class="sport-badge">{{ $court->court_type }}</span>
                                @else
                                    <span class="badge-fully-booked">Fully Booked</span>
                                @endif
                            </a>
                            <div class="cc-court-body">
                                <div>
                                    <h3>{{ $court->court_name }}</h3>
                                    <p style="margin:6px 0 0;color:var(--muted);font-size:12px;font-weight:500;">Up to {{ $court->capacity }} players</p>
                                </div>
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:12px;border-top:1px solid var(--border);">
                                    <div>
                                        <div class="cc-price">₱{{ number_format($court->hourly_rate, 0) }}<span>/hr</span></div>
                                    </div>
                                    <div class="cc-card-actions">
                                        <a href="{{ route('courts.show', $court) }}" class="btn-card">Details</a>
                                        @if ($court->status === 'available')
                                            <a href="{{ route('booking.create', ['court' => $court->id]) }}" class="btn-card btn-card-primary">Book Now</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ HOW IT WORKS ═══════════════════════ --}}
        <section class="cc-section cc-section-alt cc-flow-section" id="faq">
            <div class="site-container">
                <div class="cc-flow-head scroll-reveal reveal-fade-up">
                    <div>
                        <p class="cc-kicker">Process</p>
                        <h2>From open slot to game time in four steps.</h2>
                    </div>
                    <p>Skip the back-and-forth. CourtConnect keeps court discovery, availability, booking, and payment tracking in one flow.</p>
                </div>
                <div class="cc-flow-grid">
                    <article class="scroll-reveal reveal-fade-up stagger-1">
                        <span>01</span>
                        <div>
                            <h3>Browse courts</h3>
                            <p>Compare sport type, rate, capacity, and court status before choosing a space.</p>
                        </div>
                    </article>
                    <article class="scroll-reveal reveal-fade-up stagger-2">
                        <span>02</span>
                        <div>
                            <h3>Pick your slot</h3>
                            <p>Select date, start time, end time, and player count from the booking form.</p>
                        </div>
                    </article>
                    <article class="scroll-reveal reveal-fade-up stagger-3">
                        <span>03</span>
                        <div>
                            <h3>Confirm details</h3>
                            <p>Submit the reservation and wait for approval from the facility team.</p>
                        </div>
                    </article>
                    <article class="is-featured scroll-reveal reveal-fade-up stagger-4">
                        <span>04</span>
                        <div>
                            <h3>Show up and play</h3>
                            <p>Keep your booking record handy and arrive ready for your scheduled session.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ TESTIMONIALS ═══════════════════════ --}}
        @if ($testimonials->isNotEmpty())
            <section class="cc-section" style="overflow: hidden;">
                <div class="site-container scroll-reveal reveal-fade-up">
                    <div class="cc-section-head" style="align-items: center; margin-bottom: 32px;">
                        <div>
                            <p class="cc-kicker">Player Feedback</p>
                            <h2>What regulars say.</h2>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" id="prev-testimonial" class="btn btn-outline" style="min-height: 40px; width: 40px; border-radius: 50%; padding: 0; display: grid; place-items: center;" aria-label="Previous testimonial">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button type="button" id="next-testimonial" class="btn btn-outline" style="min-height: 40px; width: 40px; border-radius: 50%; padding: 0; display: grid; place-items: center;" aria-label="Next testimonial">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        </div>
                    </div>

                    <div style="position: relative; overflow: hidden; width: 100%;">
                        <div id="testimonial-track" style="display: flex; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); gap: 20px;">
                            @foreach ($testimonials as $testimonial)
                                <figure class="review-card" style="flex: 0 0 100%; max-width: 100%; margin: 0; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; gap: 16px;">
                                    {{-- Star rating --}}
                                    <div style="display: flex; gap: 4px; margin-bottom: 4px;">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="{{ $s <= ($testimonial->rating ?? 5) ? 'var(--lime)' : 'none' }}" stroke="{{ $s <= ($testimonial->rating ?? 5) ? 'var(--lime)' : 'var(--border)' }}" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        @endfor
                                    </div>
                                    <p style="flex: 1; margin: 0; font-size: 14px; line-height: 1.7; color: var(--text);">&ldquo;{{ $testimonial->comment }}&rdquo;</p>
                                    {{-- Author row --}}
                                    <div style="display: flex; align-items: center; gap: 12px; padding-top: 14px; border-top: 1px solid var(--border);">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--lime-dim); border: 1px solid var(--lime); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="font-family: var(--display-font); font-size: 16px; color: var(--lime); line-height: 1;">{{ strtoupper(substr($testimonial->name, 0, 1)) }}</span>
                                        </div>
                                        <figcaption style="font-size: 13px; font-weight: 700; color: var(--text); font-family: var(--ui-font); margin: 0;">{{ $testimonial->name }}</figcaption>
                                    </div>
                                </figure>
                            @endforeach
                        </div>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;" id="testimonial-dots">
                        @foreach ($testimonials as $index => $t)
                            <button type="button" class="testimonial-dot" data-index="{{ $index }}" style="width: 8px; height: 8px; border-radius: 50%; border: none; background: var(--border); cursor: pointer; padding: 0; transition: background var(--transition), transform var(--transition);" aria-label="Go to testimonial slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </section>

            <script>
            (function() {
                var track = document.getElementById('testimonial-track');
                var prevBtn = document.getElementById('prev-testimonial');
                var nextBtn = document.getElementById('next-testimonial');
                var dotsContainer = document.getElementById('testimonial-dots');
                var slides = track ? track.children : [];
                if (!track || slides.length === 0) return;

                var currentIndex = 0;
                var slideWidth = 0;

                function renderDots(maxIndex) {
                    if (!dotsContainer) return;
                    if (maxIndex === 0) {
                        dotsContainer.style.display = 'none';
                        return;
                    }
                    dotsContainer.style.display = 'flex';
                    var html = '';
                    for (var i = 0; i <= maxIndex; i++) {
                        var active = i === currentIndex;
                        var bg = active ? 'var(--lime)' : 'var(--border)';
                        var transform = active ? 'scale(1.2)' : 'none';
                        html += '<button type="button" class="testimonial-dot" data-index="' + i + '" style="width: 8px; height: 8px; border-radius: 50%; border: none; background: ' + bg + '; cursor: pointer; padding: 0; transition: background var(--transition), transform var(--transition); transform: ' + transform + ';" aria-label="Go to testimonial slide ' + (i + 1) + '"></button>';
                    }
                    dotsContainer.innerHTML = html;
                }

                function updateSlide(index) {
                    var showCount = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 768 ? 2 : 1);
                    var maxIndex = Math.max(0, slides.length - showCount);
                    if (index > maxIndex) index = maxIndex;
                    if (index < 0) index = 0;

                    currentIndex = index;
                    
                    var gap = 20;
                    var offset = index * (slideWidth + gap);
                    track.style.transform = 'translateX(-' + offset + 'px)';

                    var dots = dotsContainer.querySelectorAll('.testimonial-dot');
                    dots.forEach(function(dot, i) {
                        if (i === index) {
                            dot.style.background = 'var(--lime)';
                            dot.style.transform = 'scale(1.2)';
                        } else {
                            dot.style.background = 'var(--border)';
                            dot.style.transform = 'none';
                        }
                    });
                }

                function resize() {
                    var containerWidth = track.parentElement.getBoundingClientRect().width;
                    var gap = 20;
                    var showCount = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 768 ? 2 : 1);
                    
                    slideWidth = (containerWidth - (gap * (showCount - 1))) / showCount;
                    
                    for (var i = 0; i < slides.length; i++) {
                        slides[i].style.flex = '0 0 ' + slideWidth + 'px';
                        slides[i].style.maxWidth = slideWidth + 'px';
                    }
                    
                    var maxIndex = Math.max(0, slides.length - showCount);
                    if (currentIndex > maxIndex) {
                        currentIndex = maxIndex;
                    }

                    if (maxIndex === 0) {
                        if (prevBtn) prevBtn.style.display = 'none';
                        if (nextBtn) nextBtn.style.display = 'none';
                    } else {
                        if (prevBtn) prevBtn.style.display = 'grid';
                        if (nextBtn) nextBtn.style.display = 'grid';
                    }
                    
                    renderDots(maxIndex);
                    updateSlide(currentIndex);
                }

                window.addEventListener('resize', resize);
                setTimeout(resize, 100);

                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        var showCount = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 768 ? 2 : 1);
                        var index = currentIndex - 1;
                        if (index < 0) {
                            index = Math.max(0, slides.length - showCount);
                        }
                        updateSlide(index);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        var showCount = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 768 ? 2 : 1);
                        var maxIndex = Math.max(0, slides.length - showCount);
                        var index = currentIndex + 1;
                        if (index > maxIndex) {
                            index = 0;
                        }
                        updateSlide(index);
                    });
                }

                if (dotsContainer) {
                    dotsContainer.addEventListener('click', function(e) {
                        var dot = e.target.closest('.testimonial-dot');
                        if (!dot) return;
                        var index = parseInt(dot.getAttribute('data-index'), 10);
                        updateSlide(index);
                    });
                }
            })();
            </script>
        @endif

        {{-- ═══════════════════════ FAQS ═══════════════════════ --}}
        <section class="cc-section cc-section-alt" id="faq-section" style="border-top: 1px solid var(--border);">
            <div class="site-container scroll-reveal reveal-fade-up">
                <style>
                    .cc-faq-grid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 40px;
                    }
                    @media (min-width: 1024px) {
                        .cc-faq-grid {
                            grid-template-columns: 350px 1fr;
                            gap: 80px;
                        }
                    }
                    .cc-faq-item {
                        border-bottom: 1px solid var(--border);
                        padding-bottom: 24px;
                        margin-bottom: 24px;
                    }
                    .cc-faq-item:last-child {
                        border-bottom: none;
                        padding-bottom: 0;
                        margin-bottom: 0;
                    }
                    .cc-faq-question {
                        width: 100%;
                        background: none;
                        border: none;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        text-align: left;
                        color: var(--text);
                        font-family: var(--ui-font);
                        font-size: 17px;
                        font-weight: 700;
                        cursor: pointer;
                        padding: 0;
                        transition: color var(--transition);
                    }
                    .cc-faq-question:hover {
                        color: var(--lime);
                    }
                    .cc-faq-answer {
                        max-height: 0;
                        overflow: hidden;
                        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-top 0.3s ease;
                        color: var(--muted-mid);
                        font-family: var(--ui-font);
                        font-size: 14px;
                        line-height: 1.6;
                    }
                    .cc-faq-item.is-active .cc-faq-answer {
                        max-height: 250px;
                        margin-top: 14px;
                    }
                    .cc-faq-icon {
                        width: 20px;
                        height: 20px;
                        color: var(--muted);
                        transition: transform var(--transition), color var(--transition);
                        flex-shrink: 0;
                    }
                    .cc-faq-item.is-active .cc-faq-icon {
                        transform: rotate(45deg);
                        color: var(--lime);
                    }
                </style>
                <div class="cc-faq-grid">
                    <div>
                        <p class="cc-kicker">Help Center</p>
                        <h2 style="margin-bottom: 16px;">Frequently Asked Questions.</h2>
                        <p style="color: var(--muted-mid); font-family: var(--ui-font); font-size: 14px; line-height: 1.6;">
                            Can't find what you are looking for? Reach out to our facility support team via our <a href="{{ route('contact') }}" style="color: var(--lime); text-decoration: none; font-weight: 600;">Contact Page</a>.
                        </p>
                    </div>
                    <div>
                        <div class="cc-faq-item">
                            <button type="button" class="cc-faq-question">
                                <span>How do I reserve a court?</span>
                                <svg class="cc-faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                            <div class="cc-faq-answer">
                                <p>You can browse all available courts on our <a href="{{ route('courts.index') }}" style="color: var(--text); font-weight: 600;">Courts directory</a>, choose your preferred sport, select an available date and time slot, and submit the booking request. You can also view live real-time court occupancy using our Live Radar page.</p>
                            </div>
                        </div>
                        <div class="cc-faq-item">
                            <button type="button" class="cc-faq-question">
                                <span>What payment options do you support?</span>
                                <svg class="cc-faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                            <div class="cc-faq-answer">
                                <p>We primarily accept GCash payments. Once you request a court reservation, you will need to upload your payment receipt/screenshot via your Player Dashboard. Our team will review the receipt and approve your booking promptly.</p>
                            </div>
                        </div>
                        <div class="cc-faq-item">
                            <button type="button" class="cc-faq-question">
                                <span>How long does the booking approval take?</span>
                                <svg class="cc-faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                            <div class="cc-faq-answer">
                                <p>Our facility team typically validates submitted GCash payment receipts within 1 to 2 hours of upload. You will receive an email and system notification as soon as your reservation status changes to Approved.</p>
                            </div>
                        </div>
                        <div class="cc-faq-item">
                            <button type="button" class="cc-faq-question">
                                <span>Can I cancel or reschedule my booking?</span>
                                <svg class="cc-faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                            <div class="cc-faq-answer">
                                <p>Yes. You can cancel or request a reschedule directly from your Dashboard up to 24 hours prior to your scheduled booking time. Cancellations requested less than 24 hours before the reservation slot are non-refundable.</p>
                            </div>
                        </div>
                        <div class="cc-faq-item">
                            <button type="button" class="cc-faq-question">
                                <span>Are there player limits per court?</span>
                                <svg class="cc-faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                            <div class="cc-faq-answer">
                                <p>Yes, each court specifies a maximum player capacity for safety and play quality (e.g., 4 players for Badminton/Tennis, and 10 players for Basketball/Futsal). You can view the capacity limits on each court's details page.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var faqQuestions = document.querySelectorAll('.cc-faq-question');
            faqQuestions.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var item = btn.closest('.cc-faq-item');
                    var wasActive = item.classList.contains('is-active');
                    
                    // Close all FAQs first for clean single-accordion behavior
                    document.querySelectorAll('.cc-faq-item').forEach(function(el) {
                        el.classList.remove('is-active');
                    });
                    
                    if (!wasActive) {
                        item.classList.add('is-active');
                    }
                });
            });
            // Real-time Date and Time for Live Radar
            const liveRadarDatetime = document.getElementById('live-radar-datetime');
            if (liveRadarDatetime) {
                function updateLiveRadarDatetime() {
                    const now = new Date();
                    
                    // Options for date formatting
                    const options = { month: 'short', day: '2-digit', year: 'numeric' };
                    let datePart = now.toLocaleDateString('en-US', options).toUpperCase();
                    
                    // Time formatting
                    let hours = now.getHours();
                    let minutes = now.getMinutes();
                    let seconds = now.getSeconds();
                    let ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    seconds = seconds < 10 ? '0' + seconds : seconds;
                    let timePart = `${hours}:${minutes}:${seconds} ${ampm}`;
                    
                    liveRadarDatetime.textContent = `${datePart} • ${timePart}`;
                }
                updateLiveRadarDatetime();
                setInterval(updateLiveRadarDatetime, 1000);
            }
        });
        </script>

    </main>

    @include('partials.public-footer')
    @include('partials.welcome-modal')
</body>
</html>
