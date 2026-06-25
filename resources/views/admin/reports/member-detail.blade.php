@extends('layouts.app')

@section('title', $member->name . ' — Timesheet')

@section('content')
    <div class="page-head no-print">
        <div>
            <h1>{{ $member->name }}</h1>
            <p class="subtitle">{{ \Carbon\Carbon::parse($from)->format('M j, Y') }} &ndash;
                {{ \Carbon\Carbon::parse($to)->format('M j, Y') }}</p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost">&larr; Back</a>
            <button onclick="window.print()" class="btn btn-primary">Print</button>
        </div>
    </div>

    <div class="card no-print">
        <form method="GET" class="form-row" style="align-items:flex-end;">
            <div class="field">
                <label for="from">From</label>
                <input type="date" name="from" id="from" value="{{ $from }}">
            </div>
            <div class="field">
                <label for="to">To</label>
                <input type="date" name="to" id="to" value="{{ $to }}">
            </div>
            <div class="field" style="flex: 0 0 auto;">
                <button type="submit" class="btn btn-primary">Update range</button>
            </div>
        </form>
    </div>

    <div class="stat-row">
        <div class="stat">
            <div class="num">{{ $logsByDate->flatten()->count() }}</div>
            <div class="label">Total Tasks</div>
        </div>
        <div class="stat">
            <div class="num">{{ \App\Support\Duration::decimalHours($grandTotalMinutes) }}</div>
            <div class="label">Total Handling Time</div>
        </div>
        <div class="stat">
            <div class="num">{{ $logsByDate->count() }}</div>
            <div class="label">Days Worked</div>
        </div>
    </div>

    @if ($logsByDate->isEmpty())
        <div class="card">
            <div class="empty-state">No task logs in this range.</div>
        </div>
    @endif

    @foreach ($logsByDate as $dateKey => $logs)
        @php
            $dayCompleted = $logs->where('status', 'completed');
            $dayMinutes = $dayCompleted->sum('duration_minutes');
        @endphp
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:14px;">
                <h2 style="margin:0;">{{ \Carbon\Carbon::parse($dateKey)->format('l, F j, Y') }}</h2>
                <span class="mono text-muted">{{ $logs->count() }} tasks &middot;
                    {{ \App\Support\Duration::decimalHours($dayMinutes) }}</span>
            </div>
            <table class="compact">
                <thead>
                    <tr>
                        <th>Type of Report</th>
                        <th>Facility</th>
                        <th>Time</th>
                        <th class="nowrap">Duration</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ optional($log->reportType)->name ?? '—' }}</td>
                            <td>{{ optional($log->facility)->name ?? '—' }}</td>
                            <td class="mono nowrap">
                                {{ $log->started_at->setTimezone('Asia/Manila')->format('g:i A') }} &ndash;
                                {{ $log->ended_at ? $log->ended_at->setTimezone('Asia/Manila')->format('g:i A') : 'running' }}
                            </td>
                            <td class="mono">{{ \App\Support\Duration::short($log->duration_minutes) }}</td>
                            <td class="no-print">
                                <button type="button" class="btn btn-ghost btn-sm"
                                    onclick="openEdit({{ $log->id }}, '{{ optional($log->reportType)->id }}', '{{ optional($log->facility)->id }}', '{{ $log->work_date->format('Y-m-d') }}', '{{ $log->started_at->format('H:i') }}', '{{ $log->ended_at ? $log->ended_at->format('H:i') : '' }}')">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Edit modal: admin-only adjustment of time/date -->
    <div class="modal-overlay no-print" id="edit-modal">
        <div class="modal-box">
            <h3>Edit task log</h3>
            <form method="POST" id="edit-form">
                @csrf
                @method('PUT')
                <div class="field">
                    <label>Type of Report</label>
                    <select name="report_type_id" id="edit-report-type" required>
                        @foreach (\App\Models\ReportType::ordered()->get() as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Facility</label>
                    <select name="facility_id" id="edit-facility" required>
                        @foreach (\App\Models\Facility::ordered()->get() as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Work date</label>
                    <input type="date" name="work_date" id="edit-date" required>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label>Start time</label>
                        <input type="time" name="start_time" id="edit-start" required>
                    </div>
                    <div class="field">
                        <label>End time <span class="text-muted">(blank = still running)</span></label>
                        <input type="time" name="end_time" id="edit-end">
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeEdit()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .no-print,
            .topbar {
                display: none !important;
            }

            .card {
                border: none;
                box-shadow: none;
                padding: 0;
                margin-bottom: 24px;
            }

            body {
                background: #fff;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function openEdit(id, reportTypeId, facilityId, workDate, start, end) {
            document.getElementById('edit-form').action = `/admin/tasks/${id}`;
            document.getElementById('edit-report-type').value = reportTypeId;
            document.getElementById('edit-facility').value = facilityId;
            document.getElementById('edit-date').value = workDate;
            document.getElementById('edit-start').value = start;
            document.getElementById('edit-end').value = end;
            document.getElementById('edit-modal').classList.add('open');
        }

        function closeEdit() {
            document.getElementById('edit-modal').classList.remove('open');
        }
    </script>
@endpush
