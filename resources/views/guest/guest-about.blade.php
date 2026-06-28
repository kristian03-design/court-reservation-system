<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>About | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.png') }}">
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
        <section class="scroll-reveal reveal-fade-up" style="padding: 100px 0 60px; text-align: center;">
            <div class="site-container" style="max-width: 800px; margin-inline: auto;">
                <p class="cc-kicker" style="justify-content: center;">About CourtConnect</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(44px, 8vw, 88px); text-transform: uppercase; line-height: 1; margin: 0 0 24px; font-weight: 400; letter-spacing: 0.02em;">
                    A modern operating system for <span style="color: var(--lime);">sports courts.</span>
                </h1>
                <p class="cc-lead" style="margin: 0 auto 36px; max-width: 600px; color: var(--muted-mid); font-size: 18px;">
                    CourtConnect helps premium facilities publish schedules, manage reservations, track payments, and give players a seamless booking experience from their phone to the front desk.
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('courts.index') }}" class="btn btn-primary">Browse Courts</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ KEY STATS STRIP ═══════════════════════ --}}
        <section class="scroll-reveal reveal-fade-up stagger-1" style="padding: 40px 0; margin-bottom: 80px;">
            <div class="site-container">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1px; background: var(--border); border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border);">
                    <div style="background: var(--surface); padding: 32px 24px; text-align: center;">
                        <strong style="display: block; color: var(--lime); font-family: var(--display-font); font-size: 48px; line-height: 1; margin-bottom: 8px;">10K+</strong>
                        <span style="display: block; color: var(--muted-mid); font-family: var(--ui-font); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Bookings Managed</span>
                    </div>
                    <div style="background: var(--surface); padding: 32px 24px; text-align: center;">
                        <strong style="display: block; color: var(--lime); font-family: var(--display-font); font-size: 48px; line-height: 1; margin-bottom: 8px;">50+</strong>
                        <span style="display: block; color: var(--muted-mid); font-family: var(--ui-font); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Premium Courts</span>
                    </div>
                    <div style="background: var(--surface); padding: 32px 24px; text-align: center;">
                        <strong style="display: block; color: var(--lime); font-family: var(--display-font); font-size: 48px; line-height: 1; margin-bottom: 8px;">4.9/5</strong>
                        <span style="display: block; color: var(--muted-mid); font-family: var(--ui-font); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Player Rating</span>
                    </div>
                    <div style="background: var(--surface); padding: 32px 24px; text-align: center;">
                        <strong style="display: block; color: var(--lime); font-family: var(--display-font); font-size: 48px; line-height: 1; margin-bottom: 8px;">99.9%</strong>
                        <span style="display: block; color: var(--muted-mid); font-family: var(--ui-font); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Uptime Status</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ CORE PILLARS ═══════════════════════ --}}
        <section class="scroll-reveal reveal-fade-up" style="padding: 60px 0; margin-bottom: 80px;">
            <div class="site-container">
                <div style="text-align: center; margin-bottom: 48px; max-width: 600px; margin-inline: auto;">
                    <p class="cc-kicker" style="justify-content: center;">Our Values</p>
                    <h2 style="font-family: var(--display-font); font-size: clamp(32px, 6vw, 48px); text-transform: uppercase; line-height: 1.1; margin: 0; font-weight: 400;">
                        Built on three <span style="color: var(--lime);">fundamental pillars.</span>
                    </h2>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <!-- Pillar 1 -->
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px 28px; transition: border-color 0.25s var(--ease);" onmouseover="this.style.borderColor='rgba(191,255,0,0.25)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--lime-dim); color: var(--lime); display: grid; place-items: center; margin-bottom: 24px;">
                            <i data-lucide="zap" style="width: 20px; height: 20px;"></i>
                        </div>
                        <h3 style="font-family: var(--ui-font); font-size: 18px; font-weight: 700; margin: 0 0 12px; color: var(--text);">Simplicity First</h3>
                        <p style="margin: 0; color: var(--muted-mid); font-size: 14px; line-height: 1.6;">
                            We strip away the friction from reservation scheduling so players can get on the court in seconds. No complex workflows, just instant play access.
                        </p>
                    </div>

                    <!-- Pillar 2 -->
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px 28px; transition: border-color 0.25s var(--ease);" onmouseover="this.style.borderColor='rgba(191,255,0,0.25)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--lime-dim); color: var(--lime); display: grid; place-items: center; margin-bottom: 24px;">
                            <i data-lucide="layout-dashboard" style="width: 20px; height: 20px;"></i>
                        </div>
                        <h3 style="font-family: var(--ui-font); font-size: 18px; font-weight: 700; margin: 0 0 12px; color: var(--text);">Performance Driven</h3>
                        <p style="margin: 0; color: var(--muted-mid); font-size: 14px; line-height: 1.6;">
                            Real-time availability updates, interactive scheduler consoles, and automated confirmation states keep your operations running seamlessly 24/7.
                        </p>
                    </div>

                    <!-- Pillar 3 -->
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px 28px; transition: border-color 0.25s var(--ease);" onmouseover="this.style.borderColor='rgba(191,255,0,0.25)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--lime-dim); color: var(--lime); display: grid; place-items: center; margin-bottom: 24px;">
                            <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                        </div>
                        <h3 style="font-family: var(--ui-font); font-size: 18px; font-weight: 700; margin: 0 0 12px; color: var(--text);">Community Connected</h3>
                        <p style="margin: 0; color: var(--muted-mid); font-size: 14px; line-height: 1.6;">
                            We believe in connecting players to local premium facilities, fostering local sports leagues, and building a thriving community of active individuals.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ OUR STORY (SPLIT GRID) ═══════════════════════ --}}
        <section class="scroll-reveal reveal-fade-up" style="padding: 60px 0; margin-bottom: 80px;">
            <div class="site-container">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 48px; align-items: center;">
                    <!-- Text Column -->
                    <div>
                        <p class="cc-kicker">Our Story</p>
                        <h2 style="font-family: var(--display-font); font-size: clamp(36px, 7vw, 56px); text-transform: uppercase; line-height: 1.05; margin: 0 0 24px; font-weight: 400;">
                            Bridging the gap between <span style="color: var(--lime);">digital ease</span> and physical play.
                        </h2>
                        <p style="color: var(--muted-mid); font-size: 15px; line-height: 1.7; margin: 0 0 20px;">
                            Designed in 2026, CourtConnect was born from a simple observation: organizing and scheduling sports games shouldn't feel like administrative work. 
                        </p>
                        <p style="color: var(--muted-mid); font-size: 15px; line-height: 1.7; margin: 0;">
                            Whether you are scheduling a friendly weekly badminton match or managing a high-volume multisport complex with dozens of courts, our platform provides the tools you need. We build software that gives players instant booking visibility, automates reservation workflows, and helps club managers grow their business.
                        </p>
                    </div>

                    <!-- Graphic/Image Column with Automatic Changing Slideshow -->
                    <div style="position: relative;">
                        <div style="position: absolute; inset: -15px; background: radial-gradient(circle, rgba(191,255,0,0.1) 0%, transparent 70%); z-index: -1;"></div>
                        <div id="about-image-carousel" style="border: 1px solid var(--border); border-radius: var(--radius-xl); overflow: hidden; background: var(--surface); box-shadow: 0 20px 50px rgba(0,0,0,0.4); aspect-ratio: 4/3; position: relative;">
                            <!-- Slide 1 (active) -->
                            <img class="carousel-slide active" src="{{ asset('images/courtconnect-club-courts.png') }}" alt="Premium multisport club courts" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: saturate(0.85); opacity: 1; transition: opacity 0.8s ease-in-out;">
                            <!-- Slide 2 -->
                            <img class="carousel-slide" src="{{ asset('images/courtconnect-badminton-court.png') }}" alt="Badminton Court" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: saturate(0.85); opacity: 0; transition: opacity 0.8s ease-in-out;">
                            <!-- Slide 3 -->
                            <img class="carousel-slide" src="{{ asset('images/courtconnect-basketball-court.png') }}" alt="Basketball Court" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: saturate(0.85); opacity: 0; transition: opacity 0.8s ease-in-out;">
                            <!-- Slide 4 -->
                            <img class="carousel-slide" src="{{ asset('images/courtconnect-tennis-court.png') }}" alt="Tennis Court" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: saturate(0.85); opacity: 0; transition: opacity 0.8s ease-in-out;">
                        </div>
                        <!-- Small floating tag -->
                        <div style="position: absolute; bottom: 20px; left: 20px; z-index: 10; background: #0F0F0F; border: 1px solid var(--border); border-radius: 99px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--lime);"></span>
                            <span style="font-family: var(--ui-font); font-size: 11px; font-weight: 700; color: #FFF; text-transform: uppercase; letter-spacing: 0.05em;">EST. 2026</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ CTA BAND ═══════════════════════ --}}
        <section class="scroll-reveal reveal-fade-up" style="padding: 40px 0;">
            <div class="site-container">
                <div style="background: radial-gradient(circle at top right, rgba(191,255,0,0.06), transparent 50%), var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 60px 40px; text-align: center; position: relative; overflow: hidden;">
                    <div style="max-width: 600px; margin-inline: auto;">
                        <h2 style="font-family: var(--display-font); font-size: clamp(36px, 7vw, 64px); text-transform: uppercase; line-height: 1; margin: 0 0 16px; font-weight: 400;">
                            Ready to experience the <span style="color: var(--lime);">future of booking?</span>
                        </h2>
                        <p style="color: var(--muted-mid); font-size: 16px; line-height: 1.6; margin: 0 0 32px;">
                            Explore our available sports spaces, view schedules in real time, and reserve your slot instantly today.
                        </p>
                        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                            <a href="{{ route('courts.index') }}" class="btn btn-primary">Browse Courts</a>
                            <a href="{{ route('register') }}" class="btn btn-outline">Join as Player</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var slides = document.querySelectorAll('#about-image-carousel .carousel-slide');
        var currentSlide = 0;
        
        if (slides.length > 1) {
            setInterval(function () {
                slides[currentSlide].style.opacity = 0;
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].style.opacity = 1;
            }, 3500);
        }
    });
    </script>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
