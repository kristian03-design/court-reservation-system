@extends('admin.layouts.shell', ['pageTitle' => 'Courts', 'active' => 'courts'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Facility Management</p>
            <h1>Courts</h1>
        </div>
        <a href="{{ route('admin.courts.create') }}" class="btn btn-secondary">
            <i class="ti ti-plus" aria-hidden="true" style="font-size:14px;"></i>
            Add Court
        </a>
    </div>

    <section class="mt-6">
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Image</th>
                            <th>Court Name</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Rate / Hr</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courts as $court)
                            <tr>
                                <td data-label="Image">
                                    @if($court->image)
                                        <img src="{{ asset($court->image) }}" alt="{{ $court->court_name }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                    @else
                                        <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 6px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                                            <i class="ti ti-photo" style="font-size:20px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="font-semibold" data-label="Court Name">{{ $court->court_name }}</td>
                                <td data-label="Type">{{ $court->court_type }}</td>
                                <td data-label="Capacity">{{ $court->capacity }} pax</td>
                                <td data-label="Rate / Hr">PHP {{ number_format($court->hourly_rate, 2) }}</td>
                                <td data-label="Status"><span class="status status-{{ $court->status }}">{{ $court->status }}</span></td>
                                <td style="text-align: right;" data-label="Action">
                                    <div style="display:flex; gap: 8px; justify-content: flex-end;">
                                        <a href="{{ route('admin.courts.schedules.index', $court) }}" class="btn btn-outline btn-sm">Schedules</a>
                                        <a href="{{ route('admin.courts.edit', $court) }}" class="btn btn-outline btn-sm">Edit</a>
                                        <form action="{{ route('admin.courts.destroy', $court) }}" method="POST" class="delete-form" data-confirm="Are you sure you want to delete this court? This will delete all associated schedules and bookings." data-confirm-title="Delete Court">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;padding:32px;">No courts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($courts->hasPages())
                <div style="padding: 16px; border-top: 1px solid var(--border);">
                    {{ $courts->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
