<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Privacy Policy | CourtConnect</title>
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
                    Privacy <span style="color: var(--lime);">Policy</span>
                </h1>
                <p style="color: var(--muted); font-family: var(--ui-font); font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Last updated: June 28, 2026
                </p>
            </div>
        </section>

        {{-- ═══════════════════════ POLICY CONTENT ═══════════════════════ --}}
        <section class="site-container" style="max-width: 800px; margin-inline: auto;">
            <div class="legal-card">
                <div class="legal-content">
                    <p>At <strong>CourtConnect</strong>, accessible from our online booking portal, one of our main priorities is the privacy of our visitors and players. This Privacy Policy document contains types of information that is collected and recorded by CourtConnect and how we use it.</p>
                    
                    <h2>1. Information We Collect</h2>
                    <p>We collect several types of information to provide and improve our court reservation services to you:</p>
                    <ul>
                        <li><strong>Personal Identification Info:</strong> Name, email address, phone number, and account passwords.</li>
                        <li><strong>Reservation Details:</strong> Sport preferences, reservation dates, times, court selections, and player registration statuses.</li>
                        <li><strong>Billing & Payment Info:</strong> Invoice details and payment proofs (e.g., receipt uploads or transaction reference IDs). All direct credit card payments are processed securely by external PCI-compliant payment gateways.</li>
                        <li><strong>Usage Data:</strong> Information about how you interact with our website, including your IP address, browser type, page views, and timestamps.</li>
                    </ul>

                    <h2>2. How We Use Your Information</h2>
                    <p>We use the collected information in various ways, including to:</p>
                    <ul>
                        <li>Provide, operate, and maintain our booking platform.</li>
                        <li>Process, schedule, and confirm your court reservations.</li>
                        <li>Manage player billing, process transactions, and verify payment proofs.</li>
                        <li>Send automated reservation confirmations, reminders, and service notifications.</li>
                        <li>Communicate with you regarding support requests, facility updates, or security announcements.</li>
                        <li>Analyze usage trends to improve and optimize our website user experience.</li>
                    </ul>

                    <h2>3. Data Security & Storage</h2>
                    <p>We prioritize the security of your personal data. CourtConnect employs industry-standard administrative, physical, and electronic security measures designed to protect your information from unauthorized access, loss, misuse, or alteration.</p>
                    <p>However, please note that no method of transmission over the Internet or method of electronic storage is 100% secure, and we cannot guarantee its absolute security.</p>

                    <h2>4. Cookies and Sessions</h2>
                    <p>CourtConnect uses standard session cookies to keep you logged into your account and store your navigation preferences. These cookies do not store any sensitive personal identifier information and expire when you close your browser or log out.</p>

                    <h2>5. Third-Party Services</h2>
                    <p>We may share necessary data with trusted third-party providers only to fulfill operational needs, such as secure payment processors, email delivery services, or SMS notifications. These third parties are contractually obligated not to disclose or use the information for any other purpose.</p>

                    <h2>6. Contact Us</h2>
                    <p>If you have any questions or concerns regarding this Privacy Policy, please feel free to reach out to us:</p>
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
