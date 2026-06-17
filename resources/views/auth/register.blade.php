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
    <title>Register | CourtConnect</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="site-container site-nav">
            <a href="{{ route('home') }}" class="brand">
                @if ($layoutLogo)
                    <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                @else
                    <span class="brand-fallback">CC</span>
                @endif
                <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
            </a>
            <nav class="site-menu">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('courts.index') }}">Courts</a>
                <a href="{{ route('home') }}#pricing">Pricing</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
            </nav>
            <div class="site-actions">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn btn-outline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                @endauth
            </div>
        </div>
    </header>
    <main>
        @if (session('success') || $errors->any())
            <div class="site-container alert-wrap">
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
            </div>
        @endif
        <section class="mx-auto grid max-w-6xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="self-center">
            <p class="text-sm font-semibold text-[#57C271]">Create account</p>
            <h1 class="mt-3 text-4xl font-bold text-[#0F2447]">Reserve courts faster with CourtConnect</h1>
            <p class="mt-4 leading-7 text-[#64748B]">After registration, verify your email to unlock booking and payment features.</p>
        </div>
        <section class="card">
            <form class="grid gap-5" method="POST" action="{{ route('register') }}">
                @csrf
                <label class="grid gap-2 text-sm font-semibold">Full Name
                    <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="name" value="{{ old('name') }}" required>
                </label>
                <label class="grid gap-2 text-sm font-semibold">Email
                    <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="email" name="email" value="{{ old('email') }}" required>
                </label>
                <label class="grid gap-2 text-sm font-semibold">Mobile Number
                    <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" name="phone" value="{{ old('phone') }}" required>
                </label>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="grid gap-2 text-sm font-semibold">Password
                        <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="password" name="password" required>
                    </label>
                    <label class="grid gap-2 text-sm font-semibold">Confirm Password
                        <input class="rounded-lg border border-[#E5E7EB] px-4 py-3 font-normal" type="password" name="password_confirmation" required>
                    </label>
                </div>
                <button type="submit" class="btn btn-secondary w-full">Register</button>
            </form>
        </section>
    </section>
    </main>
    <footer class="site-footer">
        <div class="site-container footer-grid">
            <div>
                <a href="{{ route('home') }}" class="brand">
                    @if ($layoutLogo)
                        <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                    @else
                        <span class="brand-fallback">CC</span>
                    @endif
                    <span><span class="brand-name">CourtConnect</span><span class="brand-tagline">Reserve. Play. Connect.</span></span>
                </a>
                <p class="footer-copy">Reserve. Play. Connect. A modern reservation platform for sports facilities and recreational centers.</p>
            </div>
            <div>
                <h3 class="footer-title">Quick Links</h3>
                <div class="footer-links">
                    <a href="{{ route('courts.index') }}">Courts</a>
                    <a href="{{ route('about') }}">About</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
            </div>
            <div>
                <h3 class="footer-title">Contact</h3>
                <p class="footer-contact">hello@courtconnect.test<br>+63 900 123 4567</p>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
