@extends('admin.layouts.shell', ['pageTitle' => 'Court Schedules', 'active' => 'courts'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Facility Management: {{ $court->court_name }}</p>
            <h1>Court Schedules & Blocking</h1>
        </div>
        <a href="{{ route('admin.courts.index') }}" class="btn btn-outline">
            <i class="ti ti-arrow-left" aria-hidden="true" style="font-size:14px;"></i>
            Back to Courts
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[320px_1fr] mt-6">
        
        {{-- Add Schedule Form --}}
        <div>
            <div class="card">
                <h2 style="font-size:14px; margin-bottom: 12px;">Add Blocked Schedule</h2>
                <form action="{{ route('admin.courts.schedules.store', $court) }}" method="POST" class="grid gap-3">
                    @csrf
                    
                    <label class="grid gap-1 text-sm">
                        Date
                        <input type="date" name="schedule_date" required min="{{ date('Y-m-d') }}">
                    </label>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="grid gap-1 text-sm">
                            Start Time
                            <input type="time" name="start_time" required>
                        </label>
                        <label class="grid gap-1 text-sm">
                            End Time
                            <input type="time" name="end_time" required>
                        </label>
                    </div>

                    <label class="grid gap-1 text-sm">
                        Status / Type
                        <select name="availability_status" required>
                            <option value="maintenance">Maintenance</option>
                            <option value="closed">Closed</option>
                            <option value="reserved">Reserved / Admin Block</option>
                        </select>
                    </label>

                    <label class="grid gap-1 text-sm">
                        Reason (Optional)
                        <input type="text" name="reason" placeholder="e.g., General cleaning">
                    </label>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-secondary w-full" style="width: 100%;">Add Schedule</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Existing Schedules Table --}}
        <div>
            <div class="table-wrap">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($schedules as $schedule)
                                <tr>
                                    <td class="font-semibold" data-label="Date">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('M d, Y') }}</td>
                                    <td data-label="Time">{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</td>
                                    <td data-label="Status"><span class="status status-{{ $schedule->availability_status }}">{{ $schedule->availability_status }}</span></td>
                                    <td style="color:var(--muted);" data-label="Reason">{{ $schedule->reason ?? '-' }}</td>
                                    <td style="text-align: right;" data-label="Action">
                                        <form action="{{ route('admin.courts.schedules.destroy', [$court, $schedule]) }}" method="POST" class="delete-form" data-confirm="Are you sure you want to remove this schedule block?" data-confirm-title="Remove Block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 24px;">No custom schedules blocked yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($schedules->hasPages())
                    <div style="padding: 16px; border-top: 1px solid var(--border);">
                        {{ $schedules->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
