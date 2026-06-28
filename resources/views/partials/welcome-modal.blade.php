{{-- Welcome Onboarding Modal --}}
<div id="welcome-onboarding-modal" class="welcome-modal-backdrop" aria-hidden="true" role="dialog">
    <div class="welcome-modal-card">
        <!-- Close Button -->
        <button type="button" class="welcome-modal-close-btn" id="welcome-close-btn" aria-label="Close welcome guide">
            <i data-lucide="x" style="width: 18px; height: 18px;"></i>
        </button>

        <!-- Hero Header -->
        <div class="welcome-modal-hero">
            <div class="welcome-modal-hero-content">
                <span class="welcome-modal-badge">
                    <i data-lucide="info" style="width: 12px; height: 12px; margin-top:-1px;"></i>
                    Member Notice
                </span>
                <h2>Welcome to <span>CourtConnect!</span></h2>
                <p>We've made it easier than ever to start your journey. Explore premium sports courts, check real-time availability, and begin your booking with just a few clicks.</p>
            </div>
        </div>

        <!-- Body Features -->
        <div class="welcome-modal-body">
            <!-- Feature Grid -->
            <div class="welcome-feature-grid">
                <!-- Card 1 -->
                <div class="welcome-feature-card">
                    <div class="welcome-feature-icon">
                        <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div class="welcome-feature-info">
                        <h4>Easy Access</h4>
                        <p>All reservation details and schedule status in one convenient place.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="welcome-feature-card">
                    <div class="welcome-feature-icon">
                        <i data-lucide="clipboard-list" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div class="welcome-feature-info">
                        <h4>Simple Process</h4>
                        <p>Step-by-step guidance for a fast, hassle-free booking experience.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="welcome-feature-card">
                    <div class="welcome-feature-icon">
                        <i data-lucide="calendar-days" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div class="welcome-feature-info">
                        <h4>Stay Updated</h4>
                        <p>Get instant schedule changes, announcements, and slot radar updates.</p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="welcome-feature-card">
                    <div class="welcome-feature-icon">
                        <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div class="welcome-feature-info">
                        <h4>Secure & Trusted</h4>
                        <p>Your payment details and playing data are protected by industry standards.</p>
                    </div>
                </div>
            </div>

            <!-- Quote Block -->
            <div class="welcome-quote-box">
                <div class="welcome-quote-icon">
                    <i data-lucide="quote" style="width: 16px; height: 16px;"></i>
                </div>
                <blockquote class="welcome-quote-text">
                    <span class="welcome-quote-highlight">Start playing with confidence.</span>
                    "We are here to support you at every single step of your reservation journey."
                </blockquote>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="welcome-modal-footer">
            <label class="welcome-checkbox-label" for="welcome-dont-show-checkbox">
                <input type="checkbox" id="welcome-dont-show-checkbox">
                Don't show this again
            </label>
            <div class="welcome-footer-buttons">
                <a href="{{ route('courts.index') }}" class="btn btn-outline" id="welcome-browse-courts-btn" style="min-height:44px; padding-inline: 24px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    <i data-lucide="grid-3x3" style="width: 16px; height: 16px;"></i>
                    Browse Courts
                </a>
                <button type="button" class="btn btn-primary" id="welcome-get-started-btn" style="min-height:44px; padding-inline: 28px; background: var(--color-brand-primary); color: #0F0F0F; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    Get Started
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalId = 'welcome-onboarding-modal';
    var checkboxId = 'welcome-dont-show-checkbox';
    var closeBtnId = 'welcome-close-btn';
    var getStartedBtnId = 'welcome-get-started-btn';
    var browseCourtsBtnId = 'welcome-browse-courts-btn';

    var backdrop = document.getElementById(modalId);
    var checkbox = document.getElementById(checkboxId);
    var closeBtn = document.getElementById(closeBtnId);
    var getStartedBtn = document.getElementById(getStartedBtnId);
    var browseCourtsBtn = document.getElementById(browseCourtsBtnId);

    if (!backdrop) return;

    // Check if dismissed in localStorage
    var isDismissed = localStorage.getItem('courtconnect_welcome_dismissed');
    if (!isDismissed) {
        // Show after a slight delay for dramatic effect
        setTimeout(function () {
            backdrop.classList.add('active');
        }, 800);
    }

    function dismissModal() {
        backdrop.classList.remove('active');
        if (checkbox && checkbox.checked) {
            localStorage.setItem('courtconnect_welcome_dismissed', 'true');
        }
    }

    if (closeBtn) closeBtn.addEventListener('click', dismissModal);
    if (getStartedBtn) getStartedBtn.addEventListener('click', dismissModal);
    if (browseCourtsBtn) {
        browseCourtsBtn.addEventListener('click', function () {
            dismissModal();
            // Let the link navigate normally
        });
    }

    // Dismiss when clicking outside the card
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) {
            dismissModal();
        }
    });
});
</script>
