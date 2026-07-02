<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register | CourtConnect</title>
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
            <p class="cc-kicker">Create account</p>
            <h1>Reserve courts faster with CourtConnect</h1>
            <p>After registration, verify your email to unlock booking and payment features.</p>
            <div class="auth-pills">
                <span>Fast booking</span>
                <span>Payment uploads</span>
                <span>Email verified</span>
            </div>
        </div>
        <section class="auth-card">
            <div class="auth-card-head">
                <p>Player access</p>
                <h2>Register</h2>
            </div>
            <form class="auth-form" method="POST" action="{{ route('register') }}">
                @csrf
                <label><span class="auth-label-text"><i data-lucide="user" aria-hidden="true"></i>Full Name</span>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required class="@error('name') is-invalid @enderror">
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
                <label><span class="auth-label-text"><i data-lucide="mail" aria-hidden="true"></i>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. john@example.com" required class="@error('email') is-invalid @enderror">
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
                <label><span class="auth-label-text"><i data-lucide="phone" aria-hidden="true"></i>Mobile Number</span>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" placeholder="e.g. 09123456789" required class="@error('phone') is-invalid @enderror" maxlength="11">
                    @error('phone')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
                <style>
                    .password-requirements-label {
                        position: relative;
                    }
                    .auth-card {
                        overflow: visible !important;
                    }
                    .password-requirements {
                        display: flex;
                        border: 1px solid var(--border);
                        border-radius: var(--radius);
                        background: var(--surface-alt);
                        padding: 16px;
                        flex-direction: column;
                        gap: 14px;
                        z-index: 10;
                        margin-top: 8px;
                        width: 100%;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
                        box-sizing: border-box;
                    }
                    .requirements-title {
                        display: block;
                        color: var(--muted);
                        font-family: var(--ui-font);
                        font-size: 11px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                        margin-bottom: 8px;
                    }
                    .requirements-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        display: grid;
                        gap: 8px;
                    }
                    .requirement-item {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        font-family: var(--ui-font);
                        font-size: 13px;
                        color: var(--muted-mid);
                        transition: color var(--transition);
                    }
                    .requirement-item .req-bullet {
                        width: 16px;
                        height: 16px;
                        border-radius: 50%;
                        border: 1.5px solid var(--border);
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        transition: all var(--transition);
                        flex-shrink: 0;
                        color: transparent;
                        font-size: 9px;
                        font-weight: 900;
                    }
                    .requirement-item.is-valid {
                        color: var(--text);
                    }
                    .requirement-item.is-valid .req-bullet {
                        border-color: var(--lime);
                        background: var(--lime);
                        color: var(--bg);
                    }
                    .requirement-item .req-bullet::after {
                        content: "✓";
                    }
                    .samples-section {
                        border-top: 1px solid var(--border);
                        padding-top: 12px;
                    }
                    .sample-passwords {
                        display: flex;
                        flex-direction: column;
                        gap: 6px;
                    }
                    .sample-passwords code {
                        font-family: monospace;
                        font-size: 12px;
                        color: var(--lime);
                        background: var(--surface);
                        padding: 6px 10px;
                        border: 1px solid var(--border);
                        border-radius: 6px;
                        width: 100%;
                        box-sizing: border-box;
                        letter-spacing: 0.05em;
                        user-select: all;
                        cursor: pointer;
                        text-align: center;
                    }
                    @media (min-width: 1200px) {
                        .password-requirements {
                            position: absolute;
                            left: calc(100% + 20px);
                            top: 0;
                            width: 320px;
                            margin-top: 0;
                        }
                        .password-requirements::before {
                            content: "";
                            position: absolute;
                            right: 100%;
                            top: 24px;
                            border-top: 8px solid transparent;
                            border-bottom: 8px solid transparent;
                            border-right: 8px solid var(--border);
                        }
                        .password-requirements::after {
                            content: "";
                            position: absolute;
                            right: 100%;
                            top: 24px;
                            margin-right: -1px;
                            border-top: 8px solid transparent;
                            border-bottom: 8px solid transparent;
                            border-right: 8px solid var(--surface-alt);
                        }
                    }
                </style>
                <div class="auth-field-grid">
                    <label class="password-requirements-label"><span class="auth-label-text"><i data-lucide="lock-keyhole" aria-hidden="true"></i>Password</span>
                        <div class="password-input-wrap">
                            <input type="password" name="password" id="password" placeholder="••••••••" required minlength="8" class="@error('password') is-invalid @enderror">
                            <button type="button" class="toggle-password" data-target="password" aria-label="Toggle password visibility">
                                <i data-lucide="eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            @if(!str_contains($message, 'confirm') && !str_contains($message, 'match') && !str_contains($message, 'confirmation'))
                                <span class="field-error">{{ $message }}</span>
                            @endif
                        @enderror

                        {{-- Dynamic Requirements Popup --}}
                        <div id="password-requirements" class="password-requirements">
                            <div class="requirements-section">
                                <span class="requirements-title">Requirements</span>
                                <ul class="requirements-list">
                                    <li id="req-length" class="requirement-item">
                                        <span class="req-bullet"></span>
                                        <span class="req-text">Minimum of 8 characters</span>
                                    </li>
                                    <li id="req-upper" class="requirement-item">
                                        <span class="req-bullet"></span>
                                        <span class="req-text">Contains an uppercase letter</span>
                                    </li>
                                    <li id="req-number" class="requirement-item">
                                        <span class="req-bullet"></span>
                                        <span class="req-text">Contains a number (0-9)</span>
                                    </li>
                                    <li id="req-special" class="requirement-item">
                                        <span class="req-bullet"></span>
                                        <span class="req-text">Contains special characters (!@#$%^&*.)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </label>
                    <label><span class="auth-label-text"><i data-lucide="shield-check" aria-hidden="true"></i>Confirm Password</span>
                        <div class="password-input-wrap">
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required minlength="8" class="@error('password') @if(str_contains($message, 'confirm') || str_contains($message, 'match') || str_contains($message, 'confirmation')) is-invalid @endif @enderror">
                            <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Toggle confirm password visibility">
                                <i data-lucide="eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            @if(str_contains($message, 'confirm') || str_contains($message, 'match') || str_contains($message, 'confirmation'))
                                <span class="field-error">{{ $message }}</span>
                            @endif
                        @enderror
                    </label>
                </div>
                <button type="submit" class="btn btn-primary"><i data-lucide="user-plus" class="mr-2" aria-hidden="true"></i>Register</button>
            </form>
            <a class="auth-switch" href="{{ route('login') }}"><i data-lucide="log-in" class="mr-2" aria-hidden="true"></i>Already have an account?</a>
        </section>
    </section>
    </main>
    @include('partials.public-footer')
    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        const passwordInput = document.getElementById('password');
        const requirementsDiv = document.getElementById('password-requirements');
        
        if (!passwordInput || !requirementsDiv) return;

        // Generate random secure passwords matching the rules
        function generateSamplePassword() {
            const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            const lowercase = "abcdefghijklmnopqrstuvwxyz";
            const numbers = "0123456789";
            const special = "!@#$%^&*.";
            
            const all = uppercase + lowercase + numbers + special;
            
            let pass = "";
            // Ensure at least one of each required type is included
            pass += uppercase[Math.floor(Math.random() * uppercase.length)];
            pass += lowercase[Math.floor(Math.random() * lowercase.length)];
            pass += numbers[Math.floor(Math.random() * numbers.length)];
            pass += special[Math.floor(Math.random() * special.length)];
            
            // Fill remaining spaces to achieve 10-14 characters
            const length = 10 + Math.floor(Math.random() * 5);
            for (let i = 4; i < length; i++) {
                pass += all[Math.floor(Math.random() * all.length)];
            }
            
            // Shuffle
            return pass.split('').sort(() => 0.5 - Math.random()).join('');
        }

        // Set dynamic samples on load
        const s1 = document.getElementById('sample-1');
        const s2 = document.getElementById('sample-2');
        if (s1) s1.textContent = generateSamplePassword();
        if (s2) s2.textContent = generateSamplePassword();

        // Click-to-copy sample handler
        document.querySelectorAll('.sample-passwords code').forEach(code => {
            code.addEventListener('click', () => {
                navigator.clipboard.writeText(code.textContent).then(() => {
                    const originalText = code.textContent;
                    code.textContent = "Copied!";
                    setTimeout(() => {
                        code.textContent = originalText;
                    }, 1000);
                });
            });
        });

        // Requirements block is always shown by default.

        // Real-time validations on typing
        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            
            // 1. Length >= 8
            const isLengthValid = val.length >= 8;
            toggleRequirement('req-length', isLengthValid);
            
            // 2. Uppercase check
            const isUpperValid = /[A-Z]/.test(val);
            toggleRequirement('req-upper', isUpperValid);
            
            // 3. Number check
            const isNumberValid = /[0-9]/.test(val);
            toggleRequirement('req-number', isNumberValid);
            
            // 4. Special char check
            const isSpecialValid = /[!@#$%^&*.]/.test(val);
            toggleRequirement('req-special', isSpecialValid);
        });

        function toggleRequirement(id, isValid) {
            const item = document.getElementById(id);
            if (!item) return;
            if (isValid) {
                item.classList.add('is-valid');
            } else {
                item.classList.remove('is-valid');
            }
        }
    });
    </script>
</body>
</html>
