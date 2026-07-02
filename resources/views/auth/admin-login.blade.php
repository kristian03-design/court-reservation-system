@php
    $logoCandidates = ['images/courtconnect-mark.webp', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.webp', 'courtconnect-logo.svg', 'courtconnect-logo.webp'];
    $layoutLogo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner auth-page">
    <main>
        @include('partials.toast')

        <section class="auth-shell site-container">
            <div class="auth-copy">
                <p class="cc-kicker">Admin Portal</p>
                <h1>Sign in to manage facility operations</h1>
                <p>Manage courts, bookings, payments, schedules, and reports from the administrator workspace.</p>
                <div class="auth-pills">
                    <span>Admin OTP</span>
                    <span>Bookings</span>
                    <span>Payments</span>
                </div>
            </div>

            <section class="auth-card">
                <div class="auth-card-head">
                    <p>Administrator access</p>
                    <h2>Admin Login</h2>
                </div>
                <form class="auth-form" method="POST" action="{{ route('admin.login.store') }}">
                    @csrf
                    <label><span class="auth-label-text"><i data-lucide="mail" aria-hidden="true"></i>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. admin@example.com" required autofocus>
                    </label>
                    <label><span class="auth-label-text"><i data-lucide="lock-keyhole" aria-hidden="true"></i>Password</span>
                        <div class="password-input-wrap">
                            <input type="password" name="password" id="password" placeholder="••••••••" required>
                            <button type="button" class="toggle-password" data-target="password" aria-label="Toggle password visibility">
                                <i data-lucide="eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </label>
                    <div class="auth-form-row">
                        <label class="auth-check"><input type="checkbox" name="remember"> Remember me</label>
                        <a href="{{ route('password.request') }}"><i data-lucide="key-round" class="mr-2" aria-hidden="true"></i>Forgot password?</a>
                    </div>
                    <button class="btn btn-primary" type="submit"><i data-lucide="send" class="mr-2" aria-hidden="true"></i>Send Admin OTP</button>
                </form>
            </section>
        </section>
    </main>

    @stack('scripts')
</body>
</html>
