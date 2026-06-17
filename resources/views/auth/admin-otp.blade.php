@php
    $logoCandidates = ['images/courtconnect-mark.png', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.png', 'courtconnect-logo.svg', 'courtconnect-logo.png'];
    $logo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin OTP | CourtConnect</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/admin.css', 'resources/js/app.js'])
</head>
<body class="admin-otp-body">
    <main class="otp-card">
        <a href="{{ route('home') }}" class="brand">
            @if ($logo)
                <span class="brand-mark">
                    <img src="{{ asset($logo) }}" alt="CourtConnect logo">
                </span>
            @else
                <span class="brand-fallback">CC</span>
            @endif
            <span>
                <span class="brand-name">CourtConnect</span>
                <span class="brand-tagline">Reserve. Play. Connect.</span>
            </span>
        </a>

        <h1 class="otp-title">Enter admin OTP</h1>
        <p class="otp-copy">We sent a 6-digit verification code to <strong>{{ $email }}</strong>. The code expires in 10 minutes.</p>

        @if (session('success'))
            <div class="notice notice-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="notice notice-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form class="otp-form" method="POST" action="{{ route('admin.otp.verify') }}">
            @csrf
            <input class="otp-input" inputmode="numeric" autocomplete="one-time-code" maxlength="6" name="otp" placeholder="000000" required autofocus>
            <button class="submit-button" type="submit">Verify OTP</button>
        </form>

        <a class="back-link" href="{{ route('admin.login') }}">Back to admin login</a>
    </main>
</body>
</html>
