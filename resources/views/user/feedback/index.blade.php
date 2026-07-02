@extends('user.layouts.shell')

@php $pageTitle = 'My Feedback'; @endphp

@push('head')
<style>
    .star-btn {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        line-height: 1;
        color: var(--border);
        transition: color 0.15s ease, transform 0.15s ease;
    }
    .star-btn:hover,
    .star-btn.is-active {
        color: var(--lime);
        transform: scale(1.15);
    }
    .star-btn svg {
        display: block;
    }
    .feedback-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 28px;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .status-pending {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.4);
        color: #f59e0b;
    }
    .status-published {
        background: var(--lime-dim);
        border: 1px solid var(--lime);
        color: var(--lime);
    }
</style>
@endpush

@section('content')
<div style="padding: 32px 0 64px;">
    <div style="max-width: 700px; margin: 0 auto; padding: 0 24px;">

        {{-- Page Header --}}
        <div style="margin-bottom: 36px;">
            <p style="color: var(--lime); font-family: var(--ui-font); font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin: 0 0 8px;">Player Voice</p>
            <h1 style="font-family: var(--display-font); font-size: 36px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0; font-weight: 400;">My Feedback</h1>
            <p style="color: var(--muted); font-size: 14px; margin: 8px 0 0; line-height: 1.6;">Share your experience with CourtConnect. Your review may appear on our public homepage after review.</p>
        </div>

        @if (session('success'))
            <div style="background: var(--lime-dim); border: 1px solid var(--lime); border-radius: var(--radius); padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                <i data-lucide="circle-check-big" style="width: 18px; height: 18px; color: var(--lime); flex-shrink: 0;"></i>
                <span style="font-size: 14px; color: var(--text); font-weight: 500;">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.4); border-radius: var(--radius); padding: 14px 18px; margin-bottom: 24px;">
                @foreach ($errors->all() as $error)
                    <p style="margin: 0; font-size: 13px; color: #ef4444;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Feedback Form --}}
        <div class="feedback-card" style="margin-bottom: 32px;">
            <h2 style="font-family: var(--display-font); font-size: 20px; letter-spacing: 0.04em; text-transform: uppercase; margin: 0 0 6px; font-weight: 400;">
                {{ $myFeedback->isNotEmpty() ? 'Update Your Review' : 'Leave a Review' }}
            </h2>
            <p style="color: var(--muted); font-size: 13px; margin: 0 0 24px;">Your feedback helps us improve and is shared with the CourtConnect community.</p>

            <form method="POST" action="{{ route('feedback.store') }}">
                @csrf

                {{-- Star Rating Picker --}}
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 12px;">Your Rating</label>
                    <div style="display: flex; gap: 8px; align-items: center;" id="star-picker">
                        @php $existingRating = $myFeedback->first()?->rating ?? 5; @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn {{ $i <= $existingRating ? 'is-active' : '' }}" data-star="{{ $i }}" id="star-{{ $i }}" aria-label="{{ $i }} stars">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                     fill="{{ $i <= $existingRating ? 'currentColor' : 'none' }}"
                                     stroke="currentColor" stroke-width="1.5" id="star-svg-{{ $i }}">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </button>
                        @endfor
                        <span id="rating-label" style="font-family: var(--ui-font); font-size: 13px; color: var(--muted); margin-left: 8px;"></span>
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ $existingRating }}">
                </div>

                {{-- Comment --}}
                <div style="margin-bottom: 24px;">
                    <label for="comment" style="display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">Your Review</label>
                    <textarea name="comment" id="comment" rows="4" maxlength="500" placeholder="Tell us about your court experience, bookings, events, and what you love about CourtConnect..." style="width: 100%; background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px 14px; color: var(--text); font-family: var(--ui-font); font-size: 14px; resize: vertical; outline: none; box-sizing: border-box; transition: border-color 0.2s; line-height: 1.6;">{{ old('comment', $myFeedback->first()?->comment) }}</textarea>
                    <div style="display: flex; justify-content: space-between; margin-top: 6px;">
                        <span id="char-remaining" style="font-size: 11px; color: var(--muted);"></span>
                        <span style="font-size: 11px; color: var(--muted);">Max 500 characters</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i data-lucide="send" style="width: 15px; height: 15px;"></i>
                    {{ $myFeedback->isNotEmpty() ? 'Update My Review' : 'Submit Feedback' }}
                </button>
            </form>
        </div>

        {{-- Existing Feedback --}}
        @if ($myFeedback->isNotEmpty())
            @php $fb = $myFeedback->first(); @endphp
            <div class="feedback-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <h3 style="font-family: var(--display-font); font-size: 18px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.04em; margin: 0 0 6px;">Your Current Review</h3>
                        <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                            @for ($s = 1; $s <= 5; $s++)
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="{{ $s <= $fb->rating ? 'var(--lime)' : 'none' }}"
                                     stroke="{{ $s <= $fb->rating ? 'var(--lime)' : 'var(--border)' }}"
                                     stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <span class="status-badge status-published">
                        <i data-lucide="check-circle" style="width: 11px; height: 11px;"></i>
                        Live on Homepage
                    </span>
                </div>

                <blockquote style="margin: 0 0 20px; padding: 14px 18px; background: var(--surface-alt); border-left: 3px solid var(--lime); border-radius: 0 var(--radius) var(--radius) 0;">
                    <p style="margin: 0; font-size: 14px; line-height: 1.7; color: var(--text);">&ldquo;{{ $fb->comment }}&rdquo;</p>
                </blockquote>

                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <span style="font-size: 12px; color: var(--muted);">
                        Submitted {{ $fb->created_at->diffForHumans() }}
                    </span>
                    <form method="POST" action="{{ route('feedback.destroy') }}" onsubmit="return confirm('Remove your feedback? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline" style="font-size: 12px; padding: 6px 14px; color: var(--coral); border-color: var(--coral);">
                            <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                            Remove
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div style="background: var(--surface); border: 1px dashed var(--border); border-radius: var(--radius-xl); padding: 40px 24px; text-align: center;">
                <i data-lucide="message-square-heart" style="width: 40px; height: 40px; color: var(--muted); margin-bottom: 12px;"></i>
                <p style="color: var(--muted); font-size: 14px; margin: 0;">You haven't submitted any feedback yet. Use the form above to share your experience!</p>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const labels = ['', 'Poor', 'Fair', 'Good', 'Great', 'Excellent!'];
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    const ratingLabel = document.getElementById('rating-label');
    const textarea = document.getElementById('comment');
    const charRemaining = document.getElementById('char-remaining');

    let selectedRating = parseInt(ratingInput?.value || 5);

    function updateStars(rating) {
        stars.forEach((btn, i) => {
            const svg = document.getElementById('star-svg-' + (i + 1));
            const active = (i + 1) <= rating;
            btn.classList.toggle('is-active', active);
            if (svg) svg.setAttribute('fill', active ? 'currentColor' : 'none');
        });
        if (ratingLabel) ratingLabel.textContent = labels[rating] || '';
        if (ratingInput) ratingInput.value = rating;
    }

    // Initial state
    updateStars(selectedRating);

    stars.forEach((btn) => {
        const star = parseInt(btn.dataset.star);

        btn.addEventListener('mouseenter', () => {
            updateStars(star);
        });

        btn.addEventListener('click', () => {
            selectedRating = star;
            updateStars(star);
        });
    });

    document.getElementById('star-picker')?.addEventListener('mouseleave', () => {
        updateStars(selectedRating);
    });

    // Character counter
    function updateCounter() {
        if (!textarea || !charRemaining) return;
        const len = textarea.value.length;
        const remaining = 500 - len;
        charRemaining.textContent = remaining + ' characters remaining';
        charRemaining.style.color = remaining < 50 ? 'var(--coral)' : 'var(--muted)';
    }
    textarea?.addEventListener('input', updateCounter);
    updateCounter();
});
</script>
@endpush
