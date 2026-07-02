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
    <title>Reset Password | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner auth-page">
    @include('partials.public-header')
    <main>
        @include('partials.toast')

        <section class="auth-shell site-container">
            <div class="auth-copy">
                <p class="cc-kicker">New password</p>
                <h1>Create a fresh password for your account</h1>
                <p>Choose a new password, then return to your reservations and court schedule.</p>
                <div class="auth-pills">
                    <span>Secure update</span>
                    <span>Account access</span>
                    <span>Booking ready</span>
                </div>
            </div>

            <section class="auth-card">
                <div class="auth-card-head">
                    <p>Password update</p>
                    <h2>Reset Password</h2>
                </div>
                <form class="auth-form" method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <label><span class="auth-label-text"><i data-lucide="mail" aria-hidden="true"></i>Email</span>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="e.g. user@example.com" required>
                    </label>
                    <label><span class="auth-label-text"><i data-lucide="lock-keyhole" aria-hidden="true"></i>Password</span>
                        <div class="password-input-wrap">
                            <input type="password" name="password" id="password" placeholder="••••••••" required minlength="8">
                            <button type="button" class="toggle-password" data-target="password" aria-label="Toggle password visibility">
                                <i data-lucide="eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </label>
                    <label><span class="auth-label-text"><i data-lucide="shield-check" aria-hidden="true"></i>Confirm Password</span>
                        <div class="password-input-wrap">
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required minlength="8">
                            <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Toggle confirm password visibility">
                                <i data-lucide="eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </label>
                    <button type="submit" class="btn btn-primary"><i data-lucide="refresh-cw" class="mr-2" aria-hidden="true"></i>Update Password</button>
                </form>
                <a class="auth-switch" href="{{ route('login') }}"><i data-lucide="log-in" class="mr-2" aria-hidden="true"></i>Back to login</a>
            </section>
        </section>
    </main>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
