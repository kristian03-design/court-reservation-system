@extends('user.layouts.shell', ['pageTitle' => 'Notifications'])

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <p class="dash-kicker" style="margin: 0 0 4px;">Inbox Alerts</p>
            <h1 style="font-family: var(--font-display); font-size: 28px; font-weight: 800; color: var(--text-primary); margin: 0;">Notifications</h1>
        </div>
        
        @if ($notifications->where('is_read', false)->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-outline" style="padding: 8px 16px; font-size: 13px; border-radius: var(--radius-sm);">Mark All as Read</button>
            </form>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i data-lucide="mail"></i>
            </div>
            <h3>No notifications yet</h3>
            <p>You're all caught up! When reservations get approved, or payment receipts get verified, they will show up here.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top: 8px;">Back to Dashboard</a>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach ($notifications as $notification)
                <div class="card" style="border-left: 4px solid {{ $notification->is_read ? 'var(--panel-border)' : 'var(--primary)' }}; background: {{ $notification->is_read ? 'var(--panel-bg)' : 'rgba(99, 102, 241, 0.04)' }}; padding: 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; transition: var(--transition);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $notification->title }}</h3>
                            @if (!$notification->is_read)
                                <span class="status status-pending" style="font-size: 8px; padding: 2px 6px;">New</span>
                            @endif
                        </div>
                        <p style="margin: 6px 0 0; font-size: 13px; color: var(--text-secondary); line-height: 1.5;">{{ $notification->message }}</p>
                        <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 6px;">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>

                    @if (!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; border-radius: var(--radius-sm); border-color: rgba(99, 102, 241, 0.3); color: var(--primary) !important;">
                                Mark Read
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        @if (method_exists($notifications, 'links'))
            <div class="pagination-row">
                {{ $notifications->links() }}
            </div>
        @endif
    @endif
@endsection
