@extends('layouts.app')
@section('title', 'Coaching Logs')

@section('content')
    <div class="page-head">
        <div>
            <h1>Coaching Logs</h1>
            <p class="subtitle">Track coaching sessions for team members.</p>
        </div>
        <a href="{{ route('admin.coaching.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Coaching Log
        </a>
    </div>

    <div class="card" style="margin-bottom:16px;padding:16px 20px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--slate);display:block;margin-bottom:4px;">Filter by
                    Employee</label>
                <select name="member_id" onchange="this.form.submit()"
                    style="border:1px solid var(--border);border-radius:8px;padding:6px 12px;font-size:13px;">
                    <option value="">All Employees</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            @if (request('member_id'))
                <a href="{{ route('admin.coaching.index') }}" class="btn btn-ghost" style="font-size:13px;">Clear</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($logs->isEmpty())
            <div class="empty-state">No coaching logs yet.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Week</th>
                        <th>Session Date</th>
                        <th>Created By</th>
                        <th>Logged On</th>
                        <th>Acknowledgment</th>
                        <th>Acknowledged Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td><strong>{{ $log->member->name }}</strong></td>
                            <td>{{ $log->coaching_type }}</td>
                            <td>{{ $log->week_number ?? '—' }}</td>
                            <td>{{ $log->session_date ? $log->session_date->setTimezone('Asia/Manila')->format('M d, Y') : '—' }}
                            </td>
                            <td>{{ $log->creator->name }}</td>
                            <td>{{ $log->created_at->format('M d, Y') }}</td>
                            <td>
                                @if ($log->acknowledged_at)
                                    <span class="badge badge-active"
                                        style="background:#dcfce7;color:#16a34a;">Acknowledged</span>
                                @else
                                    <span class="badge" style="background:#fef3c7;color:#b45309;">Not yet
                                        acknowledged</span>
                                @endif
                            </td>
                            <td>{{ $log->acknowledged_at ? $log->acknowledged_at->setTimezone('Asia/Manila')->format('M d, Y g:i A') : '—' }}
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.coaching.show', $log) }}" class="btn btn-ghost"
                                    style="font-size:12px;">View</a>
                                <a href="{{ route('admin.coaching.edit', $log) }}" class="btn btn-ghost"
                                    style="font-size:12px;">Edit</a>
                                <form action="{{ route('admin.coaching.destroy', $log) }}" method="POST"
                                    style="display:inline;" onsubmit="return confirm('Delete this coaching log?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost" style="font-size:12px;color:var(--red);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:16px 20px;">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
