<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Terms of Service | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); padding-bottom: 120px;">
        
        {{-- ═══════════════════════ HERO ═══════════════════════ --}}
        <section style="padding: 80px 0 40px; text-align: center;">
            <div class="site-container" style="max-width: 800px; margin-inline: auto;">
                <p class="cc-kicker" style="justify-content: center;">Legal Information</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(40px, 6vw, 72px); text-transform: uppercase; line-height: 1.1; margin: 0 0 16px; font-weight: 400; letter-spacing: 0.02em;">
                    Terms of <span style="color: var(--lime);">Service</span>
                </h1>
                <p style="color: var(--muted); font-family: var(--ui-font); font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Last updated: June 28, 2026
                </p>
            </div>
        </section>

        {{-- ═══════════════════════ TERMS CONTENT ═══════════════════════ --}}
        <section class="site-container" style="max-width: 800px; margin-inline: auto;">
            <div class="legal-card">
                <div class="legal-content">
                    <p>Welcome to <strong>CourtConnect</strong>. By accessing or using our court reservation system, you agree to be bound by these Terms of Service. Please read them carefully before using our platform.</p>
                    
                    <h2>1. Acceptance of Terms</h2>
                    <p>By registering an account or placing a reservation on CourtConnect, you confirm that you have read, understood, and agreed to be bound by these Terms of Service, along with our Privacy Policy. If you do not agree to these terms, you must not use our website or services.</p>

                    <h2>2. Account Registration</h2>
                    <p>To book a court, you may be required to register for an account. You agree to provide accurate, current, and complete information during registration and to update it as necessary. You are solely responsible for safeguarding your account credentials and for all activities that occur under your account.</p>

                    <h2>3. Booking and Payment Terms</h2>
                    <ul>
                        <li><strong>Reservations:</strong> Court bookings must be placed through our online portal. Booking slots are subject to availability and capacity limits.</li>
                        <li><strong>Pricing:</strong> All rates for court rentals are displayed on the court details page and are subject to change. Rates may vary based on time slots, days, and type of sport.</li>
                        <li><strong>Payments:</strong> To secure a reservation, you must complete the payment requirements. This may include uploading transaction proofs for verification or using supported automated payment gateways. If payment is not verified, the reservation may be automatically cancelled.</li>
                    </ul>

                    <h2>4. Cancellation and Refund Policy</h2>
                    <p>We understand that schedules can change. Our cancellation and refund guidelines are as follows:</p>
                    <ul>
                        <li>Cancellations made within the permitted window (e.g., 24 hours prior to the reservation start time) are eligible for rescheduling or store credit/refund, as per facility settings.</li>
                        <li>Late cancellations or no-shows are non-refundable.</li>
                        <li>CourtConnect reserves the right to cancel bookings due to maintenance, tournament scheduling, or weather conditions. In such cases, full refunds or alternative rescheduling options will be provided.</li>
                    </ul>

                    <h2>5. Rules of the Facility and User Conduct</h2>
                    <p>All players and guests must strictly adhere to the physical facility guidelines, including but not limited to:</p>
                    <ul>
                        <li>Wearing appropriate sports attire and non-marking athletic shoes on the courts.</li>
                        <li>Respecting the maximum player limits specified for each court type.</li>
                        <li>Exiting the court promptly when your scheduled reservation time ends.</li>
                        <li>Treating facility staff, other players, and equipment with respect. Damage to property due to negligence will be billed to the booking owner.</li>
                    </ul>

                    <h2>6. Limitation of Liability</h2>
                    <p>CourtConnect acts as the booking operator. We are not liable for any personal injuries, accidents, health complications, or lost/stolen personal belongings that occur at the physical facilities before, during, or after court bookings.</p>

                    <h2>7. Amendments to Terms</h2>
                    <p>We reserve the right, at our sole discretion, to modify or replace these Terms of Service at any time. When we make updates, the "Last updated" date at the top of this page will be revised. Continued use of our platform constitutes acceptance of the new terms.</p>

                    <h2>8. Contact Information</h2>
                    <p>If you have any questions or queries regarding these Terms of Service, please contact us:</p>
                    <ul>
                        <li>Email: <strong>courtconnect2026@gmail.com</strong></li>
                        <li>Phone: <strong>+63 900 123 4567</strong></li>
                    </ul>
                </div>
            </div>
        </section>

    </main>

    @include('partials.public-footer')
</body>
</html>
