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
    <title>Admin Login | CourtConnect</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/admin.css', 'resources/js/app.js'])
</head>
<body class="admin-login-body">
    <main class="admin-login-page">
        <section class="admin-login-shell">
            <div class="admin-login-panel">
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

                <p class="admin-eyebrow">Admin Portal</p>
                <h1 class="admin-title">Manage courts, bookings, payments, and facility operations.</h1>
                <p class="admin-copy">This entrance is restricted to administrator accounts. Customer accounts should use the regular login page.</p>
            </div>

            <div class="admin-login-form-wrap">
                <h2 class="form-title">Admin Login</h2>
                <p class="form-copy">Sign in with an active administrator account. A one-time code will be sent to the admin email.</p>

                @if ($errors->any())
                    <div class="form-alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form class="admin-form" method="POST" action="{{ route('admin.login.store') }}">
                    @csrf
                    <label class="form-label">
                        Email
                        <input class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </label>

                    <label class="form-label">
                        Password
                        <input class="form-input" type="password" name="password" required>
                    </label>

                    <div class="form-row">
                        <label class="remember">
                            <input type="checkbox" name="remember">
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>

                    <button class="submit-button" type="submit">Send Admin OTP</button>
                </form>

                <a class="back-link" href="{{ route('login') }}">Use customer login</a>
            </div>
        </section>
    </main>
</body>
</html>
