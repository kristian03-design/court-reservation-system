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
    <style>
        .cc-filter-pill {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02) !important;
            color: var(--muted) !important;
        }
        .cc-filter-pill:hover, .cc-filter-pill.is-active {
            border-color: var(--lime) !important;
            color: var(--lime) !important;
            background: var(--lime-dim) !important;
        }
        @media (max-width: 768px) {
            .availability-divider {
                display: none !important;
            }
            .availability-inline-form {
                flex-direction: column;
                align-items: stretch !important;
                gap: 16px !important;
            }
        }
    </style>
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

        <div style="background:var(--bg); min-height:calc(100vh - 240px); padding-bottom:80px; padding-top: 24px;" id="courts-page-container">
            <div class="site-container">

                {{-- Date selection and filters --}}
                <div class="scroll-reveal reveal-fade-up stagger-1" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 20px 24px; margin-bottom: 40px;">
                    <form method="GET" action="{{ route('courts.index') }}" id="courts-filter-form" class="availability-inline-form" style="display: flex; align-items: center; gap: 32px; width: 100%; flex-wrap: wrap;">
                        {{-- Search Input --}}
                        <div style="display: flex; flex-direction: column; gap: 8px; flex-shrink: 0; min-width: 240px;">
                            <label for="search-input" style="color: var(--muted); font-family: var(--ui-font); font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Search Court</label>
                            <div style="position: relative; width: 100%;">
                                <i data-lucide="search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); width: 16px; height: 16px; pointer-events: none;"></i>
                                <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Search Court Name..." style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px 10px 42px; color: #fff; font-family: var(--ui-font); font-weight: 600; outline: none; transition: border-color 0.2s; box-sizing: border-box; font-size: 13px;">
                            </div>
                        </div>

                        {{-- Vertical divider --}}
                        <div class="availability-divider" style="width: 1px; height: 50px; background: var(--border); flex-shrink: 0;"></div>

                        {{-- Sport Type filter pills --}}
                        <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 320px;">
                            <label style="color: var(--muted); font-family: var(--ui-font); font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Filter by Sport</label>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;" id="sport-pills-container">
                                <a href="{{ route('courts.index', ['search' => request('search')]) }}" class="cc-filter-pill {{ !request('type') ? 'is-active' : '' }}" style="text-decoration: none; padding: 8px 16px; border-radius: 6px; font-family: var(--ui-font); font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; height: 38px; box-sizing: border-box;">All Sports</a>
                                @php $sportTypes = ['Badminton', 'Tennis', 'Basketball', 'Futsal', 'Volleyball']; @endphp
                                @foreach ($sportTypes as $sport)
                                    <a href="{{ route('courts.index', ['search' => request('search'), 'type' => $sport]) }}" class="cc-filter-pill {{ request('type') === $sport ? 'is-active' : '' }}" style="text-decoration: none; padding: 8px 16px; border-radius: 6px; font-family: var(--ui-font); font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; height: 38px; box-sizing: border-box;">{{ $sport }}</a>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

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
    <script>
        lucide.createIcons();

        document.addEventListener('DOMContentLoaded', () => {
            const pageContainer = document.getElementById('courts-page-container');
            
            if (pageContainer) {
                const fetchCourts = (url) => {
                    pageContainer.style.opacity = '0.4';
                    pageContainer.style.pointerEvents = 'none';
                    pageContainer.style.transition = 'opacity 0.15s ease';
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('courts-page-container');
                        if (newContent) {
                            pageContainer.innerHTML = newContent.innerHTML;
                            
                            // Instantly force reveal of dynamic layout elements on page change
                            pageContainer.querySelectorAll('.scroll-reveal').forEach(el => {
                                el.classList.add('is-visible');
                            });

                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        }
                        pageContainer.style.opacity = '1';
                        pageContainer.style.pointerEvents = 'auto';
                        window.history.pushState({}, '', url);
                    })
                    .catch(err => {
                        console.error(err);
                        pageContainer.style.opacity = '1';
                        pageContainer.style.pointerEvents = 'auto';
                    });
                };

                // Search input typing with debounce auto-submit
                let debounceTimeout;
                document.addEventListener('input', (e) => {
                    if (e.target && e.target.id === 'search-input') {
                        clearTimeout(debounceTimeout);
                        debounceTimeout = setTimeout(() => {
                            const form = document.getElementById('courts-filter-form');
                            if (form) {
                                const formData = new FormData(form);
                                const cleanParams = new URLSearchParams();
                                
                                // Get search query
                                const searchQuery = e.target.value;
                                if (searchQuery) cleanParams.append('search', searchQuery);
                                
                                // Get active pill type parameter if any
                                const activePill = document.querySelector('.cc-filter-pill.is-active');
                                if (activePill) {
                                    const pillUrl = new URL(activePill.href);
                                    const type = pillUrl.searchParams.get('type');
                                    if (type) cleanParams.append('type', type);
                                }
                                
                                const paramsStr = cleanParams.toString();
                                const baseUrl = form.action.split('?')[0];
                                const url = paramsStr ? `${baseUrl}?${paramsStr}` : baseUrl;
                                fetchCourts(url);
                            }
                        }, 300);
                    }
                });

                // Filter pills click delegation
                document.addEventListener('click', (e) => {
                    const pill = e.target.closest('#sport-pills-container .cc-filter-pill');
                    if (pill) {
                        e.preventDefault();
                        fetchCourts(pill.href);
                    }
                    
                    const clearBtn = e.target.closest('.cc-empty-state a');
                    if (clearBtn && clearBtn.href && clearBtn.href.includes('courts')) {
                        e.preventDefault();
                        fetchCourts(clearBtn.href);
                    }
                    
                    const paginationLink = e.target.closest('#courts-page-container .pagination a, #courts-page-container nav a');
                    if (paginationLink) {
                        e.preventDefault();
                        fetchCourts(paginationLink.href);
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
