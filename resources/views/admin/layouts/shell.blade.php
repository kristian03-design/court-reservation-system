@php
    $logoCandidates = [
        'images/courtconnect-mark.webp',
        'images/courtconnect-logo.svg',
        'images/courtconnect-logo.webp',
        'courtconnect-logo.svg',
        'courtconnect-logo.webp',
    ];
    $layoutLogo = collect($logoCandidates)->first(fn ($p) => file_exists(public_path($p)));

    $navItems = [
        'dashboard'    => ['label' => 'Dashboard',    'url' => route('admin.dashboard'),          'icon' => 'ti-home',             'section' => 'Overview'],
        'reservations' => ['label' => 'Reservations', 'url' => route('admin.reservations.index'), 'icon' => 'ti-calendar-event',   'section' => 'Management'],
        'courts'       => ['label' => 'Courts',       'url' => route('admin.courts.index'),       'icon' => 'ti-layout-grid',      'section' => 'Management'],
        'tournaments'  => ['label' => 'Tournaments',  'url' => route('admin.tournaments.index'),  'icon' => 'ti-trophy',           'section' => 'Management'],
        'events'       => ['label' => 'Events',       'url' => route('admin.events.index'),       'icon' => 'ti-calendar',         'section' => 'Management'],
        'payments'     => ['label' => 'Payments',     'url' => route('admin.payments.index'),     'icon' => 'ti-credit-card',      'section' => 'Management'],
        'reports'      => ['label' => 'Reports',      'url' => route('admin.reports.index'),      'icon' => 'ti-chart-bar',        'section' => 'Analytics'],
        'users'        => ['label' => 'Users',        'url' => route('admin.users.index'),        'icon' => 'ti-users',            'section' => 'System'],
        'settings'     => ['label' => 'Settings',     'url' => route('admin.settings.index'),     'icon' => 'ti-settings',         'section' => 'System'],
    ];

    $active ??= 'dashboard';
    $pageTitle ??= 'Admin';
    $lastSection = null;

    $adminUnreadCount   = \App\Models\AdminNotification::where('is_read', false)->count();
    $adminNotifications = \App\Models\AdminNotification::latest()->take(6)->get();
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.x/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/css/views/admin.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
<div class="admin-shell" id="admin-shell">

    <div class="admin-drawer-scrim" id="admin-drawer-scrim" aria-hidden="true"></div>

    <aside class="admin-sidebar">
        <div class="admin-sidebar-head">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                @if ($layoutLogo)
                    <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect"></span>
                @else
                    <span class="brand-fallback">CC</span>
                @endif
                <span class="admin-brand-copy">
                    <span class="brand-name">CourtConnect</span>
                    <span class="brand-tagline">ADMIN DASHBOARD</span>
                </span>
            </a>
            <button type="button" class="admin-drawer-close" id="admin-drawer-close" aria-label="Close admin menu">
                <i class="ti ti-x" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="admin-nav" aria-label="Admin navigation">
            @foreach ($navItems as $key => $item)
                @if ($item['section'] && $item['section'] !== $lastSection)
                    <span class="admin-nav-section">{{ $item['section'] }}</span>
                    @php $lastSection = $item['section']; @endphp
                @endif
                <a href="{{ $item['url'] }}"
                   class="admin-nav-link {{ $active === $key ? 'active' : '' }}"
                   @if ($active === $key) aria-current="page" @endif>
                    <i class="ti {{ $item['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="admin-sidebar-logout">
            <button type="button" id="open-logout-modal">
                <i class="ti ti-logout" aria-hidden="true"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-left">
                <button type="button" class="admin-menu-button" id="admin-menu-open" aria-label="Open admin menu">
                    <i class="ti ti-menu-2" aria-hidden="true"></i>
                </button>
                <button type="button" class="admin-collapse-button" id="admin-sidebar-toggle" aria-label="Collapse sidebar">
                    <i class="ti ti-layout-sidebar-left-collapse" aria-hidden="true"></i>
                </button>
                <span>{{ $pageTitle }}</span>
            </div>
            <div class="admin-topbar-actions">
                <a href="{{ route('home') }}" class="admin-icon-button" aria-label="Public site">
                    <i class="ti ti-world" aria-hidden="true"></i>
                </a>

                {{-- Notification Bell --}}
                <div class="admin-notif-wrap" id="admin-notif-wrap">
                    <button type="button"
                            class="admin-icon-button admin-notif-btn"
                            id="admin-notif-toggle"
                            aria-label="Notifications">
                        <i class="ti ti-bell" aria-hidden="true"></i>
                        @if ($adminUnreadCount > 0)
                            <span class="admin-notif-badge">{{ $adminUnreadCount > 9 ? '9+' : $adminUnreadCount }}</span>
                        @endif
                    </button>

                    <div class="admin-notif-popover" id="admin-notif-popover">
                        <div class="admin-notif-head">
                            <h4>
                                Notifications
                                @if ($adminUnreadCount > 0)
                                    <span style="color:var(--admin-muted);font-weight:600;font-size:12px;">({{ $adminUnreadCount }} new)</span>
                                @endif
                            </h4>
                            @if ($adminUnreadCount > 0)
                                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                    @csrf
                                    <button type="submit">Mark all read</button>
                                </form>
                            @endif
                        </div>

                        <div class="admin-notif-list">
                            @if ($adminNotifications->isEmpty())
                                <div class="admin-notif-empty">
                                    <i class="ti ti-bell-off" style="font-size:28px;display:block;margin-bottom:6px;opacity:.4;"></i>
                                    No notifications yet
                                </div>
                            @else
                                @foreach ($adminNotifications as $notif)
                                    @php
                                        $iconMap = [
                                            'reservation' => 'ti-calendar-event',
                                            'payment'     => 'ti-credit-card',
                                            'info'        => 'ti-info-circle',
                                        ];
                                        $icon = $iconMap[$notif->type] ?? 'ti-bell';
                                    @endphp
                                    <div class="admin-notif-item {{ $notif->is_read ? '' : 'unread' }}">
                                        <div class="admin-notif-item-row">
                                            <span class="admin-notif-icon type-{{ $notif->type }}">
                                                <i class="ti {{ $icon }}"></i>
                                            </span>
                                            <span class="admin-notif-title">{{ $notif->title }}</span>
                                            @if (!$notif->is_read)
                                                <form method="POST" action="{{ route('admin.notifications.read', $notif) }}">
                                                    @csrf
                                                    <button type="submit" style="min-height:auto;border:0;background:none;color:var(--admin-blue);font-size:10px;font-weight:800;padding:0;cursor:pointer;white-space:nowrap;">Mark read</button>
                                                </form>
                                            @endif
                                        </div>
                                        <span class="admin-notif-msg">{{ $notif->message }}</span>
                                        <span class="admin-notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                                        @if ($notif->action_url)
                                            <a href="{{ $notif->action_url }}" style="color:var(--admin-blue);font-size:11px;font-weight:800;margin-top:2px;" class="admin-notif-close-on-click">View →</a>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="admin-notif-foot">
                            <a href="{{ route('admin.reservations.index') }}" class="admin-notif-close-on-click">View all reservations</a>
                        </div>
                    </div>
                </div>

                 <a href="{{ route('admin.settings.index') }}" class="admin-profile-chip">
                    <span>{{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}</span>
                    <strong>{{ auth('admin')->user()->name }}</strong>
                </a>
            </div>
        </div>

        @include('partials.toast')
        <div class="admin-content">
            @yield('content')
        </div>
    </main>

    {{-- Logout Confirmation Modal --}}
    <div class="logout-modal-backdrop" id="logout-modal-backdrop">
        <div class="logout-modal" id="logout-modal">
            <div class="logout-modal-icon">
                <span><i class="ti ti-logout"></i></span>
            </div>
            <div class="logout-modal-body">
                <h3>Sign out?</h3>
                <p>You're about to sign out of the admin panel. Any unsaved changes will be lost.</p>
            </div>
            <div class="logout-modal-actions">
                <button type="button" class="logout-cancel-btn" id="close-logout-modal">
                    Stay in
                </button>
                <form method="POST" action="{{ route('admin.logout') }}" style="display:contents;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="logout-confirm-btn">
                        Yes, sign out
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Global Delete Confirmation Modal --}}
    <div class="confirm-modal-backdrop" id="delete-confirm-modal-backdrop">
        <div class="confirm-modal" id="delete-confirm-modal">
            <div class="confirm-modal-icon">
                <span><i class="ti ti-trash"></i></span>
            </div>
            <div class="confirm-modal-body">
                <h3 id="delete-confirm-title">Are you sure?</h3>
                <p id="delete-confirm-text">This action cannot be undone. Do you want to proceed?</p>
            </div>
            <div class="confirm-modal-actions">
                <button type="button" class="confirm-cancel-btn" id="delete-confirm-cancel">
                    Cancel
                </button>
                <button type="button" class="confirm-submit-btn" id="delete-confirm-submit">
                    Yes, delete
                </button>
            </div>
        </div>
    </div>
</div>
@stack('scripts')
<script>
(function () {
    const shell        = document.getElementById('admin-shell');
    const scrim        = document.getElementById('admin-drawer-scrim');
    const drawerClose  = document.getElementById('admin-drawer-close');
    const menuOpen     = document.getElementById('admin-menu-open');
    const sidebarToggle= document.getElementById('admin-sidebar-toggle');
    const notifWrap    = document.getElementById('admin-notif-wrap');
    const notifToggle  = document.getElementById('admin-notif-toggle');
    const notifPopover = document.getElementById('admin-notif-popover');
    const logoutBackdrop = document.getElementById('logout-modal-backdrop');
    const logoutModal  = document.getElementById('logout-modal');
    const openLogout   = document.getElementById('open-logout-modal');
    const closeLogout  = document.getElementById('close-logout-modal');

    // ── Drawer ──────────────────────────────────────────────
    function openDrawer() {
        shell.classList.add('drawer-open');
        if (scrim) scrim.classList.add('visible');
    }
    function closeDrawer() {
        shell.classList.remove('drawer-open');
        if (scrim) scrim.classList.remove('visible');
    }
    if (menuOpen)    menuOpen.addEventListener('click', openDrawer);
    if (scrim)       scrim.addEventListener('click', closeDrawer);
    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
    document.querySelectorAll('.admin-nav-link').forEach(function (link) {
        link.addEventListener('click', closeDrawer);
    });

    // ── Sidebar collapse ────────────────────────────────────
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            shell.classList.toggle('is-collapsed');
        });
    }

    // ── Notification popover ────────────────────────────────
    if (notifToggle && notifPopover) {
        notifToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            notifPopover.classList.toggle('active');
        });
        document.querySelectorAll('.admin-notif-close-on-click').forEach(function (el) {
            el.addEventListener('click', function () {
                notifPopover.classList.remove('active');
            });
        });
        document.addEventListener('click', function (e) {
            if (notifWrap && !notifWrap.contains(e.target)) {
                notifPopover.classList.remove('active');
            }
        });
    }

    // ── Logout modal ─────────────────────────────────────────
    if (openLogout && logoutBackdrop) {
        openLogout.addEventListener('click', function () {
            logoutBackdrop.classList.add('active');
        });
    }
    if (closeLogout && logoutBackdrop) {
        closeLogout.addEventListener('click', function () {
            logoutBackdrop.classList.remove('active');
        });
    }
    if (logoutBackdrop) {
        logoutBackdrop.addEventListener('click', function (e) {
            if (e.target === logoutBackdrop) {
                logoutBackdrop.classList.remove('active');
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') logoutBackdrop.classList.remove('active');
        });
    }

    // ── Delete confirmation modal ────────────────────────────
    const deleteBackdrop = document.getElementById('delete-confirm-modal-backdrop');
    const deleteCancel   = document.getElementById('delete-confirm-cancel');
    const deleteSubmit   = document.getElementById('delete-confirm-submit');
    const deleteTitle    = document.getElementById('delete-confirm-title');
    const deleteText     = document.getElementById('delete-confirm-text');
    let formToSubmit     = null;

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.classList.contains('delete-form')) {
            // If we've already confirmed, let it submit
            if (form.dataset.confirmed === 'true') {
                return;
            }
            
            // Prevent default form submission
            e.preventDefault();
            formToSubmit = form;
            
            // Customize text if provided
            const confirmMsg = form.getAttribute('data-confirm') || 'Are you sure you want to delete this?';
            if (deleteText) {
                deleteText.textContent = confirmMsg;
            }
            
            const confirmTitle = form.getAttribute('data-confirm-title') || 'Confirm Delete';
            if (deleteTitle) {
                deleteTitle.textContent = confirmTitle;
            }

            // Show modal
            if (deleteBackdrop) {
                deleteBackdrop.classList.add('active');
            }
        }
    });

    if (deleteCancel && deleteBackdrop) {
        deleteCancel.addEventListener('click', function () {
            deleteBackdrop.classList.remove('active');
            formToSubmit = null;
        });
    }

    if (deleteSubmit && deleteBackdrop) {
        deleteSubmit.addEventListener('click', function () {
            if (formToSubmit) {
                formToSubmit.dataset.confirmed = 'true';
                formToSubmit.submit();
            }
            deleteBackdrop.classList.remove('active');
        });
    }

    if (deleteBackdrop) {
        deleteBackdrop.addEventListener('click', function (e) {
            if (e.target === deleteBackdrop) {
                deleteBackdrop.classList.remove('active');
                formToSubmit = null;
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                deleteBackdrop.classList.remove('active');
                formToSubmit = null;
            }
        });
    }
})();
</script>
</body>
</html>
