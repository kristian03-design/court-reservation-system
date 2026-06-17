@php
    $logoCandidates = ['images/courtconnect-mark.png', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.png', 'courtconnect-logo.svg', 'courtconnect-logo.png'];
    $layoutLogo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Users | CourtConnect</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/admin.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                @if ($layoutLogo)
                    <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                @else
                    <span class="brand-fallback">CC</span>
                @endif
                <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
            </a>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.courts.index') }}">Courts</a>
                <a href="{{ route('admin.reservations.index') }}">Reservations</a>
                <a href="{{ route('admin.payments.index') }}">Payments</a>
                <a href="{{ route('admin.users.index') }}">Users</a>
                <a href="{{ route('admin.reports.index') }}">Reports</a>
                <a href="{{ route('admin.settings.index') }}">Settings</a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-topbar">
                <a href="{{ route('home') }}" class="brand">
                    @if ($layoutLogo)
                        <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                    @else
                        <span class="brand-fallback">CC</span>
                    @endif
                    <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline">Logout</button>
                </form>
            </div>
            @if (session('success') || $errors->any())
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
            @endif
            <h1 class="text-3xl font-bold text-[#0F2447]">Users</h1>
    <div class="mt-6 grid gap-4">
        @foreach ($users as $user)
            <section class="card">
                <form class="grid gap-4 md:grid-cols-[1fr_160px_160px_120px]" method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div>
                        <h2 class="font-semibold text-[#0F2447]">{{ $user->name }}</h2>
                        <p class="text-sm text-[#64748B]">{{ $user->email }} · {{ $user->phone }}</p>
                    </div>
                    <select class="rounded-lg border border-[#E5E7EB] px-3 py-2" name="role">
                        @foreach (['customer', 'admin'] as $role)
                            <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <select class="rounded-lg border border-[#E5E7EB] px-3 py-2" name="status">
                        @foreach (['active', 'disabled'] as $status)
                            <option value="{{ $status }}" @selected($user->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary">Save</button>
                </form>
            </section>
        @endforeach
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
