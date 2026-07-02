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
    <title>Admin OTP | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner auth-page">
    <main>
        @include('partials.toast')

        <section class="auth-shell site-container">
            <div class="auth-copy">
                <p class="cc-kicker">Admin verification</p>
                <h1>Enter your one-time admin code</h1>
                <p>We sent a 6-digit verification code to <strong>{{ $email }}</strong>. The code expires in 10 minutes.</p>
                <div class="auth-pills">
                    <span>One-time code</span>
                    <span>Admin access</span>
                    <span>10 minutes</span>
                </div>
            </div>

            <section class="auth-card">
                <div class="auth-card-head">
                    <p>Security check</p>
                    <h2>Admin OTP</h2>
                </div>
                <form class="auth-form" method="POST" action="{{ route('admin.otp.verify') }}">
                    @csrf
                    <label><span class="auth-label-text"><i data-lucide="shield-check" aria-hidden="true"></i>Verification Code</span>
                        <input class="auth-otp-input" inputmode="numeric" autocomplete="one-time-code" maxlength="6" name="otp" placeholder="000000" required autofocus>
                    </label>
                    <button class="btn btn-primary" type="submit"><i data-lucide="shield-check" aria-hidden="true"></i>Verify OTP</button>
                </form>
                <a class="auth-switch" href="{{ route('admin.login') }}"><i data-lucide="log-in" aria-hidden="true"></i>Back to admin login</a>
            </section>
        </section>
    </main>
    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
