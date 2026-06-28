@extends('user.layouts.shell', ['pageTitle' => 'Book a Court'])

@section('content')
    <div style="margin-bottom: 24px;">
        <p class="dash-kicker" style="margin: 0 0 4px;">Schedule Match</p>
        <h1 style="font-family: var(--font-display); font-size: 28px; font-weight: 800; color: var(--text-primary); margin: 0;">Book a Court</h1>
    </div>

    <div class="booking-shell">
        <!-- Left Side: Context / Preview -->
        <div class="booking-preview">
            <div class="booking-preview-bg">
                <img id="court-preview-image" src="{{ $court ? ($court->image ?: asset('images/courtconnect-multisport-hero.png')) : asset('images/courtconnect-multisport-hero.png') }}" alt="Court Preview">
            </div>

            <div class="booking-preview-content">
                <h1 style="margin:0;">Reserve Your Session</h1>
                <p style="margin-top: 8px; color: var(--text-secondary); line-height: 1.5; font-size: 14px;">Select your preferred court, date, and time slot. Rates adjust dynamically based on facility pricing rules.</p>
                <div class="booking-tags">
                    <span>Basketball</span>
                    <span>Volleyball</span>
                    <span>Badminton</span>
                    <span>Tennis</span>
                </div>

                <div class="court-preview-card" id="court-detail-card" style="display: {{ $court ? 'block' : 'none' }}">
                    <div class="court-preview-top">
                        <div class="court-preview-thumb">
                            <img id="court-card-image" src="{{ $court ? ($court->image ?: asset('images/courtconnect-multisport-hero.png')) : asset('images/courtconnect-multisport-hero.png') }}" alt="Court Thumbnail">
                        </div>
                        <div>
                            <p id="court-card-type">{{ $court?->court_type }}</p>
                            <h3 id="court-card-name" style="margin: 4px 0 0; color: var(--text-primary);">{{ $court?->court_name }}</h3>
                        </div>
                    </div>
                    <div class="court-preview-stats">
                        <div>
                            <span>Capacity</span>
                            <strong id="court-card-capacity">Up to {{ $court?->capacity }} players</strong>
                        </div>
                        <div>
                            <span>Hourly Rate</span>
                            <strong id="court-card-rate" style="color: var(--primary);">PHP {{ number_format($court?->hourly_rate ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="court-preview-card" id="court-empty-card" style="display: {{ $court ? 'none' : 'block' }}; text-align: center; padding: 24px;">
                    <p style="color: var(--text-muted); margin: 0 0 8px; font-size: 13px;">No court selected yet.</p>
                    <span style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">Choose a court on the form to view details</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="booking-form-panel">
            <div class="booking-form-header">
                <h2>Reservation Details</h2>
                <p>Fill out the fields below to finalize your booking.</p>
            </div>

            <form method="POST" action="{{ route('booking.store') }}" id="booking-form">
                @csrf

                <!-- Court Selection -->
                <div class="field-group">
                    <label class="field-label">Select Court Space</label>
                    <div class="select-wrap">
                        <select name="court_id" id="court-selector" required>
                            <option value="" disabled @selected(!$court)>Select a court</option>
                            @foreach ($courts as $item)
                                <option value="{{ $item->id }}" 
                                        data-rate="{{ $item->hourly_rate }}" 
                                        data-type="{{ $item->court_type }}" 
                                        data-capacity="{{ $item->capacity }}" 
                                        data-image="{{ $item->image ?: asset('images/courtconnect-multisport-hero.png') }}"
                                        @selected(old('court_id', $court?->id) == $item->id)>
                                    {{ $item->court_name }} (PHP {{ number_format($item->hourly_rate) }}/hr)
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down"></i>
                    </div>
                </div>

                <!-- Date Selection -->
                @php
                    $selectedDate = old('reservation_date', request('reservation_date', now()->toDateString()));
                    $selectedStartTime = old('start_time', request('start_time', '16:00'));
                @endphp
                <div class="field-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <label class="field-label" style="margin: 0;">Select Date</label>
                        <div style="display: inline-flex; align-items: center; gap: 6px;">
                            <span style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase;">Or Custom Date:</span>
                            <input type="date" id="custom-date-picker" value="{{ $selectedDate }}" min="{{ now()->toDateString() }}" style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 4px 8px; font-size: 12px; color: var(--text); outline: none; cursor: pointer;">
                        </div>
                    </div>
                    
                    <div class="pill-row" id="date-pills" style="display: flex !important; flex-wrap: wrap !important; gap: 8px !important; width: 100% !important; overflow: visible !important;">
                        @php
                            $inRange = false;
                            $days = [];
                            foreach(range(0, 6) as $i) {
                                $d = now()->addDays($i);
                                $days[] = $d->toDateString();
                                if ($d->toDateString() === $selectedDate) {
                                    $inRange = true;
                                }
                            }
                        @endphp

                        @foreach(range(0, 6) as $i)
                            @php $d = now()->addDays($i); @endphp
                            <button type="button" class="date-pill {{ $d->toDateString() === $selectedDate ? 'is-active' : '' }}" data-date="{{ $d->toDateString() }}">
                                <span>{{ $d->format('D') }}</span>
                                <strong>{{ $d->format('d') }}</strong>
                            </button>
                        @endforeach

                        @if (!$inRange)
                            <button type="button" class="date-pill is-active" data-date="{{ $selectedDate }}">
                                <span>{{ \Carbon\Carbon::parse($selectedDate)->format('D') }}</span>
                                <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('d') }}</strong>
                            </button>
                        @endif
                    </div>
                    <input type="hidden" name="reservation_date" id="reservation_date" value="{{ $selectedDate }}">
                </div>

                <!-- Time Selection -->
                <div class="field-group">
                    <label class="field-label">Select Time Slot</label>
                    <div class="time-grid" id="time-pills">
                        @php
                            $times = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];
                        @endphp
                        @foreach($times as $time)
                            <button type="button" class="time-pill {{ $time === $selectedStartTime ? 'is-active' : '' }}" data-time="{{ $time }}">
                                {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="start_time" id="start_time" value="{{ $selectedStartTime }}">
                    @php
                        $startHour = (int)explode(':', $selectedStartTime)[0];
                        $endHour = str_pad($startHour + 1, 2, '0', STR_PAD_LEFT);
                        $endTime = $endHour . ':' . explode(':', $selectedStartTime)[1];
                    @endphp
                    <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time', $endTime) }}">
                </div>

                <!-- Duration & Players Registered -->
                <div class="field-row-2">
                    <div class="field-group">
                        <label class="field-label">Duration</label>
                        <div class="select-wrap">
                            <select name="duration" id="duration-selector" required>
                                <option value="1" @selected(old('duration', 1) == 1)>1 Hour</option>
                                <option value="2" @selected(old('duration') == 2)>2 Hours</option>
                                <option value="3" @selected(old('duration') == 3)>3 Hours</option>
                                <option value="4" @selected(old('duration') == 4)>4 Hours</option>
                            </select>
                            <i data-lucide="chevron-down"></i>
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Players Registered</label>
                        <input type="number" name="players" min="1" max="30" value="{{ old('players', request('players', 2)) }}" required>
                    </div>
                </div>

                <!-- Notes -->
                <div class="field-group">
                    <label class="field-label">Special Requests (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Equipment rental requests, team arrangements, etc..." style="resize: none;">{{ old('notes') }}</textarea>
                </div>

                <!-- Price Preview Box -->
                <div class="price-summary-box">
                    <span class="price-summary-label">Estimated Total Amount</span>
                    <span class="price-summary-value" id="price-preview-val">PHP 0.00</span>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Reservation</button>
                    <p style="margin: 8px 0 0; text-align: center; color: var(--text-muted); font-size: 11px;">By confirming, you agree to our booking terms and cancellation policies.</p>
                </div>

                <!-- Booking Confirmation Modal (Inside Form) -->
                <input type="hidden" name="payment_method" id="real-payment-method" value="pay_at_venue">
                <div class="custom-modal-backdrop" id="booking-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
                    <div class="custom-modal" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); width: 100%; max-width: 460px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); position: relative; margin: 16px;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--lime-dim); border: 1px solid rgba(191, 255, 0, 0.25); color: var(--lime); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                <i data-lucide="calendar-check" style="width: 26px; height: 26px;"></i>
                            </div>
                            <h2 style="font-family: var(--font-display); font-size: 24px; font-weight: 800; text-transform: uppercase; margin: 0 0 6px; letter-spacing: 0.04em; color: var(--text);">Confirm Reservation</h2>
                            <p style="color: var(--muted-mid); font-size: 13px; margin: 0;">Please review your reservation details below.</p>
                        </div>

                        <div style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; margin-bottom: 20px; display: grid; gap: 12px;">
                            <div style="display: flex; justify-content: space-between; font-size: 13px; border-bottom: 1px dashed var(--border); padding-bottom: 8px;">
                                <span style="color: var(--muted); font-weight: 500;">Court Space</span>
                                <span id="modal-court-name" style="color: var(--text); font-weight: 700;">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; border-bottom: 1px dashed var(--border); padding-bottom: 8px;">
                                <span style="color: var(--muted); font-weight: 500;">Date</span>
                                <span id="modal-date" style="color: var(--text); font-weight: 700;">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; border-bottom: 1px dashed var(--border); padding-bottom: 8px;">
                                <span style="color: var(--muted); font-weight: 500;">Time Slot</span>
                                <span id="modal-time-slot" style="color: var(--text); font-weight: 700;">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; border-bottom: 1px dashed var(--border); padding-bottom: 8px;">
                                <span style="color: var(--muted); font-weight: 500;">Duration</span>
                                <span id="modal-duration" style="color: var(--text); font-weight: 700;">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; padding-top: 4px; align-items: center;">
                                <span style="color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em;">Total Amount</span>
                                <span id="modal-total-amount" style="color: var(--lime); font-size: 18px; font-weight: 700; font-family: var(--font-display);">-</span>
                            </div>
                        </div>

                        <!-- Payment Option inside Modal -->
                        <div style="margin-bottom: 20px;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">Select Payment Option</span>
                            <div class="payment-cards-grid" style="grid-template-columns: 1fr; gap: 8px;">
                                <label class="payment-card-option">
                                    <input type="radio" name="payment_type" id="pay-venue-radio" value="pay_at_venue" checked>
                                    <div class="payment-card-content" style="padding: 12px; gap: 10px;">
                                        <div class="payment-card-icon-wrap" style="width: 32px; height: 32px; border-radius: 6px;">
                                            <i data-lucide="wallet" style="width: 14px; height: 14px;"></i>
                                        </div>
                                        <div class="payment-card-text">
                                            <strong style="font-size: 13px; text-align: left;">Pay at Venue</strong>
                                            <span style="font-size: 10px; line-height: 1.3; text-align: left;">Pay on arrival via Cash or Card.</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="payment-card-option">
                                    <input type="radio" name="payment_type" id="online-radio" value="online">
                                    <div class="payment-card-content" style="padding: 12px; gap: 10px;">
                                        <div class="payment-card-icon-wrap" style="width: 32px; height: 32px; border-radius: 6px;">
                                            <i data-lucide="credit-card" style="width: 14px; height: 14px;"></i>
                                        </div>
                                        <div class="payment-card-text">
                                            <strong style="font-size: 13px; text-align: left;">Online Payment</strong>
                                            <span style="font-size: 10px; line-height: 1.3; text-align: left;">Pay via GCash, Maya, Card, or Bank.</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Online Sub-Options inside Modal (Collapsible) -->
                        <div id="online-sub-methods-wrap" style="display: none; margin-bottom: 20px;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Select Online Method</span>
                            <div class="online-sub-methods-grid" style="grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px;">
                                <label class="sub-method-card">
                                    <input type="radio" name="online_payment_method" class="sub-method-radio" value="gcash" checked>
                                    <div class="sub-method-content" style="padding: 6px 2px;">
                                        <span class="badge badge-gcash" style="font-size: 9px; padding: 2px 4px;">GCash</span>
                                    </div>
                                </label>
                                <label class="sub-method-card">
                                    <input type="radio" name="online_payment_method" class="sub-method-radio" value="maya">
                                    <div class="sub-method-content" style="padding: 6px 2px;">
                                        <span class="badge badge-maya" style="font-size: 9px; padding: 2px 4px;">Maya</span>
                                    </div>
                                </label>
                                <label class="sub-method-card">
                                    <input type="radio" name="online_payment_method" class="sub-method-radio" value="bank_transfer">
                                    <div class="sub-method-content" style="padding: 6px 2px;">
                                        <span class="badge badge-bank" style="font-size: 9px; padding: 2px 4px;">Bank</span>
                                    </div>
                                </label>
                                <label class="sub-method-card">
                                    <input type="radio" name="online_payment_method" class="sub-method-radio" value="card">
                                    <div class="sub-method-content" style="padding: 6px 2px;">
                                        <span class="badge badge-card" style="font-size: 9px; padding: 2px 4px;">Card</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <button type="button" class="btn btn-outline" id="btn-cancel-modal" style="width: 100%;">Go Back</button>
                            <button type="button" class="btn btn-primary" id="btn-confirm-modal" style="width: 100%;">Confirm & Book</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const courtSelector = document.getElementById('court-selector');
            const datePills = document.querySelectorAll('.date-pill');
            const dateInput = document.getElementById('reservation_date');
            const customDatePicker = document.getElementById('custom-date-picker');

            const timePills = document.querySelectorAll('.time-pill');
            const startInput = document.getElementById('start_time');
            const endInput = document.getElementById('end_time');
            const durationSelector = document.getElementById('duration-selector');

            // Dynamic duration options based on operating hours (ends at 22:00)
            const updateDurationOptions = () => {
                const startTime = startInput.value;
                if (!startTime) return;
                const startHour = parseInt(startTime.split(':')[0]);
                const maxHours = 22 - startHour; // Capped at 22:00

                Array.from(durationSelector.options).forEach(option => {
                    const val = parseInt(option.value);
                    if (val > maxHours) {
                        option.disabled = true;
                        option.text = `${val} ${val === 1 ? 'Hour' : 'Hours'} (Exceeds 10:00 PM)`;
                    } else {
                        option.disabled = false;
                        option.text = val === 1 ? '1 Hour' : `${val} Hours`;
                    }
                });

                // If currently selected duration is disabled, reset to 1 Hour
                if (durationSelector.options[durationSelector.selectedIndex].disabled) {
                    durationSelector.value = "1";
                }
            };

            const updateEndTime = () => {
                const startTime = startInput.value;
                if (!startTime) return;

                const duration = parseInt(durationSelector.value) || 1;
                const [hours, mins] = startTime.split(':');
                const endHours = String(parseInt(hours) + duration).padStart(2, '0');
                endInput.value = `${endHours}:${mins}`;
            };

            // Fetch availability from backend via AJAX
            const fetchAvailability = () => {
                const courtId = courtSelector.value;
                const selectedDate = dateInput.value;

                if (!courtId) {
                    return;
                }

                // Show loading state by lowering opacity on time pills
                timePills.forEach(pill => {
                    pill.classList.add('skeleton-loading');
                    pill.style.pointerEvents = 'none';
                });

                const url = `/courts/${courtId}/availability?date=${selectedDate}`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(slots => {
                        slots.forEach(slot => {
                            const timeVal = slot.start;
                            const pill = Array.from(timePills).find(p => p.dataset.time === timeVal);
                            if (pill) {
                                pill.classList.remove('skeleton-loading');
                                pill.style.pointerEvents = 'auto';
                                if (slot.available) {
                                    pill.disabled = false;
                                    pill.style.opacity = '1';
                                    pill.style.cursor = 'pointer';
                                    pill.style.background = '';
                                    pill.style.borderColor = '';
                                    pill.style.color = '';
                                } else {
                                    pill.disabled = true;
                                    pill.classList.remove('is-active');
                                    
                                    // Style for booked slots
                                    pill.style.opacity = '0.25';
                                    pill.style.cursor = 'not-allowed';
                                    pill.style.background = 'rgba(255, 92, 58, 0.05)';
                                    pill.style.borderColor = 'rgba(255, 92, 58, 0.15)';
                                    pill.style.color = 'var(--coral)';
                                }
                            }
                        });

                        // If currently selected slot is now disabled, clear it
                        const selectedActivePill = Array.from(timePills).find(p => p.classList.contains('is-active'));
                        if (selectedActivePill && selectedActivePill.disabled) {
                            selectedActivePill.classList.remove('is-active');
                            startInput.value = '';
                            endInput.value = '';
                        }

                        updateDurationOptions();
                        updateEndTime();
                        calculateTotal();
                    })
                    .catch(err => {
                        console.error('Error fetching availability:', err);
                        timePills.forEach(pill => {
                            pill.classList.remove('skeleton-loading');
                            pill.style.pointerEvents = 'auto';
                        });
                    });
            };

            const updateDateSelection = (selectedDate) => {
                dateInput.value = selectedDate;
                if (customDatePicker) {
                    customDatePicker.value = selectedDate;
                }

                let found = false;
                const originalPillsContainer = document.getElementById('date-pills');
                const pills = originalPillsContainer.querySelectorAll('.date-pill');
                
                pills.forEach((pill, idx) => {
                    if (idx < 7) {
                        if (pill.dataset.date === selectedDate) {
                            pill.classList.add('is-active');
                            found = true;
                        } else {
                            pill.classList.remove('is-active');
                        }
                    }
                });

                let customPill = pills[7] || null;
                if (!found) {
                    const dateObj = new Date(selectedDate);
                    const daysShort = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                    const dayName = daysShort[dateObj.getUTCDay()];
                    const dayNum = String(dateObj.getUTCDate()).padStart(2, '0');

                    if (!customPill) {
                        customPill = document.createElement('button');
                        customPill.type = 'button';
                        customPill.className = 'date-pill';
                        originalPillsContainer.appendChild(customPill);
                    }
                    customPill.dataset.date = selectedDate;
                    customPill.innerHTML = `<span>${dayName}</span><strong>${dayNum}</strong>`;
                    
                    pills.forEach(p => p.classList.remove('is-active'));
                    customPill.classList.add('is-active');
                    
                    // Explicit click listener for dynamic custom pill
                    customPill.onclick = () => {
                        updateDateSelection(selectedDate);
                    };
                } else {
                    if (customPill) {
                        customPill.remove();
                    }
                }

                fetchAvailability();
            };

            datePills.forEach(pill => {
                pill.addEventListener('click', () => {
                    updateDateSelection(pill.dataset.date);
                });
            });

            if (customDatePicker) {
                customDatePicker.addEventListener('change', (e) => {
                    const selectedDate = e.target.value;
                    if (selectedDate) {
                        updateDateSelection(selectedDate);
                    }
                });
            }

            timePills.forEach(pill => {
                pill.addEventListener('click', () => {
                    timePills.forEach(p => p.classList.remove('is-active'));
                    pill.classList.add('is-active');

                    const startTime = pill.dataset.time;
                    startInput.value = startTime;

                    updateDurationOptions();
                    updateEndTime();
                    calculateTotal();
                });
            });

            durationSelector.addEventListener('change', () => {
                updateEndTime();
                calculateTotal();
            });

            // Calculate initial duration on load if start and end are set
            if (startInput.value && endInput.value) {
                const startHour = parseInt(startInput.value.split(':')[0]);
                const endHour = parseInt(endInput.value.split(':')[0]);
                const diff = endHour - startHour;
                if (diff >= 1 && diff <= 4) {
                    durationSelector.value = String(diff);
                }
                updateDurationOptions();
            }

            // Dynamic preview & price calculator
            const courtDetailCard = document.getElementById('court-detail-card');
            const courtEmptyCard = document.getElementById('court-empty-card');
            
            const previewImage = document.getElementById('court-preview-image');
            const cardImage = document.getElementById('court-card-image');
            const cardName = document.getElementById('court-card-name');
            const cardType = document.getElementById('court-card-type');
            const cardCapacity = document.getElementById('court-card-capacity');
            const cardRate = document.getElementById('court-card-rate');
            const pricePreview = document.getElementById('price-preview-val');

            const calculateTotal = () => {
                const selectedOption = courtSelector.options[courtSelector.selectedIndex];
                if (!selectedOption || selectedOption.disabled || !selectedOption.value) {
                    pricePreview.textContent = 'PHP 0.00';
                    return;
                }

                const rate = parseFloat(selectedOption.dataset.rate);
                const startTime = startInput.value;
                const endTime = endInput.value;

                if (rate && startTime && endTime) {
                    const startHour = parseInt(startTime.split(':')[0]);
                    const endHour = parseInt(endTime.split(':')[0]);
                    const hours = Math.max(1, endHour - startHour);
                    const total = rate * hours;
                    pricePreview.textContent = `PHP ${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            };

            const updateCourtDetails = () => {
                const selectedOption = courtSelector.options[courtSelector.selectedIndex];
                if (!selectedOption || selectedOption.disabled || !selectedOption.value) {
                    courtDetailCard.style.display = 'none';
                    courtEmptyCard.style.display = 'block';
                    pricePreview.textContent = 'PHP 0.00';
                    return;
                }

                courtDetailCard.style.display = 'block';
                courtEmptyCard.style.display = 'none';

                const rate = parseFloat(selectedOption.dataset.rate);
                const type = selectedOption.dataset.type;
                const capacity = selectedOption.dataset.capacity;
                const image = selectedOption.dataset.image;
                const name = selectedOption.text.split('(')[0].trim();

                previewImage.src = image;
                cardImage.src = image;
                cardName.textContent = name;
                cardType.textContent = type;
                cardCapacity.textContent = `Up to ${capacity} players`;
                cardRate.textContent = `PHP ${rate.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}/hr`;
                
                calculateTotal();
            };

            courtSelector.addEventListener('change', () => {
                updateCourtDetails();
                fetchAvailability();
            });

            // Initial calculation
            updateCourtDetails();

            // Confirmation Modal Logic
            const bookingForm = document.getElementById('booking-form');
            const bookingModalBackdrop = document.getElementById('booking-modal-backdrop');
            const btnCancelModal = document.getElementById('btn-cancel-modal');
            const btnConfirmModal = document.getElementById('btn-confirm-modal');

            const modalCourtName = document.getElementById('modal-court-name');
            const modalDate = document.getElementById('modal-date');
            const modalTimeSlot = document.getElementById('modal-time-slot');
            const modalDuration = document.getElementById('modal-duration');
            const modalTotalAmount = document.getElementById('modal-total-amount');

            bookingForm.addEventListener('submit', (e) => {
                if (bookingForm.dataset.confirmed === 'true') {
                    return;
                }

                e.preventDefault();

                // Populate modal
                const courtOpt = courtSelector.options[courtSelector.selectedIndex];
                modalCourtName.textContent = courtOpt ? courtOpt.text.split('(')[0].trim() : '-';
                
                const dateVal = dateInput.value;
                if (dateVal) {
                    const dateObj = new Date(dateVal);
                    modalDate.textContent = dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
                } else {
                    modalDate.textContent = '-';
                }

                const startVal = startInput.value;
                const endVal = endInput.value;
                const formatTime = (timeStr) => {
                    if (!timeStr) return '';
                    const [h, m] = timeStr.split(':');
                    const hr = parseInt(h);
                    const ampm = hr >= 12 ? 'PM' : 'AM';
                    const displayHr = hr % 12 || 12;
                    return `${displayHr}:${m} ${ampm}`;
                };
                modalTimeSlot.textContent = `${formatTime(startVal)} - ${formatTime(endVal)}`;

                const durationVal = durationSelector.value;
                modalDuration.textContent = durationVal === '1' ? '1 Hour' : `${durationVal} Hours`;
                modalTotalAmount.textContent = pricePreview.textContent;

                bookingModalBackdrop.style.display = 'flex';
                
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });

            btnCancelModal.addEventListener('click', () => {
                bookingModalBackdrop.style.display = 'none';
            });

            btnConfirmModal.addEventListener('click', () => {
                bookingForm.dataset.confirmed = 'true';
                bookingForm.submit();
            });

            // Payment method selector script
            const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
            const onlineSubMethodsWrap = document.getElementById('online-sub-methods-wrap');
            const subMethodRadios = document.querySelectorAll('input[name="online_payment_method"]');
            const realPaymentMethodInput = document.getElementById('real-payment-method');

            const updatePaymentMethod = () => {
                const checkedTypeInput = document.querySelector('input[name="payment_type"]:checked');
                if (!checkedTypeInput) return;

                const selectedType = checkedTypeInput.value;
                if (selectedType === 'pay_at_venue') {
                    realPaymentMethodInput.value = 'pay_at_venue';
                    if (onlineSubMethodsWrap) onlineSubMethodsWrap.style.display = 'none';
                } else {
                    if (onlineSubMethodsWrap) onlineSubMethodsWrap.style.display = 'block';
                    const checkedSubInput = document.querySelector('input[name="online_payment_method"]:checked');
                    if (checkedSubInput) {
                        realPaymentMethodInput.value = checkedSubInput.value;
                    }
                }
            };

            paymentTypeRadios.forEach(radio => {
                radio.addEventListener('change', updatePaymentMethod);
            });

            subMethodRadios.forEach(radio => {
                radio.addEventListener('change', updatePaymentMethod);
            });

            // Run initial setup
            updatePaymentMethod();
            fetchAvailability();

            bookingModalBackdrop.addEventListener('click', (e) => {
                if (e.target === bookingModalBackdrop) {
                    bookingModalBackdrop.style.display = 'none';
                }
            });
        });
    </script>

    <style>
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 0.25; }
            100% { opacity: 0.6; }
        }
        .skeleton-loading {
            animation: pulse 1.2s infinite ease-in-out !important;
            background: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
            color: transparent !important;
        }
    </style>
@endsection