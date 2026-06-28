@extends('admin.layouts.shell', ['pageTitle' => 'Reservation Details', 'active' => 'reservations'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Reservation Management</p>
            <h1>{{ $reservation->reservation_number }}</h1>
        </div>
        <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline">
            <i class="ti ti-arrow-left" aria-hidden="true" style="font-size:14px;"></i>
            Back to List
        </a>
    </div>

    <!-- Card: Reservation Details -->
    <div class="card card-large mt-6">
        <div class="card-details-grid">
            <!-- Column 1: Booking Info -->
            <div class="card-details-col">
                <h2>Booking Info</h2>
                <div class="mt-4 grid gap-3 text-sm">
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Court</strong>{{ $reservation->court->court_name }}</div>
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Date</strong>{{ $reservation->reservation_date->format('l, F j, Y') }}</div>
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Time</strong>{{ substr($reservation->start_time,0,5) }} to {{ substr($reservation->end_time,0,5) }}</div>
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Players</strong>{{ $reservation->players }} {{ \Illuminate\Support\Str::plural('Player', $reservation->players) }}</div>
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Status</strong><span class="status status-{{ $reservation->status }} mt-1">{{ $reservation->status }}</span></div>
                </div>
            </div>

            <!-- Column 2: Customer Info -->
            <div class="card-details-col">
                <h2>Customer Info</h2>
                <div class="mt-4 grid gap-3 text-sm">
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Name</strong>{{ $reservation->user->name ?? 'N/A' }}</div>
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Email</strong>{{ $reservation->user->email ?? 'N/A' }}</div>
                    <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Contact</strong>{{ $reservation->user->phone ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Column 3: Payment Info -->
            <div class="card-details-col">
                <h2>Payment Info</h2>
                @if($reservation->payment)
                    <div class="mt-4 grid gap-3 text-sm">
                        <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Method</strong><span style="text-transform:uppercase;">{{ $reservation->payment->payment_method }}</span></div>
                        <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Status</strong><span class="status status-{{ $reservation->payment->payment_status }} mt-1">{{ $reservation->payment->payment_status }}</span></div>
                        <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Amount</strong>PHP {{ number_format($reservation->payment->amount, 2) }}</div>
                        @if($reservation->payment->reference_number)
                            <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Reference #</strong>{{ $reservation->payment->reference_number }}</div>
                        @endif
                    </div>
                @else
                    <p style="color: var(--muted); margin-top: 16px; font-size: 13px;">No payment record found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes Card (if present) -->
    @if($reservation->notes)
        <div class="card mt-6">
            <h2>Special Requests</h2>
            <div class="mt-4 p-4" style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius); font-size: 13px; color: var(--muted-mid);">
                {{ $reservation->notes }}
            </div>
        </div>
    @endif

    <!-- Proof of Payment Card -->
    @if($reservation->payment && $reservation->payment->proof_image)
        <div class="card mt-6">
            <h2>Proof of Payment</h2>
            <div class="mt-4">
                <a href="{{ route('admin.payments.proof.show', $reservation->payment) }}" target="_blank">
                    <img src="{{ route('admin.payments.proof.show', $reservation->payment) }}" alt="Proof of Payment" style="max-height: 320px; border-radius: var(--radius); border: 1px solid var(--border); object-fit: contain; background: var(--surface-2); padding: 8px;">
                </a>
                <p style="color: var(--muted); font-size: 11px; margin-top: 8px;">Click image to view full size.</p>
            </div>
        </div>
    @endif

    <!-- Manage Status Actions Card -->
    <div class="card card-compact ml-auto mt-6">
        <h2>Manage Reservation</h2>
        <p style="color: var(--muted); font-size: 13px; margin: 4px 0 16px;">Perform status action updates below.</p>
        
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            @if($reservation->status === 'pending' || $reservation->status === 'under_review' || $reservation->status === 'rejected')
                @if($reservation->payment && $reservation->payment->payment_method === 'pay_at_venue')
                    <form action="{{ route('admin.payments.update', $reservation->payment) }}" method="POST" onsubmit="return confirm('Confirm payment received at venue?');">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-primary" style="background: var(--lime); color: var(--bg); font-weight: 700; border: none; padding: 8px 16px; border-radius: var(--radius);">Mark Paid & Confirm</button>
                    </form>

                    <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST" onsubmit="return confirm('Are you sure you want to CANCEL this reservation?');">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-outline" style="border-color: var(--coral); color: var(--coral) !important; padding: 8px 16px; border-radius: var(--radius);">Cancel Reservation</button>
                    </form>
                @else
                    @if($reservation->status === 'under_review')
                        <a href="{{ route('admin.payments.show', $reservation->payment) }}" class="btn btn-primary" style="background: var(--lime); color: var(--bg); font-weight: 700; border: none; padding: 8px 16px; border-radius: var(--radius); text-decoration: none;">Review Payment Proof</a>
                    @elseif($reservation->status === 'pending')
                        <span style="color: var(--muted); font-size: 13px;">Awaiting payment proof from player.</span>
                    @elseif($reservation->status === 'rejected')
                        <span style="color: var(--coral); font-size: 13px; font-weight: 700;">Payment proof rejected. Awaiting re-upload.</span>
                    @endif

                    <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST" onsubmit="return confirm('Are you sure you want to CANCEL this reservation?');">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-outline" style="border-color: var(--coral); color: var(--coral) !important; padding: 8px 16px; border-radius: var(--radius);">Cancel Reservation</button>
                    </form>
                @endif
            @elseif($reservation->status === 'approved')
                <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST" onsubmit="return confirm('Mark this reservation session as COMPLETED?');">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-primary" style="background: var(--lime); color: var(--bg); font-weight: 700; border: none; padding: 8px 16px; border-radius: var(--radius);">Complete Session</button>
                </form>

                <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST" onsubmit="return confirm('Are you sure you want to CANCEL this reservation?');">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-outline" style="border-color: var(--coral); color: var(--coral) !important; padding: 8px 16px; border-radius: var(--radius);">Cancel Reservation</button>
                </form>
            @else
                <p style="color: var(--muted); font-size: 13px; margin: 0;">No status actions available for <span class="status status-{{ $reservation->status }}">{{ $reservation->status }}</span> reservations.</p>
            @endif
        </div>
    </div>

    {{-- Approve Confirmation Modal --}}
    <div class="confirm-modal-backdrop" id="approve-confirm-modal-backdrop">
        <div class="confirm-modal" id="approve-confirm-modal">
            <div class="confirm-modal-icon" style="background: rgba(191,255,0,0.1); border: 1px solid rgba(191,255,0,0.2); color: var(--lime); display: grid; place-items: center; margin: 0 auto 18px; width: 52px; height: 52px; border-radius: 50%;">
                <span style="display: flex; align-items: center; justify-content: center;"><i class="ti ti-circle-check" style="font-size: 24px;"></i></span>
            </div>
            <div class="confirm-modal-body">
                <h3 style="margin: 0 0 8px; color: var(--text); font-family: var(--display-font); font-size: 26px; letter-spacing: 0.06em; text-transform: uppercase;">Approve Booking?</h3>
                <p style="margin: 0; color: var(--muted-mid); font-size: 13px; line-height: 1.6;">Are you sure you want to approve this reservation? A confirmation email will be sent to the customer.</p>
            </div>
            <div class="confirm-modal-actions" style="display: flex; gap: 10px; margin-top: 22px;">
                <button type="button" class="confirm-cancel-btn" id="approve-confirm-cancel" style="flex: 1; min-height: 42px; border: 1px solid var(--border); border-radius: var(--radius); background: transparent; color: var(--text); font-family: var(--ui-font); font-size: 13px; font-weight: 700; cursor: pointer; transition: all var(--transition);">
                    Cancel
                </button>
                <button type="button" class="confirm-submit-btn" id="approve-confirm-submit" style="flex: 1; min-height: 42px; border: 1px solid var(--lime); border-radius: var(--radius); background: var(--lime); color: var(--bg); font-family: var(--ui-font); font-size: 13px; font-weight: 700; cursor: pointer; transition: all var(--transition);">
                    Yes, Approve
                </button>
            </div>
        </div>
    </div>

    {{-- Reject Confirmation Modal --}}
    <div class="confirm-modal-backdrop" id="reject-confirm-modal-backdrop">
        <div class="confirm-modal" id="reject-confirm-modal">
            <div class="confirm-modal-icon" style="background: rgba(255,92,58,0.1); border: 1px solid rgba(255,92,58,0.2); color: var(--coral); display: grid; place-items: center; margin: 0 auto 18px; width: 52px; height: 52px; border-radius: 50%;">
                <span style="display: flex; align-items: center; justify-content: center;"><i class="ti ti-circle-x" style="font-size: 24px;"></i></span>
            </div>
            <div class="confirm-modal-body">
                <h3 style="margin: 0 0 8px; color: var(--text); font-family: var(--display-font); font-size: 26px; letter-spacing: 0.06em; text-transform: uppercase;">Reject Booking?</h3>
                <p style="margin: 0; color: var(--muted-mid); font-size: 13px; line-height: 1.6;">Are you sure you want to reject this reservation? A status update will be sent to the customer.</p>
            </div>
            <div class="confirm-modal-actions" style="display: flex; gap: 10px; margin-top: 22px;">
                <button type="button" class="confirm-cancel-btn" id="reject-confirm-cancel" style="flex: 1; min-height: 42px; border: 1px solid var(--border); border-radius: var(--radius); background: transparent; color: var(--text); font-family: var(--ui-font); font-size: 13px; font-weight: 700; cursor: pointer; transition: all var(--transition);">
                    Cancel
                </button>
                <button type="button" class="confirm-submit-btn" id="reject-confirm-submit" style="flex: 1; min-height: 42px; border: 1px solid var(--coral); border-radius: var(--radius); background: var(--coral); color: #fff; font-family: var(--ui-font); font-size: 13px; font-weight: 700; cursor: pointer; transition: all var(--transition);">
                    Yes, Reject
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const approveBackdrop = document.getElementById('approve-confirm-modal-backdrop');
        const approveTrigger  = document.getElementById('btn-trigger-approve');
        const approveCancel   = document.getElementById('approve-confirm-cancel');
        const approveSubmit   = document.getElementById('approve-confirm-submit');
        const approveForm     = document.getElementById('approve-form');

        const rejectBackdrop = document.getElementById('reject-confirm-modal-backdrop');
        const rejectTrigger  = document.getElementById('btn-trigger-reject');
        const rejectCancel   = document.getElementById('reject-confirm-cancel');
        const rejectSubmit   = document.getElementById('reject-confirm-submit');
        const rejectForm     = document.getElementById('reject-form');

        // Approve triggers
        if (approveTrigger && approveBackdrop) {
            approveTrigger.addEventListener('click', function () {
                approveBackdrop.classList.add('active');
            });
        }
        if (approveCancel && approveBackdrop) {
            approveCancel.addEventListener('click', function () {
                approveBackdrop.classList.remove('active');
            });
        }
        if (approveSubmit && approveForm) {
            approveSubmit.addEventListener('click', function () {
                approveForm.submit();
            });
        }

        // Reject triggers
        if (rejectTrigger && rejectBackdrop) {
            rejectTrigger.addEventListener('click', function () {
                rejectBackdrop.classList.add('active');
            });
        }
        if (rejectCancel && rejectBackdrop) {
            rejectCancel.addEventListener('click', function () {
                rejectBackdrop.classList.remove('active');
            });
        }
        if (rejectSubmit && rejectForm) {
            rejectSubmit.addEventListener('click', function () {
                rejectForm.submit();
            });
        }

        // Close on click backdrop
        [approveBackdrop, rejectBackdrop].forEach(function (backdrop) {
            if (backdrop) {
                backdrop.addEventListener('click', function (e) {
                    if (e.target === backdrop) {
                        backdrop.classList.remove('active');
                    }
                });
            }
        });

        // Close on Escape keypress
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (approveBackdrop) approveBackdrop.classList.remove('active');
                if (rejectBackdrop) rejectBackdrop.classList.remove('active');
            }
        });
    });
    </script>
    @endpush
@endsection
