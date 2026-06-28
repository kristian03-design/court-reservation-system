<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner auth-page">
    @include('partials.public-header')
    <main>
        @include('partials.toast')
        <section class="auth-shell site-container">
        <div class="auth-copy">
            <p class="cc-kicker">Welcome back</p>
            <h1>Sign in to manage your reservations</h1>
            <p>Check upcoming bookings, upload payment proof, and keep your play schedule organized.</p>
            <div class="auth-pills">
                <span>Live schedules</span>
                <span>Payment tracking</span>
                <span>Court history</span>
            </div>
        </div>
        <section class="auth-card">
            <div class="auth-card-head">
                <p>Customer access</p>
                <h2>Login</h2>
            </div>
            <form class="auth-form" method="POST" action="{{ route('login') }}">
                @csrf
                <label><span class="auth-label-text"><i data-lucide="mail" aria-hidden="true"></i>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. john@example.com" required autofocus>
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
                <button type="submit" class="btn btn-primary"><i data-lucide="log-in" class="mr-2" aria-hidden="true"></i>Login</button>
            </form>
            <a class="auth-switch" href="{{ route('register') }}"><i data-lucide="user-plus" class="mr-2" aria-hidden="true"></i>Register a new account</a>
        </section>
    </section>
    </main>
    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
