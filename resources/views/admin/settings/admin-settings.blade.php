@extends('admin.layouts.shell', ['pageTitle' => 'Settings', 'active' => 'settings'])

@section('content')
    <div class="settings-page">

        {{-- Page Header --}}
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Configuration</p>
                <h1>Settings</h1>
                <p class="settings-subtitle">Manage your facility information and operational preferences.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="settings-alert settings-alert-success">
                <i class="ti ti-circle-check-filled"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="settings-alert settings-alert-error">
                <i class="ti ti-alert-circle-filled"></i>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.store') }}" method="POST" id="settings-form">
            @csrf

            <div class="settings-grid">

                {{-- Facility Information --}}
                <section class="settings-card">
                    <div class="settings-card-head">
                        <span class="settings-card-icon"><i class="ti ti-building"></i></span>
                        <div>
                            <h2>Facility Information</h2>
                            <p>Basic details about your sports facility.</p>
                        </div>
                    </div>
                    <div class="settings-card-body">

                        <div class="settings-field">
                            <label for="facility_name" class="settings-label">Facility Name</label>
                            <p class="settings-hint">Displayed on all public pages and receipts.</p>
                            <input type="text" id="facility_name" name="facility_name"
                                value="{{ old('facility_name', $settings['facility_name'] ?? 'CourtConnect') }}"
                                placeholder="e.g. CourtConnect Sports Center" required>
                        </div>

                        <div class="settings-field">
                            <label for="contact_email" class="settings-label">Contact Email</label>
                            <p class="settings-hint">Used for booking confirmations sent to users.</p>
                            <input type="email" id="contact_email" name="contact_email"
                                value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                placeholder="hello@yourfacility.com" required>
                        </div>

                        <div class="settings-field">
                            <label for="contact_phone" class="settings-label">Contact Phone</label>
                            <p class="settings-hint">Shown on customer-facing pages and receipts.</p>
                            <input type="text" id="contact_phone" name="contact_phone"
                                value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                placeholder="+63 900 000 0000">
                        </div>

                    </div>
                </section>

                {{-- Operations --}}
                <section class="settings-card">
                    <div class="settings-card-head">
                        <span class="settings-card-icon settings-card-icon-alt"><i class="ti ti-clock"></i></span>
                        <div>
                            <h2>Operations</h2>
                            <p>Hours of operation and booking policies.</p>
                        </div>
                    </div>
                    <div class="settings-card-body">

                        <div class="settings-field">
                            <label for="operating_hours" class="settings-label">Operating Hours</label>
                            <p class="settings-hint">e.g. Mon–Sun, 6:00 AM – 10:00 PM</p>
                            <input type="text" id="operating_hours" name="operating_hours"
                                value="{{ old('operating_hours', $settings['operating_hours'] ?? '') }}"
                                placeholder="Mon–Sun, 6:00 AM – 10:00 PM" required>
                        </div>

                        <div class="settings-field">
                            <label for="reservation_rules" class="settings-label">Reservation Rules</label>
                            <p class="settings-hint">Policy text shown to users during the booking flow.</p>
                            <textarea id="reservation_rules" name="reservation_rules" rows="6"
                                placeholder="e.g. Cancellations must be made 24 hours in advance. No-shows forfeit their booking fee."
                            >{{ old('reservation_rules', $settings['reservation_rules'] ?? '') }}</textarea>
                        </div>

                    </div>
                </section>

            </div>{{-- .settings-grid --}}

            {{-- Save Bar --}}
            <div class="settings-save-bar">
                <p class="settings-save-note">
                    <i class="ti ti-info-circle"></i>
                    Changes will take effect immediately across all pages.
                </p>
                <button type="submit" class="btn btn-primary" id="settings-save-btn">
                    <i class="ti ti-device-floppy"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <style>
        /* ── Layout ──────────────────────────────────────────── */
        .settings-page {
            display: grid;
            gap: 24px;
        }

        .settings-subtitle {
            margin: 4px 0 0;
            color: var(--muted-mid);
            font-size: 13px;
        }

        /* Two-column card grid on wide screens */
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 860px) {
            .settings-grid { grid-template-columns: 1fr; }
        }

        /* ── Alerts ──────────────────────────────────────────── */
        .settings-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 18px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 600;
        }
        .settings-alert .ti { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .settings-alert ul  { margin: 0; padding-left: 16px; }
        .settings-alert li  { margin-bottom: 2px; }

        .settings-alert-success {
            background: rgba(191,255,0,0.07);
            border: 1px solid rgba(191,255,0,0.18);
            color: var(--lime);
        }
        .settings-alert-error {
            background: rgba(255,92,58,0.07);
            border: 1px solid rgba(255,92,58,0.18);
            color: var(--coral);
        }

        /* ── Card ────────────────────────────────────────────── */
        .settings-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            background: var(--surface-2);
            overflow: hidden;
        }

        .settings-card-head {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            background: var(--surface-3);
        }

        .settings-card-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 11px;
            background: var(--lime-dim);
            border: 1px solid rgba(191,255,0,0.15);
            display: grid;
            place-items: center;
            color: var(--lime);
        }

        .settings-card-icon-alt {
            background: rgba(59,130,246,0.08);
            border-color: rgba(59,130,246,0.15);
            color: #60a5fa;
        }

        .settings-card-icon .ti { font-size: 19px; }

        .settings-card-head h2 {
            margin: 0 0 3px;
            color: var(--text);
            font-family: var(--display-font);
            font-size: 18px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            line-height: 1;
        }

        .settings-card-head p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        .settings-card-body {
            padding: 22px;
            display: grid;
            gap: 20px;
        }

        /* ── Fields ──────────────────────────────────────────── */
        .settings-field {
            display: grid;
            gap: 5px;
        }

        label.settings-label {
            display: block;
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: none;
            margin: 0;
        }

        .settings-hint {
            margin: 0;
            color: var(--muted);
            font-size: 11px;
            font-weight: 500;
            text-transform: none !important;
            letter-spacing: 0;
        }

        .settings-field input,
        .settings-field textarea {
            display: block;
            width: 100%;
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: var(--ui-font);
            font-size: 14px;
            font-weight: 500;
            padding: 10px 14px;
            transition: border-color var(--transition), box-shadow var(--transition);
            resize: vertical;
        }

        .settings-field input:focus,
        .settings-field textarea:focus {
            outline: none;
            border-color: var(--lime);
            box-shadow: 0 0 0 3px rgba(191,255,0,0.07);
        }

        .settings-field input::placeholder,
        .settings-field textarea::placeholder {
            color: var(--muted);
        }

        /* ── Save Bar ────────────────────────────────────────── */
        .settings-save-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 22px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--surface-2);
            flex-wrap: wrap;
        }

        .settings-save-note {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 13px;
        }

        .settings-save-note .ti { color: var(--lime); font-size: 15px; }
    </style>
@endsection
