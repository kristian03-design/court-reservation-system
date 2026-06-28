<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tournaments | CourtConnect</title>
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
                <p class="cc-kicker" style="justify-content: center;">Competitions & Leagues</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(48px, 9vw, 96px); text-transform: uppercase; line-height: 0.95; margin: 0 0 24px; font-weight: 400;">
                    TEST YOUR LIMITS. <span style="color: var(--lime);">CLIMB THE RANKS.</span>
                </h1>
                <p class="cc-lead" style="margin: 0 auto 36px; max-width: 620px;">
                    Join official CourtConnect tournaments, play in competitive leagues, and test your skills against local talent. From casual club tiers to high-stakes tournaments.
                </p>
            </div>
        </section>

        {{-- Tournament Grid --}}
        <section class="cc-section cc-section-alt" style="padding-top: 64px; padding-bottom: 96px;">
            <div class="site-container">
                <div class="cc-section-head scroll-reveal reveal-fade-up">
                    <div>
                        <p class="cc-kicker">Upcoming Tournaments</p>
                        <h2>Featured tournaments</h2>
                    </div>
                </div>

                <div class="cc-court-grid">
                    {{-- Tournament 1 --}}
                    <article class="cc-court-card scroll-reveal reveal-fade-up stagger-1">
                        <div class="cc-court-image">
                            <img src="{{ asset('images/courtconnect-multisport-hero.png') }}" alt="Badminton Open" style="filter: brightness(0.65) saturate(0.85);">
                            <span class="sport-badge" style="background: var(--lime); color: var(--bg);">Badminton</span>
                        </div>
                        <div class="cc-court-body" style="gap: 12px; padding: 24px;">
                            <div>
                                <span class="status status-available" style="margin-bottom: 8px;">Registration Open</span>
                                <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); line-height: 1.1; margin: 4px 0 8px;">Summer Smash Open</h3>
                                <p style="color: var(--muted-mid); font-size: 14px; margin: 0;">August 15 - 18, 2026</p>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 8px; display: grid; gap: 8px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Prize Pool:</span><strong style="color: var(--lime);">₱50,000</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Slots Filled:</span><strong style="color: var(--text);">32 / 64 Players</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Format:</span><strong style="color: var(--text);">Singles & Doubles</strong></div>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                                <a href="{{ route('contact') }}?subject=Summer%20Smash%20Open%20Registration" class="btn btn-primary" style="width: 100%; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em;">Register Now</a>
                            </div>
                        </div>
                    </article>

                    {{-- Tournament 2 --}}
                    <article class="cc-court-card scroll-reveal reveal-fade-up stagger-2">
                        <div class="cc-court-image">
                            <img src="{{ asset('images/courtconnect-multisport-hero.png') }}" alt="Futsal Champions Cup" style="filter: brightness(0.65) saturate(0.85);">
                            <span class="sport-badge" style="background: var(--lime); color: var(--bg);">Futsal</span>
                        </div>
                        <div class="cc-court-body" style="gap: 12px; padding: 24px;">
                            <div>
                                <span class="status status-available" style="margin-bottom: 8px;">Registration Open</span>
                                <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); line-height: 1.1; margin: 4px 0 8px;">Futsal Cup 2026</h3>
                                <p style="color: var(--muted-mid); font-size: 14px; margin: 0;">September 05 - 08, 2026</p>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 8px; display: grid; gap: 8px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Prize Pool:</span><strong style="color: var(--lime);">₱100,000</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Teams Filled:</span><strong style="color: var(--text);">8 / 16 Teams</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Format:</span><strong style="color: var(--text);">5v5 Knockout</strong></div>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                                <a href="{{ route('contact') }}?subject=Futsal%20Cup%202026%20Registration" class="btn btn-primary" style="width: 100%; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em;">Register Team</a>
                            </div>
                        </div>
                    </article>

                    {{-- Tournament 3 --}}
                    <article class="cc-court-card scroll-reveal reveal-fade-up stagger-3">
                        <div class="cc-court-image">
                            <img src="{{ asset('images/courtconnect-multisport-hero.png') }}" alt="HoopFest 3v3" style="filter: brightness(0.4) saturate(0.7);">
                            <span class="sport-badge" style="background: var(--lime); color: var(--bg);">Basketball</span>
                        </div>
                        <div class="cc-court-body" style="gap: 12px; padding: 24px;">
                            <div>
                                <span class="status status-pending" style="margin-bottom: 8px;">Coming Soon</span>
                                <h3 style="font-family: var(--display-font); font-size: 32px; color: var(--text); line-height: 1.1; margin: 4px 0 8px;">HoopFest 3v3</h3>
                                <p style="color: var(--muted-mid); font-size: 14px; margin: 0;">October 01 - 03, 2026</p>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 8px; display: grid; gap: 8px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Prize Pool:</span><strong style="color: var(--lime);">₱30,000</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Teams Limit:</span><strong style="color: var(--text);">24 Teams</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--muted);">Format:</span><strong style="color: var(--text);">Half Court 3v3</strong></div>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                                <a href="{{ route('contact') }}?subject=HoopFest%203v3%20Inquiry" class="btn btn-outline" style="width: 100%; min-height: 40px; font-family: var(--display-font); font-size: 16px; letter-spacing: 0.04em;">Get Notified</a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- Features Band --}}
        <section class="cc-section" style="padding-top: 80px; padding-bottom: 80px; border-top: 1px solid var(--border);">
            <div class="site-container scroll-reveal reveal-fade-up" style="max-width: 1000px; margin-inline: auto;">
                <div style="text-align: center; margin-bottom: 48px;">
                    <p class="cc-kicker" style="justify-content: center;">Why Compete</p>
                    <h2 style="font-family: var(--display-font); font-size: 48px; text-transform: uppercase; margin: 0;">Tournament features</h2>
                </div>
                <div class="cc-feature-row" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                    <article>
                        <strong style="color: var(--lime); font-family: var(--display-font); font-size: 32px; display: block; margin-bottom: 8px;">Referees</strong>
                        <span>Official match officials and certified referees for all matches to ensure fair gameplay.</span>
                    </article>
                    <article>
                        <strong style="color: var(--lime); font-family: var(--display-font); font-size: 32px; display: block; margin-bottom: 8px;">Scoreboards</strong>
                        <span>Live scoreboard tracking and tournament trees updated in real-time.</span>
                    </article>
                    <article>
                        <strong style="color: var(--lime); font-family: var(--display-font); font-size: 32px; display: block; margin-bottom: 8px;">Prizes</strong>
                        <span>Cash prizes, trophies, medals, and gear awards for top-tier finishers.</span>
                    </article>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
