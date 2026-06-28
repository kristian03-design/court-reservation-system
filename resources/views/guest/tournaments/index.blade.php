<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tournaments | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/css/views/user.css', 'resources/js/app.js'])
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); padding-bottom: 120px;">
        {{-- Hero --}}
        <section class="scroll-reveal reveal-fade-up" style="padding: 100px 0 60px; text-align: center;">
            <div class="site-container" style="max-width: 800px; margin-inline: auto; padding: 0 20px;">
                <p class="cc-kicker" style="justify-content: center;">Competitions & Leagues</p>
                <h1 style="font-family: var(--display-font); font-size: clamp(44px, 8vw, 76px); text-transform: uppercase; line-height: 1; margin: 0 0 24px; font-weight: 400; letter-spacing: 0.02em;">
                    TEST YOUR LIMITS. <span style="color: var(--lime);">CLIMB THE RANKS.</span>
                </h1>
                <p class="cc-lead" style="margin: 0 auto 36px; max-width: 600px; color: var(--muted-mid); font-size: 16px;">
                    Track standings, follow real-time score updates, and browse current player brackets. Select an event to check out matchups.
                </p>
            </div>
        </section>

        {{-- Active & Upcoming --}}
        <section class="site-container" style="max-width: 1100px; margin-inline: auto; padding: 0 20px;">
            <h2 style="font-family: var(--display-font); font-size: 28px; text-transform: uppercase; color: #fff; border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 24px;">Active & Upcoming</h2>
            
            @if ($active->isEmpty() && $upcoming->isEmpty())
                <div style="text-align:center;padding:48px;background:var(--surface-3);border:1px solid var(--border);border-radius:12px;">
                    <p style="color:var(--muted-mid);margin:0;">No active or upcoming tournaments scheduled at this time. Check back soon!</p>
                </div>
            @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:24px;">
                    @foreach ($active->concat($upcoming) as $t)
                        <article style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;display:flex;flex-direction:column;justify-content:space-between;transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--lime)'" onmouseout="this.style.borderColor='var(--border)'">
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                    <span style="font-size:10px;text-transform:uppercase;font-weight:700;color:var(--lime);background:var(--lime-dim);padding:2px 8px;border-radius:4px;">{{ str_replace('_', ' ', $t->status) }}</span>
                                    <span style="color:var(--muted);font-size:12px;">Entry Fee: <strong>₱{{ number_format($t->entry_fee, 2) }}</strong></span>
                                </div>
                                <h3 style="margin:0 0 8px;color:#fff;font-size:18px;">{{ $t->name }}</h3>
                                <p style="font-size:13px;color:var(--muted-mid);margin-bottom:16px;">{{ Str::limit($t->description, 100) }}</p>
                            </div>
                            <div style="border-top:1px solid rgba(255,255,255,0.05);padding-top:16px;display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:12px;color:var(--muted);">Starts: <strong>{{ $t->start_date->format('M d, Y') }}</strong></span>
                                <a href="{{ route('tournaments.show', $t) }}" class="btn btn-primary btn-sm" style="background:var(--lime);color:#000;font-weight:700;padding:6px 12px;border-radius:4px;">View Bracket</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Completed --}}
        <section class="site-container" style="max-width: 1100px; margin-inline: auto; padding: 48px 20px 0;">
            <h2 style="font-family: var(--display-font); font-size: 28px; text-transform: uppercase; color: #fff; border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 24px;">Past Tournaments</h2>
            
            @if ($completed->isEmpty())
                <div style="text-align:center;padding:32px;background:var(--surface-3);border:1px solid var(--border);border-radius:12px;">
                    <p style="color:var(--muted-mid);margin:0;">No completed tournaments records found.</p>
                </div>
            @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:24px;">
                    @foreach ($completed as $t)
                        <article style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;opacity:0.8;display:flex;flex-direction:column;justify-content:space-between;">
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                    <span style="font-size:10px;text-transform:uppercase;font-weight:700;color:var(--text);background:rgba(255,255,255,0.05);padding:2px 8px;border-radius:4px;">Completed</span>
                                </div>
                                <h3 style="margin:0 0 8px;color:#fff;font-size:18px;">{{ $t->name }}</h3>
                            </div>
                            <div style="border-top:1px solid rgba(255,255,255,0.05);padding-top:16px;display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:12px;color:var(--muted);">Ended: <strong>{{ $t->end_date ? $t->end_date->format('M d, Y') : $t->updated_at->format('M d, Y') }}</strong></span>
                                <a href="{{ route('tournaments.show', $t) }}" class="btn btn-outline btn-sm" style="padding:6px 12px;border-radius:4px;">View Standings</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
