@php
    $logoCandidates = ['images/courtconnect-mark.webp', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.webp', 'courtconnect-logo.svg', 'courtconnect-logo.webp'];
    $layoutLogo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
    
    $navItems = [
        ['label' => 'Club Home', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'home', 'section' => 'Overview'],
        ['label' => 'Reserve', 'href' => route('booking.create'), 'active' => request()->routeIs('booking.*'), 'icon' => 'calendar-plus', 'section' => 'Play'],
        ['label' => 'My Slots', 'href' => route('reservations.index'), 'active' => request()->routeIs('reservations.*'), 'icon' => 'clipboard-list', 'section' => 'Play'],
        ['label' => 'Tournaments', 'href' => route('tournaments.index'), 'active' => request()->routeIs('tournaments.*') || request()->routeIs('dashboard.tournaments.*'), 'icon' => 'trophy', 'section' => 'Play'],
        ['label' => 'Events', 'href' => route('events'), 'active' => request()->routeIs('events*'), 'icon' => 'calendar-days', 'section' => 'Play'],
        ['label' => 'Inbox', 'href' => route('notifications.index'), 'active' => request()->routeIs('notifications.*'), 'icon' => 'mail', 'section' => 'Account'],
        ['label' => 'Profile', 'href' => route('profile.edit'), 'active' => request()->routeIs('profile.*'), 'icon' => 'user-round', 'section' => 'Account'],
        ['label' => 'Payments', 'href' => route('payments.index'), 'active' => request()->routeIs('payments.*'), 'icon' => 'credit-card', 'section' => 'Account'],
        ['label' => 'My Feedback', 'href' => route('feedback.index'), 'active' => request()->routeIs('feedback.*'), 'icon' => 'message-circle', 'section' => 'Account'],
    ];

    $hour = now()->hour;
    if ($hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($hour < 18) {
        $greeting = 'Good Afternoon';
    } else {
        $greeting = 'Good Evening';
    }

    $unreadNotificationsCount = auth()->user()->systemNotifications()->where('is_read', false)->count();
    $recentNotifications = auth()->user()->systemNotifications()->latest()->take(5)->get();
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Player Dashboard' }} | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/user.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="public-page padele-inner">
    @include('partials.toast')

    {{-- Dashboard Skeleton Overlay --}}
    <div id="site-loader" class="site-loader-overlay" style="align-items: stretch; justify-content: stretch; flex-direction: row; gap: 0;">
        <!-- Sidebar Skeleton -->
        <aside style="width: 260px; background: #161616; border-right: 1px solid rgba(255, 255, 255, 0.05); padding: 24px; flex-direction: column; gap: 28px;" class="hidden md:flex">
            <!-- Sidebar Brand -->
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="skeleton-shimmer" style="width: 32px; height: 32px; border-radius: 8px;"></div>
                <div style="display: flex; flex-direction: column; gap: 6px; flex: 1;">
                    <div class="skeleton-shimmer" style="height: 12px; border-radius: 4px; width: 80%;"></div>
                    <div class="skeleton-shimmer" style="height: 8px; border-radius: 3px; width: 50%;"></div>
                </div>
            </div>
            <!-- Sidebar Nav Items -->
            <div style="display: flex; flex-direction: column; gap: 14px; margin-top: 16px;">
                <div class="skeleton-shimmer" style="height: 10px; width: 40%; border-radius: 3px; opacity: 0.5; margin-bottom: 4px;"></div>
                @for ($i = 0; $i < 6; $i++)
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="skeleton-shimmer" style="width: 20px; height: 20px; border-radius: 6px;"></div>
                        <div class="skeleton-shimmer" style="height: 12px; border-radius: 4px; flex: 1;"></div>
                    </div>
                @endfor
            </div>
            <!-- Sidebar Footer -->
            <div style="margin-top: auto; display: flex; align-items: center; gap: 12px; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 16px;">
                <div class="skeleton-shimmer" style="width: 32px; height: 32px; border-radius: 50%;"></div>
                <div style="display: flex; flex-direction: column; gap: 6px; flex: 1;">
                    <div class="skeleton-shimmer" style="height: 10px; border-radius: 3px; width: 70%;"></div>
                    <div class="skeleton-shimmer" style="height: 8px; border-radius: 2px; width: 45%;"></div>
                </div>
            </div>
        </aside>

        <!-- Main Content Skeleton -->
        <div style="flex: 1; display: flex; flex-direction: column; background: #0F0F0F;">
            <!-- Topbar Skeleton -->
            <header style="height: 68px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: space-between; padding-inline: 24px;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div class="skeleton-shimmer md:hidden" style="width: 24px; height: 24px; border-radius: 4px;"></div>
                    <div class="skeleton-shimmer" style="width: 140px; height: 16px; border-radius: 4px;"></div>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div class="skeleton-shimmer" style="width: 24px; height: 24px; border-radius: 50%;"></div>
                    <div class="skeleton-shimmer" style="width: 80px; height: 16px; border-radius: 4px;"></div>
                </div>
            </header>

            <!-- View Body Skeleton -->
            <div style="flex: 1; padding: 24px; display: flex; flex-direction: column; gap: 24px; overflow: hidden;">
                <!-- Greeting or Title Banner -->
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div class="skeleton-shimmer" style="height: 14px; width: 120px; border-radius: 4px;"></div>
                    <div class="skeleton-shimmer" style="height: 28px; width: 320px; border-radius: 6px;"></div>
                </div>

                <!-- Stats/Metrics Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                    @for ($i = 0; $i < 3; $i++)
                        <div style="background: #161616; border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div class="skeleton-shimmer" style="height: 10px; width: 80px; border-radius: 3px;"></div>
                                <div class="skeleton-shimmer" style="width: 20px; height: 20px; border-radius: 50%;"></div>
                            </div>
                            <div class="skeleton-shimmer" style="height: 24px; width: 140px; border-radius: 4px;"></div>
                            <div class="skeleton-shimmer" style="height: 8px; width: 100px; border-radius: 2px;"></div>
                        </div>
                    @endfor
                </div>

                <!-- Main Layout Split Grid (mimicking panels) -->
                <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 24px; flex: 1; min-height: 250px;" class="grid-cols-1 lg:grid-cols-[1.8fr_1fr]">
                    <!-- Table/List skeleton -->
                    <div style="background: #161616; border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding-bottom: 16px;">
                            <div class="skeleton-shimmer" style="height: 16px; width: 150px; border-radius: 4px;"></div>
                            <div class="skeleton-shimmer" style="height: 28px; width: 80px; border-radius: 6px;"></div>
                        </div>
                        @for ($i = 0; $i < 3; $i++)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                    <div class="skeleton-shimmer" style="width: 32px; height: 32px; border-radius: 8px;"></div>
                                    <div style="display: flex; flex-direction: column; gap: 6px; flex: 1;">
                                        <div class="skeleton-shimmer" style="height: 12px; border-radius: 4px; width: 60%;"></div>
                                        <div class="skeleton-shimmer" style="height: 8px; border-radius: 3px; width: 30%;"></div>
                                    </div>
                                </div>
                                <div class="skeleton-shimmer" style="height: 12px; width: 60px; border-radius: 4px;"></div>
                            </div>
                        @endfor
                    </div>
                    
                    <!-- Side Panel skeleton -->
                    <div style="background: #161616; border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                        <div class="skeleton-shimmer" style="height: 16px; width: 120px; border-radius: 4px; margin-bottom: 8px;"></div>
                        @for ($i = 0; $i < 3; $i++)
                            <div style="display: flex; flex-direction: column; gap: 8px; background: rgba(255, 255, 255, 0.02); border-radius: 12px; padding: 14px; border: 1px solid rgba(255, 255, 255, 0.02);">
                                <div class="skeleton-shimmer" style="height: 12px; width: 70%; border-radius: 4px;"></div>
                                <div class="skeleton-shimmer" style="height: 8px; width: 40%; border-radius: 2px;"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="user-layout" id="user-layout">
        <!-- Desktop Sidebar -->
        <aside class="user-sidebar">
            <div class="user-sidebar-head">
                <a href="{{ route('home') }}" class="brand" title="CourtConnect Member Club">
                    @if ($layoutLogo)
                        <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="Logo"></span>
                    @else
                        <span class="brand-mark">CC</span>
                    @endif
                    <div>
                        <span class="brand-name">CourtConnect</span>
                        <span class="brand-tagline">Member Club</span>
                    </div>
                </a>
            </div>

            <nav class="user-sidebar-nav" aria-label="Sidebar navigation">
                @php $lastSection = null; @endphp
                @foreach ($navItems as $item)
                    @if ($item['section'] !== $lastSection)
                        <span class="nav-section-label">{{ $item['section'] }}</span>
                        @php $lastSection = $item['section']; @endphp
                    @endif
                    <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'active' : '' }}" title="{{ $item['label'] }}">
                        <i data-lucide="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="user-sidebar-footer">
                <div class="sidebar-user-info">
                    <div class="sidebar-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-user-details">
                        <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                        <span class="sidebar-user-role">CourtConnect member</span>
                    </div>
                </div>
                <button type="button" class="sidebar-logout-btn trigger-logout-modal" title="Logout">
                    <i data-lucide="log-out"></i>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Column -->
        <div class="user-content">
            <!-- Topbar -->
            <header class="user-topbar">
                <div class="topbar-left">
                    <button type="button" class="mobile-menu-btn" id="mobile-menu-open" aria-label="Open menu">
                        <i data-lucide="menu"></i>
                    </button>
                    <button type="button" class="user-collapse-button" id="user-sidebar-toggle" aria-label="Collapse sidebar">
                        <i data-lucide="panel-left-close" id="user-sidebar-icon-close"></i>
                        <i data-lucide="panel-left-open" id="user-sidebar-icon-open" style="display:none;"></i>
                    </button>
                    <span class="user-page-title">{{ $pageTitle ?? 'Dashboard' }}</span>
                </div>

                <div class="topbar-actions">
                    <!-- Notifications Trigger & Popover -->
                    <div style="position: relative;">
                        <button type="button" class="notifications-trigger" id="notifications-trigger" aria-label="Notifications">
                            <i data-lucide="mail"></i>
                            @if ($unreadNotificationsCount > 0)
                                <span class="notifications-badge">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                            @endif
                        </button>

                        <div class="notifications-popover" id="notifications-popover">
                            <div class="notifications-popover-head">
                                <h4>Recent Notifications</h4>
                                @if ($unreadNotificationsCount > 0)
                                    <form method="POST" action="{{ route('notifications.read-all') }}">
                                        @csrf
                                        <button type="submit">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="notifications-popover-list">
                                @if ($recentNotifications->isEmpty())
                                    <div style="padding: 24px; text-align: center; color: var(--text-secondary); font-size: 13px;">
                                        No notifications yet
                                    </div>
                                @else
                                    @foreach ($recentNotifications as $notification)
                                        <div class="notification-popover-item {{ $notification->is_read ? '' : 'unread' }}">
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                                                <span class="notification-popover-title">{{ $notification->title }}</span>
                                                @if (!$notification->is_read)
                                                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                                        @csrf
                                                        <button type="submit" style="background:none; border:none; color: var(--primary); font-size: 10px; cursor: pointer; padding: 0;">Mark Read</button>
                                                    </form>
                                                @endif
                                            </div>
                                            <span class="notification-popover-desc">{{ $notification->message }}</span>
                                            <span class="notification-popover-time">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="notifications-popover-foot">
                                <a href="{{ route('notifications.index') }}" class="notif-close-on-click">View All Notifications</a>
                            </div>
                        </div>
                    </div>

                    <!-- Profile quick shortcut -->
                    <a href="{{ route('profile.edit') }}" class="user-menu-trigger">
                        <div class="user-menu-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="user-menu-name">{{ explode(' ', auth()->user()->name)[0] }}</span>
                    </a>
                </div>
            </header>

            <!-- Main view space -->
            <main class="main-shell">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div class="mobile-drawer-backdrop" id="mobile-drawer-backdrop" style="display:none;"></div>
    <aside class="mobile-drawer" id="mobile-drawer">
        <div class="user-sidebar-head" style="border-bottom: 1px solid var(--panel-border);">
            <a href="{{ route('home') }}" class="brand">
                @if ($layoutLogo)
                    <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="Logo"></span>
                @else
                    <span class="brand-mark">CC</span>
                @endif
                <div>
                    <span class="brand-name">CourtConnect</span>
                    <span class="brand-tagline">Member Club</span>
                </div>
            </a>
            <button type="button" class="sidebar-toggle-btn" id="mobile-drawer-close">
                <i data-lucide="x"></i>
            </button>
        </div>
        <nav class="user-sidebar-nav" style="padding-top: 16px;">
            @php $lastSection = null; @endphp
            @foreach ($navItems as $item)
                @if ($item['section'] !== $lastSection)
                    <span class="nav-section-label">{{ $item['section'] }}</span>
                    @php $lastSection = $item['section']; @endphp
                @endif
                <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'active' : '' }} drawer-close-link" title="{{ $item['label'] }}">
                    <i data-lucide="{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
        <div class="user-sidebar-footer" style="margin-top: auto;">
            <button type="button" class="sidebar-logout-btn trigger-logout-modal" title="Logout">
                <i data-lucide="log-out"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="mobile-bottom-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Home">
            <i data-lucide="home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.*') ? 'active' : '' }}" title="Book">
            <i data-lucide="calendar-plus"></i>
            <span>Book</span>
        </a>
        <a href="{{ route('reservations.index') }}" class="{{ request()->routeIs('reservations.*') ? 'active' : '' }}" title="Reservations">
            <i data-lucide="clipboard-list"></i>
            <span>Reservations</span>
        </a>
        <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}" title="Inbox">
            <i data-lucide="mail"></i>
            <span>Inbox</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profile">
            <i data-lucide="user-round"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div class="custom-modal-backdrop" id="logout-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="custom-modal" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); width: 100%; max-width: 380px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); position: relative; margin: 16px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255, 92, 58, 0.1); border: 1px solid rgba(255, 92, 58, 0.25); color: var(--coral); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i data-lucide="log-out" style="width: 24px; height: 24px; color: var(--coral);"></i>
            </div>
            <h2 style="font-family: var(--font-display); font-size: 24px; font-weight: 800; text-transform: uppercase; margin: 0 0 8px; letter-spacing: 0.04em; color: var(--text);">Confirm Logout</h2>
            <p style="color: var(--muted-mid); font-size: 13px; margin: 0 0 24px; line-height: 1.5;">Are you sure you want to log out of your session?</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button type="button" class="btn btn-outline" id="close-logout-modal" style="width: 100%; min-height: 40px;">Cancel</button>
                <form method="POST" action="{{ route('logout') }}" style="display: contents;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width: 100%; min-height: 40px; background: var(--coral); border-color: var(--coral); color: white;">Logout</button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
<script>
(function () {
    var layout             = document.getElementById('user-layout');
    var sidebarToggle      = document.getElementById('sidebar-toggle-btn');
    var sidebarIconLeft    = document.getElementById('sidebar-icon-left');
    var sidebarIconRight   = document.getElementById('sidebar-icon-right');
    var mobileOpen         = document.getElementById('mobile-menu-open');
    var mobileClose        = document.getElementById('mobile-drawer-close');
    var mobileBackdrop     = document.getElementById('mobile-drawer-backdrop');
    var mobileDrawer       = document.getElementById('mobile-drawer');
    var notifTrigger       = document.getElementById('notifications-trigger');
    var notifPopover       = document.getElementById('notifications-popover');

    // ── Sidebar collapse (desktop) ─────────────────────
    var userSidebarToggle  = document.getElementById('user-sidebar-toggle');
    var userIconClose      = document.getElementById('user-sidebar-icon-close');
    var userIconOpen       = document.getElementById('user-sidebar-icon-open');

    if (userSidebarToggle) {
        userSidebarToggle.addEventListener('click', function () {
            var collapsed = layout.classList.toggle('collapsed');
            if (userIconClose) userIconClose.style.display = collapsed ? 'none' : '';
            if (userIconOpen)  userIconOpen.style.display  = collapsed ? '' : 'none';
            if (window.renderLucideIcons) window.renderLucideIcons();
        });
    }

    // ── Mobile drawer ───────────────────────────────
    function openMobileDrawer() {
        mobileBackdrop.style.display = '';
        mobileDrawer.classList.add('active');
    }
    function closeMobileDrawer() {
        mobileBackdrop.style.display = 'none';
        mobileDrawer.classList.remove('active');
    }
    if (mobileOpen)     mobileOpen.addEventListener('click', openMobileDrawer);
    if (mobileClose)    mobileClose.addEventListener('click', closeMobileDrawer);
    if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeMobileDrawer);
    document.querySelectorAll('.drawer-close-link').forEach(function (link) {
        link.addEventListener('click', closeMobileDrawer);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMobileDrawer();
    });

    // ── Notifications popover ────────────────────────
    if (notifTrigger && notifPopover) {
        notifTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            notifPopover.classList.toggle('active');
        });
        document.querySelectorAll('.notif-close-on-click').forEach(function (el) {
            el.addEventListener('click', function () {
                notifPopover.classList.remove('active');
            });
        });
        document.addEventListener('click', function (e) {
            if (!notifTrigger.contains(e.target) && !notifPopover.contains(e.target)) {
                notifPopover.classList.remove('active');
            }
        });
    }

    // ── Logout Modal ─────────────────────────────────
    const logoutModalBackdrop = document.getElementById('logout-modal-backdrop');
    const closeLogoutModal = document.getElementById('close-logout-modal');
    const triggerButtons = document.querySelectorAll('.trigger-logout-modal');

    if (logoutModalBackdrop) {
        triggerButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                logoutModalBackdrop.style.display = 'flex';
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        });

        if (closeLogoutModal) {
            closeLogoutModal.addEventListener('click', () => {
                logoutModalBackdrop.style.display = 'none';
            });
        }

        logoutModalBackdrop.addEventListener('click', (e) => {
            if (e.target === logoutModalBackdrop) {
                logoutModalBackdrop.style.display = 'none';
            }
        });
    }
})();
</script>
</body>
</html>
