@extends('user.layouts.shell', ['pageTitle' => 'Account Settings'])

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="profile-shell">
        <header class="profile-heading">
            <p class="dash-kicker">Player Profile</p>
            <h1>Edit Profile</h1>
        </header>

        <section class="card profile-card">
            <div class="avatar-row">
                <div class="avatar-circle">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="avatar-meta">
                    <strong>{{ $user->name }}</strong>
                    <span>Registered since {{ $user->created_at->format('F Y') }}</span>
                </div>
            </div>

            <p class="form-section-title">Personal Information</p>
            <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
                @csrf
                @method('PUT')

                <div class="field-row-2">
                    <div class="field-group">
                        <label class="field-label">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="e.g. 09123456789" required maxlength="11">
                    @error('phone')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>

            <hr class="divider-line">

            <p class="form-section-title">Change Password</p>
            <form method="POST" action="{{ route('profile.password') }}" class="profile-form">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label class="field-label">Current Password</label>
                    <input type="password" name="current_password" required>
                    @error('current_password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-row-2">
                    <div class="field-group">
                        <label class="field-label">New Password</label>
                        <input type="password" name="password" required>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required>
                    </div>
                </div>

                <div class="profile-form-actions">
                    <button type="submit" class="btn btn-outline">Update Password</button>
                </div>
            </form>
        </section>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
    </script>
    @endpush
@endsection
