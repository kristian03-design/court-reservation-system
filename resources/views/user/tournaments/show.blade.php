@extends('user.layouts.shell', ['pageTitle' => $tournament->name])

@section('content')
    <style>
        .rr-match-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #fff;
            font-weight: 400;
        }
        .rr-match-row.is-winner {
            color: var(--lime);
            font-weight: 700;
        }
    </style>
    <div style="margin-bottom: 24px; padding: 20px 0 0;">
        <a href="{{ route('dashboard') }}"
           style="color:#fff;text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;background:rgba(255,255,255,0.05);padding:8px 16px;border-radius:8px;border:1px solid var(--border);transition:all 0.15s ease;"
           onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.borderColor='var(--lime)';"
           onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='var(--border)';">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to Dashboard
        </a>
        <h1 style="font-family: var(--display-font); font-size: 38px; text-transform: uppercase; line-height: 1.1; margin: 12px 0 8px; font-weight: 400; color: #fff;">
            {{ $tournament->name }}
        </h1>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span class="status status-{{ $tournament->status }}" style="text-transform:uppercase;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;">
                {{ str_replace('_', ' ', $tournament->status) }}
            </span>
            <span style="color:var(--muted);font-size:12px;">Format: <strong style="color:#fff;">{{ $tournament->type === 'single' ? 'Single Elimination' : ($tournament->type === 'double' ? 'Double Elimination' : 'Round Robin') }}</strong></span>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;border-bottom:1px solid var(--border);margin-bottom:24px;gap:20px;overflow-x:auto;">
        <button type="button" class="tab-btn active" onclick="switchTab('bracket')" id="tab-btn-bracket" style="background:none;border:none;border-bottom:2px solid var(--lime);color:#fff;padding:10px 4px;font-weight:600;font-size:13px;cursor:pointer;white-space:nowrap;">Live Bracket</button>
        <button type="button" class="tab-btn" onclick="switchTab('info')" id="tab-btn-info" style="background:none;border:none;border-bottom:2px solid transparent;color:var(--muted);padding:10px 4px;font-weight:600;font-size:13px;cursor:pointer;white-space:nowrap;">Information & Rules</button>
        <button type="button" class="tab-btn" onclick="switchTab('entrants')" id="tab-btn-entrants" style="background:none;border:none;border-bottom:2px solid transparent;color:var(--muted);padding:10px 4px;font-weight:600;font-size:13px;cursor:pointer;white-space:nowrap;">Participants ({{ $tournament->participants->count() }})</button>
    </div>

    {{-- Tab Contents --}}
    
    {{-- BRACKET TAB --}}
    <div class="tab-pane active" id="tab-pane-bracket">
        @if ($tournament->matches->isEmpty())
            <div style="text-align:center;padding:48px;background:var(--surface);border:1px solid var(--border);border-radius:12px;color:var(--muted);">
                <i data-lucide="sitemap" style="width:48px;height:48px;margin-inline:auto;margin-bottom:12px;opacity:0.4;"></i>
                <h3>Bracket Pending Generation</h3>
                <p style="margin:0;">Match brackets are generated once player registration is completed. Stay tuned!</p>
            </div>
        @elseif ($tournament->type === 'round_robin')
            <div class="rr-grid">
                @foreach ($tournament->matches as $match)
                    <div class="rr-match-card no-click" style="cursor:default;">
                        <div class="rr-match-card-header">
                            <span>Round {{ $match->round_number }}</span>
                            <span class="status status-{{ $match->status }}">{{ $match->status }}</span>
                        </div>
                        <div class="rr-match-card-body">
                            <div class="rr-match-row {{ $match->winner_id === $match->participant1_id && $match->winner_id ? 'is-winner' : '' }}">
                                <span>{{ $match->participant1?->display_name ?? 'TBD' }}</span>
                                <strong>{{ $match->sets->sum('participant1_score') }}</strong>
                            </div>
                            <div class="rr-match-row {{ $match->winner_id === $match->participant2_id && $match->winner_id ? 'is-winner' : '' }}">
                                <span>{{ $match->participant2?->display_name ?? 'TBD' }}</span>
                                <strong>{{ $match->sets->sum('participant2_score') }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            @php
                $totalRounds = $matchesByRound->count();
                $roundLabels = [];
                foreach ($matchesByRound->keys() as $i => $round) {
                    $remaining = $totalRounds - $i;
                    if ($remaining === 1) $roundLabels[$round] = 'Championship';
                    elseif ($remaining === 2) $roundLabels[$round] = 'Semi-Finals';
                    elseif ($remaining === 3) $roundLabels[$round] = 'Quarter-Finals';
                    else $roundLabels[$round] = 'Round ' . $round;
                }
            @endphp
            <div class="bracket-wrap">
                <div class="bracket-toolbar">
                    <h3><i data-lucide="git-fork" style="width:18px;height:18px;display:inline;margin-right:8px;vertical-align:middle;"></i>Live Bracket</h3>
                    <div class="bracket-zoom-controls">
                        <button type="button" onclick="bracketZoom(-0.1)">−</button>
                        <span class="bracket-zoom-label" id="bracket-zoom-label">100%</span>
                        <button type="button" onclick="bracketZoom(0.1)">+</button>
                        <button type="button" onclick="bracketZoomReset()" style="font-size:11px;width:auto;padding:0 8px;">Reset</button>
                    </div>
                </div>

                <div class="bracket-scroll-area" id="bracketScrollArea">
                    <div class="bracket-canvas" id="bracketCanvas">
                        <svg class="bracket-svg-connectors" id="bracketSvg" aria-hidden="true"></svg>

                        @foreach ($matchesByRound as $round => $matches)
                            @php
                                $label = $roundLabels[$round] ?? 'Round ' . $round;
                                $isFinal = ($label === 'Championship');
                            @endphp
                            <div class="bracket-round-col" data-round="{{ $round }}">
                                <div class="bracket-round-label {{ $isFinal ? 'is-final' : '' }}">
                                    {{ $label }}
                                </div>
                                <div class="bracket-round-matches">
                                    @foreach ($matches as $match)
                                        @php
                                            $p1Won = $match->winner_id && $match->winner_id === $match->participant1_id;
                                            $p2Won = $match->winner_id && $match->winner_id === $match->participant2_id;
                                            $isBye = $match->status === 'bye';
                                            $isLive = $match->status === 'live';
                                        @endphp
                                        <div class="bracket-match-card no-click {{ $isBye ? 'is-bye' : '' }} {{ $isLive ? 'is-live' : '' }}"
                                             id="match-{{ $match->id }}"
                                             data-match-id="{{ $match->id }}"
                                             data-next-match-id="{{ $isFinal ? 'champion' : $match->next_match_id }}"
                                             data-has-winner="{{ $match->winner_id ? '1' : '0' }}"
                                             style="cursor:default;">

                                            <div class="bracket-card-header">
                                                <span class="bracket-card-round-label">M{{ $match->match_number }}</span>
                                                <span class="bracket-card-status s-{{ $match->status }}">
                                                    {{ strtoupper($match->status) }}
                                                </span>
                                            </div>

                                            <div class="bracket-match-body">
                                                <div class="bracket-participant-row {{ $p1Won ? 'is-winner' : ($p2Won && $match->winner_id ? 'is-loser' : '') }} {{ !$match->participant1 ? 'is-tbd' : '' }}">
                                                    <div class="bracket-participant-name">
                                                        @if ($match->participant1)
                                                            <span class="bracket-seed-badge">#{{ $match->participant1->seed }}</span>
                                                        @endif
                                                        <span>{{ $match->participant1?->display_name ?? 'TBD' }}</span>
                                                    </div>
                                                    <span class="bracket-score-cell">
                                                        {{ $match->sets->isNotEmpty() ? $match->sets->sum('participant1_score') : '' }}
                                                    </span>
                                                </div>

                                                <div class="bracket-participant-row {{ $p2Won ? 'is-winner' : ($p1Won && $match->winner_id ? 'is-loser' : '') }} {{ !$match->participant2 ? 'is-tbd' : '' }}">
                                                    <div class="bracket-participant-name">
                                                        @if ($match->participant2)
                                                            <span class="bracket-seed-badge">#{{ $match->participant2->seed }}</span>
                                                        @endif
                                                        <span>{{ $match->participant2?->display_name ?? 'TBD' }}</span>
                                                    </div>
                                                    <span class="bracket-score-cell">
                                                        {{ $match->sets->isNotEmpty() ? $match->sets->sum('participant2_score') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        {{-- Champion Column --}}
                        @php
                            $championshipRound = $matchesByRound->last();
                            $championshipMatch = $championshipRound ? $championshipRound->first() : null;
                        @endphp
                        <div class="bracket-round-col" data-round="champion">
                            <div class="bracket-round-label is-champion" style="color: var(--lime);">
                                Champion
                            </div>
                            <div class="bracket-round-matches">
                                <div class="bracket-match-card no-click is-champion-card"
                                     id="match-champion"
                                     data-match-id="champion"
                                     style="border-color: var(--lime); background: rgba(191, 255, 0, 0.05); cursor: default;">
                                    <div class="bracket-card-header" style="justify-content: center; border-bottom: 1px solid rgba(191, 255, 0, 0.15); padding: 6px 10px; background: rgba(191, 255, 0, 0.03);">
                                        <span class="bracket-card-round-label" style="color: var(--lime); font-weight: 800; font-size: 11px; letter-spacing: 0.1em;">🏆 CHAMPION 🏆</span>
                                    </div>
                                    <div class="bracket-match-body" style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 50px; padding: 6px;">
                                        @if ($championshipMatch && $championshipMatch->winner)
                                            <div style="font-weight: 800; color: #fff; font-size: 14px; text-transform: uppercase; letter-spacing: 0.03em; text-align: center;">
                                                {{ $championshipMatch->winner->display_name }}
                                            </div>
                                            <div style="font-size: 10px; color: var(--lime); font-weight: 600; margin-top: 2px;">
                                                Seed #{{ $championshipMatch->winner->seed }}
                                            </div>
                                        @else
                                            <div style="color: var(--muted); font-style: italic; font-size: 13px;">
                                                TBD
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- INFO TAB --}}
    <div class="tab-pane" id="tab-pane-info" style="display:none;">
        <div style="display:flex;flex-wrap:wrap;gap:24px;">
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;flex:1;min-width:280px;">
                <h3 style="margin:0 0 12px;font-family:var(--display-font);font-size:24px;color:#fff;letter-spacing:1px;">Description & Rules</h3>
                <p style="margin:0;color:var(--muted-mid);line-height:1.6;font-size:13px;white-space:pre-line;">
                    {{ $tournament->description ?: 'No description provided for this tournament.' }}
                </p>
            </div>
            
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;display:flex;flex-direction:column;gap:16px;width:280px;min-width:280px;flex-shrink:0;max-width:100%;height:fit-content;">
                <h4 style="margin:0;font-size:13px;text-transform:uppercase;color:var(--muted);letter-spacing:1px;font-weight:600;">Details</h4>
                <div style="display:flex;flex-direction:column;gap:12px;font-size:13px;">
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);">Entry Fee</span>
                        <strong style="color:var(--lime);">₱{{ number_format($tournament->entry_fee, 2) }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);">Max Entrants</span>
                        <strong style="color:#fff;">{{ $tournament->max_participants }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);">Registration Closes</span>
                        <strong style="color:#fff;">{{ $tournament->registration_end->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ENTRANTS TAB --}}
    <div class="tab-pane" id="tab-pane-entrants" style="display:none;">
        <div class="card" style="background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
            @if ($tournament->participants->isEmpty())
                <div style="padding:40px;text-align:center;color:var(--muted);">
                    No entrants registered yet.
                </div>
            @else
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border);color:var(--muted);font-weight:600;background:rgba(255,255,255,0.02);">
                            <th style="padding:12px 20px;">Name</th>
                            <th style="padding:12px 20px;">Registration Seed</th>
                            <th style="padding:12px 20px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tournament->participants as $p)
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:16px 20px;font-weight:600;color:#fff;">{{ $p->display_name }}</td>
                                <td style="padding:16px 20px;">#{{ $p->seed }}</td>
                                <td style="padding:16px 20px;text-transform:capitalize;">{{ $p->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <script>
    /* ── Bracket layout constants ─────────────────────────────────── */
    const CARD_H  = 92;
    const COL_W   = 224;
    const COL_GAP = 72;
    const PAD_T   = 48;
    const PAD_L   = 32;
    let bracketScale = 1;

    function layoutBracket() {
        const canvas = document.getElementById('bracketCanvas');
        if (!canvas) return;
        const cols = Array.from(canvas.querySelectorAll('.bracket-round-col'));
        if (!cols.length) return;

        const rounds = cols.map(col => ({
            col, label: col.querySelector('.bracket-round-label'),
            cards: Array.from(col.querySelectorAll('.bracket-match-card'))
        }));

        const n1     = rounds[0].cards.length;
        const slotH  = Math.max(CARD_H + 24, 110);
        const totalH = n1 * slotH + PAD_T * 2;
        const centreY = {};

        // Reverse feed map: nextMatchId → [sourceMatchIds]
        const feedMap = {};
        canvas.querySelectorAll('.bracket-match-card[data-next-match-id]').forEach(c => {
            const nid = c.getAttribute('data-next-match-id');
            const mid = c.getAttribute('data-match-id');
            if (!nid) return;
            feedMap[nid] = feedMap[nid] || [];
            feedMap[nid].push(mid);
        });

        // Round 1: evenly distribute
        rounds[0].cards.forEach((card, i) => {
            centreY[card.getAttribute('data-match-id')] = PAD_T + slotH * i + slotH / 2;
        });

        // Later rounds: centre between source cards
        for (let r = 1; r < rounds.length; r++) {
            rounds[r].cards.forEach(card => {
                const mid = card.getAttribute('data-match-id');
                const src = feedMap[mid] || [];
                let cy;
                if (src.length >= 2 && centreY[src[0]] !== undefined && centreY[src[1]] !== undefined) {
                    cy = (centreY[src[0]] + centreY[src[1]]) / 2;
                } else if (src.length === 1 && centreY[src[0]] !== undefined) {
                    cy = centreY[src[0]];
                } else {
                    cy = PAD_T + (rounds[r].cards.indexOf(card) + 0.5) * (totalH / rounds[r].cards.length);
                }
                centreY[mid] = cy;
            });
        }

        // Apply positions
        rounds.forEach(round => {
            round.col.style.position   = 'relative';
            round.col.style.width      = COL_W + 'px';
            round.col.style.height     = totalH + 'px';
            round.col.style.marginRight = COL_GAP + 'px';
            round.col.style.flexShrink = '0';
            if (round.label) {
                round.label.style.position = 'absolute';
                round.label.style.top = '0'; round.label.style.left = '0'; round.label.style.right = '0';
            }
            round.cards.forEach(card => {
                const cy = centreY[card.getAttribute('data-match-id')];
                if (cy === undefined) return;
                card.style.position = 'absolute';
                card.style.left = '0'; card.style.right = '0';
                card.style.top = Math.round(cy - CARD_H / 2) + 'px';
                card.style.height = CARD_H + 'px';
            });
        });

        const totalW = rounds.length * (COL_W + COL_GAP) - COL_GAP + PAD_L * 2;
        canvas.style.width = totalW + 'px';
        canvas.style.height = totalH + 'px';
        canvas.style.minWidth = 'unset';
        canvas.style.position = 'relative';
        canvas.style.display = 'flex';
        canvas.style.flexDirection = 'row';
        canvas.style.alignItems = 'flex-start';
        canvas.style.padding = PAD_T + 'px ' + PAD_L + 'px';
    }

    function drawConnectors() {
        const svg = document.getElementById('bracketSvg');
        const canvas = document.getElementById('bracketCanvas');
        if (!svg || !canvas) return;
        svg.innerHTML = '';
        const cr = canvas.getBoundingClientRect();

        canvas.querySelectorAll('.bracket-match-card[data-next-match-id]').forEach(card => {
            const nid = card.getAttribute('data-next-match-id');
            if (!nid) return;
            const nc = document.getElementById('match-' + nid);
            if (!nc) return;
            const fr = card.getBoundingClientRect(), tr = nc.getBoundingClientRect();
            const sx = fr.right  - cr.left;
            const sy = fr.top + fr.height / 2 - cr.top;
            const ex = tr.left   - cr.left;
            const ey = tr.top + tr.height / 2 - cr.top;
            const cx = sx + (ex - sx) * 0.55;
            const won = card.getAttribute('data-has-winner') === '1';
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', `M ${sx} ${sy} C ${cx} ${sy}, ${cx} ${ey}, ${ex} ${ey}`);
            if (won) path.setAttribute('class', 'won');
            svg.appendChild(path);
        });

        const w = parseFloat(canvas.style.width) || canvas.scrollWidth;
        const h = parseFloat(canvas.style.height) || canvas.scrollHeight;
        svg.setAttribute('width', w); svg.setAttribute('height', h);
        svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
        svg.style.cssText = 'position:absolute;top:0;left:0;pointer-events:none;';
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            const a = btn.id === 'tab-btn-' + tab;
            btn.classList.toggle('active', a);
            btn.style.borderBottomColor = a ? 'var(--lime)' : 'transparent';
            btn.style.color = a ? '#fff' : 'var(--muted)';
        });
        document.querySelectorAll('.tab-pane').forEach(p => {
            p.style.display = p.id === 'tab-pane-' + tab ? 'block' : 'none';
        });
        if (tab === 'bracket') setTimeout(function() { layoutBracket(); drawConnectors(); }, 40);
    }

    function bracketZoom(delta) {
        bracketScale = Math.min(2, Math.max(0.4, bracketScale + delta));
        const c = document.getElementById('bracketCanvas');
        if (c) { c.style.transform = 'scale(' + bracketScale + ')'; c.style.transformOrigin = 'top left'; }
        document.getElementById('bracket-zoom-label').textContent = Math.round(bracketScale * 100) + '%';
        setTimeout(drawConnectors, 60);
    }
    function bracketZoomReset() {
        bracketScale = 1;
        const c = document.getElementById('bracketCanvas');
        if (c) { c.style.transform = ''; c.style.transformOrigin = ''; }
        const l = document.getElementById('bracket-zoom-label');
        if (l) l.textContent = '100%';
        setTimeout(drawConnectors, 60);
    }

    // Auto-run on page load (bracket is the default tab here)
    window.addEventListener('load', function() { setTimeout(function() { layoutBracket(); drawConnectors(); }, 80); });
    window.addEventListener('resize', drawConnectors);

    // Drag to scroll
    (function() {
        const el = document.getElementById('bracketScrollArea');
        if (!el) return;
        let d = false, ox, oy, sl, st;
        el.addEventListener('mousedown', e => { d = true; ox = e.pageX - el.offsetLeft; oy = e.pageY - el.offsetTop; sl = el.scrollLeft; st = el.scrollTop; });
        el.addEventListener('mouseleave', () => d = false);
        el.addEventListener('mouseup',   () => d = false);
        el.addEventListener('mousemove', e => { if (!d) return; e.preventDefault(); el.scrollLeft = sl-(e.pageX-el.offsetLeft-ox); el.scrollTop = st-(e.pageY-el.offsetTop-oy); });
    })();
    </script>
@endsection
