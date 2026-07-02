<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); min-height: calc(100vh - 120px); padding: 80px 0;">
        <div class="site-container cc-detail-layout">
            
            <!-- Left Side: Contact Info -->
            <div>
                <p class="cc-kicker">Contact Us</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(36px, 6vw, 64px); text-transform: uppercase; line-height: 1; margin: 0 0 20px; font-weight: 400;">
                    Talk to the <span style="color: var(--lime);">facility team.</span>
                </h1>
                <p class="cc-lead" style="margin-bottom: 40px;">
                    Questions about schedules, pricing, payments, or events? Send us a note and our operations team will respond promptly.
                </p>
                
                <div style="display: grid; gap: 20px;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid var(--border); background: var(--surface); display: grid; place-items: center; color: var(--lime); flex-shrink: 0;">
                            <i data-lucide="mail" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <span style="display: block; color: var(--muted); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 2px;">Email Support</span>
                            <strong style="color: var(--text); font-size: 15px; font-weight: 600;">courtconnect2026@gmail.com</strong>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid var(--border); background: var(--surface); display: grid; place-items: center; color: var(--lime); flex-shrink: 0;">
                            <i data-lucide="phone" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <span style="display: block; color: var(--muted); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 2px;">Phone</span>
                            <strong style="color: var(--text); font-size: 15px; font-weight: 600;">+63 995 876 0534</strong>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid var(--border); background: var(--surface); display: grid; place-items: center; color: var(--lime); flex-shrink: 0;">
                            <i data-lucide="clock" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <span style="display: block; color: var(--muted); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 2px;">Operating Hours</span>
                            <strong style="color: var(--text); font-size: 15px; font-weight: 600;">Daily, 6:00 AM – 10:00 PM</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Contact Form Card -->
            <div class="card" style="position: relative; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 32px; overflow: hidden;">
                <div style="position: absolute; inset: 0 auto auto; height: 2px; background: linear-gradient(90deg, transparent, var(--lime) 50%, transparent); left: 0; right: 0;"></div>
                
                <h2 style="font-family: var(--display-font); font-size: 24px; text-transform: uppercase; letter-spacing: 0.04em; margin: 0 0 20px; font-weight: 400;">Send a message</h2>
                
                <form action="#" style="display: grid; gap: 16px;" onsubmit="event.preventDefault(); alert('Message sent successfully! We will get back to you as soon as possible.');">
                    <div style="display: grid; gap: 6px;">
                        <label style="color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Full Name</label>
                        <input type="text" placeholder="Enter your full name" required style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; color: var(--text); width: 100%;">
                    </div>
                    <div style="display: grid; gap: 6px;">
                        <label style="color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Email Address</label>
                        <input type="email" placeholder="Enter your email address" required style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; color: var(--text); width: 100%;">
                    </div>
                    <div style="display: grid; gap: 6px;">
                        <label style="color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Message</label>
                        <textarea placeholder="How can we help?" required rows="4" style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; color: var(--text); resize: none; min-height: 120px; width: 100%;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; min-height: 46px; border: 0;">Send Message</button>
                </form>
            </div>

        </div>
    </main>

    @include('partials.public-footer')
    @stack('scripts')
</body>
</html>
