@extends('layouts.app')

@section('title', 'Team Overview')

@section('content')
    <div class="page-head no-print">
        <div>
            <h1>Team Overview</h1>
            <p class="subtitle">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <form method="GET" style="display:flex; gap:8px; align-items:center;">
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                    style="width:auto;">
            </form>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost btn-sm">
                <iconify-icon icon="material-symbols:bar-chart-4-bars-outline" width="15" height="15"></iconify-icon>
                Full Reports
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <iconify-icon icon="material-symbols:print-outline" width="15" height="15"></iconify-icon>
                Print Summary
            </button>
        </div>
    </div>

    {{-- ── Print header (only visible when printing) ── --}}
    <div class="print-only print-header">
        <h1>Shift Summary</h1>
        <p>{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</p>
    </div>

    {{-- ── Summary stat cards ── --}}
    <div class="stat-grid no-print">
        <div class="stat-card">
            <div class="stat-card__icon teal">
                <iconify-icon icon="material-symbols:group-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ $summaries->count() }}</div>
                <div class="stat-card__label">Total Employees</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon green">
                <iconify-icon icon="material-symbols:login" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">
                    {{ $summaries->filter(fn($r) => $r['shift'] && $r['shift']->isOnShift())->count() }}</div>
                <div class="stat-card__label">On Shift</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon amber">
                <iconify-icon icon="material-symbols:play-circle-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ $summaries->filter(fn($r) => $r['has_running'])->count() }}</div>
                <div class="stat-card__label">Currently Working</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon purple">
                <iconify-icon icon="material-symbols:task-alt-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ $summaries->sum('task_count') }}</div>
                <div class="stat-card__label">Total Tasks Today</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon blue">
                <iconify-icon icon="material-symbols:schedule-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ \App\Support\Duration::decimalHours($summaries->sum('total_minutes')) }}
                </div>
                <div class="stat-card__label">Total Hours Today</div>
            </div>
        </div>
        @php $pendingLeaves = \App\Models\LeaveRequest::pending()->count(); @endphp
        <div class="stat-card">
            <div class="stat-card__icon red">
                <iconify-icon icon="material-symbols:event-available-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ $pendingLeaves }}</div>
                <div class="stat-card__label">Pending Leave Requests</div>
            </div>
        </div>
    </div>

    {{-- ── Pending leave quick-view ── --}}
    @if ($pendingLeaves > 0)
        @php
            $pendingList = \App\Models\LeaveRequest::with(['user', 'leaveType'])
                ->pending()
                ->orderBy('created_at')
                ->take(3)
                ->get();
        @endphp
        <div class="card no-print" style="border-left: 3px solid var(--amber); margin-bottom: 20px;">
            <div class="card-header">
                <h2 style="color:var(--amber);">
                    <iconify-icon icon="material-symbols:warning-outline" width="16" height="16"
                        style="vertical-align:-2px;"></iconify-icon>
                    Pending Leave Requests
                </h2>
                <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-ghost btn-sm">View All</a>
            </div>
            <table class="compact">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendingList as $lr)
                        <tr>
                            <td><strong>{{ $lr->user->name }}</strong></td>
                            <td>{{ optional($lr->leaveType)->name }}</td>
                            <td class="nowrap text-muted">
                                {{ $lr->start_date->format('M j') }}
                                @if (!$lr->start_date->equalTo($lr->end_date))
                                    &ndash; {{ $lr->end_date->format('M j, Y') }}
                                @else
                                    , {{ $lr->start_date->format('Y') }}
                                @endif
                            </td>
                            <td>{{ number_format($lr->total_days, 0) }}d</td>
                            <td>
                                <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-ghost btn-sm">Review</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ── Print-only shift summary table ── --}}
    <div class="print-only">
        <table class="print-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Shift Duration</th>
                    <th>Tasks</th>
                    <th>Hours Worked</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summaries as $row)
                    @php
                        $shift = $row['shift'];
                        $tz = 'Asia/Manila';
                        $onShift = $shift && $shift->isOnShift();
                        $shiftEnded = $shift && !$shift->isOnShift();
                    @endphp
                    <tr>
                        <td>{{ $row['user']->name }}</td>
                        <td>{{ $shift ? $shift->login_at->setTimezone($tz)->format('g:i A') : '—' }}</td>
                        <td>{{ $shiftEnded ? $shift->logout_at->setTimezone($tz)->format('g:i A') : '—' }}</td>
                        <td>{{ $shift ? \App\Support\Duration::short($shift->shiftDurationMinutes()) : '—' }}</td>
                        <td>{{ $row['task_count'] }}</td>
                        <td>{{ \App\Support\Duration::decimalHours($row['total_minutes']) }}</td>
                        <td>
                            @if ($onShift)
                                On Shift
                            @elseif ($shiftEnded)
                                Shift Ended
                            @else
                                No Shift
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Totals</strong></td>
                    <td><strong>{{ $summaries->sum('task_count') }}</strong></td>
                    <td><strong>{{ \App\Support\Duration::decimalHours($summaries->sum('total_minutes')) }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ── Employee summaries ── --}}
    @if ($summaries->isEmpty())
        <div class="card">
            <div class="empty-state">No employees yet. Add your team under Employees.</div>
        </div>
    @endif

    @foreach ($summaries as $row)
        @php
            $shift = $row['shift'];
            $onShift = $shift && $shift->isOnShift();
            $shiftEnded = $shift && !$shift->isOnShift();
            $tz = 'Asia/Manila';
        @endphp
        <div class="card no-print">
            <div
                style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 14px; flex-wrap:wrap; gap:10px;">
                <div>
                    <h2 style="margin-bottom:2px;">{{ $row['user']->name }}</h2>
                    <span class="text-muted" style="font-size:12px;">
                        {{ $row['task_count'] }} task{{ $row['task_count'] != 1 ? 's' : '' }}
                        &middot; {{ \App\Support\Duration::decimalHours($row['total_minutes']) }} hrs
                    </span>
                </div>
                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    @if ($onShift)
                        <span class="badge badge-on-shift">
                            <span
                                style="width:6px;height:6px;border-radius:50%;background:currentColor;margin-right:5px;animation:pulse 1.4s infinite;display:inline-block;"></span>
                            On Shift
                            <span class="shift-clock mono" data-login="{{ $shift->login_at->timestamp * 1000 }}"
                                style="margin-left:6px; font-size:11px; opacity:0.85;">
                                —
                            </span>
                        </span>
                    @elseif ($shiftEnded)
                        <span class="badge badge-shift-ended"
                            title="Shift: {{ $shift->login_at->setTimezone($tz)->format('g:i A') }} – {{ $shift->logout_at->setTimezone($tz)->format('g:i A') }}">
                            <iconify-icon icon="material-symbols:logout" width="12" height="12"
                                style="margin-right:4px;"></iconify-icon>
                            Shift Ended
                            &middot;
                            {{ \App\Support\Duration::short($shift->shiftDurationMinutes()) }}
                        </span>
                    @else
                        <span class="badge badge-inactive">No Shift</span>
                    @endif

                    @if ($row['has_running'])
                        <span class="badge badge-running">
                            <span
                                style="width:6px;height:6px;border-radius:50%;background:currentColor;margin-right:5px;animation:pulse 1.4s infinite;display:inline-block;"></span>
                            Working now
                        </span>
                    @else
                        <span class="badge badge-inactive">Idle</span>
                    @endif

                    <a href="{{ route('admin.reports.member-detail', $row['user']) }}" class="btn btn-ghost btn-sm">
                        Full history
                    </a>
                </div>
            </div>

            @if ($shift)
                <div class="shift-summary-row">
                    <iconify-icon icon="material-symbols:schedule-outline" width="13" height="13"></iconify-icon>
                    <span>
                        Shift started at <strong>{{ $shift->login_at->setTimezone($tz)->format('g:i A') }}</strong>
                        @if ($shiftEnded)
                            &ndash; ended at <strong>{{ $shift->logout_at->setTimezone($tz)->format('g:i A') }}</strong>
                            &middot; Total:
                            <strong>{{ \App\Support\Duration::short($shift->shiftDurationMinutes()) }}</strong>
                        @else
                            &middot; In progress
                        @endif
                    </span>
                </div>
            @endif

            @if ($row['logs']->isEmpty())
                <div class="empty-state" style="padding: 16px;">No tasks stamped today.</div>
            @else
                <table class="compact">
                    <thead>
                        <tr>
                            <th>Type of Report</th>
                            <th>Facility</th>
                            <th>Time</th>
                            <th class="nowrap">Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($row['logs'] as $log)
                            <tr>
                                <td>{{ optional($log->reportType)->name ?? '—' }}</td>
                                <td>{{ optional($log->facility)->name ?? '—' }}</td>
                                <td class="mono nowrap">
                                    {{ $log->started_at->setTimezone($tz)->format('g:i A') }}
                                    &ndash;
                                    @if ($log->ended_at)
                                        {{ $log->ended_at->setTimezone($tz)->format('g:i A') }}
                                    @else
                                        running
                                    @endif
                                </td>
                                <td class="mono">{{ \App\Support\Duration::short($log->duration_minutes) }}</td>
                                <td>
                                    @if ($log->isRunning())
                                        <span class="badge badge-running">Running</span>
                                    @else
                                        <span class="badge badge-completed">Done</span>
                                    @endif
                                    @if ($log->last_edited_by)
                                        <span class="text-muted text-sm"
                                            title="Edited by {{ optional($log->lastEditedBy)->name }}">&middot;
                                            edited</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
@endsection

@push('styles')
    <style>
        .badge-on-shift {
            background: #e6f4ee;
            color: #0a6640;
            display: inline-flex;
            align-items: center;
        }

        .badge-shift-ended {
            background: #f1f3f5;
            color: var(--slate);
            display: inline-flex;
            align-items: center;
        }

        .shift-summary-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--slate);
            background: var(--bg);
            border-radius: 7px;
            padding: 7px 12px;
            margin-bottom: 12px;
        }

        .stat-card__icon.green {
            background: #e6f4ee;
            color: #0a6640;
        }

        /* ── Print ── */
        .print-only {
            display: none;
        }

        @media print {

            .no-print,
            .topbar {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .print-header {
                margin-bottom: 20px;
            }

            .print-header h1 {
                font-size: 20px;
                margin: 0 0 4px;
            }

            .print-header p {
                font-size: 13px;
                color: #555;
                margin: 0;
            }

            .print-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #ccc;
                padding: 6px 10px;
                text-align: left;
            }

            .print-table thead {
                background: #f0f0f0;
            }

            .print-table tfoot {
                background: #f8f8f8;
                font-weight: bold;
            }

            body {
                background: #fff;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('.shift-clock[data-login]').forEach(el => {
            const started = parseInt(el.dataset.login);

            function tick() {
                const diff = Math.max(0, Math.floor((Date.now() - started) / 1000));
                const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                const s = String(diff % 60).padStart(2, '0');
                el.textContent = h + ':' + m + ':' + s;
            }
            tick();
            setInterval(tick, 1000);
        });
    </script>
@endpush
