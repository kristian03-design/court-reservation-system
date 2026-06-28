@extends('user.layouts.shell', ['pageTitle' => 'Billing History'])

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <p class="dash-kicker" style="margin: 0 0 4px;">Billing Log</p>
            <h1 style="font-family: var(--font-display); font-size: 28px; font-weight: 800; color: var(--text-primary); margin: 0;">Payments</h1>
        </div>
        <a href="{{ route('booking.create') }}" class="btn btn-primary">Book a Court</a>
    </div>

    @php
        $payments = $payments ?? collect();
    @endphp

    @if ($payments->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i data-lucide="credit-card"></i>
            </div>
            <h3>No transaction records yet</h3>
            <p>Your billing logs and receipt verification statuses will appear here once you schedule a court slot.</p>
            <a href="{{ route('booking.create') }}" class="btn btn-primary" style="margin-top: 8px;">Book a Court</a>
        </div>
    @else
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reservation</th>
                            <th>Court Space</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td data-label="Reservation" style="font-weight: 700; color: var(--text-primary);">{{ $payment->reservation->reservation_number }}</td>
                                <td data-label="Court Space" style="font-weight: 500; color: var(--text-primary);">{{ $payment->reservation->court->court_name }}</td>
                                <td data-label="Method">
                                    <span style="font-size: 13px; text-transform: capitalize; color: var(--text-secondary);">
                                        {{ str_replace('_', ' ', $payment->payment_method ?? 'pay at venue') }}
                                    </span>
                                </td>
                                <td data-label="Amount" style="color: var(--primary); font-weight: 700;">PHP {{ number_format($payment->amount, 2) }}</td>
                                <td data-label="Status">
                                    <span class="status status-{{ $payment->payment_status }}">{{ str_replace('_', ' ', $payment->payment_status) }}</span>
                                </td>
                                <td data-label="Date">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td data-label="Action" style="text-align:right;">
                                    <a href="{{ route('reservations.show', $payment->reservation) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; border-radius: var(--radius-sm);">View Ticket</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($payments, 'links'))
            <div class="pagination-row">
                {{ $payments->links() }}
            </div>
        @endif
    @endif
@endsection