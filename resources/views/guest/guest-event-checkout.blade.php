<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Event Checkout — {{ $event->title }} | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); min-height: calc(100vh - 120px); padding-top: 80px; padding-bottom: 120px;">
        <section class="site-container" style="max-width: 680px; margin-inline: auto; padding: 0 20px;">
            
            {{-- Header --}}
            <div style="margin-bottom: 28px; text-align: center;">
                <h1 style="font-family: var(--display-font); font-size: 42px; text-transform: uppercase; color: #fff; line-height: 1; margin: 0 0 8px;">
                    Event Registration
                </h1>
                <p style="color: var(--muted); font-size: 14px; margin: 0;">Complete your details below to secure your spot.</p>
            </div>

            {{-- Checkout Form --}}
            <form action="{{ route('events.submit-register', $event->slug) }}" method="POST" enctype="multipart/form-data" style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 32px;">
                @csrf

                {{-- Event Summary Card --}}
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 28px; display: flex; gap: 16px; align-items: center;">
                    <img src="{{ $event->image ? asset(ltrim($event->image, '/')) : asset('images/courtconnect-multisport-hero.webp') }}" alt="Event" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                    <div>
                        <span style="font-size: 10px; font-weight: 700; color: var(--lime); text-transform: uppercase; display: block; margin-bottom: 4px;">{{ $event->sport }} · {{ $event->event_type }}</span>
                        <strong style="color: #fff; font-size: 18px; display: block;">{{ $event->title }}</strong>
                        <span style="font-size: 13px; color: var(--muted); display: block; margin-top: 4px;">
                            {{ $event->start_date->format('M d, Y') }} @ {{ date('g:i A', strtotime($event->start_time)) }}
                        </span>
                    </div>
                </div>

                {{-- User Info --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;">Registrant Name</label>
                        <input type="text" value="{{ auth()->user()->name }}" disabled style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; color: var(--muted); box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;">Email Address</label>
                        <input type="text" value="{{ auth()->user()->email }}" disabled style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; color: var(--muted); box-sizing: border-box;">
                    </div>
                </div>

                {{-- Payment Section --}}
                @if($event->price > 0)
                    <div style="border-top: 1px solid var(--border); padding-top: 24px; margin-top: 24px;">
                        <h3 style="font-family: var(--display-font); font-size: 24px; text-transform: uppercase; color: #fff; margin: 0 0 16px;">
                            Payment Details
                        </h3>

                        {{-- Payment Method Tabs --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                            <label style="border: 1px solid var(--border); border-radius: 8px; padding: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; background: rgba(255,255,255,0.02);" id="tab-card">
                                <input type="radio" name="payment_method" value="credit_card" checked onclick="switchPayment('card')" style="accent-color: var(--lime);">
                                <i data-lucide="credit-card" style="width:16px; height:16px; color:var(--lime);"></i>
                                <span style="font-size:14px; font-weight:600; color:#fff;">Credit Card</span>
                            </label>
                            <label style="border: 1px solid var(--border); border-radius: 8px; padding: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; background: rgba(255,255,255,0.02);" id="tab-gcash">
                                <input type="radio" name="payment_method" value="gcash" onclick="switchPayment('gcash')" style="accent-color: var(--lime);">
                                <i data-lucide="wallet" style="width:16px; height:16px; color:var(--lime);"></i>
                                <span style="font-size:14px; font-weight:600; color:#fff;">GCash QR</span>
                            </label>
                        </div>

                        {{-- Credit Card form --}}
                        <div id="payment-card-fields" style="display: block;">
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;">Card Number</label>
                                <input type="text" id="card_number" name="card_number" placeholder="4111 2222 3333 4444" maxlength="19" inputmode="numeric" autocomplete="cc-number" class="@error('card_number') is-error @enderror" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; color: #fff; box-sizing: border-box; letter-spacing: 0.08em;">
                                @error('card_number')<span style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div>
                                    <label style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;">Expiry Date</label>
                                    <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5" inputmode="numeric" autocomplete="cc-exp" class="@error('card_expiry') is-error @enderror" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; color: #fff; box-sizing: border-box;">
                                    @error('card_expiry')<span style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;">CVC / CVV</label>
                                    <input type="text" id="card_cvc" name="card_cvc" placeholder="123" maxlength="4" inputmode="numeric" autocomplete="cc-csc" class="@error('card_cvc') is-error @enderror" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; color: #fff; box-sizing: border-box;">
                                    @error('card_cvc')<span style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- GCash form --}}
                        <div id="payment-gcash-fields" style="display: none;">
                            <div style="background: rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:12px; padding:20px; text-align:center; margin-bottom:20px;">
                                <div style="font-size:13px; color:var(--muted); margin-bottom:12px;">Scan QR below to pay via GCash:</div>
                                {{-- Mock QR Code Design --}}
                                <div style="width: 140px; height: 140px; background: #fff; margin: 0 auto; border: 4px solid var(--lime); display: flex; align-items: center; justify-content: center; border-radius: 8px; position:relative;">
                                    {{-- Just some squares to make it look like a QR code --}}
                                    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; width: 100px; height: 100px;">
                                        @for($j=0; $j<16; $j++)
                                            <div style="background: {{ $j%3===0 ? '#000' : 'transparent' }}; border: {{ $j%5===0 ? '2px solid #000' : 'none' }};"></div>
                                        @endfor
                                    </div>
                                    <span style="position:absolute; background:var(--lime); color:#000; font-size:9px; font-weight:800; padding:2px 6px; border-radius:4px; bottom:-10px;">GCASH PAY</span>
                                </div>
                                <div style="font-size:14px; font-weight:700; color:#fff; margin-top:20px;">₱{{ number_format($event->price, 2) }}</div>
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;">Reference Number</label>
                                <input type="text" name="reference_number" placeholder="Enter 13-digit GCash Ref #" class="@error('reference_number') is-error @enderror" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; color: #fff; box-sizing: border-box;">
                                @error('reference_number')<span style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;">Upload Proof of Payment (Screenshot)</label>
                                <input type="file" name="proof_image" class="@error('proof_image') is-error @enderror" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); border-radius: 8px; padding: 8px; color: #fff; box-sizing: border-box;">
                                @error('proof_image')<span style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Free Event hidden payment method --}}
                    <input type="hidden" name="payment_method" value="credit_card">
                @endif

                {{-- Action buttons --}}
                <div style="display:flex; gap:16px; margin-top:32px; border-top:1px solid var(--border); padding-top:24px;">
                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline" style="flex:1; text-align:center; display:block; padding:12px; border-radius:8px; font-weight:700; text-decoration:none; font-size:14px; box-sizing:border-box;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" style="flex:1; padding:12px; font-size:14px; font-weight:700; border-radius:8px;">
                        @if($event->price > 0)
                            Pay and Confirm
                        @else
                            Complete Registration
                        @endif
                    </button>
                </div>
            </form>
        </section>
    </main>

    @include('partials.public-footer')

    <script>
        lucide.createIcons();

        function switchPayment(method) {
            const cardFields = document.getElementById('payment-card-fields');
            const gcashFields = document.getElementById('payment-gcash-fields');
            const tabCard = document.getElementById('tab-card');
            const tabGcash = document.getElementById('tab-gcash');

            if (method === 'card') {
                cardFields.style.display = 'block';
                gcashFields.style.display = 'none';
                tabCard.style.borderColor = 'var(--lime)';
                tabGcash.style.borderColor = 'var(--border)';
            } else {
                cardFields.style.display = 'none';
                gcashFields.style.display = 'block';
                tabCard.style.borderColor = 'var(--border)';
                tabGcash.style.borderColor = 'var(--lime)';
            }
        }

        // Init borders
        switchPayment('card');

        // Card Number formatting: max 16 digits, auto-space every 4
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function (e) {
                let v = this.value.replace(/\D/g, '').slice(0, 16);
                this.value = v.replace(/(\d{4})(?=\d)/g, '$1 ');
            });
            cardNumberInput.addEventListener('keydown', function (e) {
                if (!/[\d\s]/.test(e.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        }

        // Expiry: auto-insert slash after MM
        const cardExpiryInput = document.getElementById('card_expiry');
        if (cardExpiryInput) {
            cardExpiryInput.addEventListener('input', function (e) {
                let v = this.value.replace(/\D/g, '').slice(0, 4);
                if (v.length >= 3) {
                    v = v.slice(0, 2) + '/' + v.slice(2);
                }
                this.value = v;
            });
            cardExpiryInput.addEventListener('keydown', function (e) {
                if (!/[\d\/]/.test(e.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        }

        // CVC: digits only, max 4
        const cardCvcInput = document.getElementById('card_cvc');
        if (cardCvcInput) {
            cardCvcInput.addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 4);
            });
            cardCvcInput.addEventListener('keydown', function (e) {
                if (!/[\d]/.test(e.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        }
    </script>
</body>
</html>
