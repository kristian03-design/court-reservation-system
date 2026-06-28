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
    <title>Forgot Password | CourtConnect</title>
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
                <p class="cc-kicker">Account recovery</p>
                <h1>Reset access to your CourtConnect account</h1>
                <p>Enter your email and we will send a secure password reset link.</p>
                <div class="auth-pills">
                    <span>Email reset</span>
                    <span>Secure link</span>
                    <span>Fast return</span>
                </div>
            </div>

            <section class="auth-card">
                <div class="auth-card-head">
                    <p>Password help</p>
                    <h2>Reset Password</h2>
                </div>
                <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <label><span class="auth-label-text"><i data-lucide="mail" aria-hidden="true"></i>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </label>
                    <button type="submit" class="btn btn-primary"><i data-lucide="send" aria-hidden="true"></i>Send Reset Link</button>
                </form>
                <a class="auth-switch" href="{{ route('login') }}"><i data-lucide="log-in" aria-hidden="true"></i>Back to login</a>
            </section>
        </section>
    </main>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
