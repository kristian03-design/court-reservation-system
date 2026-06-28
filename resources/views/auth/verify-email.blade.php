<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email | CourtConnect</title>
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
                <p class="cc-kicker">Verify email</p>
                <h1>Confirm your email to start booking courts</h1>
                <p>A verification link has been sent to your email address. Verify your account to unlock booking and payment features.</p>
                <div class="auth-pills">
                    <span>Email check</span>
                    <span>Bookings</span>
                    <span>Payments</span>
                </div>
            </div>

            <section class="auth-card">
                <div class="auth-card-head">
                    <p>Account verification</p>
                    <h2>Verify Email</h2>
                </div>
                <form class="auth-form" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i data-lucide="send" aria-hidden="true"></i>Resend Verification Link</button>
                </form>
            </section>
        </section>
    </main>
    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
