@extends('admin.layouts.shell', ['pageTitle' => 'Player Feedback', 'active' => 'testimonials'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Community</p>
            <h1>Player Feedback</h1>
        </div>
    </div>

    @if (session('success'))
        <div style="background: rgba(163,230,53,0.08); border: 1px solid rgba(163,230,53,0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: #a3e635; flex-shrink: 0;"></i>
            <span style="font-size: 13px; color: var(--text);">{{ session('success') }}</span>
        </div>
    @endif

    <section class="mt-6">
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($testimonials as $testimonial)
                            <tr>
                                <td>
                                    <strong style="font-size: 13px;">{{ $testimonial->name }}</strong>
                                    @if ($testimonial->user_id)
                                        <span style="display: block; font-size: 11px; color: var(--muted); margin-top: 2px;">User #{{ $testimonial->user_id }}</span>
                                    @else
                                        <span style="display: block; font-size: 11px; color: var(--muted); margin-top: 2px;">Seeded</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 2px;">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                                 fill="{{ $s <= $testimonial->rating ? '#a3e635' : 'none' }}"
                                                 stroke="{{ $s <= $testimonial->rating ? '#a3e635' : '#333' }}"
                                                 stroke-width="2">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </td>
                                <td style="max-width: 340px;">
                                    <p style="margin: 0; font-size: 13px; color: var(--text); line-height: 1.5; white-space: normal;">{{ Str::limit($testimonial->comment, 120) }}</p>
                                </td>
                                <td>
                                    @if ($testimonial->is_featured)
                                        <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(163,230,53,0.1); border: 1px solid rgba(163,230,53,0.4); color: #a3e635; padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                            <i data-lucide="eye" style="width: 10px; height: 10px;"></i> Published
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.4); color: #f59e0b; padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                            <i data-lucide="clock" style="width: 10px; height: 10px;"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: var(--muted); white-space: nowrap;">{{ $testimonial->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <form method="POST" action="{{ route('admin.testimonials.toggle', $testimonial) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline" style="font-size: 11px; padding: 5px 12px;">
                                                <i data-lucide="{{ $testimonial->is_featured ? 'eye-off' : 'eye' }}" style="width: 12px; height: 12px;"></i>
                                                {{ $testimonial->is_featured ? 'Hide' : 'Publish' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" onsubmit="return confirm('Delete this testimonial permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline" style="font-size: 11px; padding: 5px 12px; color: #ef4444; border-color: #ef4444;">
                                                <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">No feedback submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 16px;">
            {{ $testimonials->links() }}
        </div>
    </section>
@endsection
