@extends('layouts.app')

@section('title', 'My Day')

@section('content')
    <div class="page-head">
        <div>
            <h1>My Day</h1>
            <p class="subtitle">{{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</p>
        </div>
    </div>

    {{-- ── Coaching notifications ── --}}
    @php
        $unreadNotifs = \App\Models\Notification::forUser(auth()->id())
            ->unread()
            ->latest()
            ->get();
    @endphp

    @if ($unreadNotifs->isNotEmpty())
        <div class="notif-stack" style="margin-bottom:20px;">
            @foreach ($unreadNotifs as $notif)
                @php
                    $viewUrl = match ($notif->notifiable_type) {
                        \App\Models\CoachingLog::class => $notif->notifiable
                            ? route('member.coaching.show', $notif->notifiable_id)
                            : null,
                        default => null,
                    };
                @endphp
                <div class="notif-banner">
                    <div class="notif-banner__body">
                        <iconify-icon icon="material-symbols:info-outline" width="18" height="18"
                            style="color:var(--blue);flex-shrink:0;margin-top:1px;"></iconify-icon>
                        <div>
                            <strong>{{ $notif->title }}</strong>
                            @if ($notif->message)
                                <p class="notif-banner__msg">{{ $notif->message }}</p>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        @if ($viewUrl)
                            <a href="{{ $viewUrl }}" class="notif-banner__ack" style="text-decoration:none;">
                                <iconify-icon icon="material-symbols:visibility-outline" width="16"
                                    height="16"></iconify-icon>
                                View
                            </a>
                        @endif
                        <form method="POST" action="{{ route('member.notifications.read', $notif) }}">
                            @csrf
                            <button type="submit" class="notif-banner__ack">
                                <iconify-icon icon="material-symbols:check-small" width="16"
                                    height="16"></iconify-icon>
                                Got it
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if ($unreadNotifs->count() > 1)
                <div style="display:flex;justify-content:flex-end;margin-top:6px;">
                    <form method="POST" action="{{ route('member.notifications.readAll') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost" style="font-size:12px;color:var(--slate);">Dismiss
                            all</button>
                    </form>
                </div>
            @endif
        </div>
    @endif

    {{-- ── Quick actions ── --}}
    <div class="quick-actions">
        @if ($shiftLog && $shiftLog->isOnShift())
            <form method="POST" action="{{ route('shift.out') }}" style="display:inline;">
                @csrf
                <button type="submit" class="quick-btn quick-btn--danger">
                    <iconify-icon icon="material-symbols:logout" width="18" height="18"></iconify-icon>
                    Time Out
                    <span class="quick-btn__meta">since
                        {{ $shiftLog->login_at->setTimezone('Asia/Manila')->format('M d, g:i A') }}</span>
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('shift.in') }}" style="display:inline;">
                @csrf
                <button type="submit" class="quick-btn quick-btn--success">
                    <iconify-icon icon="material-symbols:login" width="18" height="18"></iconify-icon>
                    Time In
                </button>
            </form>
        @endif
        <a href="{{ route('member.leaves.create') }}" class="quick-btn">
            <iconify-icon icon="material-symbols:add-circle-outline" width="18" height="18"></iconify-icon>
            File Leave
        </a>
        <a href="{{ route('member.leaves.index') }}" class="quick-btn">
            <iconify-icon icon="material-symbols:event-available-outline" width="18" height="18"></iconify-icon>
            My Leave History
        </a>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-bottom: 20px;">
        <div class="stat-card">
            <div class="stat-card__icon teal">
                <iconify-icon icon="material-symbols:task-alt-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ $taskCount }}</div>
                <div class="stat-card__label">Tasks Today</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon blue">
                <iconify-icon icon="material-symbols:schedule-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ \App\Support\Duration::decimalHours($totalMinutes) }}</div>
                <div class="stat-card__label">Total Hours</div>
            </div>
        </div>
        @php
            $myLeaveCount = \App\Models\LeaveRequest::where('user_id', auth()->id())
                ->pending()
                ->count();
        @endphp
        <div class="stat-card">
            <div class="stat-card__icon amber">
                <iconify-icon icon="material-symbols:event-available-outline" width="22" height="22"></iconify-icon>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__num">{{ $myLeaveCount }}</div>
                <div class="stat-card__label">Leave Pending</div>
            </div>
        </div>
    </div>


    {{-- ── Attendance ── --}}
    <div class="card">
        <div class="card-header">
            <h2>
                <iconify-icon icon="material-symbols:schedule-outline" width="16" height="16"
                    style="vertical-align:-2px;"></iconify-icon>
                Today's Attendance
            </h2>
        </div>

        @if ($shiftLog)
            @php
                $tz = 'Asia/Manila';
                $onShift = $shiftLog->isOnShift();
            @endphp
            <div class="stat-row" style="margin-bottom:0;">
                <div class="stat">
                    <div class="num" style="font-size:20px;">
                        {{ $shiftLog->login_at->setTimezone($tz)->format('g:i A') }}
                    </div>
                    <div class="label">Time In</div>
                </div>
                <div class="stat">
                    <div class="num" style="font-size:20px;">
                        @if ($onShift)
                            <span style="color:var(--slate);">—</span>
                        @else
                            {{ $shiftLog->logout_at->setTimezone($tz)->format('g:i A') }}
                        @endif
                    </div>
                    <div class="label">Time Out</div>
                </div>
                <div class="stat">
                    <div class="num" style="font-size:20px;">
                        @if ($onShift)
                            <span id="attendance-clock" class="mono" style="font-size:18px;">—</span>
                        @else
                            {{ \App\Support\Duration::short($shiftLog->shiftDurationMinutes()) }}
                        @endif
                    </div>
                    <div class="label">Total Shift Time</div>
                </div>
                <div class="stat">
                    <div class="num" style="font-size:20px;">
                        @if ($onShift)
                            <span class="badge badge-on-shift" style="font-size:12px;">
                                <span
                                    style="width:6px;height:6px;border-radius:50%;background:currentColor;margin-right:5px;animation:pulse 1.4s infinite;display:inline-block;"></span>
                                On Shift
                            </span>
                        @else
                            <span class="badge badge-inactive">Shift Ended</span>
                        @endif
                    </div>
                    <div class="label">Status</div>
                </div>
            </div>
        @else
            <div class="empty-state" style="padding:20px;">
                You haven't timed in today.
            </div>
        @endif
    </div>

    {{-- ── Timer banner (running task) ── --}}
    @if ($runningLog)
        <div class="timer-banner" id="timer-banner" data-started="{{ $runningLog->started_at->timestamp * 1000 }}">
            <div class="info">
                <span class="pulse"></span>
                <div>
                    <strong>{{ optional($runningLog->reportType)->name }}</strong>
                    <span class="text-muted"> &middot; {{ optional($runningLog->facility)->name }}</span>
                    <div class="text-muted" style="font-size:12px; margin-top:2px;">Started
                        {{ $runningLog->started_at->setTimezone('Asia/Manila')->format('g:i A') }}</div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:16px;">
                <span class="clock mono" id="timer-clock">00:00:00</span>
                <form method="POST" action="{{ route('tasks.stop', $runningLog) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Stop task</button>
                </form>
            </div>
        </div>
    @else
        {{-- ── Start task form ── --}}
        <div class="card">
            <h2>Start a Task</h2>
            <form method="POST" action="{{ route('tasks.start') }}">
                @csrf
                <div class="form-row">
                    <div class="field">
                        <label for="report_type_id">Type of Report / Task</label>
                        <select name="report_type_id" id="report_type_id" required>
                            <option value="">Select task type&hellip;</option>
                            @foreach ($reportTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="facility_id">Facility</label>
                        <select name="facility_id" id="facility_id" required>
                            <option value="">Select facility&hellip;</option>
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label for="notes">Notes <span class="text-muted">(optional)</span></label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Anything worth noting about this task"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="material-symbols:play-circle-outline" width="16"
                        height="16"></iconify-icon>
                    Start task — timer begins now
                </button>
            </form>
        </div>
    @endif

    {{-- ── Today's task log ── --}}
    <div class="card">
        <h2>Today's Tasks</h2>
        @if ($logs->isEmpty())
            <div class="empty-state">No tasks stamped yet today.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Type of Report</th>
                        <th>Facility</th>
                        <th>Time</th>
                        <th class="nowrap">Duration</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ optional($log->reportType)->name ?? '—' }}</td>
                            <td>{{ optional($log->facility)->name ?? '—' }}</td>
                            <td class="mono nowrap">
                                {{ $log->started_at->setTimezone('Asia/Manila')->format('g:i A') }}
                                &ndash;
                                @if ($log->ended_at)
                                    {{ $log->ended_at->setTimezone('Asia/Manila')->format('g:i A') }}
                                @else
                                    running
                                @endif
                            </td>
                            <td class="mono">{{ \App\Support\Duration::short($log->duration_minutes) }}</td>
                            <td>
                                @if ($log->isRunning())
                                    <span class="badge badge-running">Running</span>
                                @else
                                    <span class="badge badge-completed">Completed</span>
                                @endif
                                @if ($log->last_edited_by)
                                    <span class="text-muted text-sm" title="Edited by admin">&middot; edited</span>
                                @endif
                            </td>
                            <td>
                                @if ($log->isRunning())
                                    <form method="POST" action="{{ route('tasks.destroy', $log) }}"
                                        onsubmit="return confirm('Remove this task? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        /* ── Notification banners ── */
        .notif-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .notif-banner {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-left: 3px solid var(--blue, #3b82f6);
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 13.5px;
        }

        .notif-banner__body {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            flex: 1;
        }

        .notif-banner__msg {
            margin: 2px 0 0;
            color: var(--slate);
            font-size: 12.5px;
        }

        .notif-banner__ack {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: transparent;
            border: 1px solid #93c5fd;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--blue, #3b82f6);
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s;
        }

        .notif-banner__ack:hover {
            background: #dbeafe;
        }

        /* ── Shift banner (member view) ── */
        .shift-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: var(--radius);
            padding: 14px 18px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 13.5px;
        }

        .shift-banner--active {
            background: #e6f4ee;
            border: 1px solid #b2dcc6;
            border-left: 3px solid #0a6640;
        }

        .shift-banner--ended {
            background: var(--teal-soft);
            border: 1px solid #c2ddd8;
            border-left: 3px solid var(--teal);
        }

        .shift-banner__info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .shift-banner__pulse {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #0a6640;
            animation: pulse 1.4s infinite;
            flex-shrink: 0;
        }

        .shift-banner__clock {
            font-size: 17px;
            font-weight: 600;
            color: #0a6640;
        }

        .badge-on-shift-sm {
            background: #c8ecd8;
            color: #0a6640;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 20px;
        }

        .quick-btn--success {
            background: #dcfce7;
            border-color: #86efac;
            color: #16a34a;
        }

        .quick-btn--success:hover {
            background: #bbf7d0;
        }

        .quick-btn--danger {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .quick-btn--danger:hover {
            background: #fecaca;
        }

        .quick-btn__meta {
            font-size: 11px;
            font-weight: 500;
            opacity: 0.8;
            margin-left: 2px;
        }

        .badge-on-shift {
            background: #e6f4ee;
            color: #0a6640;
            display: inline-flex;
            align-items: center;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Task timer
        const banner = document.getElementById('timer-banner');
        if (banner) {
            const started = parseInt(banner.dataset.started);
            const clockEl = document.getElementById('timer-clock');

            function tick() {
                const diff = Math.max(0, Math.floor((Date.now() - started) / 1000));
                const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                const s = String(diff % 60).padStart(2, '0');
                clockEl.textContent = `${h}:${m}:${s}`;
            }
            tick();
            setInterval(tick, 1000);
        }

        // Shift clock (live counter on active shift)
        const shiftBanner = document.getElementById('shift-banner');
        if (shiftBanner) {
            const loginTime = parseInt(shiftBanner.dataset.login);
            const shiftClockEl = document.getElementById('shift-clock');

            function tickShift() {
                const diff = Math.max(0, Math.floor((Date.now() - loginTime) / 1000));
                const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                const s = String(diff % 60).padStart(2, '0');
                shiftClockEl.textContent = `${h}:${m}:${s}`;
            }
            tickShift();
            setInterval(tickShift, 1000);
        }

        // Attendance shift clock
        @if ($shiftLog && $shiftLog->isOnShift())
            const attendanceClock = document.getElementById('attendance-clock');
            if (attendanceClock) {
                const loginTs = {{ $shiftLog->login_at->timestamp * 1000 }};

                function tickAttendance() {
                    const diff = Math.max(0, Math.floor((Date.now() - loginTs) / 1000));
                    const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                    const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                    const s = String(diff % 60).padStart(2, '0');
                    attendanceClock.textContent = h + ':' + m + ':' + s;
                }
                tickAttendance();
                setInterval(tickAttendance, 1000);
            }
        @endif
    </script>
@endpush
