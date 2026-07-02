@extends('admin.layouts.shell', ['pageTitle' => 'Events', 'active' => 'events'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Facility Management</p>
            <h1>Events Management</h1>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn btn-secondary">
            <i class="ti ti-plus" aria-hidden="true" style="font-size:14px;"></i>
            Add Event
        </a>
    </div>

    {{-- Stats Cards --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin: 24px 0;">
        <div style="background:var(--surface); border:1px solid var(--border); padding:20px; border-radius:12px;">
            <p style="margin:0; font-size:11px; text-transform:uppercase; color:var(--muted); font-weight:700;">Total Events</p>
            <h2 style="margin:8px 0 0; font-size:28px; font-weight:700; color:#fff; font-family:var(--display-font);">{{ $stats['total_events'] }}</h2>
        </div>
        <div style="background:var(--surface); border:1px solid var(--border); padding:20px; border-radius:12px;">
            <p style="margin:0; font-size:11px; text-transform:uppercase; color:var(--muted); font-weight:700;">Active Events</p>
            <h2 style="margin:8px 0 0; font-size:28px; font-weight:700; color:var(--lime); font-family:var(--display-font);">{{ $stats['active_events'] }}</h2>
        </div>
        <div style="background:var(--surface); border:1px solid var(--border); padding:20px; border-radius:12px;">
            <p style="margin:0; font-size:11px; text-transform:uppercase; color:var(--muted); font-weight:700;">Total Registrations</p>
            <h2 style="margin:8px 0 0; font-size:28px; font-weight:700; color:#fff; font-family:var(--display-font);">{{ $stats['total_registrants'] }}</h2>
        </div>
        <div style="background:var(--surface); border:1px solid var(--border); padding:20px; border-radius:12px;">
            <p style="margin:0; font-size:11px; text-transform:uppercase; color:var(--muted); font-weight:700;">Total Revenue</p>
            <h2 style="margin:8px 0 0; font-size:28px; font-weight:700; color:var(--lime); font-family:var(--display-font);">₱{{ number_format($stats['total_revenue'], 2) }}</h2>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <form method="GET" action="{{ route('admin.events.index') }}" style="display:flex; flex:1; flex-wrap:wrap; gap:10px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search event title, sport..." style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:8px; padding:8px 12px; color:#fff; font-size:13px; min-width:200px; flex:1;">
            
            <select name="status" style="background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:8px 12px; color:#fff; font-size:13px; cursor:pointer;">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="sport" style="background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:8px 12px; color:#fff; font-size:13px; cursor:pointer;">
                <option value="">All Sports</option>
                @foreach(\App\Models\Event::select('sport')->distinct()->pluck('sport') as $sportName)
                    <option value="{{ $sportName }}" {{ request('sport') === $sportName ? 'selected' : '' }}>{{ $sportName }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-outline" style="padding:8px 16px; font-size:13px;">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'sport']))
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline" style="padding:8px 16px; font-size:13px; color:#ef4444; border-color:rgba(239,68,68,0.2); text-decoration:none; display:inline-flex; align-items:center;">Clear</a>
            @endif
        </form>
    </div>

    {{-- Events List Table --}}
    <section>
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Image</th>
                            <th>Event Details</th>
                            <th>Category & Type</th>
                            <th>Price</th>
                            <th>Capacity</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            <tr>
                                <td data-label="Image">
                                    @if($event->image)
                                        <img src="{{ asset($event->image) }}" alt="{{ $event->title }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                    @else
                                        <div style="width: 48px; height: 48px; background: var(--surface-3); border-radius: 6px; display:flex; align-items:center; justify-content:center; color:var(--muted);">
                                            <i class="ti ti-photo" style="font-size:20px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td data-label="Event Details">
                                    <strong style="color:#fff;">{{ $event->title }}</strong>
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $event->location }}</div>
                                </td>
                                <td data-label="Category & Type">
                                    <span style="color:var(--lime); font-weight:600; font-size:12px;">{{ $event->sport }}</span>
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $event->event_type }}</div>
                                </td>
                                <td data-label="Price" style="font-weight:700;">
                                    @if($event->price > 0)
                                        ₱{{ number_format($event->price, 2) }}
                                    @else
                                        <span style="color:var(--lime);">Free</span>
                                    @endif
                                </td>
                                <td data-label="Capacity">
                                    <span style="font-weight:600; color:#fff;">{{ $event->registered }}</span> / {{ $event->max_slots }} spots
                                </td>
                                <td data-label="Start Date">
                                    {{ $event->start_date->format('M d, Y') }}
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ date('g:i A', strtotime($event->start_time)) }}</div>
                                </td>
                                <td data-label="Status">
                                    <span class="status status-{{ $event->status === 'open' ? 'approved' : ($event->status === 'draft' ? 'pending' : 'rejected') }}">
                                        {{ $event->status }}
                                    </span>
                                </td>
                                <td style="text-align: right;" data-label="Action">
                                    <div style="display:flex; gap: 8px; justify-content: flex-end; align-items:center;">
                                        <a href="{{ route('admin.events.registrants', $event) }}" class="btn btn-outline btn-sm" style="color:#fff; border-color:var(--border);">
                                            Registrants ({{ $event->registrations()->count() }})
                                        </a>

                                        <form action="{{ route('admin.events.publish', $event) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                {{ $event->published ? 'Unpublish' : 'Publish' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.events.duplicate', $event) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm" title="Duplicate Event">
                                                Duplicate
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline btn-sm">Edit</a>
                                        
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event? This will delete all registrations.')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" style="background:#ef4444; border:none;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" style="text-align:center;padding:32px;">No events found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($events->hasPages())
                <div style="padding: 16px; border-top: 1px solid var(--border);">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
