@php
    $logoCandidates = ['images/courtconnect-mark.webp', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.webp', 'courtconnect-logo.svg', 'courtconnect-logo.webp'];
    $layoutLogo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
    $isCustomerArea = request()->routeIs('dashboard', 'booking.*', 'reservations.*', 'payments.*', 'profile.*');
    $publicNavItems = [
        ['label' => 'Home',         'href' => route('home'),                          'active' => request()->routeIs('home'),      'icon' => 'home'],
        ['label' => 'Courts',       'href' => route('courts.index'),                  'active' => request()->routeIs('courts.*'),  'icon' => 'grid-3x3'],
        ['label' => 'Availability', 'href' => route('availability'),                  'active' => request()->routeIs('availability'),'icon' => 'calendar-days'],
        ['label' => 'Events',       'href' => route('events'),                        'active' => request()->routeIs('events'),    'icon' => 'calendar-plus'],
        ['label' => 'Tournaments',  'href' => route('tournaments'),                   'active' => request()->routeIs('tournaments'),'icon' => 'trophy'],
        ['label' => 'About',        'href' => route('about'),                         'active' => request()->routeIs('about'),     'icon' => 'info'],
        ['label' => 'Contact',      'href' => route('contact'),                       'active' => request()->routeIs('contact'),   'icon' => 'phone-call'],
        ['label' => 'FAQ',          'href' => route('home') . '#faq-section',         'active' => false,                          'icon' => 'circle-help'],
    ];
    $customerNavItems = [
        ['label' => 'Home',         'href' => route('dashboard'),                     'active' => request()->routeIs('dashboard'),       'icon' => 'home'],
        ['label' => 'Courts',       'href' => route('courts.index'),                  'active' => request()->routeIs('courts.*'),        'icon' => 'grid-3x3'],
        ['label' => 'Book',         'href' => route('booking.create'),                'active' => request()->routeIs('booking.*'),       'icon' => 'plus'],
        ['label' => 'Reservations', 'href' => route('reservations.index'),            'active' => request()->routeIs('reservations.*'),  'icon' => 'clipboard-list'],
        ['label' => 'Profile',      'href' => route('profile.edit'),                  'active' => request()->routeIs('profile.*'),       'icon' => 'user-round'],
    ];
    $desktopNavItems = $isCustomerArea ? $customerNavItems : array_slice($publicNavItems, 0, 7);
@endphp

{{-- Brand Site Loader --}}
@if(! request()->routeIs('dashboard', 'booking.*', 'reservations.*', 'payments.*', 'profile.*'))
<div id="site-loader" class="site-loader-overlay">
    <div class="site-loader-content">
        @if ($layoutLogo)
            <img class="site-loader-logo-img" src="{{ asset($layoutLogo) }}" alt="CourtConnect Logo">
        @else
            <div class="site-loader-logo">CC</div>
        @endif
        <div class="site-loader-brand-info">
            <h1 class="site-loader-brand-name">CourtConnect</h1>
            <p class="site-loader-brand-tagline">Reserve. Play. Connect.</p>
        </div>
    </div>
    <div class="site-loader-spinner"></div>
</div>
@endif

<header class="site-header">
    <div class="site-container site-nav" id="site-nav">
        <a href="{{ route('home') }}" class="brand">
            @if ($layoutLogo)
                <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
            @else
                <span class="brand-fallback">CC</span>
            @endif
            <span class="brand-text">
                <span class="brand-name">CourtConnect</span>
                <span class="brand-tagline">Reserve. Play. Connect.</span>
            </span>
        </a>

        <button type="button" class="mobile-menu-toggle md:hidden" id="mobile-menu-toggle" aria-label="Open menu">
            <i data-lucide="menu" aria-hidden="true"></i>
        </button>

        <nav class="site-menu" aria-label="Main navigation">
            @foreach ($desktopNavItems as $item)
                <a href="{{ $item['href'] }}" @class(['active' => $item['active']])>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="site-actions hidden md:flex">
            @auth
                @if($isCustomerArea)
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline btn-sm" type="submit">Logout</button>
                    </form>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="login-link">Dashboard</a>
                @else
                    <a href="{{ route('dashboard') }}" class="login-link">Dashboard</a>
                    <a href="{{ route('booking.create') }}" class="btn btn-primary btn-sm">Book a court</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="login-link">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
            @endauth
        </div>
    </div>
</header>

{{-- Mobile drawer backdrop --}}
<div class="site-drawer-backdrop" id="site-drawer-backdrop" aria-hidden="true"></div>

{{-- Mobile drawer --}}
<aside class="site-drawer" id="site-drawer" aria-label="Mobile menu">
    <div class="site-drawer-head">
        <div class="site-drawer-brand">
            @if ($layoutLogo)
                <img class="site-drawer-logo" src="{{ asset($layoutLogo) }}" alt="Logo">
            @else
                <span class="site-drawer-logo-fallback">CC</span>
            @endif
            <span class="site-drawer-title">{{ $isCustomerArea ? 'Player Menu' : 'CourtConnect' }}</span>
        </div>
        <button type="button" class="site-drawer-close" id="site-drawer-close" aria-label="Close menu">
            <i data-lucide="x" aria-hidden="true"></i>
        </button>
    </div>
    <nav class="site-drawer-list" aria-label="Mobile navigation">
        @foreach ($isCustomerArea ? $customerNavItems : $publicNavItems as $item)
            <a href="{{ $item['href'] }}" @class(['active' => $item['active']]) class="drawer-menu-link">
                <i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    <div class="site-drawer-actions">
        @auth
            @if (! auth()->user()->isAdmin())
                <a href="{{ route('booking.create') }}" class="btn btn-primary">
                    <i data-lucide="plus" aria-hidden="true"></i>
                    Book a Court
                </a>
            @endif
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn btn-outline">
                <i data-lucide="layout-dashboard" aria-hidden="true"></i>
                Dashboard
            </a>
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">
                <i data-lucide="user-plus" aria-hidden="true"></i>
                Get Started
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline">
                <i data-lucide="log-in" aria-hidden="true"></i>
                Login
            </a>
        @endauth
    </div>
</aside>

@auth
    @if ($isCustomerArea && ! auth()->user()->isAdmin())
        <nav class="customer-bottom-nav" aria-label="Customer mobile navigation">
            @foreach ($customerNavItems as $item)
                <a href="{{ $item['href'] }}" @class(['active' => $item['active'], 'is-booking' => $item['label'] === 'Book'])>
                    <i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    @endif
@endauth

<script>
(function () {
    var toggle   = document.getElementById('mobile-menu-toggle');
    var closeBtn = document.getElementById('site-drawer-close');
    var backdrop = document.getElementById('site-drawer-backdrop');
    var drawer   = document.getElementById('site-drawer');

    function openMenu() {
        backdrop.classList.add('active');
        drawer.classList.add('active');
    }
    function closeMenu() {
        backdrop.classList.remove('active');
        drawer.classList.remove('active');
    }

    if (toggle)   toggle.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (backdrop) backdrop.addEventListener('click', closeMenu);
    document.querySelectorAll('.drawer-menu-link').forEach(function (el) {
        el.addEventListener('click', closeMenu);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
})();
</script>
