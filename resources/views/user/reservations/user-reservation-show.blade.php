@extends('user.layouts.shell', ['pageTitle' => 'Reservation Details'])

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('reservations.index') }}" class="btn btn-outline btn-sm" style="margin-bottom: 12px; display: inline-flex; align-items: center; gap: 4px;">
            <i data-lucide="chevron-left" style="width: 14px; height: 14px;"></i>
            Back to Reservations
        </a>
        <h1 style="font-family: var(--display-font); font-size: 32px; font-weight: 400; color: var(--text); margin: 0; letter-spacing: 0.04em; text-transform: uppercase;">Reservation Ticket</h1>
    </div>

    <div class="reservation-detail-grid">
        <!-- Left Side: Ticket Card -->
        <article class="reservation-detail-card">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid var(--border); margin-bottom: 20px;">
                <div>
                    <p class="dash-kicker" style="margin: 0 0 4px;">Receipt Slip</p>
                    <h2 style="font-family: var(--display-font); font-size: 26px; font-weight: 400; letter-spacing: 0.04em; color: var(--text); margin: 0;">#{{ $reservation->reservation_number }}</h2>
                </div>
                <span class="status status-{{ $reservation->status }}">{{ str_replace('_', ' ', $reservation->status) }}</span>
            </div>

            <div style="display: grid; gap: 14px; margin-bottom: 24px;">
                <div class="booking-receipt-row">
                    <span class="key">Court Space</span>
                    <span class="val">{{ $reservation->court->court_name }}</span>
                </div>
                <div class="booking-receipt-row">
                    <span class="key">Reservation Date</span>
                    <span class="val">{{ $reservation->reservation_date->format('F d, Y') }}</span>
                </div>
                <div class="booking-receipt-row">
                    <span class="key">Reserved Time Slot</span>
                    <span class="val">{{ substr($reservation->start_time, 0, 5) }} &ndash; {{ substr($reservation->end_time, 0, 5) }}</span>
                </div>
                <div class="booking-receipt-row">
                    <span class="key">Players Registered</span>
                    <span class="val">{{ $reservation->players }} {{ \Illuminate\Support\Str::plural('Player', $reservation->players) }}</span>
                </div>
                
                @if ($reservation->notes)
                    <div style="margin-top: 12px;">
                        <span class="key" style="display: block; margin-bottom: 6px; color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Special Requests</span>
                        <div style="font-size: 13px; color: var(--muted-mid); background: var(--surface-alt); padding: 12px; border-radius: var(--radius); border: 1px solid var(--border);">{{ $reservation->notes }}</div>
                    </div>
                @endif

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: var(--lime-dim); border: 1px dashed rgba(191, 255, 0, 0.2); border-radius: var(--radius); margin-top: 12px;">
                    <div>
                        <span class="key" style="display: block; margin-bottom: 4px; color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Total Bill Amount</span>
                        <span class="val" style="font-size: 24px; font-weight: 700; font-family: var(--display-font); color: var(--lime); line-height: 1;">PHP {{ number_format($reservation->total_amount, 2) }}</span>
                    </div>
                    <div style="text-align: right;">
                        <span class="key" style="display: block; margin-bottom: 4px; color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Payment Status</span>
                        <span class="status status-{{ $reservation->payment->payment_status }}">{{ str_replace('_', ' ', $reservation->payment->payment_status) }}</span>
                    </div>
                </div>
            </div>

            @if ($reservation->status === 'held' || $reservation->status === 'pending_payment')
                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                    <form method="POST" action="{{ route('reservations.cancel', $reservation) }}" id="cancel-reservation-form">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline" style="border-color: var(--coral); color: var(--coral) !important;">Cancel Reservation</button>
                    </form>
                </div>
            @endif
        </article>

        <!-- Right Side: Status Feedback Panel & Payment Upload -->
        <aside class="side-console">
            @if ($reservation->status === 'expired')
                <div class="card" style="text-align: center; border-color: var(--coral); background: rgba(239, 68, 68, 0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--coral-dim); border: 1px solid rgba(239, 68, 68, 0.25); color: var(--coral); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i data-lucide="x-circle" style="width: 22px; height: 22px;"></i>
                    </div>
                    <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400;">Ticket Expired</h2>
                    <p style="margin: 0 0 20px; color: var(--muted-mid); font-size: 13px; line-height: 1.6;">The checkout timer for this reservation expired before payment was completed. Please book another court.</p>
                    <a href="{{ route('booking.create') }}" class="btn btn-primary" style="width: 100%;">Book Another Court</a>
                </div>
            @elseif ($reservation->status === 'cancelled')
                <div class="card" style="text-align: center; border-color: var(--coral); background: rgba(239, 68, 68, 0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--coral-dim); border: 1px solid rgba(239, 68, 68, 0.25); color: var(--coral); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i data-lucide="x-circle" style="width: 22px; height: 22px;"></i>
                    </div>
                    <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400;">Booking Cancelled</h2>
                    <p style="margin: 0 0 20px; color: var(--muted-mid); font-size: 13px; line-height: 1.6;">This reservation has been cancelled. If you want to play, please schedule a new match.</p>
                    <a href="{{ route('booking.create') }}" class="btn btn-primary" style="width: 100%;">Book Another Court</a>
                </div>
            @elseif ($reservation->payment->payment_status === 'pending_verification')
                <div class="card" style="text-align: center; border-color: rgba(255, 200, 0, 0.2); background: rgba(255, 200, 0, 0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255, 200, 0, 0.1); border: 1px solid rgba(255, 200, 0, 0.25); color: #FFC800; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i data-lucide="alert-triangle" style="width: 22px; height: 22px;"></i>
                    </div>
                    <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400;">Verification Pending</h2>
                    <p style="margin: 0 0 20px; color: var(--muted-mid); font-size: 13px; line-height: 1.6;">We have received your payment proof receipt. Our admin staff is currently reviewing the attachment. Your reservation slot will be fully approved shortly.</p>
                    
                    @if ($reservation->payment->proof_image)
                        <div style="margin-top: 20px; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; max-height: 200px; background: var(--surface-alt);">
                            <img src="{{ route('payments.proof.show', $reservation->payment) }}" alt="Submitted Proof" style="width: 100%; height: 100%; object-fit: contain; margin: 0 auto;">
                        </div>
                    @endif

                    <a href="{{ route('dashboard') }}" class="btn btn-outline" style="margin-top: 24px; width: 100%;">Return to Dashboard</a>
                </div>
            @elseif ($reservation->payment->payment_status === 'paid' || $reservation->status === 'confirmed' || $reservation->status === 'completed')
                <div class="card" style="text-align: center; border-color: rgba(191, 255, 0, 0.2); background: rgba(191, 255, 0, 0.02); position: relative;">
                    <canvas id="confetti-canvas" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; pointer-events: none; z-index: 9999;"></canvas>

                    <div class="confirmation-ring">
                        <span class="confirmation-check">✓</span>
                    </div>
                    <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400;">Booking Confirmed</h2>
                    <p style="margin: 0 0 20px; color: var(--muted-mid); font-size: 13px; line-height: 1.6;">Your court reservation is fully approved and confirmed. A slot has been locked for your team. Show this ticket upon arrival at the venue!</p>
                    
                    <div class="qr-placeholder" style="margin: 20px auto 0;">
                        <span style="color: var(--lime); display: grid; place-items: center;"><i data-lucide="qr-code" style="width: 48px; height: 48px;"></i></span>
                    </div>
                    <span style="display: block; margin-top: 8px; font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;">CC-TICKET-{{ $reservation->id }}</span>

                    <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top: 24px; width: 100%;">Go to Dashboard</a>
                </div>
            @elseif ($reservation->payment->payment_method === 'pay_at_venue')
                <!-- Pay at Venue Card -->
                <div class="card">
                    @php
                        $heldSeconds = max(0, $reservation->created_at->addMinutes(10)->timestamp - time());
                    @endphp
                    @if ($reservation->status === 'held' && $heldSeconds > 0)
                        <div style="text-align: center; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 20px;">
                            <div style="font-size: 24px; font-weight: 700; font-family: monospace; color: var(--coral);" id="countdown-timer" data-seconds="{{ $heldSeconds }}">
                                10:00
                            </div>
                            <span style="font-size: 11px; color: var(--muted); text-transform: uppercase;">Hold Time Remaining</span>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const timerEl = document.getElementById('countdown-timer');
                                    let seconds = parseInt(timerEl.getAttribute('data-seconds'));
                                    function updateTimer() {
                                        if (seconds <= 0) {
                                            timerEl.innerHTML = "EXPIRED";
                                            window.location.reload();
                                            return;
                                        }
                                        const mins = Math.floor(seconds / 60);
                                        const secs = seconds % 60;
                                        timerEl.innerHTML = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                                        seconds--;
                                        setTimeout(updateTimer, 1000);
                                    }
                                    updateTimer();
                                });
                            </script>
                        </div>
                    @endif

                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--lime-dim); border: 1px solid rgba(191, 255, 0, 0.25); color: var(--lime); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i data-lucide="wallet" style="width: 22px; height: 22px;"></i>
                    </div>
                    <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400; text-align: center;">Pay at Venue</h2>
                    <p style="margin: 0 0 20px; color: var(--muted-mid); font-size: 13px; line-height: 1.6; text-align: center;">Your court space is held. Please settle your total amount at the facility front desk upon arrival.</p>
                    
                    <div style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 24px;">
                        <span style="display: block; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Instructions</span>
                        <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: var(--muted-mid); display: grid; gap: 8px;">
                            <li>Show your reservation reference # to staff.</li>
                            <li>Payment accepted via Cash, GCash, or Card.</li>
                            <li>Arrival 10 mins before your schedule is advised.</li>
                        </ul>
                    </div>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline" style="width: 100%;">Return to Dashboard</a>
                </div>
            @else
                <!-- Online Payment options (Manual Proof Upload or Credit Card) -->
                @php
                    $heldSeconds = max(0, $reservation->created_at->addMinutes(10)->timestamp - time());
                @endphp
                @if ($reservation->status === 'held' && $heldSeconds > 0)
                    <div class="card" style="border-color: rgba(255, 158, 11, 0.2); background: rgba(255, 158, 11, 0.02); text-align: center; margin-bottom: 20px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255, 158, 11, 0.1); border: 1px solid rgba(255, 158, 11, 0.25); color: #F59E0B; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i data-lucide="clock" style="width: 22px; height: 22px;"></i>
                        </div>
                        <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400;">Reservation Held</h2>
                        <p style="margin: 0 0 16px; color: var(--muted-mid); font-size: 13px; line-height: 1.6;">Your selected time slot is locked. Please complete your payment before the timer expires.</p>
                        
                        <div style="font-size: 28px; font-weight: 700; font-family: monospace; color: var(--coral); background: var(--surface-alt); padding: 12px; border-radius: var(--radius); border: 1px solid var(--border); display: inline-block;" id="countdown-timer" data-seconds="{{ $heldSeconds }}">
                            10:00
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const timerEl = document.getElementById('countdown-timer');
                                let seconds = parseInt(timerEl.getAttribute('data-seconds'));
                                function updateTimer() {
                                    if (seconds <= 0) {
                                        timerEl.innerHTML = "EXPIRED";
                                        window.location.reload();
                                        return;
                                    }
                                    const mins = Math.floor(seconds / 60);
                                    const secs = seconds % 60;
                                    timerEl.innerHTML = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                                    seconds--;
                                    setTimeout(updateTimer, 1000);
                                }
                                updateTimer();
                            });
                        </script>
                    </div>
                @endif
                @if ($reservation->payment->payment_method === 'card')
                    <!-- Credit Card Checkout Form -->
                    <div class="card">
                        <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400;">Secure Card Checkout</h2>
                        <p style="margin: 0 0 20px; font-size: 13px; color: var(--muted-mid); line-height: 1.5;">Enter your card details to complete your payment instantly.</p>
                        
                        @if ($errors->any())
                            <div style="background: var(--coral-dim); border: 1px solid var(--coral); border-radius: var(--radius); padding: 12px; margin-bottom: 16px; font-size: 12px; color: var(--text);">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('payments.pay-card', $reservation->payment) }}" style="display: grid; gap: 14px;">
                            @csrf
                            <div style="display: grid; gap: 6px;">
                                <label style="font-size: 11px; font-weight: 700; color: var(--muted-mid); text-transform: uppercase; letter-spacing: 0.05em;">Card Number</label>
                                <input type="text" name="card_number" required placeholder="•••• •••• •••• ••••" style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; font-size: 14px; color: var(--text); outline: none;" maxlength="19">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div style="display: grid; gap: 6px;">
                                    <label style="font-size: 11px; font-weight: 700; color: var(--muted-mid); text-transform: uppercase; letter-spacing: 0.05em;">Expiry Date</label>
                                    <input type="text" name="card_expiry" required placeholder="MM/YY" style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; font-size: 14px; color: var(--text); outline: none;" maxlength="5">
                                </div>
                                <div style="display: grid; gap: 6px;">
                                    <label style="font-size: 11px; font-weight: 700; color: var(--muted-mid); text-transform: uppercase; letter-spacing: 0.05em;">CVC / CVV</label>
                                    <input type="text" name="card_cvc" required placeholder="•••" style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; font-size: 14px; color: var(--text); outline: none;" maxlength="4">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Pay PHP {{ number_format($reservation->total_amount, 2) }}</button>
                        </form>
                    </div>
                @else
                    <!-- Manual Online Upload (GCash, Maya, Bank Transfer) -->
                    <div class="card">
                        @if ($reservation->payment->payment_status === 'rejected')
                            <div style="background: var(--coral-dim); border: 1px solid var(--coral); border-radius: var(--radius); padding: 16px; margin-bottom: 16px; display: flex; gap: 12px; align-items: start;">
                                <i data-lucide="alert-octagon" style="width: 20px; height: 20px; color: var(--coral); flex-shrink: 0; margin-top: 2px;"></i>
                                <div>
                                    <h4 style="margin: 0 0 4px; color: var(--text); font-weight: 700; font-size: 13px;">Payment Proof Rejected</h4>
                                    <p style="margin: 0; color: #fff; font-size: 12px; line-height: 1.5; font-weight: 500;">
                                        {{ $reservation->payment->rejection_reason ?? 'Your submitted receipt could not be verified by our staff. Please double check and upload a valid proof.' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <h2 style="font-family: var(--display-font); font-size: 22px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 8px; font-weight: 400;">Submit Payment</h2>
                        <p style="margin: 0 0 16px; font-size: 13px; color: var(--muted-mid); line-height: 1.5;">Please send the payment using the details below and upload your receipt.</p>

                        <div style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 20px;">
                            @if ($reservation->payment->payment_method === 'gcash')
                                <span style="display: block; font-size: 10px; font-weight: 700; color: #005ae0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">GCash Payment</span>
                                <strong style="display: block; font-size: 16px; color: var(--text); margin-bottom: 4px;">0917-123-4567</strong>
                                <span style="font-size: 12px; color: var(--muted-mid);">Account Name: CourtConnect Inc.</span>
                            @elseif ($reservation->payment->payment_method === 'maya')
                                <span style="display: block; font-size: 10px; font-weight: 700; color: #00cc72; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Maya Payment</span>
                                <strong style="display: block; font-size: 16px; color: var(--text); margin-bottom: 4px;">0917-123-4567</strong>
                                <span style="font-size: 12px; color: var(--muted-mid);">Account Name: CourtConnect Inc.</span>
                            @elseif ($reservation->payment->payment_method === 'bank_transfer')
                                <span style="display: block; font-size: 10px; font-weight: 700; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Bank Transfer (BDO)</span>
                                <strong style="display: block; font-size: 16px; color: var(--text); margin-bottom: 4px;">0123-4567-8910</strong>
                                <span style="font-size: 12px; color: var(--muted-mid);">Account Name: CourtConnect Inc.</span>
                            @else
                                <span style="display: block; font-size: 10px; font-weight: 700; color: var(--lime); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Online Payment</span>
                                <strong style="display: block; font-size: 16px; color: var(--text);">Send PHP {{ number_format($reservation->total_amount, 2) }}</strong>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('payments.proof', $reservation->payment) }}" enctype="multipart/form-data" style="display: grid; gap: 14px;">
                             @csrf
                             
                             <div style="display: grid; gap: 6px;">
                                 <label style="font-size: 11px; font-weight: 700; color: var(--muted-mid); text-transform: uppercase; letter-spacing: 0.05em;">Transaction Reference Number</label>
                                 <input type="text" name="reference_number" required placeholder="e.g. 500123456789" value="{{ old('reference_number', $reservation->payment->reference_number) }}" style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; font-size: 14px; color: var(--text); outline: none;">
                             </div>

                             <div style="display: grid; gap: 6px;">
                                 <label style="font-size: 11px; font-weight: 700; color: var(--muted-mid); text-transform: uppercase; letter-spacing: 0.05em;">Upload Receipt Slip</label>
                                 <div style="position: relative; border: 2px dashed var(--border); border-radius: var(--radius); background: var(--surface-alt); padding: 24px; text-align: center; cursor: pointer; transition: border-color var(--transition);" id="upload-zone">
                                     <input type="file" name="proof_image" id="proof-file-input" accept="image/*" required style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2;">
                                     <div style="position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                         <i data-lucide="upload-cloud" style="width: 28px; height: 28px; color: var(--lime);"></i>
                                         <span id="file-name" style="font-weight: 700; color: var(--text); font-size: 13px;">Click or drag receipt here</span>
                                         <span style="font-size: 11px; color: var(--muted);">Supports JPG, PNG (Max 5MB)</span>
                                     </div>
                                 </div>
                             </div>

                             <!-- Image Preview Slot -->
                             <div id="upload-preview" style="display: none; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; max-height: 220px; background: var(--surface-alt);">
                                 <img src="" id="preview-image" alt="Proof Preview" style="width: 100%; height: 100%; object-fit: contain;">
                             </div>

                             <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 6px;">Upload Proof Receipt</button>
                        </form>
                    </div>
                @endif
            @endif
        </aside>
        </aside>
    </div>

    <!-- Cancellation Confirmation Modal -->
    <div class="custom-modal-backdrop" id="cancel-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="custom-modal" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); width: 100%; max-width: 440px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); position: relative; margin: 16px;">
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--coral-dim); border: 1px solid rgba(255, 92, 58, 0.25); color: var(--coral); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <i data-lucide="alert-triangle" style="width: 26px; height: 26px;"></i>
                </div>
                <h2 style="font-family: var(--display-font); font-size: 24px; font-weight: 800; text-transform: uppercase; margin: 0 0 6px; letter-spacing: 0.04em; color: var(--text);">Cancel Reservation</h2>
                <p style="color: var(--muted-mid); font-size: 13px; margin: 0; line-height: 1.5;">Are you sure you want to cancel your reservation for <strong>{{ $reservation->court->court_name }}</strong> on <strong>{{ $reservation->reservation_date->format('F d, Y') }}</strong>?</p>
            </div>

            <div style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 24px; text-align: center; font-size: 13px; color: var(--muted); border-left: 3px solid var(--coral);">
                This action will release the slot and make it available for other players.
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button type="button" class="btn btn-outline" id="btn-close-cancel-modal" style="width: 100%;">Keep Booking</button>
                <button type="button" class="btn" id="btn-confirm-cancel-modal" style="width: 100%; background: var(--coral); color: #FFF; font-weight: 700; border: none;">Yes, Cancel</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Cancellation Modal Logic
        const cancelForm = document.getElementById('cancel-reservation-form');
        if (cancelForm) {
            const cancelModalBackdrop = document.getElementById('cancel-modal-backdrop');
            const btnCloseCancelModal = document.getElementById('btn-close-cancel-modal');
            const btnConfirmCancelModal = document.getElementById('btn-confirm-cancel-modal');

            cancelForm.addEventListener('submit', (e) => {
                if (cancelForm.dataset.confirmed === 'true') {
                    return;
                }

                e.preventDefault();
                cancelModalBackdrop.style.display = 'flex';
                
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });

            btnCloseCancelModal.addEventListener('click', () => {
                cancelModalBackdrop.style.display = 'none';
            });

            btnConfirmCancelModal.addEventListener('click', () => {
                cancelForm.dataset.confirmed = 'true';
                cancelForm.submit();
            });

            cancelModalBackdrop.addEventListener('click', (e) => {
                if (e.target === cancelModalBackdrop) {
                    cancelModalBackdrop.style.display = 'none';
                }
            });
        }

        // GCash Proof image upload logic
        const fileInput = document.getElementById('proof-file-input');
        const uploadZone = document.getElementById('upload-zone');
        const fileName = document.getElementById('file-name');
        const previewContainer = document.getElementById('upload-preview');
        const previewImage = document.getElementById('preview-image');

        if (fileInput && uploadZone) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    fileName.textContent = file.name;
                    uploadZone.style.borderColor = 'var(--lime)';
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Drag and drop events
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadZone.style.borderColor = 'var(--lime)';
                    uploadZone.style.background = 'var(--lime-dim)';
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                uploadZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    if (!fileInput.files.length) {
                        uploadZone.style.borderColor = 'var(--border)';
                        uploadZone.style.background = 'var(--surface-alt)';
                    }
                }, false);
            });
        }

        // Confetti script for confirmation/success
        const canvas = document.getElementById('confetti-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            const colors = ['#BFFF00', '#FF5C3A', '#F5F5F0', '#6B6B6B'];
            const particles = [];
            
            for (let i = 0; i < 120; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height - canvas.height,
                    size: Math.random() * 8 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    speed: Math.random() * 3 + 2,
                    angle: Math.random() * Math.PI * 2,
                    rotationSpeed: Math.random() * 0.2 - 0.1
                });
            }
            
            let frame = 0;
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                let active = false;
                
                particles.forEach(p => {
                    p.y += p.speed;
                    p.x += Math.sin(p.angle) * 0.5;
                    p.angle += p.rotationSpeed;
                    
                    if (p.y < canvas.height) {
                        active = true;
                        ctx.fillStyle = p.color;
                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate(p.angle);
                        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
                        ctx.restore();
                    }
                });
                
                if (active && frame < 200) {
                    frame++;
                    requestAnimationFrame(animate);
                } else {
                    canvas.style.display = 'none';
                }
            }
            
            animate();
            
            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        }
    });
    </script>
    @endpush
@endsection