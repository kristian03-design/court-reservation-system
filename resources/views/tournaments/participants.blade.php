@php
    $activeTab = request()->query('tab', 'participants');
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tournament->name }} — {{ $activeTab === 'leaderboard' ? 'Leaderboard' : 'Participants' }} | CourtConnect</title>
    <link rel="icon" href="{{ asset('images/courtconnect-mark.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/views/guest.css', 'resources/css/views/user.css', 'resources/js/app.js'])
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            border-color: var(--lime);
            transform: translateY(-2px);
        }
        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            font-weight: 700;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            font-family: var(--display-font);
            letter-spacing: 0.03em;
        }
        .filter-bar {
            position: sticky;
            top: 70px;
            z-index: 10;
            background: rgba(18, 18, 18, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .filter-pills {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .filter-pill {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            color: rgba(255, 255, 255, 0.6);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
        }
        .filter-pill:hover {
            color: #fff;
            border-color: var(--muted-mid);
        }
        .filter-pill.active {
            background: var(--lime-dim);
            border-color: var(--lime);
            color: var(--lime) !important;
        }
        .search-input-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
            max-width: 360px;
        }
        .search-input {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px 10px 38px !important;
            padding-left: 38px !important;
            color: #fff;
            font-size: 13px;
            transition: all 0.15s ease;
        }
        .search-input:focus {
            outline: none;
            border-color: var(--lime);
            background: rgba(255,255,255,0.05);
        }
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            width: 16px;
            height: 16px;
            pointer-events: none;
        }
        .sort-select {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .sort-select:focus {
            border-color: var(--lime);
        }
        .sort-select option {
            color: #111;
            background: #fff;
        }
        .participant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }
        .participant-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 200px;
            position: relative;
            transition: all 0.2s ease;
        }
        .participant-card:hover {
            border-color: var(--lime);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .avatar-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--lime-dim), rgba(255,255,255,0.1));
            color: var(--lime);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            border: 1px solid var(--border);
        }
        .seed-badge {
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            background: rgba(255,255,255,0.08);
            padding: 3px 8px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .card-title {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
        }
        .card-subtitle {
            font-size: 12px;
            color: var(--muted);
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .card-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 12px;
            color: var(--muted-mid);
        }
        .card-stats strong {
            color: #fff;
        }
        .card-actions {
            display: flex;
            gap: 8px;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 14px;
        }
        .badge-status {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 4px;
            letter-spacing: 0.05em;
            width: fit-content;
        }
        .status-active { background: var(--lime-dim); color: var(--lime); }
        .status-eliminated { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .status-champion { background: rgba(234, 179, 8, 0.1); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.2); }
        .status-registered { background: rgba(255,255,255,0.05); color: var(--muted); }

        /* Drawer Styles */
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .drawer-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }
        .drawer {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            max-width: 100%;
            height: 100%;
            background: var(--surface-3);
            border-left: 1px solid var(--border);
            z-index: 101;
            box-shadow: -10px 0 30px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .drawer.open {
            right: 0;
        }
        .drawer-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .drawer-body {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }
        .drawer-footer {
            padding: 20px;
            border-top: 1px solid var(--border);
            background: var(--surface);
        }
        .skeleton {
            background: linear-gradient(90deg, var(--surface) 25%, var(--surface-3) 50%, var(--surface) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .tab-nav-btn {
            background:none;
            border:none;
            border-bottom:2px solid transparent;
            color:var(--muted);
            padding:10px 20px;
            font-weight:600;
            font-size:13px;
            cursor:pointer;
            white-space:nowrap;
            transition:color 0.15s,border-color 0.15s;
        }
        .tab-nav-btn.active {
            border-bottom-color:var(--lime);
            color:#fff;
        }
    </style>
</head>
<body class="public-page padele-home padele-inner">
    @include('partials.public-header')
    @include('partials.toast')

    <main style="background: var(--bg); padding-bottom: 120px; padding-top: 80px;">
        <section class="site-container" style="max-width: 1200px; margin-inline: auto; padding: 0 20px;">

            {{-- Header --}}
            <div style="margin-bottom: 28px;">
                <a href="{{ route('tournaments') }}"
                   style="color:#fff;text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;background:rgba(255,255,255,0.05);padding:8px 16px;border-radius:8px;border:1px solid var(--border);transition:all 0.15s ease;"
                   onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.borderColor='var(--lime)';"
                   onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='var(--border)';">
                    <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to Tournaments
                </a>
                <h1 style="font-family: var(--display-font); font-size: clamp(32px, 5vw, 58px); text-transform: uppercase;
                            line-height: 1; margin: 0 0 10px; font-weight: 400; color: #fff;">
                    {{ $tournament->name }}
                </h1>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span class="status status-{{ $tournament->status }}"
                          style="text-transform:uppercase;font-size:10px;font-weight:800;padding:3px 8px;border-radius:4px;letter-spacing:0.06em;">
                        {{ str_replace('_', ' ', $tournament->status) }}
                    </span>
                    <span style="color:var(--muted);font-size:13px;">
                        Format: <strong style="color:#fff;">
                            {{ $tournament->type === 'single' ? 'Single Elimination' : ($tournament->type === 'double' ? 'Double Elimination' : 'Round Robin') }}
                        </strong>
                    </span>
                    <span style="color:var(--muted);font-size:13px;">
                        Entry: <strong style="color:var(--lime);">₱{{ number_format($tournament->entry_fee, 2) }}</strong>
                    </span>
                </div>
            </div>

            {{-- Tabs --}}
            <div style="display:flex;border-bottom:1px solid var(--border);margin-bottom:28px;gap:0;overflow-x:auto;">
                <a href="{{ route('tournaments.show', $tournament) }}?tab=overview" class="tab-nav-btn">
                    Overview
                </a>
                <a href="{{ route('tournaments.participants', $tournament) }}" class="tab-nav-btn {{ $activeTab === 'participants' ? 'active' : '' }}">
                    Participants ({{ $stats['total_participants'] }})
                </a>
                <a href="{{ route('tournaments.show', $tournament) }}?tab=bracket" class="tab-nav-btn">
                    Live Bracket
                </a>
                <a href="{{ route('tournaments.show', $tournament) }}?tab=schedule" class="tab-nav-btn">
                    Schedule
                </a>
                <a href="{{ route('tournaments.show', $tournament) }}?tab=rules" class="tab-nav-btn">
                    Rules
                </a>
                <a href="{{ route('tournaments.participants', $tournament) }}?tab=leaderboard" class="tab-nav-btn {{ $activeTab === 'leaderboard' ? 'active' : '' }}">
                    Leaderboard
                </a>
            </div>

            {{-- ═══════════════ LEADERBOARD TAB ═══════════════ --}}
            @if($activeTab === 'leaderboard')
                <div>
                    <h3 style="font-family: var(--display-font); font-size: 28px; text-transform: uppercase; color: #fff; margin-bottom: 20px;">
                        Standings Leaderboard
                    </h3>

                    @php
                        // Fetch rankings sorted by wins desc, losses asc
                        $rankings = $tournament->participants()->orderBy('wins', 'desc')->orderBy('losses', 'asc')->orderBy('seed', 'asc')->get();
                    @endphp

                    @if($rankings->isEmpty())
                        <div style="text-align:center;padding:60px;background:var(--surface);border:1px solid var(--border);border-radius:12px;color:var(--muted);">
                            <i data-lucide="trophy" style="width:48px;height:48px;margin:0 auto 16px;display:block;opacity:0.3;"></i>
                            <h4 style="color:#fff;margin-bottom:8px;">No participants registered yet</h4>
                            <p style="margin:0;font-size:13px;">Leaderboard standings will update as match scores are submitted.</p>
                        </div>
                    @else
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
                            <table style="width:100%;border-collapse:collapse;font-size:13px;text-align:left;">
                                <thead>
                                    <tr style="border-bottom:1px solid var(--border);color:var(--muted);font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
                                        <th style="padding:16px 20px;">Rank</th>
                                        <th style="padding:16px 20px;">Player / Team</th>
                                        <th style="padding:16px 20px;text-align:center;">Wins</th>
                                        <th style="padding:16px 20px;text-align:center;">Losses</th>
                                        <th style="padding:16px 20px;text-align:center;">Played</th>
                                        <th style="padding:16px 20px;text-align:center;">Win Rate</th>
                                        <th style="padding:16px 20px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rankings as $idx => $r)
                                        @php
                                            $totalMatches = $r->wins + $r->losses;
                                            $winRate = $totalMatches > 0 ? round(($r->wins / $totalMatches) * 100) . '%' : '0%';
                                        @endphp
                                        <tr style="border-bottom:1px solid var(--border); transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.backgroundColor='transparent'">
                                            <td style="padding:16px 20px;font-weight:700;font-size:15px;color:<?php echo $idx === 0 ? 'var(--lime)' : '#fff'; ?>;">
                                                #{{ $idx + 1 }}
                                            </td>
                                            <td style="padding:16px 20px;">
                                                <div style="display:flex;align-items:center;gap:12px;">
                                                    <div class="avatar-circle" style="width:32px;height:32px;font-size:12px;">
                                                        {{ strtoupper(substr($r->display_name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div style="font-weight:600;color:#fff;">{{ $r->display_name }}</div>
                                                        @if($r->team_name)
                                                            <div style="font-size:11px;color:var(--muted);">{{ $r->team_name }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding:16px 20px;text-align:center;font-weight:600;color:var(--lime);">{{ $r->wins }}</td>
                                            <td style="padding:16px 20px;text-align:center;font-weight:600;color:#ef4444;">{{ $r->losses }}</td>
                                            <td style="padding:16px 20px;text-align:center;color:var(--muted-mid);">{{ $r->matches_played }}</td>
                                            <td style="padding:16px 20px;text-align:center;font-weight:600;color:#fff;">{{ $winRate }}</td>
                                            <td style="padding:16px 20px;">
                                                <span class="badge-status status-{{ $r->status }}">
                                                    {{ $r->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            {{-- ═══════════════ PARTICIPANTS TAB ═══════════════ --}}
            @else
                <div>
                    {{-- Statistics Cards --}}
                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-label">Participants</span>
                            <span class="stat-value">{{ $stats['total_participants'] }}</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Teams</span>
                            <span class="stat-value">{{ $stats['teams_count'] }}</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Matches</span>
                            <span class="stat-value">{{ $stats['matches_count'] }}</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Prize Pool</span>
                            <span class="stat-value" style="color: var(--lime);">₱{{ number_format($stats['prize_pool'], 2) }}</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Courts Used</span>
                            <span class="stat-value">{{ $stats['courts_count'] }}</span>
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="filter-bar">
                        <div class="search-input-wrap">
                            <i data-lucide="search" class="search-icon"></i>
                            <input type="text" id="searchBox" class="search-input" placeholder="Search Player or Team...">
                        </div>

                        <div class="filter-pills" id="filterContainer">
                            <button type="button" class="filter-pill active" data-filter="all">All</button>
                            <button type="button" class="filter-pill" data-filter="teams">Teams</button>
                            <button type="button" class="filter-pill" data-filter="players">Players</button>
                            <button type="button" class="filter-pill" data-filter="active">Active</button>
                            <button type="button" class="filter-pill" data-filter="eliminated">Eliminated</button>
                            <button type="button" class="filter-pill" data-filter="seed">Seeded</button>
                        </div>

                        <select id="sortSelect" class="sort-select">
                            <option value="newest">Newest</option>
                            <option value="wins">Most Wins</option>
                            <option value="seed">Seed Ranking</option>
                            <option value="name">Alphabetical</option>
                        </select>
                    </div>

                    {{-- Grid Container --}}
                    <div class="participant-grid" id="participantGrid">
                        {{-- Loading Skeleton --}}
                        @for($i=0; $i<8; $i++)
                            <div class="participant-card skeleton-card">
                                <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                                    <div class="skeleton" style="width:40px; height:40px; border-radius:50%;"></div>
                                    <div class="skeleton" style="width:60px; height:20px;"></div>
                                </div>
                                <div class="skeleton" style="width:80%; height:24px; margin-bottom:8px;"></div>
                                <div class="skeleton" style="width:60%; height:16px; margin-bottom:16px;"></div>
                                <div style="display:flex; gap:8px;">
                                    <div class="skeleton" style="flex:1; height:32px;"></div>
                                    <div class="skeleton" style="flex:1; height:32px;"></div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    {{-- Pagination Controls --}}
                    <div id="paginationWrap" style="display:flex;justify-content:center;align-items:center;gap:12px;margin-top:40px;">
                        {{-- Filled dynamically via JS --}}
                    </div>
                </div>
            @endif

        </section>
    </main>

    {{-- Team / Player Detail Drawer --}}
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer" id="detailDrawer">
        <div class="drawer-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="avatar-circle" id="drawerAvatar" style="width:40px;height:40px;font-size:14px;">TF</div>
                <div>
                    <h3 id="drawerName" style="margin:0;font-size:18px;color:#fff;">Team Falcons</h3>
                    <span class="badge-status status-active" id="drawerStatus" style="font-size:8px;">ACTIVE</span>
                </div>
            </div>
            <button type="button" onclick="closeDrawer()" style="background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="drawer-body">
            {{-- Stats Grid inside Drawer --}}
            <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:12px;margin-bottom:24px;background:rgba(255,255,255,0.02);padding:14px;border-radius:8px;border:1px solid var(--border);">
                <div style="text-align:center;">
                    <div style="font-size:10px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Wins</div>
                    <div style="font-size:18px;font-weight:700;color:var(--lime);" id="drawerWins">0</div>
                </div>
                <div style="text-align:center;border-inline:1px solid var(--border);">
                    <div style="font-size:10px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Losses</div>
                    <div style="font-size:18px;font-weight:700;color:#ef4444;" id="drawerLosses">0</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:10px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Seed</div>
                    <div style="font-size:18px;font-weight:700;color:#fff;" id="drawerSeed">#1</div>
                </div>
            </div>

            {{-- Detail Sections --}}
            <div style="display:flex;flex-direction:column;gap:20px;">
                {{-- Team Captain / User info --}}
                <div id="drawerCaptainSec">
                    <h4 style="margin:0 0 8px;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:0.05em;">Registered By (Captain)</h4>
                    <div style="background:rgba(255,255,255,0.01);padding:12px;border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;gap:10px;">
                        <i data-lucide="user" style="width:16px;height:16px;color:var(--lime);"></i>
                        <span id="drawerCaptainName" style="font-size:13px;color:#fff;font-weight:600;">Alex Rivera</span>
                    </div>
                </div>

                {{-- Members (if team) --}}
                <div id="drawerMembersSec">
                    <h4 style="margin:0 0 8px;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:0.05em;">Team Members</h4>
                    <div style="display:flex;flex-direction:column;gap:8px;" id="drawerMembersList">
                        {{-- Filled dynamically --}}
                    </div>
                </div>

                {{-- Upcoming Match --}}
                <div>
                    <h4 style="margin:0 0 8px;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:0.05em;">Upcoming Match</h4>
                    <div id="drawerUpcomingMatch" style="font-size:13px;color:var(--muted-mid);background:rgba(255,255,255,0.01);padding:12px;border:1px solid var(--border);border-radius:8px;">
                        None scheduled.
                    </div>
                </div>

                {{-- History / Recent Matches --}}
                <div>
                    <h4 style="margin:0 0 8px;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:0.05em;">Match History</h4>
                    <div style="display:flex;flex-direction:column;gap:8px;" id="drawerHistoryList">
                        {{-- Filled dynamically --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="drawer-footer">
            <a href="#" id="drawerBracketBtn" class="btn btn-primary" style="width:100%;justify-content:center;display:flex;background:var(--lime);color:#000;font-weight:700;padding:12px;border-radius:8px;text-decoration:none;text-align:center;font-size:14px;box-sizing:border-box;">
                <i data-lucide="git-fork" style="width:16px;height:16px;margin-right:6px;display:inline;vertical-align:middle;"></i> Open Bracket Location
            </a>
        </div>
    </div>

    @include('partials.public-footer')

    <script>
        const tournamentId = parseInt("{{ $tournament->id }}", 10);
        const bracketUrl = "{{ route('tournaments.show', $tournament) }}?tab=bracket";
        let currentPage = 1;
        let activeFilter = 'all';
        let searchQuery = '';
        let sortOrder = 'newest';
        let allParticipantsData = []; // Cached full list of tournament participants for local filtering

        // 1. Fetch data from API
        async function fetchParticipants(page = 1) {
            currentPage = page;
            const grid = document.getElementById('participantGrid');
            if (!grid) return;

            try {
                const response = await fetch(`/api/tournaments/${tournamentId}/participants?page=${page}&filter=${activeFilter}&search=${encodeURIComponent(searchQuery)}&sort=${sortOrder}&per_page=12`);
                const data = await response.json();
                
                // Cache data locally to populate team member info when drawer opens
                if (page === 1 && searchQuery === '' && activeFilter === 'all') {
                    allParticipantsData = data.participants;
                }

                renderGrid(data.participants);
                renderPagination(data);
                lucide.createIcons();
            } catch (err) {
                console.error("Error fetching participants:", err);
                grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:#ef4444;">Failed to load participants. Please refresh.</div>`;
            }
        }

        // 2. Render Grid Cards
        function renderGrid(participants) {
            const grid = document.getElementById('participantGrid');
            if (participants.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column:1/-1;text-align:center;padding:60px;background:var(--surface);border:1px solid var(--border);border-radius:12px;color:var(--muted);">
                        <i data-lucide="users-2" style="width:48px;height:48px;margin:0 auto 16px;display:block;opacity:0.3;"></i>
                        <h4 style="color:#fff;margin-bottom:8px;">No participants found</h4>
                        <p style="margin:0;font-size:13px;">Try adjusting your search or filters.</p>
                        ${tournamentId ? `<a href="{{ route('tournaments.show', $tournament) }}?tab=overview" class="btn btn-primary btn-sm" style="margin-top:16px;background:var(--lime);color:#000;font-weight:700;display:inline-block;padding:8px 16px;border-radius:6px;text-decoration:none;">Register Now</a>` : ''}
                    </div>
                `;
                return;
            }

            grid.innerHTML = participants.map(p => {
                const initials = p.display_name ? p.display_name.substring(0, 2).toUpperCase() : '??';
                const statusLabel = p.status.toUpperCase();
                const totalPlayed = parseInt(p.wins || 0) + parseInt(p.losses || 0);

                let isTeam = p.participant_type === 'team' || (p.team_name && p.team_name.trim() !== '');
                let teamDisplayHtml = '';
                if (p.team_name) {
                    teamDisplayHtml = `<span style="display:inline-flex;align-items:center;gap:4px;color:var(--lime);font-weight:600;"><i data-lucide="shield" style="width:12px;height:12px;"></i> ${p.team_name}</span>`;
                } else if (p.participant_type === 'team') {
                    teamDisplayHtml = `<span style="display:inline-flex;align-items:center;gap:4px;color:var(--lime);font-weight:600;"><i data-lucide="shield" style="width:12px;height:12px;"></i> Team</span>`;
                } else {
                    teamDisplayHtml = `<span style="display:inline-flex;align-items:center;gap:4px;color:var(--muted-mid);"><i data-lucide="user" style="width:12px;height:12px;"></i> Individual</span>`;
                }

                // Action buttons logic
                const viewBtnText = isTeam ? 'View Team' : 'Player Details';
                const bracketHighlightUrl = `${bracketUrl}&highlight=${encodeURIComponent(p.display_name)}`;

                return `
                    <div class="participant-card" id="participant-card-${p.id}">
                        <div>
                            <div class="card-top">
                                <div class="avatar-circle">
                                    ${p.avatar ? `<img src="${p.avatar}" alt="${p.display_name}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">` : initials}
                                </div>
                                <span class="seed-badge">Seed #${p.seed}</span>
                            </div>
                            <h3 class="card-title">${p.display_name}</h3>
                            <div class="card-subtitle">
                                ${teamDisplayHtml}
                            </div>
                            <div class="card-stats">
                                <span><strong>${p.wins || 0}</strong> Wins</span>
                                <span>·</span>
                                <span><strong>${p.losses || 0}</strong> Losses</span>
                                <span>·</span>
                                <span><strong>${totalPlayed}</strong> Match${totalPlayed === 1 ? '' : 'es'}</span>
                            </div>
                            <span class="badge-status status-${p.status}">
                                ${statusLabel}
                            </span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn btn-outline btn-sm" onclick="openDetails(${p.id})" style="flex:1;justify-content:center;font-size:11px;padding:6px 8px;border-radius:6px;border:1px solid var(--border);color:#fff;background:transparent;cursor:pointer;">
                                ${viewBtnText}
                            </button>
                            <a href="${bracketHighlightUrl}" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;font-size:11px;padding:6px 8px;border-radius:6px;background:var(--lime-dim);color:var(--lime) !important;border:1px solid var(--lime);text-decoration:none;text-align:center;box-sizing:border-box;display:inline-flex;align-items:center;gap:4px;">
                                <i data-lucide="git-fork" style="width:10px;height:10px;"></i> Bracket Pos
                            </a>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // 3. Render Pagination
        function renderPagination(data) {
            const wrap = document.getElementById('paginationWrap');
            if (!wrap || data.last_page <= 1) {
                if (wrap) wrap.innerHTML = '';
                return;
            }

            let html = '';
            // Prev
            if (data.current_page > 1) {
                html += `<button type="button" onclick="fetchParticipants(${data.current_page - 1})" style="background:rgba(255,255,255,0.05);border:1px solid var(--border);color:#fff;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;">Prev</button>`;
            } else {
                html += `<button type="button" disabled style="opacity:0.3;background:rgba(255,255,255,0.02);border:1px solid var(--border);color:var(--muted);padding:6px 12px;border-radius:6px;font-size:12px;">Prev</button>`;
            }

            // Pages numbers
            for (let i = 1; i <= data.last_page; i++) {
                const isActive = i === data.current_page;
                const activeStyle = isActive ? 'background:var(--lime);color:#000;font-weight:700;' : 'background:rgba(255,255,255,0.05);color:#fff;';
                html += `<button type="button" onclick="fetchParticipants(${i})" style="${activeStyle}border:1px solid var(--border);padding:6px 12px;border-radius:6px;cursor:pointer;min-width:32px;text-align:center;font-size:12px;">${i}</button>`;
            }

            // Next
            if (data.current_page < data.last_page) {
                html += `<button type="button" onclick="fetchParticipants(${data.current_page + 1})" style="background:rgba(255,255,255,0.05);border:1px solid var(--border);color:#fff;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;">Next</button>`;
            } else {
                html += `<button type="button" disabled style="opacity:0.3;background:rgba(255,255,255,0.02);border:1px solid var(--border);color:var(--muted);padding:6px 12px;border-radius:6px;font-size:12px;">Next</button>`;
            }

            wrap.innerHTML = html;
        }

        // 4. Open Drawer Details
        async function openDetails(participantId) {
            // Find participant object
            let response = await fetch(`/api/tournaments/${tournamentId}/participants?per_page=100`);
            let data = await response.json();
            let p = data.participants.find(item => item.id == participantId);
            if (!p) return;

            const isTeam = p.participant_type === 'team' || (p.team_name && p.team_name.trim() !== '');

            // Set basic details
            document.getElementById('drawerAvatar').textContent = p.display_name.substring(0, 2).toUpperCase();
            document.getElementById('drawerName').textContent = p.display_name;
            
            const statusEl = document.getElementById('drawerStatus');
            statusEl.className = `badge-status status-${p.status}`;
            statusEl.textContent = p.status.toUpperCase();

            document.getElementById('drawerWins').textContent = p.wins || 0;
            document.getElementById('drawerLosses').textContent = p.losses || 0;
            document.getElementById('drawerSeed').textContent = `#${p.seed}`;

            // Set Captain
            if (isTeam && p.team_name) {
                document.getElementById('drawerCaptainSec').style.display = 'block';
                document.getElementById('drawerCaptainName').textContent = p.user ? p.user.name : p.display_name + ' Captain';
            } else {
                document.getElementById('drawerCaptainSec').style.display = 'block';
                document.getElementById('drawerCaptainName').textContent = p.user ? p.user.name : p.display_name;
            }

            // Members section
            const membersSec = document.getElementById('drawerMembersSec');
            const membersList = document.getElementById('drawerMembersList');
            if (isTeam && p.team_name) {
                membersSec.style.display = 'block';
                // Find all participants with this team name
                const teamMembers = data.participants.filter(item => item.team_name === p.team_name);
                if (teamMembers.length > 0) {
                    membersList.innerHTML = teamMembers.map(tm => `
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#fff;background:rgba(255,255,255,0.02);padding:8px 12px;border:1px solid var(--border);border-radius:6px;">
                            <i data-lucide="user" style="width:12px;height:12px;color:var(--lime);"></i>
                            <span>${tm.display_name}</span>
                            ${tm.id === p.id ? '<span style="font-size:9px;color:var(--lime);background:var(--lime-dim);padding:1px 4px;border-radius:2px;margin-left:auto;">Captain</span>' : ''}
                        </div>
                    `).join('');
                } else {
                    membersList.innerHTML = `<div style="font-size:12px;color:var(--muted);">No registered team members found.</div>`;
                }
            } else {
                membersSec.style.display = 'none';
            }

            // Upcoming Match & History Details from matches API / HTML elements
            // Let's query matches for this participant
            try {
                const matchesRes = await fetch(`/api/tournaments/${tournamentId}/matches-data`);
                if (matchesRes.ok) {
                    const matchesData = await matchesRes.json();
                    const myMatches = matchesData.filter(m => m.participant1_id == p.id || m.participant2_id == p.id);
                    
                    // History
                    const historyList = document.getElementById('drawerHistoryList');
                    const historyMatches = myMatches.filter(m => m.status === 'finished');
                    if (historyMatches.length > 0) {
                        historyList.innerHTML = historyMatches.map(m => {
                            const opp = m.participant1_id == p.id ? m.participant2 : m.participant1;
                            const oppName = opp ? opp.display_name : 'TBD';
                            const isWin = m.winner_id == p.id;
                            const badge = isWin ? '<span style="color:var(--lime);font-weight:700;">WIN</span>' : '<span style="color:#ef4444;font-weight:700;">LOSS</span>';
                            
                            return `
                                <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.02);border:1px solid var(--border);padding:8px 12px;border-radius:6px;font-size:12px;">
                                    <span>vs <strong>${oppName}</strong></span>
                                    <span>${badge}</span>
                                </div>
                            `;
                        }).join('');
                    } else {
                        historyList.innerHTML = `<div style="font-size:12px;color:var(--muted);">No completed matches record.</div>`;
                    }

                    // Upcoming
                    const upcomingEl = document.getElementById('drawerUpcomingMatch');
                    const upcomingMatch = myMatches.find(m => m.status === 'live' || m.status === 'ready' || m.status === 'scheduled');
                    if (upcomingMatch) {
                        const opp = upcomingMatch.participant1_id == p.id ? upcomingMatch.participant2 : upcomingMatch.participant1;
                        const oppName = opp ? opp.display_name : 'TBD';
                        const time = upcomingMatch.schedule ? new Date(upcomingMatch.schedule.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'TBD';
                        const court = upcomingMatch.schedule && upcomingMatch.schedule.court ? upcomingMatch.schedule.court.court_name : 'TBD';
                        
                        upcomingEl.innerHTML = `
                            <div style="font-weight:600;color:#fff;margin-bottom:4px;">vs ${oppName}</div>
                            <div style="font-size:11px;color:var(--muted);display:flex;align-items:center;gap:12px;margin-top:4px;">
                                <span><i data-lucide="clock" style="width:10px;height:10px;display:inline;vertical-align:middle;margin-right:2px;"></i> ${time}</span>
                                <span><i data-lucide="map-pin" style="width:10px;height:10px;display:inline;vertical-align:middle;margin-right:2px;"></i> ${court}</span>
                            </div>
                        `;
                    } else {
                        upcomingEl.innerHTML = `<span style="color:var(--muted);">None scheduled.</span>`;
                    }
                }
            } catch (matchErr) {
                console.warn("Matches data not available, using mock display for history.");
                // Mock fallback
                document.getElementById('drawerUpcomingMatch').innerHTML = `<span style="color:var(--muted);">None scheduled.</span>`;
                document.getElementById('drawerHistoryList').innerHTML = `<div style="font-size:12px;color:var(--muted);">No completed matches record.</div>`;
            }

            // Set Bracket button link
            document.getElementById('drawerBracketBtn').href = `${bracketUrl}&highlight=${encodeURIComponent(p.display_name)}`;

            // Show Drawer
            document.getElementById('drawerOverlay').classList.add('open');
            document.getElementById('detailDrawer').classList.add('open');
            lucide.createIcons();
        }

        function closeDrawer() {
            document.getElementById('drawerOverlay').classList.remove('open');
            document.getElementById('detailDrawer').classList.remove('open');
        }

        // 5. Search Debounce
        let debounceTimer;
        const searchBox = document.getElementById('searchBox');
        if (searchBox) {
            searchBox.addEventListener('input', function(e) {
                searchQuery = e.target.value;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchParticipants(1);
                }, 300);
            });
        }

        // 6. Filter Pills
        const pills = document.querySelectorAll('.filter-pill');
        pills.forEach(pill => {
            pill.addEventListener('click', function() {
                pills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                activeFilter = this.getAttribute('data-filter');
                fetchParticipants(1);
            });
        });

        // 7. Sort Dropdown
        const sortSel = document.getElementById('sortSelect');
        if (sortSel) {
            sortSel.addEventListener('change', function() {
                sortOrder = this.value;
                fetchParticipants(1);
            });
        }

        // Initialize
        window.addEventListener('DOMContentLoaded', () => {
            fetchParticipants(1);
            lucide.createIcons();
        });
    </script>
</body>
</html>
