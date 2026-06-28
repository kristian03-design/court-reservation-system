@php
    $logoCandidates = ['images/courtconnect-mark.png', 'images/courtconnect-logo.svg', 'images/courtconnect-logo.png', 'courtconnect-logo.svg', 'courtconnect-logo.png'];
    $layoutLogo = collect($logoCandidates)->first(fn ($path) => file_exists(public_path($path)));
@endphp

<footer class="site-footer">
    <div class="site-container footer-grid">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="brand">
                @if ($layoutLogo)
                    <span class="brand-mark"><img src="{{ asset($layoutLogo) }}" alt="CourtConnect logo"></span>
                @else
                    <span class="brand-fallback">CC</span>
                @endif
                <span class="brand-text">
                    <span class="brand-name">CourtConnect</span>
                    <span class="brand-tagline">Reserve. Play. Connect.</span>
                </span>
            </a>
            <p class="footer-copy">Premium court reservations, live availability, and facility operations for modern sports clubs.</p>
            <div class="footer-badges">
                <span>Live availability</span>
                <span>Secure payments</span>
                <span>Multi-sport</span>
            </div>
            <div class="footer-socials">
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                </a>
                <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-twitter"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
                </a>
                <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-youtube"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17z"/><path d="m10 15 5-3-5-3z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <h2 class="footer-title">Explore</h2>
            <div class="footer-links">
                <a href="{{ route('courts.index') }}">Courts</a>
                <a href="{{ route('booking.create') }}">Book a court</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
        </div>

        <div class="footer-contact-card">
            <h2 class="footer-title">Contact</h2>
            <div class="footer-contact">
                <div class="contact-item">
                    <i data-lucide="mail" class="contact-icon"></i>
                    <span>courtconnect2026@gmail.com</span>
                </div>
                <div class="contact-item">
                    <i data-lucide="phone" class="contact-icon"></i>
                    <span>+63 900 123 4567</span>
                </div>
                <div class="contact-item">
                    <i data-lucide="clock" class="contact-icon"></i>
                    <span>Daily, 6:00 AM – 10:00 PM</span>
                </div>
            </div>
        </div>
    </div>

    <div class="site-container footer-bottom">
        <div class="footer-bottom-left">
            <span>&copy; {{ now()->year }} CourtConnect</span>
            <span class="footer-dot">•</span>
            <a href="{{ route('privacy') }}" class="footer-legal-link">Privacy Policy</a>
            <span class="footer-dot">•</span>
            <a href="{{ route('terms') }}" class="footer-legal-link">Terms of Service</a>
        </div>
        <span>Reserve. Play. Connect.</span>
    </div>
</footer>
