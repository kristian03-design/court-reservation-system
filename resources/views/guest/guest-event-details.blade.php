<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->title }} | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); min-height: calc(100vh - 120px); padding-top: 80px; padding-bottom: 120px;">
        <section class="site-container" style="max-width: 1200px; margin-inline: auto; padding: 0 20px;">
            
            {{-- Back button --}}
            <div style="margin-bottom: 28px;">
                <a href="{{ route('events') }}"
                   style="color:#fff;text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;background:rgba(255,255,255,0.05);padding:8px 16px;border-radius:8px;border:1px solid var(--border);transition:all 0.15s ease;"
                   onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.borderColor='var(--lime)';"
                   onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='var(--border)';">
                    <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to Events
                </a>
            </div>

            {{-- Main Grid --}}
            <div style="display: grid; grid-template-columns: 1fr 380px; gap: 40px; align-items: start;">
                
                {{-- Left Content Column --}}
                <div>
                    {{-- Cover Image --}}
                    <div style="position: relative; border-radius: 16px; overflow: hidden; height: 380px; border: 1px solid var(--border); margin-bottom: 32px;">
                        <img src="{{ $event->image ? asset(ltrim($event->image, '/')) : asset('images/courtconnect-multisport-hero.webp') }}" alt="{{ $event->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(0deg, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0) 100%); padding: 32px 24px;">
                            <span style="background: var(--lime); color: var(--bg); font-weight: 700; text-transform: uppercase; font-size: 11px; padding: 4px 10px; border-radius: 4px; display: inline-block; margin-bottom: 12px; letter-spacing: 0.05em;">
                                {{ $event->sport }}
                            </span>
                            <h1 style="font-family: var(--display-font); font-size: clamp(32px, 5vw, 54px); text-transform: uppercase; color: #fff; line-height: 1; margin: 0 0 8px;">
                                {{ $event->title }}
                            </h1>
                            <span class="status status-available" style="font-size: 11px; padding: 2px 8px;">
                                {{ $event->event_type }}
                            </span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 32px; margin-bottom: 32px;">
                        <h2 style="font-family: var(--display-font); font-size: 28px; text-transform: uppercase; color: #fff; margin: 0 0 16px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
                            About The Event
                        </h2>
                        <p style="color: var(--text); font-size: 15px; line-height: 1.6; margin: 0; white-space: pre-line;">
                            {{ $event->description }}
                        </p>
                    </div>

                    {{-- Location & Details --}}
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 32px; margin-bottom: 32px;">
                        <h2 style="font-family: var(--display-font); font-size: 28px; text-transform: uppercase; color: #fff; margin: 0 0 16px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
                            Venue Details
                        </h2>
                        <p style="color: var(--text); font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
                            This event will be hosted at the premier facilities of **{{ $event->location }}**. 
                            @if($event->court)
                                Specifically, activities will take place on **{{ $event->court->court_name }}**.
                            @endif
                        </p>
                        <div style="display:flex; gap:16px; align-items:center; background: rgba(255,255,255,0.02); padding:16px; border: 1px solid var(--border); border-radius:8px;">
                            <i data-lucide="map-pin" style="width:24px; height:24px; color:var(--lime); flex-shrink:0;"></i>
                            <div>
                                <strong style="color:#fff; display:block; font-size:14px;">{{ $event->location }}</strong>
                                <span style="font-size:12px; color:var(--muted);">Full changing rooms, spectator seating, and refreshments are available at venue.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar Column --}}
                <div style="position: sticky; top: 100px;">
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 24px; box-sizing: border-box;">
                        
                        {{-- Price Badge --}}
                        <div style="text-align: center; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 20px;">
                            <span style="font-size: 11px; text-transform: uppercase; color: var(--muted); font-weight: 700; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Registration Fee</span>
                            <strong style="font-size: 36px; font-weight: 800; color: var(--lime); display: block; font-family: var(--display-font);">
                                {{ $event->price > 0 ? '₱' . number_format($event->price, 2) : 'FREE' }}
                            </strong>
                        </div>

                        {{-- Metadata Grid --}}
                        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                            <div style="display: flex; gap: 12px; align-items: start;">
                                <i data-lucide="calendar" style="width:16px; height:16px; color:var(--lime); margin-top:2px;"></i>
                                <div>
                                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; display: block; font-weight: 600;">Date</span>
                                    <strong style="color: #fff; font-size: 13px;">
                                        {{ $event->start_date->format('F d, Y') }}
                                        @if($event->end_date && $event->end_date->gt($event->start_date))
                                            <span style="display:block; font-weight:500; font-size:12px; color:var(--muted-mid); margin-top:2px;">to {{ $event->end_date->format('F d, Y') }}</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>

                            <div style="display: flex; gap: 12px; align-items: start;">
                                <i data-lucide="clock" style="width:16px; height:16px; color:var(--lime); margin-top:2px;"></i>
                                <div>
                                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; display: block; font-weight: 600;">Time</span>
                                    <strong style="color: #fff; font-size: 13px;">
                                        {{ date('g:i A', strtotime($event->start_time)) }} – {{ date('g:i A', strtotime($event->end_time)) }}
                                    </strong>
                                </div>
                            </div>

                            <div style="display: flex; gap: 12px; align-items: start;">
                                <i data-lucide="users" style="width:16px; height:16px; color:var(--lime); margin-top:2px;"></i>
                                <div>
                                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; display: block; font-weight: 600;">Availability</span>
                                    <strong style="color: #fff; font-size: 13px;">
                                        {{ $event->max_slots - $event->registered }} of {{ $event->max_slots }} slots remaining
                                        <div style="width: 100%; height: 4px; background: var(--border); border-radius: 99px; overflow: hidden; margin-top: 6px;">
                                            @php $percentage = $event->max_slots > 0 ? ($event->registered / $event->max_slots) * 100 : 0; @endphp
                                            <div style="width: {{ $percentage }}%; height: 100%; background: var(--lime); border-radius: 99px;"></div>
                                        </div>
                                    </strong>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div style="border-top: 1px solid var(--border); padding-top: 20px;">
                            @guest
                                <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-primary" style="width: 100%; text-align: center; display: block; padding: 12px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 14px; box-sizing: border-box;">
                                    Login to Register
                                </a>
                                <p style="text-align: center; font-size: 11px; color: var(--muted); margin: 8px 0 0;">An account is required to register for events.</p>
                            @else
                                @php
                                    $userRegistration = $event->registrations()->where('user_id', auth()->id())->first();
                                @endphp

                                @if($userRegistration)
                                    <div style="background: rgba(255,255,255,0.02); padding: 14px; border: 1px solid var(--border); border-radius: 8px; text-align: center;">
                                        <i data-lucide="check-circle" style="width: 28px; height: 28px; color: var(--lime); margin: 0 auto 8px; display: block;"></i>
                                        <span style="font-size: 12px; color: var(--muted); text-transform: uppercase; font-weight: 700; display: block; letter-spacing: 0.05em;">Your Status</span>
                                        <strong style="color: #fff; font-size: 15px; display: block; margin-top: 4px; text-transform: uppercase;">
                                            {{ $userRegistration->registration_status }}
                                        </strong>
                                        @if($userRegistration->payment_status === 'pending_verification')
                                            <span style="font-size: 11px; color: var(--muted-mid); display: block; margin-top: 4px;">Payment verification pending admin approval.</span>
                                        @endif
                                    </div>
                                @else
                                    @if($event->status === 'closed' || $event->status === 'completed' || $event->status === 'cancelled')
                                        <button disabled class="btn btn-outline" style="width: 100%; opacity: 0.5; padding: 12px; font-size: 14px; font-weight: 700;">
                                            Closed
                                        </button>
                                    @elseif($event->registered >= $event->max_slots)
                                        @if($event->allow_waitlist)
                                            <a href="{{ route('events.register', $event->slug) }}" class="btn btn-outline" style="width: 100%; text-align: center; display: block; padding: 12px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 14px; color: var(--lime); border-color: var(--lime); box-sizing: border-box;">
                                                Join Waitlist
                                            </a>
                                        @else
                                            <button disabled class="btn btn-outline" style="width: 100%; opacity: 0.5; padding: 12px; font-size: 14px; font-weight: 700;">
                                                Sold Out
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('events.register', $event->slug) }}" class="btn btn-primary" style="width: 100%; text-align: center; display: block; padding: 12px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 14px; box-sizing: border-box;">
                                            {{ $event->price > 0 ? 'Book Slot' : 'Register Now' }}
                                        </a>
                                    @endif
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Events --}}
            @if($relatedEvents->isNotEmpty())
                <div style="margin-top: 80px; border-top: 1px solid var(--border); padding-top: 60px;">
                    <h3 style="font-family: var(--display-font); font-size: 32px; text-transform: uppercase; color: #fff; margin-bottom: 24px;">
                        Related Events
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                        @foreach($relatedEvents as $rel)
                            <article class="cc-court-card" style="display: flex; flex-direction: column;">
                                <div class="cc-court-image">
                                    <img src="{{ $rel->image ? asset($rel->image) : asset('images/courtconnect-multisport-hero.png') }}" alt="{{ $rel->title }}" style="filter: brightness(0.65) saturate(0.85); object-fit: cover; width:100%; height:160px;">
                                    <span class="sport-badge" style="background: var(--lime); color: var(--bg);">{{ $rel->sport }}</span>
                                </div>
                                <div class="cc-court-body" style="gap: 8px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; flex: 1;">
                                    <div>
                                        <h4 style="font-family: var(--display-font); font-size: 24px; color: var(--text); line-height: 1.1; margin: 0 0 6px;">
                                            <a href="{{ route('events.show', $rel->slug) }}" style="color: inherit; text-decoration: none;">{{ $rel->title }}</a>
                                        </h4>
                                        <p style="color: var(--muted-mid); font-size: 12px; margin: 0;">
                                            {{ $rel->start_date->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div style="border-top: 1px solid var(--border); padding-top: 12px; margin-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: var(--lime); font-weight: 700; font-size: 13px;">{{ $rel->price > 0 ? '₱' . number_format($rel->price) : 'Free' }}</span>
                                        <a href="{{ route('events.show', $rel->slug) }}" style="color: #fff; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            View Details <i data-lucide="chevron-right" style="width:12px; height:12px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

        </section>
    </main>

    @include('partials.public-footer')
    <script>lucide.createIcons();</script>
</body>
</html>
