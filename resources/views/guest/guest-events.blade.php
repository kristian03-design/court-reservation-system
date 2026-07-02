<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Events | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
    <style>
        .events-filter-bar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 24px;
            margin-bottom: 40px;
        }
        .events-filter-form {
            display: grid;
            grid-template-columns: 2fr 1fr auto auto;
            gap: 16px;
            align-items: center;
        }
        .events-search-wrap {
            position: relative;
            width: 100%;
        }
        .events-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            width: 16px;
            height: 16px;
            pointer-events: none;
        }
        .events-search-input {
            width: 100%;
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 14px 12px 42px !important;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }
        .events-search-input:focus {
            border-color: var(--lime);
            background: rgba(255, 255, 255, 0.05);
        }
        .events-select-wrap {
            position: relative;
            width: 100%;
        }
        .events-select-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            width: 16px;
            height: 16px;
            pointer-events: none;
        }
        .events-select {
            width: 100%;
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 36px 12px 42px !important;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            transition: all 0.2s ease;
        }
        .events-select:focus {
            border-color: var(--lime);
        }
        .events-select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            width: 14px;
            height: 14px;
            pointer-events: none;
        }
        .events-select option {
            color: #111;
            background: #fff;
        }
        @media (max-width: 768px) {
            .events-filter-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); min-height: calc(100vh - 120px); padding-top: 80px;">
        {{-- Header Hero --}}
        <section class="cc-section" style="padding-bottom: 48px;">
            <div class="site-container scroll-reveal reveal-fade-up" style="text-align: center; max-width: 900px; margin-inline: auto;">
                <p class="cc-kicker" style="justify-content: center;">Special Events & Sessions</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(48px, 9vw, 96px); text-transform: uppercase; line-height: 0.95; margin: 0 0 24px; font-weight: 400;">
                    BEYOND THE GAME. <span style="color: var(--lime);">CONNECT & GROW.</span>
                </h1>
                <p class="cc-lead" style="margin: 0 auto 36px; max-width: 620px;">
                    From coaching clinics and training camps to community social matches and club nights. CourtConnect events bring players together to network, practice, and level up.
                </p>
            </div>
        </section>

        {{-- Events Grid --}}
        <section class="cc-section cc-section-alt" style="padding-top: 64px; padding-bottom: 96px;">
            <div class="site-container">
                <div class="cc-section-head scroll-reveal reveal-fade-up">
                    <div>
                        <p class="cc-kicker">Calendar</p>
                        <h2>Upcoming events</h2>
                    </div>
                </div>

                {{-- My Registered Events (logged-in users only) --}}
                @auth
                    @if (isset($joinedEvents) && $joinedEvents->isNotEmpty())
                        <div style="margin-bottom: 40px; background: var(--surface); border: 1px solid var(--lime); border-radius: 14px; padding: 24px;" class="scroll-reveal reveal-fade-up">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(163,230,53,0.2);">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i data-lucide="calendar-check" style="width: 18px; height: 18px; color: var(--lime);"></i>
                                    <h3 style="font-family: var(--display-font); font-size: 22px; text-transform: uppercase; color: var(--lime); margin: 0; font-weight: 400; letter-spacing: 0.04em;">My Events</h3>
                                </div>
                                <span style="font-size: 12px; color: var(--muted);">{{ $joinedEvents->count() }} registered</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach ($joinedEvents as $event)
                                    @php
                                        $reg = $event->registrations()->where('user_id', auth()->id())->first();
                                        $statusClass = $reg?->registration_status === 'confirmed' ? 'approved' : ($reg?->registration_status === 'cancelled' ? 'rejected' : 'pending');
                                    @endphp
                                    <div style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                                        <div style="width: 44px; height: 44px; border-radius: 8px; background: var(--lime-dim); border: 1px solid var(--lime); display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="font-size: 8px; color: var(--lime); font-weight: 700; line-height: 1;">EVT</span>
                                            <strong style="color: var(--lime); font-size: 12px; line-height: 1.2;">{{ strtoupper(substr($event->event_type, 0, 3)) }}</strong>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                                                <span class="status status-{{ $statusClass }}" style="font-size: 10px; padding: 2px 8px;">{{ $reg?->registration_status ?? 'registered' }}</span>
                                                <span style="font-size: 11px; color: var(--muted);">{{ $event->sport }}</span>
                                            </div>
                                            <p style="margin: 0; font-size: 14px; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $event->title }}</p>
                                            <p style="margin: 2px 0 0; font-size: 11px; color: var(--muted);">{{ $event->start_date->format('M d, Y') }} &middot; {{ $event->price > 0 ? '₱'.number_format($event->price) : 'Free' }}</p>
                                        </div>
                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-sm" style="background: var(--lime); color: #000; font-weight: 700; font-size: 12px; padding: 7px 14px; flex-shrink: 0;">
                                            <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                                            Details
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endauth

                {{-- Search & Filters --}}
                <div class="events-filter-bar scroll-reveal reveal-fade-up">
                    <form method="GET" action="{{ route('events') }}" class="events-filter-form">
                        
                        {{-- Search Text --}}
                        <div class="events-search-wrap">
                            <i class="events-search-icon" data-lucide="search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Event Name..." class="events-search-input">
                        </div>
                        
                        {{-- Sport Dropdown --}}
                        <div class="events-select-wrap">
                            <i class="events-select-icon" data-lucide="filter"></i>
                            <select name="sport" class="events-select" onchange="this.form.submit()">
                                <option value="all">All Sports</option>
                                <option value="Social Play" @selected(request('sport') === 'Social Play')>Social Play</option>
                                <option value="Tennis" @selected(request('sport') === 'Tennis')>Tennis</option>
                                <option value="Basketball" @selected(request('sport') === 'Basketball')>Basketball</option>
                                <option value="Badminton" @selected(request('sport') === 'Badminton')>Badminton</option>
                                <option value="Futsal" @selected(request('sport') === 'Futsal')>Futsal</option>
                                <option value="Volleyball" @selected(request('sport') === 'Volleyball')>Volleyball</option>
                                <option value="Padel" @selected(request('sport') === 'Padel')>Padel</option>
                            </select>
                            <i class="events-select-arrow" data-lucide="chevron-down"></i>
                        </div>
                        
                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-primary" style="height: 46px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.05em; padding: 0 28px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-sizing: border-box; background: var(--lime); color: #000; border: none;">
                            <i data-lucide="search" style="width: 16px; height: 16px;"></i> Search
                        </button>
                        
                        {{-- Clear Button --}}
                        @if(request()->anyFilled(['search', 'sport']))
                            <a href="{{ route('events') }}" class="btn btn-outline" style="height: 46px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.05em; padding: 0 20px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 4px; box-sizing: border-box; color: #ef4444; border-color: rgba(239,68,68,0.2); text-decoration: none;">
                                <i data-lucide="x" style="width: 16px; height: 16px;"></i> Clear
                            </a>
                        @endif
                    </form>
                </div>

                <div id="events-list-container">
                    <div class="cc-court-grid">
                        @forelse($events as $event)
                            @php
                                $btnText = 'Register Now';
                                if ($event->event_type === 'Training Camp') {
                                    $btnText = 'Register Kid';
                                } elseif ($event->event_type === 'League') {
                                    $btnText = 'Join League';
                                } elseif ($event->price > 0) {
                                    $btnText = 'Book Slot';
                                }
                            @endphp
                            {{-- Event Card --}}
                            <article class="cc-court-card scroll-reveal reveal-fade-up stagger-{{ $loop->iteration }}" style="display: flex; flex-direction: column;">
                                <div class="cc-court-image">
                                    <img src="{{ $event->image ? asset($event->image) : asset('images/courtconnect-multisport-hero.png') }}" alt="{{ $event->title }}" style="filter: brightness(0.65) saturate(0.85); object-fit: cover; width:100%; height:200px;">
                                    <span class="sport-badge" style="background: var(--lime); color: var(--bg);">{{ $event->sport }}</span>
                                </div>
                                <div class="cc-court-body" style="gap: 12px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; flex: 1;">
                                    <div>
                                        <span class="status status-available" style="margin-bottom: 8px; display: inline-block;">{{ $event->event_type }}</span>
                                        <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); line-height: 1.1; margin: 4px 0 8px;">
                                            <a href="{{ route('events.show', $event->slug) }}" style="color: inherit; text-decoration: none;">{{ $event->title }}</a>
                                        </h3>
                                        <p style="color: var(--muted-mid); font-size: 14px; margin: 0;">
                                            {{ $event->start_date->format('M d, Y') }}
                                            @if($event->end_date && $event->end_date->gt($event->start_date))
                                                - {{ $event->end_date->format('M d, Y') }}
                                            @endif
                                        </p>
                                    </div>
                                    <p style="color: var(--muted-mid); font-size: 13px; line-height: 1.5; margin: 8px 0 16px; flex: 1;">
                                        {{ Str::limit($event->description, 140) }}
                                    </p>
                                    <div>
                                        <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 8px; display: grid; gap: 8px; font-size: 13px;">
                                            <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Entry Fee:</span><strong style="color: var(--lime);">{{ $event->price > 0 ? '₱' . number_format($event->price, 2) : 'Free' }}</strong></div>
                                            <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Availability:</span><strong style="color: var(--text);">{{ $event->max_slots - $event->registered }} / {{ $event->max_slots }} spots</strong></div>
                                        </div>
                                        <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                                            @if($event->status === 'closed' || $event->status === 'completed' || $event->status === 'cancelled')
                                                <button disabled class="btn btn-outline" style="width: 100%; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em; opacity: 0.5;">Closed</button>
                                            @elseif($event->registered >= $event->max_slots)
                                                @if($event->allow_waitlist)
                                                    <div style="display: flex; gap: 8px;">
                                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline" style="flex: 1; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em; text-decoration: none; text-align: center; display: block; line-height: 38px; box-sizing: border-box; border-color: var(--border); color: var(--text);">Details</a>
                                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline" style="flex: 1; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em; color: var(--lime); border-color: var(--lime); text-decoration: none; text-align: center; display: block; line-height: 38px; box-sizing: border-box;">Join Waitlist</a>
                                                    </div>
                                                @else
                                                    <div style="display: flex; gap: 8px;">
                                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline" style="flex: 1; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em; text-decoration: none; text-align: center; display: block; line-height: 38px; box-sizing: border-box; border-color: var(--border); color: var(--text);">Details</a>
                                                        <button disabled class="btn btn-outline" style="flex: 1; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em; opacity: 0.5;">Sold Out</button>
                                                    </div>
                                                @endif
                                            @else
                                                <div style="display: flex; gap: 8px;">
                                                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline" style="flex: 1; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em; text-decoration: none; text-align: center; display: block; line-height: 38px; box-sizing: border-box; border-color: var(--border); color: var(--text);">Details</a>
                                                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary" style="flex: 1; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em; text-decoration: none; text-align: center; display: block; line-height: 40px; box-sizing: border-box; background: var(--lime); color: #000; border: none; font-weight: 700;">{{ $btnText }}</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; color: var(--muted);">
                                <i class="ti ti-calendar-off" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.3;"></i>
                                <h4 style="color: #fff; margin-bottom: 8px;">No events scheduled</h4>
                                <p style="margin: 0; font-size: 13px;">Check back later or adjust your search filters.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($events->hasPages())
                        <div style="margin-top: 40px; display: flex; justify-content: center;">
                            {{ $events->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
    <script>
        lucide.createIcons();

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.events-filter-form');
            const container = document.getElementById('events-list-container');
            
            if (form && container) {
                const fetchEvents = (url) => {
                    container.style.opacity = '0.4';
                    container.style.pointerEvents = 'none';
                    container.style.transition = 'opacity 0.15s ease';
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('events-list-container');
                        if (newContent) {
                            container.innerHTML = newContent.innerHTML;
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        }
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                        window.history.pushState({}, '', url);
                    })
                    .catch(err => {
                        console.error(err);
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                    });
                };

                // Form submit handler
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(form);
                    // Filter out empty params
                    const cleanParams = new URLSearchParams();
                    for (const [key, val] of formData.entries()) {
                        if (val && val !== 'all') {
                            cleanParams.append(key, val);
                        }
                    }
                    const paramsStr = cleanParams.toString();
                    const baseUrl = form.action.split('?')[0];
                    const url = paramsStr ? `${baseUrl}?${paramsStr}` : baseUrl;
                    fetchEvents(url);
                });
                
                // Debounced input search
                let debounceTimeout;
                const searchInput = form.querySelector('.events-search-input');
                if (searchInput) {
                    searchInput.addEventListener('input', () => {
                        clearTimeout(debounceTimeout);
                        debounceTimeout = setTimeout(() => {
                            form.dispatchEvent(new Event('submit'));
                        }, 300);
                    });
                }

                // Dropdown auto-submit
                const selectSport = form.querySelector('.events-select');
                if (selectSport) {
                    selectSport.addEventListener('change', () => {
                        form.dispatchEvent(new Event('submit'));
                    });
                }

                // AJAX pagination links
                document.addEventListener('click', (e) => {
                    const paginationLink = e.target.closest('#events-list-container .pagination a, #events-list-container nav a');
                    if (paginationLink) {
                        e.preventDefault();
                        fetchEvents(paginationLink.href);
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
