<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registration Successful | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); min-height: calc(100vh - 120px); padding-top: 100px; padding-bottom: 120px;">
        <section class="site-container" style="max-width: 580px; margin-inline: auto; padding: 0 20px; text-align: center;">
            
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 48px 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                
                {{-- Success Icon --}}
                <div style="width: 72px; height: 72px; background: rgba(191,255,0,0.1); color: var(--lime); display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 auto 24px; font-size: 36px; border: 1px solid rgba(191,255,0,0.2);">
                    <i data-lucide="check-circle" style="width:36px; height:36px;"></i>
                </div>

                {{-- Status Message --}}
                <h1 style="font-family: var(--display-font); font-size: 40px; text-transform: uppercase; color: #fff; margin: 0 0 12px; line-height: 1;">
                    Registration Submitted
                </h1>
                
                @if($registration->registration_status === 'confirmed')
                    <p style="color: var(--lime); font-weight: 600; font-size: 15px; margin: 0 0 24px;">Your spot has been successfully booked!</p>
                @elseif($registration->registration_status === 'waitlisted')
                    <p style="color: #f59e0b; font-weight: 600; font-size: 15px; margin: 0 0 24px;">You have joined the waitlist for this event.</p>
                @else
                    <p style="color: #3b82f6; font-weight: 600; font-size: 15px; margin: 0 0 24px;">Pending admin verification of your payment.</p>
                @endif

                {{-- Event Details Card --}}
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 32px; text-align: left;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--muted); font-weight: 700; margin-bottom: 8px;">Event Details</div>
                    <strong style="color: #fff; font-size: 18px; display: block; margin-bottom: 6px;">{{ $event->title }}</strong>
                    
                    <div style="display:flex; flex-direction:column; gap:8px; font-size:13px; color:var(--muted-mid); margin-top:12px; border-top:1px solid var(--border); padding-top:12px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <i data-lucide="calendar" style="width:14px; height:14px; color:var(--lime);"></i>
                            <span>{{ $event->start_date->format('M d, Y') }} @ {{ date('g:i A', strtotime($event->start_time)) }}</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <i data-lucide="map-pin" style="width:14px; height:14px; color:var(--lime);"></i>
                            <span>{{ $event->location }}</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <i data-lucide="credit-card" style="width:14px; height:14px; color:var(--lime);"></i>
                            <span>Payment Method: <strong style="color:#fff; text-transform:uppercase;">{{ str_replace('_', ' ', $registration->payment_method) }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Guidance notice --}}
                <div style="color: var(--muted); font-size: 13px; line-height: 1.5; margin-bottom: 32px;">
                    @if($registration->registration_status === 'confirmed')
                        An confirmation receipt was generated. Please bring proof of registration to the venue. Enjoy your play!
                    @elseif($registration->registration_status === 'waitlisted')
                        If an active participant cancels or fails to verify payment, waitlisted spots will be promoted automatically in order of registration.
                    @else
                        Our team will verify your GCash receipt/reference reference: **{{ $registration->reference_number }}** within 24 hours. Your registration status will update once completed.
                    @endif
                </div>

                {{-- CTA buttons --}}
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <a href="{{ route('events') }}" class="btn btn-primary" style="padding:12px; font-size:14px; font-weight:700; width:100%; border-radius:8px; text-decoration:none; text-align:center; box-sizing:border-box;">
                        Back to Events
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding:12px; font-size:14px; font-weight:700; width:100%; border-radius:8px; text-decoration:none; text-align:center; box-sizing:border-box;">
                        Go to Dashboard
                    </a>
                </div>

            </div>

        </section>
    </main>

    @include('partials.public-footer')

    <script>lucide.createIcons();</script>
</body>
</html>
