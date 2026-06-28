@extends('admin.layouts.shell', ['pageTitle' => $tournament->name, 'active' => 'tournaments'])

@section('content')
    {{-- Page header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
        <div>
            <a href="{{ route('admin.tournaments.index') }}"
               style="color:var(--muted);text-decoration:none;font-size:12px;display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;">
                <i class="ti ti-arrow-left"></i> Back to tournaments
            </a>
            <h1 style="margin:0;font-family:var(--display-font);font-size:clamp(28px,4vw,42px);color:#fff;line-height:1;text-transform:uppercase;letter-spacing:0.04em;">
                {{ $tournament->name }}
            </h1>
            <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap;">
                <span class="status status-{{ $tournament->status }}"
                      style="text-transform:uppercase;font-size:10px;font-weight:800;padding:3px 8px;border-radius:4px;letter-spacing:0.06em;">
                    {{ str_replace('_', ' ', $tournament->status) }}
                </span>
                <span style="color:var(--muted);font-size:12px;">
                    Format: <strong style="color:#fff;">
                        {{ $tournament->type === 'single' ? 'Single Elimination' : ($tournament->type === 'double' ? 'Double Elimination' : 'Round Robin') }}
                    </strong>
                </span>
                <span style="color:var(--muted);font-size:12px;">
                    Entry: <strong style="color:var(--lime);">₱{{ number_format($tournament->entry_fee, 2) }}</strong>
                </span>
            </div>
        </div>

        <div style="display:inline-flex;gap:10px;flex-wrap:wrap;">
            @if ($tournament->status === 'draft')
                <form method="POST" action="{{ route('admin.tournaments.publish', $tournament) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary"
                            style="background:var(--lime);color:#000;font-weight:700;padding:9px 16px;border:none;border-radius:6px;display:inline-flex;align-items:center;gap:6px;">
                        <i class="ti ti-send"></i> Publish Registration
                    </button>
                </form>
            @endif

            @if (in_array($tournament->status, ['registration_closed', 'seeding']))
                <form method="POST" action="{{ route('admin.tournaments.generate', $tournament) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary"
                            style="background:var(--lime);color:#000;font-weight:700;padding:9px 16px;border:none;border-radius:6px;display:inline-flex;align-items:center;gap:6px;">
                        <i class="ti ti-sitemap"></i> Generate Bracket
                    </button>
                </form>
            @endif

            @if ($tournament->status === 'seeding')
                <form method="POST" action="{{ route('admin.tournaments.schedule', $tournament) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline"
                            style="border-color:var(--lime);color:var(--lime);padding:9px 16px;border-radius:6px;display:inline-flex;align-items:center;gap:6px;">
                        <i class="ti ti-calendar-event"></i> Auto-Schedule Courts
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="btn btn-outline"
               style="padding:9px 16px;border-radius:6px;display:inline-flex;align-items:center;gap:6px;">
                <i class="ti ti-settings"></i> Edit Settings
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;border-bottom:1px solid var(--border);margin-bottom:24px;gap:0;overflow-x:auto;">
        @php
            $tabs = [
                'overview'     => 'Overview',
                'bracket'      => 'Visual Bracket',
                'matches'      => 'Match List',
                'participants' => 'Participants (' . $tournament->participants->count() . ')',
            ];
        @endphp
        @foreach ($tabs as $key => $label)
            <button type="button" class="tab-btn {{ $key === 'overview' ? 'active' : '' }}"
                    onclick="switchTab('{{ $key }}')"
                    id="tab-btn-{{ $key }}"
                    style="background:none;border:none;border-bottom:2px solid {{ $key === 'overview' ? 'var(--lime)' : 'transparent' }};
                           color:{{ $key === 'overview' ? '#fff' : 'var(--muted)' }};
                           padding:10px 18px;font-weight:600;font-size:13px;cursor:pointer;white-space:nowrap;transition:color 0.15s,border-color 0.15s;">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ════════════════════════ OVERVIEW TAB ════════════════════════ --}}
    <div class="tab-pane" id="tab-pane-overview">
        <div style="display:flex;flex-wrap:wrap;gap:24px;">

            <div class="admin-panel" style="padding:24px;flex:1;min-width:280px;">
                <h3 style="margin:0 0 12px;font-family:var(--display-font);font-size:26px;color:#fff;letter-spacing:1px;">
                    Rules &amp; Description
                </h3>
                <p style="margin:0;color:var(--muted-mid);line-height:1.7;font-size:13px;white-space:pre-line;">
                    {{ $tournament->description ?: 'No description provided for this tournament.' }}
                </p>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;width:280px;min-width:280px;flex-shrink:0;max-width:100%;">
                <div class="admin-panel" style="padding:20px;">
                    <h4 style="margin:0 0 16px;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:1px;font-weight:700;">
                        Tournament Details
                    </h4>
                    <div style="display:flex;flex-direction:column;gap:12px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--muted);">Entry Fee</span>
                            <strong style="color:var(--lime);">₱{{ number_format($tournament->entry_fee, 2) }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--muted);">Max Entrants</span>
                            <strong style="color:#fff;">{{ $tournament->max_participants }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--muted);">Registered</span>
                            <strong style="color:#fff;">{{ $tournament->participants->count() }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--muted);">Reg. Start</span>
                            <strong style="color:#fff;">{{ $tournament->registration_start->format('M d, Y') }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--muted);">Reg. End</span>
                            <strong style="color:#fff;">{{ $tournament->registration_end->format('M d, Y') }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--muted);">Start Date</span>
                            <strong style="color:#fff;">{{ $tournament->start_date->format('M d, Y') }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--muted);">Auto-schedule</span>
                            <strong style="color:#fff;">{{ $tournament->auto_schedule ? 'Yes' : 'No' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════ BRACKET TAB ═════════════════════════ --}}
    <div class="tab-pane" id="tab-pane-bracket" style="display:none;">
        @if ($tournament->matches->isEmpty())
            <div class="admin-panel" style="padding:60px;text-align:center;color:var(--muted);">
                <i class="ti ti-sitemap" style="font-size:52px;display:block;margin-bottom:14px;opacity:0.25;"></i>
                <h3 style="margin:0 0 8px;color:var(--muted-mid);">No Brackets Generated Yet</h3>
                <p style="margin:0 0 20px;max-width:440px;margin-inline:auto;font-size:13px;">
                    Once registration is finalized and closed, click "Generate Bracket" to automatically create seed pairings and all match rounds up to the championship.
                </p>
            </div>
        @elseif ($tournament->type === 'round_robin')
            <div class="rr-grid">
                @foreach ($tournament->matches as $match)
                    <div class="rr-match-card"
                         onclick="openScoreModal({{ $match->id }}, '{{ addslashes($match->participant1?->display_name ?? 'TBD') }}', '{{ addslashes($match->participant2?->display_name ?? 'TBD') }}', {{ json_encode($match->sets) }})">
                        <div class="rr-match-card-header">
                            <span>Round {{ $match->round_number }} · Match {{ $match->match_number }}</span>
                            <span class="bracket-card-status s-{{ $match->status }}">{{ strtoupper($match->status) }}</span>
                        </div>
                        <div class="rr-match-card-body">
                            <div style="display:flex;justify-content:space-between;color:{{ $match->winner_id === $match->participant1_id && $match->winner_id ? 'var(--lime)' : '#fff' }};font-size:13px;">
                                <span>{{ $match->participant1?->display_name ?? 'TBD' }}</span>
                                <strong>{{ $match->sets->sum('participant1_score') }}</strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;color:{{ $match->winner_id === $match->participant2_id && $match->winner_id ? 'var(--lime)' : '#fff' }};font-size:13px;">
                                <span>{{ $match->participant2?->display_name ?? 'TBD' }}</span>
                                <strong>{{ $match->sets->sum('participant2_score') }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Single/Double Elimination Bracket --}}
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
                    <h3><i class="ti ti-sitemap" style="margin-right:8px;font-size:18px;vertical-align:middle;"></i>Live Bracket</h3>
                    <div class="bracket-zoom-controls">
                        <button type="button" onclick="bracketZoom(-0.1)" title="Zoom out">−</button>
                        <span class="bracket-zoom-label" id="bracket-zoom-label">100%</span>
                        <button type="button" onclick="bracketZoom(0.1)" title="Zoom in">+</button>
                        <button type="button" onclick="bracketZoomReset()" title="Reset zoom" style="font-size:11px;width:auto;padding:0 8px;">Reset</button>
                    </div>
                </div>

                <div class="bracket-scroll-area" id="bracketScrollArea">
                    <div class="bracket-canvas" id="bracketCanvas">
                        {{-- SVG connector layer --}}
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
                                <div class="bracket-round-matches" id="round-{{ $round }}-matches">
                                    @foreach ($matches as $match)
                                        @php
                                            $p1Won = $match->winner_id && $match->winner_id === $match->participant1_id;
                                            $p2Won = $match->winner_id && $match->winner_id === $match->participant2_id;
                                            $isBye = $match->status === 'bye';
                                            $isLive = $match->status === 'live';
                                        @endphp
                                        <div class="bracket-match-card {{ $isBye ? 'is-bye' : '' }} {{ $isLive ? 'is-live' : '' }}"
                                             id="match-{{ $match->id }}"
                                             data-match-id="{{ $match->id }}"
                                             data-next-match-id="{{ $isFinal ? 'champion' : $match->next_match_id }}"
                                             data-has-winner="{{ $match->winner_id ? '1' : '0' }}"
                                             @if (!$isBye)
                                             onclick="if(isDraggingActive) return; openScoreModal({{ $match->id }}, '{{ addslashes($match->participant1?->display_name ?? 'TBD') }}', '{{ addslashes($match->participant2?->display_name ?? 'TBD') }}', {{ json_encode($match->sets) }})"
                                             ondragover="handleDragOver(event, {{ $match->id }})"
                                             ondragenter="handleDragEnter(event, {{ $match->id }})"
                                             ondragleave="handleDragLeave(event, {{ $match->id }})"
                                             ondrop="handleDrop(event, {{ $match->id }})"
                                             @endif
                                             title="{{ $isBye ? 'Bye match — winner advanced automatically' : 'Click to enter scores or drag player to next match' }}">

                                            <div class="bracket-card-header">
                                                <span class="bracket-card-round-label">M{{ $match->match_number }}</span>
                                                <span class="bracket-card-status s-{{ $match->status }}">
                                                    {{ strtoupper($match->status) }}
                                                </span>
                                            </div>

                                            <div class="bracket-match-body">
                                                {{-- Participant 1 --}}
                                                <div class="bracket-participant-row {{ $p1Won ? 'is-winner' : ($p2Won && $match->winner_id ? 'is-loser' : '') }} {{ !$match->participant1 ? 'is-tbd' : '' }}"
                                                     @if($match->participant1 && !$isBye)
                                                     draggable="true"
                                                     ondragstart="handleDragStart(event, {{ $match->id }}, {{ $match->participant1_id }}, 1, '{{ $isFinal ? 'champion' : $match->next_match_id }}')"
                                                     ondragend="handleDragEnd(event)"
                                                     @endif>
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

                                                {{-- Participant 2 --}}
                                                <div class="bracket-participant-row {{ $p2Won ? 'is-winner' : ($p1Won && $match->winner_id ? 'is-loser' : '') }} {{ !$match->participant2 ? 'is-tbd' : '' }}"
                                                     @if($match->participant2 && !$isBye)
                                                     draggable="true"
                                                     ondragstart="handleDragStart(event, {{ $match->id }}, {{ $match->participant2_id }}, 2, '{{ $isFinal ? 'champion' : $match->next_match_id }}')"
                                                     ondragend="handleDragEnd(event)"
                                                     @endif>
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
                                     ondragover="handleDragOver(event, 'champion')"
                                     ondragenter="handleDragEnter(event, 'champion')"
                                     ondragleave="handleDragLeave(event, 'champion')"
                                     ondrop="handleDrop(event, 'champion')"
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

    {{-- ════════════════════════ MATCH LIST TAB ══════════════════════ --}}
    <div class="tab-pane" id="tab-pane-matches" style="display:none;">
        <div class="admin-panel">
            @if ($tournament->matches->isEmpty())
                <div style="padding:40px;text-align:center;color:var(--muted);">
                    No matches scheduled yet.
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--border);color:var(--muted);font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
                                <th style="padding:10px 20px;text-align:left;">Round</th>
                                <th style="padding:10px 16px;text-align:left;">Match</th>
                                <th style="padding:10px 16px;text-align:left;">Participant 1</th>
                                <th style="padding:10px 16px;text-align:left;">Participant 2</th>
                                <th style="padding:10px 16px;text-align:left;">Court / Schedule</th>
                                <th style="padding:10px 16px;text-align:left;">Status</th>
                                <th style="padding:10px 20px;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tournament->matches as $match)
                                <tr style="border-bottom:1px solid var(--border);">
                                    <td style="padding:14px 20px;color:var(--muted);">Round {{ $match->round_number }}</td>
                                    <td style="padding:14px 16px;">M{{ $match->match_number }}</td>
                                    <td style="padding:14px 16px;color:{{ $match->winner_id === $match->participant1_id && $match->winner_id ? 'var(--lime)' : '#fff' }};font-weight:{{ $match->winner_id === $match->participant1_id && $match->winner_id ? '700' : '400' }};">
                                        {{ $match->participant1?->display_name ?? 'TBD' }}
                                    </td>
                                    <td style="padding:14px 16px;color:{{ $match->winner_id === $match->participant2_id && $match->winner_id ? 'var(--lime)' : '#fff' }};font-weight:{{ $match->winner_id === $match->participant2_id && $match->winner_id ? '700' : '400' }};">
                                        {{ $match->participant2?->display_name ?? 'TBD' }}
                                    </td>
                                    <td style="padding:14px 16px;">
                                        @if ($match->schedule)
                                            <strong style="color:#fff;display:block;">{{ $match->schedule->court->court_name }}</strong>
                                            <span style="font-size:11px;color:var(--muted);">{{ $match->schedule->start_time->format('M d, g:i A') }}</span>
                                        @else
                                            <span style="color:var(--muted);">Not scheduled</span>
                                        @endif
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <span class="bracket-card-status s-{{ $match->status }}" style="padding:3px 7px;border-radius:3px;font-size:10px;font-weight:800;text-transform:uppercase;">
                                            {{ $match->status }}
                                        </span>
                                    </td>
                                    <td style="padding:14px 20px;text-align:right;">
                                        <button type="button" class="btn btn-outline btn-sm"
                                                style="padding:4px 10px;font-size:11px;"
                                                onclick="openScoreModal({{ $match->id }}, '{{ addslashes($match->participant1?->display_name ?? 'TBD') }}', '{{ addslashes($match->participant2?->display_name ?? 'TBD') }}', {{ json_encode($match->sets) }})">
                                            Record Score
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════ PARTICIPANTS TAB ═════════════════════ --}}
    <div class="tab-pane" id="tab-pane-participants" style="display:none;">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div>
                    <h2>Registered Entrants</h2>
                    <p>{{ $tournament->participants->count() }} / {{ $tournament->max_participants }} spots filled.</p>
                </div>
            </div>

            @if ($tournament->participants->isEmpty())
                <div style="padding:40px;text-align:center;color:var(--muted);">
                    No participants registered yet.
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--border);color:var(--muted);font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
                                <th style="padding:10px 20px;text-align:left;">Display Name</th>
                                <th style="padding:10px 16px;text-align:left;">Member Account</th>
                                <th style="padding:10px 16px;text-align:center;">Seed</th>
                                <th style="padding:10px 16px;text-align:left;">Check-in</th>
                                <th style="padding:10px 16px;text-align:left;">Status</th>
                                <th style="padding:10px 20px;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tournament->participants as $p)
                                <tr style="border-bottom:1px solid var(--border);">
                                    <td style="padding:14px 20px;">
                                        <strong style="color:#fff;">{{ $p->display_name }}</strong>
                                        @if ($p->team_name)
                                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Team: {{ $p->team_name }}</div>
                                        @endif
                                    </td>
                                    <td style="padding:14px 16px;color:var(--muted-mid);">
                                        {{ $p->user ? $p->user->name : 'Guest' }}
                                    </td>
                                    <td style="padding:14px 16px;text-align:center;">
                                        <span style="background:var(--surface-3);border:1px solid var(--border);padding:2px 8px;border-radius:4px;font-weight:700;font-size:12px;">#{{ $p->seed }}</span>
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <span class="status status-{{ $p->checked_in ? 'approved' : 'pending' }}"
                                              style="font-size:10px;font-weight:800;text-transform:uppercase;padding:2px 7px;border-radius:3px;">
                                            {{ $p->checked_in ? 'Checked In' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td style="padding:14px 16px;text-transform:capitalize;color:var(--muted-mid);">
                                        {{ $p->status }}
                                    </td>
                                    <td style="padding:14px 20px;text-align:right;">
                                        <form method="POST" action="{{ route('admin.participants.check-in', $p) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm"
                                                    style="padding:4px 10px;font-size:11px;">
                                                {{ $p->checked_in ? 'Check Out' : 'Check In' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════ SCORE MODAL ══════════════════════════ --}}
    <div class="score-modal-backdrop" id="score-modal-backdrop">
        <div class="score-modal">
            <form method="POST" id="score-form">
                @csrf
                <div class="score-modal-header">
                    <h3>Record Match Result</h3>
                    <button type="button" onclick="closeScoreModal()"
                            style="background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;line-height:1;padding:0;">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                <div class="score-modal-body">
                    <div class="score-match-vs">
                        <span id="score-p1-label">Player 1</span>
                        <span>vs</span>
                        <span id="score-p2-label">Player 2</span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:6px;margin-bottom:6px;font-size:10px;font-weight:700;text-transform:uppercase;color:var(--muted);text-align:center;">
                        <span>P1</span><span>Set</span><span>P2</span>
                    </div>

                    <div id="sets-container">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="score-modal-row">
                                <input type="number" name="sets[{{ $i }}][p1]" class="set-p1" min="0" value="0">
                                <span class="set-label">{{ $i + 1 }}</span>
                                <input type="number" name="sets[{{ $i }}][p2]" class="set-p2" min="0" value="0">
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="score-modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeScoreModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary"
                            style="background:var(--lime);color:#000;font-weight:700;border:none;padding:9px 20px;border-radius:6px;">
                        Save Score &amp; Advance Winner
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    /* ══════════════════════════════════════════════════════════
       BRACKET LAYOUT ENGINE
       Each match card gets an exact absolute Y position so the
       bracket tree renders correctly from Round 1 → Championship.
       Cards in round 1 are spaced evenly. Cards in later rounds
       are centred between the two source cards that feed into them.
    ══════════════════════════════════════════════════════════ */
    const CARD_H  = 92;   // fixed card height in px
    const COL_W   = 224;  // match .bracket-round-col width in CSS
    const COL_GAP = 72;   // gap between round columns
    const PAD_T   = 48;   // top padding inside canvas
    const PAD_L   = 32;   // left padding inside canvas

    let bracketScale = 1;
    let layoutDone   = false;

    function layoutBracket() {
        const canvas = document.getElementById('bracketCanvas');
        if (!canvas) return;

        const cols = Array.from(canvas.querySelectorAll('.bracket-round-col'));
        if (!cols.length) return;

        // Collect all cards per round
        const rounds = cols.map(col => ({
            col,
            label:  col.querySelector('.bracket-round-label'),
            cards:  Array.from(col.querySelectorAll('.bracket-match-card'))
        }));

        const n1       = rounds[0].cards.length;         // matches in round 1
        const slotH    = Math.max(CARD_H + 24, 110);     // slot per R1 card
        const totalH   = n1 * slotH + PAD_T * 2;

        // Build card-centre map: matchId → centreY (canvas-relative)
        const centreY = {};

        // Build reverse-feed map: nextMatchId → [sourceMatchId, ...]
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
            const cy = PAD_T + slotH * i + slotH / 2;
            centreY[card.getAttribute('data-match-id')] = cy;
        });

        // Later rounds: centre = midpoint of two feeding cards
        for (let r = 1; r < rounds.length; r++) {
            rounds[r].cards.forEach(card => {
                const mid     = card.getAttribute('data-match-id');
                const sources = feedMap[mid] || [];
                let cy;
                if (sources.length >= 2 && centreY[sources[0]] !== undefined && centreY[sources[1]] !== undefined) {
                    cy = (centreY[sources[0]] + centreY[sources[1]]) / 2;
                } else if (sources.length === 1 && centreY[sources[0]] !== undefined) {
                    cy = centreY[sources[0]];
                } else {
                    // Fallback
                    const idx = rounds[r].cards.indexOf(card);
                    cy = PAD_T + (idx + 0.5) * (totalH / rounds[r].cards.length);
                }
                centreY[mid] = cy;
            });
        }

        // Apply positions
        rounds.forEach((round, r) => {
            round.col.style.position  = 'relative';
            round.col.style.width     = COL_W + 'px';
            round.col.style.height    = totalH + 'px';
            round.col.style.marginRight = COL_GAP + 'px';
            round.col.style.flexShrink = '0';
            if (round.label) {
                round.label.style.position = 'absolute';
                round.label.style.top      = '0';
                round.label.style.left     = '0';
                round.label.style.right    = '0';
            }
            round.cards.forEach(card => {
                const mid = card.getAttribute('data-match-id');
                const cy  = centreY[mid];
                if (cy === undefined) return;
                card.style.position = 'absolute';
                card.style.left     = '0';
                card.style.right    = '0';
                card.style.top      = Math.round(cy - CARD_H / 2) + 'px';
                card.style.height   = CARD_H + 'px';
            });
        });

        // Size the canvas
        const totalW = rounds.length * (COL_W + COL_GAP) - COL_GAP + PAD_L * 2;
        canvas.style.width        = totalW + 'px';
        canvas.style.height       = totalH + 'px';
        canvas.style.minWidth     = 'unset';
        canvas.style.position     = 'relative';
        canvas.style.display      = 'flex';
        canvas.style.flexDirection= 'row';
        canvas.style.alignItems   = 'flex-start';
        canvas.style.padding      = PAD_T + 'px ' + PAD_L + 'px';

        layoutDone = true;
    }

    /* ── SVG connector curves ──────────────────────────────────── */
    function drawConnectors() {
        const svg    = document.getElementById('bracketSvg');
        const canvas = document.getElementById('bracketCanvas');
        if (!svg || !canvas) return;
        svg.innerHTML = '';

        const canvasRect = canvas.getBoundingClientRect();
        const sa         = document.getElementById('bracketScrollArea');

        canvas.querySelectorAll('.bracket-match-card[data-next-match-id]').forEach(card => {
            const nextId   = card.getAttribute('data-next-match-id');
            if (!nextId) return;
            const nextCard = document.getElementById('match-' + nextId);
            if (!nextCard) return;

            const fr = card.getBoundingClientRect();
            const tr = nextCard.getBoundingClientRect();
            const sx = fr.right  - canvasRect.left;
            const sy = fr.top + fr.height / 2 - canvasRect.top;
            const ex = tr.left   - canvasRect.left;
            const ey = tr.top + tr.height / 2 - canvasRect.top;
            const cx = sx + (ex - sx) * 0.55;

            const won  = card.getAttribute('data-has-winner') === '1';
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', `M ${sx} ${sy} C ${cx} ${sy}, ${cx} ${ey}, ${ex} ${ey}`);
            if (won) path.setAttribute('class', 'won');
            svg.appendChild(path);
        });

        const w = parseFloat(canvas.style.width)  || canvas.scrollWidth;
        const h = parseFloat(canvas.style.height) || canvas.scrollHeight;
        svg.setAttribute('width',   w);
        svg.setAttribute('height',  h);
        svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
        svg.style.position = 'absolute';
        svg.style.top      = '0';
        svg.style.left     = '0';
    }

    /* ── Tab switching ───────────────────────────────────────────── */
    function switchTab(tab) {
        localStorage.setItem('active_tab_tournament_{{ $tournament->id }}', tab);
        document.querySelectorAll('.tab-btn').forEach(btn => {
            const isActive = btn.id === 'tab-btn-' + tab;
            btn.classList.toggle('active', isActive);
            btn.style.borderBottomColor = isActive ? 'var(--lime)' : 'transparent';
            btn.style.color = isActive ? '#fff' : 'var(--muted)';
        });
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.style.display = pane.id === 'tab-pane-' + tab ? 'block' : 'none';
        });
        if (tab === 'bracket') {
            setTimeout(function() { layoutBracket(); drawConnectors(); }, 40);
        }
    }

    /* ── Score Modal ─────────────────────────────────────────────── */
    function openScoreModal(matchId, p1Name, p2Name, existingSets) {
        const backdrop = document.getElementById('score-modal-backdrop');
        const form = document.getElementById('score-form');

        document.getElementById('score-p1-label').textContent = p1Name;
        document.getElementById('score-p2-label').textContent = p2Name;
        form.action = '/admin/matches/' + matchId + '/score';

        const p1Inputs = document.querySelectorAll('.set-p1');
        const p2Inputs = document.querySelectorAll('.set-p2');
        p1Inputs.forEach(i => i.value = 0);
        p2Inputs.forEach(i => i.value = 0);

        if (existingSets && existingSets.length > 0) {
            existingSets.forEach((set, index) => {
                if (p1Inputs[index]) p1Inputs[index].value = set.participant1_score;
                if (p2Inputs[index]) p2Inputs[index].value = set.participant2_score;
            });
        }

        backdrop.classList.add('active');
    }

    function closeScoreModal() {
        document.getElementById('score-modal-backdrop').classList.remove('active');
    }

    document.getElementById('score-modal-backdrop').addEventListener('click', function(e) {
        if (e.target === this) closeScoreModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeScoreModal();
    });

    /* ── Drag and Drop Advancement ─────────────────────────────────── */
    let isDraggingActive = false;
    let currentDragData = null;

    function handleDragStart(e, matchId, participantId, playerIndex, nextMatchId) {
        isDraggingActive = true;
        currentDragData = {
            matchId: matchId,
            participantId: participantId,
            playerIndex: playerIndex,
            nextMatchId: nextMatchId
        };
        e.dataTransfer.setData('text/plain', JSON.stringify(currentDragData));
        e.dataTransfer.effectAllowed = 'move';
        e.currentTarget.classList.add('dragging');
    }

    function handleDragEnd(e) {
        e.currentTarget.classList.remove('dragging');
        setTimeout(() => {
            isDraggingActive = false;
            currentDragData = null;
        }, 80);
    }

    function handleDragOver(e, targetMatchId) {
        if (currentDragData && String(currentDragData.nextMatchId) === String(targetMatchId)) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        }
    }

    function handleDragEnter(e, targetMatchId) {
        if (currentDragData && String(currentDragData.nextMatchId) === String(targetMatchId)) {
            e.preventDefault();
            const card = document.getElementById('match-' + targetMatchId);
            if (card) {
                card.style.borderColor = 'var(--lime)';
                card.style.boxShadow = '0 0 12px rgba(191, 255, 0, 0.3)';
            }
        }
    }

    function handleDragLeave(e, targetMatchId) {
        const card = document.getElementById('match-' + targetMatchId);
        if (card) {
            card.style.borderColor = '';
            card.style.boxShadow = '';
        }
    }

    function handleDrop(e, targetMatchId) {
        e.preventDefault();
        try {
            const data = JSON.parse(e.dataTransfer.getData('text/plain'));
            if (!data || !data.matchId) return;

            if (String(data.nextMatchId) !== String(targetMatchId)) {
                alert('Invalid advancement! You can only drag a player to their designated next match.');
                return;
            }

            const form = document.getElementById('score-form');
            if (!form) return;

            form.action = '/admin/matches/' + data.matchId + '/score';
            
            const p1Inputs = form.querySelectorAll('.set-p1');
            const p2Inputs = form.querySelectorAll('.set-p2');

            p1Inputs.forEach(i => i.value = 0);
            p2Inputs.forEach(i => i.value = 0);

            if (data.playerIndex === 1) {
                if (p1Inputs[0]) p1Inputs[0].value = 6;
                if (p2Inputs[0]) p2Inputs[0].value = 0;
                if (p1Inputs[1]) p1Inputs[1].value = 6;
                if (p2Inputs[1]) p2Inputs[1].value = 0;
            } else {
                if (p1Inputs[0]) p1Inputs[0].value = 0;
                if (p2Inputs[0]) p2Inputs[0].value = 6;
                if (p1Inputs[1]) p1Inputs[1].value = 0;
                if (p2Inputs[1]) p2Inputs[1].value = 6;
            }

            // Remove drag highlight
            const card = document.getElementById('match-' + targetMatchId);
            if (card) {
                card.style.borderColor = '';
                card.style.boxShadow = '';
            }

            form.submit();
        } catch (err) {
            console.error('Drag-and-drop error:', err);
        }
    }

    /* ── Zoom controls ───────────────────────────────────────────── */
    function bracketZoom(delta) {
        bracketScale = Math.min(2, Math.max(0.4, bracketScale + delta));
        const canvas = document.getElementById('bracketCanvas');
        if (canvas) { canvas.style.transform = 'scale(' + bracketScale + ')'; canvas.style.transformOrigin = 'top left'; }
        document.getElementById('bracket-zoom-label').textContent = Math.round(bracketScale * 100) + '%';
        setTimeout(drawConnectors, 60);
    }

    function bracketZoomReset() {
        bracketScale = 1;
        const canvas = document.getElementById('bracketCanvas');
        if (canvas) { canvas.style.transform = ''; canvas.style.transformOrigin = ''; }
        const lbl = document.getElementById('bracket-zoom-label');
        if (lbl) lbl.textContent = '100%';
        setTimeout(drawConnectors, 60);
    }

    window.addEventListener('resize', () => {
        if (document.getElementById('tab-pane-bracket') &&
            document.getElementById('tab-pane-bracket').style.display !== 'none') {
            drawConnectors();
        }
    });

    // Drag-to-scroll in bracket area
    (function() {
        const el = document.getElementById('bracketScrollArea');
        if (!el) return;
        let isDragging = false, startX, startY, scrollLeft, scrollTop;

        el.addEventListener('mousedown', e => {
            isDragging = true;
            startX = e.pageX - el.offsetLeft;
            startY = e.pageY - el.offsetTop;
            scrollLeft = el.scrollLeft;
            scrollTop = el.scrollTop;
        });
        el.addEventListener('mouseleave', () => isDragging = false);
        el.addEventListener('mouseup', () => isDragging = false);
        el.addEventListener('mousemove', e => {
            if (!isDragging) return;
            e.preventDefault();
            el.scrollLeft = scrollLeft - (e.pageX - el.offsetLeft - startX);
            el.scrollTop  = scrollTop  - (e.pageY - el.offsetTop  - startY);
        });
    })();

    // Restore active tab on load
    (function() {
        const savedTab = localStorage.getItem('active_tab_tournament_{{ $tournament->id }}');
        if (savedTab) {
            switchTab(savedTab);
        } else {
            switchTab('overview');
        }
    })();
    </script>
@endsection
