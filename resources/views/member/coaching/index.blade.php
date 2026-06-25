@extends('layouts.app')
@section('title', 'My Coaching Logs')

@section('content')
    <div class="page-head">
        <div>
            <h1>My Coaching Logs</h1>
            <p class="subtitle">Coaching sessions logged for you.</p>
        </div>
    </div>

    <div class="card">
        @if ($logs->isEmpty())
            <div class="empty-state">No coaching logs yet.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Week</th>
                        <th>Session Date</th>
                        <th>Logged By</th>
                        <th>Acknowledgment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->coaching_type }}</td>
                            <td>{{ $log->week_number ?? '—' }}</td>
                            <td>{{ $log->session_date ? $log->session_date->setTimezone('Asia/Manila')->format('M d, Y') : '—' }}
                            </td>
                            <td>{{ $log->creator->name }}</td>
                            <td>
                                @if ($log->acknowledged_at)
                                    <span class="badge badge-active"
                                        style="background:#dcfce7;color:#16a34a;">Acknowledged</span>
                                @else
                                    <span class="badge" style="background:#fef3c7;color:#b45309;">Not yet acknowledged</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('member.coaching.show', $log) }}" class="btn btn-ghost"
                                    style="font-size:12px;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:16px 20px;">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
