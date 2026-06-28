@extends('admin.layouts.shell', ['pageTitle' => 'Tournaments', 'active' => 'tournaments'])

@section('content')
    {{-- Page header --}}
    <header class="admin-welcome" style="margin-bottom: 28px;">
        <div>
            <p class="page-eyebrow">Management</p>
            <h1>Tournament &amp; Event Manager</h1>
            <p>Create, seed, and manage live brackets for your facility events.</p>
        </div>
        <a href="{{ route('admin.tournaments.create') }}"
           class="btn btn-primary"
           style="background:var(--lime);color:#000;font-weight:700;padding:10px 18px;border-radius:8px;display:inline-flex;align-items:center;gap:8px;font-size:13px;white-space:nowrap;">
            <i class="ti ti-plus" aria-hidden="true"></i> New Tournament
        </a>
    </header>

    {{-- Stat cards --}}
    <section class="admin-stat-strip" aria-label="Tournament statistics" style="margin-bottom: 28px;">
        <article class="admin-stat-card stat-green">
            <span class="stat-icon"><i class="ti ti-trophy" aria-hidden="true"></i></span>
            <div>
                <p>Total Tournaments</p>
                <strong>{{ $stats['total'] }}</strong>
                <small>All created events</small>
            </div>
        </article>
        <article class="admin-stat-card stat-orange">
            <span class="stat-icon"><i class="ti ti-antenna" aria-hidden="true"></i></span>
            <div>
                <p>Active Live</p>
                <strong>{{ $stats['active'] }}</strong>
                <small>Matches in progress</small>
            </div>
        </article>
        <article class="admin-stat-card stat-blue">
            <span class="stat-icon"><i class="ti ti-circle-check" aria-hidden="true"></i></span>
            <div>
                <p>Completed</p>
                <strong>{{ $stats['completed'] }}</strong>
                <small>Final standings resolved</small>
            </div>
        </article>
        <article class="admin-stat-card stat-red">
            <span class="stat-icon"><i class="ti ti-users" aria-hidden="true"></i></span>
            <div>
                <p>Total Entrants</p>
                <strong>{{ $stats['participants'] }}</strong>
                <small>Registered players / teams</small>
            </div>
        </article>
    </section>

    {{-- Tournaments table --}}
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>All Tournaments</h2>
                <p>Click a tournament name to manage its bracket, participants, and matches.</p>
            </div>
        </div>

        @if ($tournaments->isEmpty())
            <div style="padding: 60px 24px; text-align: center; color: var(--muted);">
                <i class="ti ti-trophy" style="font-size: 52px; display: block; margin-bottom: 14px; opacity: 0.25;"></i>
                <h3 style="margin: 0 0 8px; color: var(--muted-mid); font-size: 18px;">No tournaments yet</h3>
                <p style="margin: 0 0 20px; font-size: 13px;">Create your first tournament to get started with bracket generation.</p>
                <a href="{{ route('admin.tournaments.create') }}"
                   style="background:var(--lime);color:#000;font-weight:700;padding:10px 20px;border-radius:8px;display:inline-flex;align-items:center;gap:8px;font-size:13px;">
                    <i class="ti ti-plus"></i> Create Tournament
                </a>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border);color:var(--muted);font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
                            <th style="padding:10px 20px;text-align:left;">Tournament</th>
                            <th style="padding:10px 16px;text-align:left;">Format</th>
                            <th style="padding:10px 16px;text-align:left;">Registration Period</th>
                            <th style="padding:10px 16px;text-align:left;">Start Date</th>
                            <th style="padding:10px 16px;text-align:center;">Players</th>
                            <th style="padding:10px 16px;text-align:right;">Entry Fee</th>
                            <th style="padding:10px 16px;text-align:left;">Status</th>
                            <th style="padding:10px 20px;text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tournaments as $t)
                            <tr style="border-bottom:1px solid var(--border);transition:background 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.025)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Name + icon --}}
                                <td style="padding:16px 20px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <span style="width:36px;height:36px;border-radius:8px;background:var(--lime-dim);border:1px solid rgba(191,255,0,0.15);display:grid;place-items:center;flex-shrink:0;">
                                            <i class="ti ti-trophy" style="color:var(--lime);font-size:16px;"></i>
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.tournaments.show', $t) }}"
                                               style="color:#fff;font-weight:600;font-size:13px;display:block;line-height:1.3;">
                                                {{ $t->name }}
                                            </a>
                                            @if($t->description)
                                                <span style="color:var(--muted);font-size:11px;display:block;margin-top:1px;">
                                                    {{ Str::limit($t->description, 50) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Format --}}
                                <td style="padding:16px;color:var(--muted-mid);">
                                    {{ $t->type === 'single' ? 'Single Elim.' : ($t->type === 'double' ? 'Double Elim.' : 'Round Robin') }}
                                </td>

                                {{-- Registration --}}
                                <td style="padding:16px;color:var(--muted);">
                                    {{ $t->registration_start->format('M d') }} – {{ $t->registration_end->format('M d, Y') }}
                                </td>

                                {{-- Start Date --}}
                                <td style="padding:16px;color:var(--text);">
                                    {{ $t->start_date->format('M d, Y') }}
                                </td>

                                {{-- Players --}}
                                <td style="padding:16px;text-align:center;">
                                    <span style="color:var(--text);font-weight:600;">{{ $t->participants_count }}</span>
                                    <span style="color:var(--muted);"> / {{ $t->max_participants }}</span>
                                </td>

                                {{-- Entry fee ₱ --}}
                                <td style="padding:16px;text-align:right;font-weight:700;color:var(--lime);">
                                    ₱{{ number_format($t->entry_fee, 2) }}
                                </td>

                                {{-- Status badge --}}
                                <td style="padding:16px;">
                                    <span class="status status-{{ $t->status }}"
                                          style="padding:3px 8px;border-radius:4px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;">
                                        {{ str_replace('_', ' ', $t->status) }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td style="padding:16px 20px;text-align:right;">
                                    <div style="display:inline-flex;gap:8px;align-items:center;">
                                        <a href="{{ route('admin.tournaments.show', $t) }}"
                                           class="btn btn-outline btn-sm"
                                           style="padding:5px 10px;font-size:11px;font-weight:600;white-space:nowrap;">
                                            <i class="ti ti-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.tournaments.edit', $t) }}"
                                           class="btn btn-outline btn-sm"
                                           style="padding:5px 10px;font-size:11px;font-weight:600;">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.tournaments.destroy', $t) }}"
                                              class="delete-form"
                                              data-confirm-title="Delete Tournament"
                                              data-confirm="Delete '{{ $t->name }}'? All matches, brackets, and schedules will be permanently removed.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-outline btn-sm"
                                                    style="padding:5px 10px;font-size:11px;color:var(--coral);border-color:rgba(255,92,58,0.2);">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($tournaments->hasPages())
                <div style="padding:14px 20px;border-top:1px solid var(--border);">
                    {{ $tournaments->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection
