@extends('admin.layouts.shell', ['pageTitle' => 'Event Registrants', 'active' => 'events'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Event Registrants</p>
            <h1>{{ $event->title }}</h1>
            <p style="color:var(--muted); font-size:13px; margin:4px 0 0;">{{ $event->registered }} / {{ $event->max_slots }} spots filled.</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline">
            <i class="ti ti-arrow-left" style="font-size:14px;"></i>
            Back to Events
        </a>
    </div>

    {{-- Registrants Table --}}
    <section class="mt-6">
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Email & Phone</th>
                            <th>Payment Info</th>
                            <th>Proof of Payment</th>
                            <th>Payment Status</th>
                            <th>Reg Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registrations as $reg)
                            <tr>
                                <td data-label="User Name">
                                    <strong style="color:#fff;">{{ $reg->user->name }}</strong>
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">Registered on {{ $reg->created_at->format('M d, Y') }}</div>
                                </td>
                                <td data-label="Email & Phone">
                                    {{ $reg->user->email }}
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $reg->user->phone ?? 'N/A' }}</div>
                                </td>
                                <td data-label="Payment Info">
                                    <span style="text-transform:uppercase; font-size:11px; font-weight:700;">{{ str_replace('_', ' ', $reg->payment_method ?? 'N/A') }}</span>
                                    @if($reg->reference_number)
                                        <div style="font-size:11px; color:var(--muted); margin-top:2px;">Ref: {{ $reg->reference_number }}</div>
                                    @endif
                                </td>
                                <td data-label="Proof of Payment">
                                    @if($reg->proof_image)
                                        <a href="{{ asset($reg->proof_image) }}" target="_blank">
                                            <img src="{{ asset($reg->proof_image) }}" alt="Proof" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border:1px solid var(--border);">
                                        </a>
                                    @else
                                        <span style="color:var(--muted); font-size:12px;">None</span>
                                    @endif
                                </td>
                                <td data-label="Payment Status">
                                    <form action="{{ route('admin.registrations.update-payment', $reg) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                        @csrf
                                        <select name="payment_status" style="background:var(--surface-3); border:1px solid var(--border); border-radius:6px; padding:6px 10px; color:#fff; font-size:12px; cursor:pointer;">
                                            <option value="unpaid" @selected($reg->payment_status === 'unpaid')>Unpaid</option>
                                            <option value="pending_verification" @selected($reg->payment_status === 'pending_verification')>Verification</option>
                                            <option value="paid" @selected($reg->payment_status === 'paid')>Paid</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding:4px 8px; font-size:11px;">Update</button>
                                    </form>
                                </td>
                                <td data-label="Reg Status">
                                    <form action="{{ route('admin.registrations.update-status', $reg) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                        @csrf
                                        <select name="registration_status" style="background:var(--surface-3); border:1px solid var(--border); border-radius:6px; padding:6px 10px; color:#fff; font-size:12px; cursor:pointer;">
                                            <option value="pending" @selected($reg->registration_status === 'pending')>Pending</option>
                                            <option value="confirmed" @selected($reg->registration_status === 'confirmed')>Confirmed</option>
                                            <option value="waitlisted" @selected($reg->registration_status === 'waitlisted')>Waitlist</option>
                                            <option value="cancelled" @selected($reg->registration_status === 'cancelled')>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding:4px 8px; font-size:11px;">Update</button>
                                    </form>
                                </td>
                                <td style="text-align: right;" data-label="Action">
                                    <span class="status status-{{ $reg->registration_status === 'confirmed' ? 'approved' : ($reg->registration_status === 'cancelled' ? 'rejected' : 'pending') }}">
                                        {{ $reg->registration_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;padding:32px;">No registrants yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registrations->hasPages())
                <div style="padding: 16px; border-top: 1px solid var(--border);">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
