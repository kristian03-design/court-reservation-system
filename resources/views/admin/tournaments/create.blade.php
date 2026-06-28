@extends('admin.layouts.shell', ['pageTitle' => 'Create Tournament', 'active' => 'tournaments'])

@section('content')
    <div style="max-width:860px;margin-inline:auto;">
        <div style="margin-bottom:24px;">
            <a href="{{ route('admin.tournaments.index') }}" style="color:var(--muted);text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:4px;">
                <i class="ti ti-arrow-left"></i> Back to list
            </a>
            <h2 style="margin:8px 0 0;font-family:var(--display-font);font-size:32px;color:#fff;">Create New Tournament</h2>
        </div>

        <div class="card" style="background:var(--surface);border:1px solid var(--border);border-radius:12px;max-width:800px;margin-inline:auto;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid var(--border);background:var(--surface-2);">
                <h3 style="margin:0;font-size:14px;font-weight:600;color:#fff;">Tournament Configuration</h3>
            </div>
            
            <form method="POST" action="{{ route('admin.tournaments.store') }}" style="padding:24px;">
                @csrf
                
                <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-bottom:24px;">
                    <div>
                        <label for="name" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Tournament Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="e.g. Summer Smash Padel Cup" style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                        @error('name') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="description" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Description & Rules</label>
                        <textarea name="description" id="description" rows="4" placeholder="Describe rules, rewards, and eligibility..." style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;resize:vertical;">{{ old('description') }}</textarea>
                        @error('description') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:20px;margin-bottom:24px;">
                    <div>
                        <label for="type" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Tournament Type</label>
                        <select name="type" id="type" required style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                            <option value="single" {{ old('type') === 'single' ? 'selected' : '' }}>Single Elimination (Knockout)</option>
                            <option value="double" {{ old('type') === 'double' ? 'selected' : '' }}>Double Elimination</option>
                            <option value="round_robin" {{ old('type') === 'round_robin' ? 'selected' : '' }}>Round Robin (League)</option>
                        </select>
                        @error('type') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="max_participants" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Max Entrants</label>
                        <input type="number" name="max_participants" id="max_participants" required value="{{ old('max_participants', 16) }}" min="2" style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                        @error('max_participants') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="entry_fee" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Entry Fee (₱)</label>
                        <input type="number" name="entry_fee" id="entry_fee" required value="{{ old('entry_fee', 0.00) }}" min="0" step="0.01" style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                        @error('entry_fee') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:20px;margin-bottom:24px;">
                    <div>
                        <label for="registration_start" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Registration Start</label>
                        <input type="datetime-local" name="registration_start" id="registration_start" required value="{{ old('registration_start') }}" style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                        @error('registration_start') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="registration_end" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Registration End</label>
                        <input type="datetime-local" name="registration_end" id="registration_end" required value="{{ old('registration_end') }}" style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                        @error('registration_end') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:20px;margin-bottom:24px;">
                    <div>
                        <label for="start_date" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Tournament Start</label>
                        <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}" style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                        @error('start_date') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="end_date" style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted-mid);">Tournament End (Optional)</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:10px;color:#fff;font-size:13px;">
                        @error('end_date') <small style="color:var(--coral);display:block;margin-top:4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="margin-bottom:24px;display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="auto_schedule" id="auto_schedule" value="1" {{ old('auto_schedule', true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--lime);">
                    <label for="auto_schedule" style="font-weight:600;color:var(--muted-mid);">Enable smart auto-scheduling on courts</label>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid var(--border);padding-top:20px;">
                    <a href="{{ route('admin.tournaments.index') }}" class="btn btn-outline" style="padding:10px 20px;">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="background:var(--lime);color:#000;font-weight:700;padding:10px 20px;border-radius:6px;border:none;">Create Tournament</button>
                </div>
            </form>
        </div>
    </div>
@endsection
