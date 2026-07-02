@extends('admin.layouts.shell', ['pageTitle' => 'Edit Event', 'active' => 'events'])

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Facility Management</p>
        <h1>Edit Event: {{ $event->title }}</h1>
    </div>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline">
        <i class="ti ti-arrow-left" style="font-size:14px;"></i>
        Back to Events
    </a>
</div>

<form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="court-form-grid" id="event-form">
    @csrf
    @method('PUT')

    {{-- Left Column: Main Info --}}
    <div class="court-form-main" style="display:flex; flex-direction:column; gap:20px;">
        <div class="cf-card" style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:24px;">
            <div class="cf-card-head" style="display:flex; align-items:center; gap:12px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:16px;">
                <span class="cf-card-icon" style="background:rgba(191,255,0,0.1); color:var(--lime); width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:18px;">
                    <i class="ti ti-calendar"></i>
                </span>
                <div>
                    <h3 style="margin:0; font-size:16px; color:#fff;">Event Details</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:var(--muted);">Basic information about this event</p>
                </div>
            </div>

            <div class="cf-field" style="margin-bottom:16px;">
                <label class="cf-label" for="title" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Event Title <span class="cf-required" style="color:#ef4444;">*</span></label>
                <input id="title" type="text" name="title" value="{{ old('title', $event->title) }}" placeholder="e.g. Weekend Social Mixer" class="cf-input @error('title') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box;" required>
                @error('title')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
            </div>

            <div class="cf-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="cf-field">
                    <label class="cf-label" for="sport" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Sport Category <span class="cf-required" style="color:#ef4444;">*</span></label>
                    <select id="sport" name="sport" class="cf-input @error('sport') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box; cursor:pointer;" required>
                        <option value="" disabled>Select sport</option>
                        @foreach(['Social Play', 'Tennis', 'Basketball', 'Badminton', 'Futsal', 'Volleyball', 'Padel'] as $sport)
                            <option value="{{ $sport }}" @selected(old('sport', $event->sport) == $sport)>{{ $sport }}</option>
                        @endforeach
                    </select>
                    @error('sport')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="event_type" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Event Type <span class="cf-required" style="color:#ef4444;">*</span></label>
                    <select id="event_type" name="event_type" class="cf-input @error('event_type') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box; cursor:pointer;" required>
                        <option value="" disabled>Select type</option>
                        @foreach(['Weekly Session', 'Skills Clinic', 'Training Camp', 'Open Play', 'League'] as $type)
                            <option value="{{ $type }}" @selected(old('event_type', $event->event_type) == $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('event_type')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="cf-field" style="margin-bottom:16px;">
                <label class="cf-label" for="description" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Description <span class="cf-required" style="color:#ef4444;">*</span></label>
                <textarea id="description" name="description" rows="5" class="cf-input @error('description') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box; font-family:inherit; resize:vertical;" placeholder="Write a comprehensive description about this event..." required>{{ old('description', $event->description) }}</textarea>
                @error('description')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="cf-card" style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:24px;">
            <div class="cf-card-head" style="display:flex; align-items:center; gap:12px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:16px;">
                <span class="cf-card-icon" style="background:rgba(191,255,0,0.1); color:var(--lime); width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:18px;">
                    <i class="ti ti-map-pin"></i>
                </span>
                <div>
                    <h3 style="margin:0; font-size:16px; color:#fff;">Date, Time & Location</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:var(--muted);">Set the schedule and venue</p>
                </div>
            </div>

            <div class="cf-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="cf-field">
                    <label class="cf-label" for="start_date" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Start Date <span class="cf-required" style="color:#ef4444;">*</span></label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d') : '') }}" class="cf-input @error('start_date') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box;" required>
                    @error('start_date')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="end_date" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">End Date (Optional)</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}" class="cf-input @error('end_date') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box;">
                    @error('end_date')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="cf-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="cf-field">
                    <label class="cf-label" for="start_time" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Start Time <span class="cf-required" style="color:#ef4444;">*</span></label>
                    <input id="start_time" type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}" class="cf-input @error('start_time') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box;" required>
                    @error('start_time')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="end_time" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">End Time <span class="cf-required" style="color:#ef4444;">*</span></label>
                    <input id="end_time" type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}" class="cf-input @error('end_time') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box;" required>
                    @error('end_time')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="cf-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="cf-field">
                    <label class="cf-label" for="location" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Venue Location <span class="cf-required" style="color:#ef4444;">*</span></label>
                    <input id="location" type="text" name="location" value="{{ old('location', $event->location) }}" placeholder="e.g. CourtConnect Main Arena" class="cf-input @error('location') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box;" required>
                    @error('location')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="court_id" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Associated Court (Optional)</label>
                    <select id="court_id" name="court_id" class="cf-input @error('court_id') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box; cursor:pointer;">
                        <option value="">No Court Assigned</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" @selected(old('court_id', $event->court_id) == $court->id)>{{ $court->court_name }} ({{ $court->court_type }})</option>
                        @endforeach
                    </select>
                    @error('court_id')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Side settings --}}
    <div class="court-form-side" style="display:flex; flex-direction:column; gap:20px;">
        <div class="cf-card" style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:24px;">
            <div class="cf-card-head" style="display:flex; align-items:center; gap:12px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:16px;">
                <span class="cf-card-icon" style="background:rgba(191,255,0,0.1); color:var(--lime); width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:18px;">
                    <i class="ti ti-settings"></i>
                </span>
                <div>
                    <h3 style="margin:0; font-size:16px; color:#fff;">Status & Settings</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:var(--muted);">Pricing, status, and capacities</p>
                </div>
            </div>

            <div class="cf-field" style="margin-bottom:16px;">
                <label class="cf-label" for="price" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Price (PHP) <span class="cf-required" style="color:#ef4444;">*</span></label>
                <div style="position:relative; display:flex; align-items:center;">
                    <span style="position:absolute; left:14px; color:var(--muted); font-size:14px; font-weight:600;">₱</span>
                    <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $event->price) }}" placeholder="0.00" class="cf-input @error('price') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px 10px 30px; color:#fff; box-sizing:border-box;" required>
                </div>
                @error('price')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
            </div>

            <div class="cf-field" style="margin-bottom:16px;">
                <label class="cf-label" for="max_slots" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Max Slots (Capacity) <span class="cf-required" style="color:#ef4444;">*</span></label>
                <input id="max_slots" type="number" min="1" name="max_slots" value="{{ old('max_slots', $event->max_slots) }}" placeholder="e.g. 20" class="cf-input @error('max_slots') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box;" required>
                @error('max_slots')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
            </div>

            <div class="cf-field" style="margin-bottom:16px;">
                <label class="cf-label" for="status" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Status <span class="cf-required" style="color:#ef4444;">*</span></label>
                <select id="status" name="status" class="cf-input @error('status') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:#fff; box-sizing:border-box; cursor:pointer;" required>
                    <option value="draft" @selected(old('status', $event->status) == 'draft')>Draft</option>
                    <option value="open" @selected(old('status', $event->status) == 'open')>Open</option>
                    <option value="closed" @selected(old('status', $event->status) == 'closed')>Closed</option>
                    <option value="completed" @selected(old('status', $event->status) == 'completed')>Completed</option>
                    <option value="cancelled" @selected(old('status', $event->status) == 'cancelled')>Cancelled</option>
                </select>
                @error('status')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
            </div>

            <div class="cf-field" style="margin-bottom:20px;">
                <label class="cf-label" for="image" style="display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;">Cover Image</label>
                @if($event->image)
                    <div style="margin-bottom:10px;">
                        <img src="{{ asset($event->image) }}" alt="Preview" style="width:100%; max-height:120px; object-fit:cover; border-radius:6px; border:1px solid var(--border);">
                    </div>
                @endif
                <input id="image" type="file" name="image" class="cf-input @error('image') is-error @enderror" style="width:100%; background:var(--surface-3); border:1px solid var(--border); border-radius:8px; padding:8px; color:#fff; box-sizing:border-box; font-size:12px;">
                @error('image')<span class="cf-error" style="color:#ef4444; font-size:11px; margin-top:4px; display:block;">{{ $message }}</span>@enderror
            </div>

            {{-- Toggle Toggles --}}
            <div style="display:flex; flex-direction:column; gap:12px; border-top:1px solid var(--border); padding-top:16px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:13px; color:#fff;">
                    <input type="checkbox" name="requires_payment" value="1" @checked(old('requires_payment', $event->requires_payment)) style="accent-color:var(--lime); width:16px; height:16px;">
                    <span>Requires Payment</span>
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:13px; color:#fff;">
                    <input type="checkbox" name="allow_waitlist" value="1" @checked(old('allow_waitlist', $event->allow_waitlist)) style="accent-color:var(--lime); width:16px; height:16px;">
                    <span>Allow Waitlist</span>
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:13px; color:#fff;">
                    <input type="checkbox" name="featured" value="1" @checked(old('featured', $event->featured)) style="accent-color:var(--lime); width:16px; height:16px;">
                    <span>Featured Event</span>
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:13px; color:#fff;">
                    <input type="checkbox" name="published" value="1" @checked(old('published', $event->published)) style="accent-color:var(--lime); width:16px; height:16px;">
                    <span>Published</span>
                </label>
            </div>
        </div>

        <div style="display:flex; gap:12px; justify-content:flex-end;">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline" style="flex:1; justify-content:center; text-align:center; padding:12px;">Cancel</a>
            <button type="submit" class="btn btn-secondary" style="flex:1; justify-content:center; padding:12px;">Save Changes</button>
        </div>
    </div>
</form>
@endsection
