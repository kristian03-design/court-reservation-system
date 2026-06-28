@extends('admin.layouts.shell', ['pageTitle' => 'Payment Details', 'active' => 'payments'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Financial</p>
            <h1>Payment Details</h1>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline">
            <i class="ti ti-arrow-left" aria-hidden="true" style="font-size:14px;"></i>
            Back to Payments
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2 mt-6">
        <div class="card">
            <h2>Payment Summary</h2>
            <div class="mt-4 grid gap-3 text-sm">
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Status</strong><span class="status status-{{ $payment->payment_status }} mt-1">{{ $payment->payment_status }}</span></div>
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Amount</strong>PHP {{ number_format($payment->amount, 2) }}</div>
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Method</strong><span style="text-transform:uppercase;">{{ $payment->payment_method }}</span></div>
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Reference Number</strong>{{ $payment->reference_number ?? 'Not Provided' }}</div>
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Date Submitted</strong>{{ $payment->created_at->format('l, F j, Y h:i A') }}</div>
            </div>

            @if($payment->payment_status !== 'paid')
                <div class="mt-6 pt-4 border-t border-gray-200" style="display: flex; gap: 12px;">
                    <form action="{{ route('admin.payments.update', $payment) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-secondary w-full" style="width:100%; border: none; font-weight: 700; border-radius: var(--radius); padding: 8px 16px;">Approve & Mark Paid</button>
                    </form>
                    @if($payment->payment_status !== 'rejected')
                        <button type="button" id="btn-trigger-reject-payment" class="btn btn-outline" style="flex: 1; border-color: var(--coral); color: var(--coral) !important; border-radius: var(--radius); padding: 8px 16px;">Reject Proof</button>
                    @endif
                </div>
            @endif
        </div>

        <div class="card">
            <h2>Sender & Reservation Info</h2>
            <div class="mt-4 grid gap-3 text-sm">
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Sender Name</strong>{{ $payment->reservation->user->name ?? 'N/A' }}</div>
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Sender Email</strong>{{ $payment->reservation->user->email ?? 'N/A' }}</div>
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Reservation #</strong><a href="{{ route('admin.reservations.show', $payment->reservation_id) }}" style="color:var(--lime);font-weight:600;">{{ $payment->reservation->reservation_number }}</a></div>
                <div><strong class="block text-xs text-slate-500 uppercase tracking-wide">Court</strong>{{ $payment->reservation->court->court_name }}</div>
            </div>
        </div>

        <div class="card md:col-span-2">
            <h2>Proof of Payment</h2>
            <div class="mt-4">
                @if($payment->proof_image)
                    <a href="{{ route('admin.payments.proof.show', $payment) }}" target="_blank">
                        <img src="{{ route('admin.payments.proof.show', $payment) }}" alt="Proof of Payment" style="max-width: 100%; border-radius: 8px; border: 1px solid var(--border);">
                    </a>
                    <p class="mt-2 text-xs text-slate-500">Click image to view full size.</p>
                @else
                    <div style="padding: 32px; text-align: center; background: rgba(255,255,255,0.02); border-radius: 8px; border: 1px dashed var(--border); color: var(--muted);">
                        No proof of payment uploaded.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Reject Payment Modal --}}
    <div class="confirm-modal-backdrop" id="reject-payment-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="confirm-modal" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); width: 100%; max-width: 440px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); position: relative; margin: 16px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: rgba(255,92,58,0.1); border: 1px solid rgba(255,92,58,0.2); color: var(--coral); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <i class="ti ti-circle-x" style="font-size: 24px;"></i>
                </div>
                <h3 style="margin: 0 0 6px; color: var(--text); font-family: var(--display-font); font-size: 24px; letter-spacing: 0.04em; text-transform: uppercase;">Reject Payment</h3>
                <p style="margin: 0; color: var(--muted-mid); font-size: 13px; line-height: 1.5;">Please state the reason for rejecting this payment proof. The player will see this reason and can re-upload their receipt.</p>
            </div>
            
            <form action="{{ route('admin.payments.update', $payment) }}" method="POST" id="reject-payment-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="reject">
                
                <div style="display: grid; gap: 6px; margin-bottom: 20px; text-align: left;">
                    <label style="font-size: 11px; font-weight: 700; color: var(--muted-mid); text-transform: uppercase; letter-spacing: 0.05em;">Reason for Rejection</label>
                    <textarea name="rejection_reason" required rows="3" placeholder="e.g. Reference number does not match, receipt is unreadable, incorrect amount sent..." style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; font-size: 13px; color: var(--text); outline: none; resize: none;"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn btn-outline" id="btn-cancel-reject-payment" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn" style="flex: 1; background: var(--coral); color: #fff; border: none; font-weight: 700;">Submit Rejection</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const rejectBackdrop = document.getElementById('reject-payment-modal-backdrop');
        const rejectTrigger  = document.getElementById('btn-trigger-reject-payment');
        const rejectCancel   = document.getElementById('btn-cancel-reject-payment');

        if (rejectTrigger && rejectBackdrop) {
            rejectTrigger.addEventListener('click', function () {
                rejectBackdrop.style.display = 'flex';
            });
        }
        if (rejectCancel && rejectBackdrop) {
            rejectCancel.addEventListener('click', function () {
                rejectBackdrop.style.display = 'none';
            });
        }
        if (rejectBackdrop) {
            rejectBackdrop.addEventListener('click', function (e) {
                if (e.target === rejectBackdrop) {
                    rejectBackdrop.style.display = 'none';
                }
            });
        }
    });
    </script>
    @endpush
@endsection
