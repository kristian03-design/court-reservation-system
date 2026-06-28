@extends('admin.layouts.shell', ['pageTitle' => 'Payments', 'active' => 'payments'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Financial</p>
            <h1>Payments</h1>
        </div>
    </div>

    <section class="mt-6">
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reference #</th>
                            <th>Reservation</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="font-semibold" data-label="Reference #">{{ $payment->reference_number ?? 'N/A' }}</td>
                                <td data-label="Reservation"><a href="{{ route('admin.reservations.show', $payment->reservation_id) }}" style="color:var(--lime);font-weight:600;">{{ $payment->reservation->reservation_number }}</a></td>
                                <td style="text-transform:uppercase;" data-label="Method">{{ $payment->payment_method }}</td>
                                <td data-label="Amount">PHP {{ number_format($payment->amount, 2) }}</td>
                                <td data-label="Status"><span class="status status-{{ $payment->payment_status }}">{{ $payment->payment_status }}</span></td>
                                <td data-label="Date">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td style="text-align: right;" data-label="Action">
                                    <div style="display:flex; gap: 8px; justify-content: flex-end;">
                                        <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-outline btn-sm">View Details</a>
                                        @if($payment->payment_status === 'pending_verification' || $payment->payment_status === 'pending')
                                            <form action="{{ route('admin.payments.update', $payment) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-secondary btn-sm">Mark Paid</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;padding:32px;">No payments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($payments->hasPages())
                <div style="padding: 16px; border-top: 1px solid var(--border);">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
