<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Events | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); min-height: calc(100vh - 120px);">
        {{-- Header Hero --}}
        <section class="cc-section" style="padding-bottom: 48px;">
            <div class="site-container scroll-reveal reveal-fade-up" style="text-align: center; max-width: 900px; margin-inline: auto;">
                <p class="cc-kicker" style="justify-content: center;">Special Events & Sessions</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(48px, 9vw, 96px); text-transform: uppercase; line-height: 0.95; margin: 0 0 24px; font-weight: 400;">
                    BEYOND THE GAME. <span style="color: var(--lime);">CONNECT & GROW.</span>
                </h1>
                <p class="cc-lead" style="margin: 0 auto 36px; max-width: 620px;">
                    From coaching clinics and training camps to community social matches and club nights. CourtConnect events bring players together to network, practice, and level up.
                </p>
            </div>
        </section>

        {{-- Events Grid --}}
        <section class="cc-section cc-section-alt" style="padding-top: 64px; padding-bottom: 96px;">
            <div class="site-container">
                <div class="cc-section-head scroll-reveal reveal-fade-up">
                    <div>
                        <p class="cc-kicker">Calendar</p>
                        <h2>Upcoming events</h2>
                    </div>
                </div>

                <div class="cc-court-grid">
                    {{-- Event 1 --}}
                    <article class="cc-court-card scroll-reveal reveal-fade-up stagger-1">
                        <div class="cc-court-image">
                            <img src="{{ asset('images/courtconnect-multisport-hero.png') }}" alt="Weekend Social Play" style="filter: brightness(0.65) saturate(0.85);">
                            <span class="sport-badge" style="background: var(--lime); color: var(--bg);">Social Play</span>
                        </div>
                        <div class="cc-court-body" style="gap: 12px; padding: 24px;">
                            <div>
                                <span class="status status-available" style="margin-bottom: 8px;">Weekly Session</span>
                                <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); line-height: 1.1; margin: 4px 0 8px;">Weekend Social Mixer</h3>
                                <p style="color: var(--muted-mid); font-size: 14px; margin: 0;">Every Saturday, 4:00 PM - 8:00 PM</p>
                            </div>
                            <p style="color: var(--muted-mid); font-size: 13px; line-height: 1.5; margin: 0;">
                                Show up solo or with friends. We organize casual matches and match you with players of similar skill levels. Perfect for netplay, scrimmage, and networking.
                            </p>
                            <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 8px; display: grid; gap: 8px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Entry Fee:</span><strong style="color: var(--lime);">₱150 / Player</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Includes:</span><strong style="color: var(--text);">Court Fee & Hydration</strong></div>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                                <a href="{{ route('contact') }}?subject=Weekend%20Social%20Mixer%20Inquiry" class="btn btn-primary" style="width: 100%; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em;">Register Now</a>
                            </div>
                        </div>
                    </article>

                    {{-- Event 2 --}}
                    <article class="cc-court-card scroll-reveal reveal-fade-up stagger-2">
                        <div class="cc-court-image">
                            <img src="{{ asset('images/courtconnect-multisport-hero.png') }}" alt="Tennis Coaching Clinic" style="filter: brightness(0.65) saturate(0.85);">
                            <span class="sport-badge" style="background: var(--lime); color: var(--bg);">Tennis</span>
                        </div>
                        <div class="cc-court-body" style="gap: 12px; padding: 24px;">
                            <div>
                                <span class="status status-available" style="margin-bottom: 8px;">Skills Clinic</span>
                                <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); line-height: 1.1; margin: 4px 0 8px;">Doubles Serve & Volley</h3>
                                <p style="color: var(--muted-mid); font-size: 14px; margin: 0;">August 25, 2026, 8:00 AM - 11:00 AM</p>
                            </div>
                            <p style="color: var(--muted-mid); font-size: 13px; line-height: 1.5; margin: 0;">
                                Master positioning, defensive returns, and high-impact volleys. Guided by Coach Anna (former national team member). Open to intermediate players.
                            </p>
                            <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 8px; display: grid; gap: 8px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Coached By:</span><strong style="color: var(--text);">Coach Anna Ramos</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Entry Fee:</span><strong style="color: var(--lime);">₱500 / Slot</strong></div>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                                <a href="{{ route('contact') }}?subject=Doubles%20Serve%20and%20Volley%20Clinic" class="btn btn-primary" style="width: 100%; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em;">Book Slot</a>
                            </div>
                        </div>
                    </article>

                    {{-- Event 3 --}}
                    <article class="cc-court-card scroll-reveal reveal-fade-up stagger-3">
                        <div class="cc-court-image">
                            <img src="{{ asset('images/courtconnect-multisport-hero.png') }}" alt="Kids Basketball Camp" style="filter: brightness(0.65) saturate(0.85);">
                            <span class="sport-badge" style="background: var(--lime); color: var(--bg);">Basketball</span>
                        </div>
                        <div class="cc-court-body" style="gap: 12px; padding: 24px;">
                            <div>
                                <span class="status status-available" style="margin-bottom: 8px;">Training Camp</span>
                                <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); line-height: 1.1; margin: 4px 0 8px;">Youth Basketball Camp</h3>
                                <p style="color: var(--muted-mid); font-size: 14px; margin: 0;">Sept 1 - Oct 3, 2026 (10 Sessions)</p>
                            </div>
                            <p style="color: var(--muted-mid); font-size: 13px; line-height: 1.5; margin: 0;">
                                Comprehensive developmental program for ages 8-15. Focuses on shooting mechanics, dribbling foundation, team plays, and athletic coordination.
                            </p>
                            <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 8px; display: grid; gap: 8px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Sessions:</span><strong style="color: var(--text);">Every Tue & Thu (5-7 PM)</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Fee:</span><strong style="color: var(--lime);">₱2,500 (Includes Jersey)</strong></div>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                                <a href="{{ route('contact') }}?subject=Youth%20Basketball%20Camp%20Registration" class="btn btn-primary" style="width: 100%; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em;">Register Kid</a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
