@extends('layouts.app')
@section('title', 'My Correction Requests')
@section('content')

    <div class="page-head">
        <div>
            <h1>Attendance Corrections</h1>
            <p class="subtitle">Track your submitted correction requests.</p>
        </div>
        <a href="{{ route('member.corrections.create') }}" class="btn btn-primary btn-sm">
            <iconify-icon icon="material-symbols:add" width="15" height="15"></iconify-icon>
            New Request
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    <div class="card">
        @if ($corrections->isEmpty())
            <div class="empty-state">No correction requests yet.</div>
        @else
            <table class="compact">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Original</th>
                        <th>Requested</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Admin Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($corrections as $c)
                        @php
                            $tz = 'Asia/Manila';
                            $log = $c->loginLog;
                        @endphp
                        <tr>
                            <td class="nowrap">{{ $c->work_date->format('M j, Y') }}</td>
                            <td class="mono nowrap text-muted" style="font-size:12px;">
                                In: {{ $log->login_at->setTimezone($tz)->format('g:i A') }}<br>
                                Out: {{ $log->logout_at ? $log->logout_at->setTimezone($tz)->format('g:i A') : '—' }}
                            </td>
                            <td class="mono nowrap" style="font-size:12px;">
                                @if ($c->requested_login_at)
                                    In: {{ \Carbon\Carbon::parse($c->requested_login_at)->format('g:i A') }}<br>
                                @endif
                                @if ($c->requested_logout_at)
                                    Out: {{ \Carbon\Carbon::parse($c->requested_logout_at)->format('g:i A') }}
                                @endif
                            </td>
                            <td style="font-size:13px;">{{ $c->reason }}</td>
                            <td>
                                @if ($c->status === 'pending')
                                    <span class="badge badge-inactive"
                                        style="background:#fef9c3;color:#854d0e;">Pending</span>
                                @elseif ($c->status === 'approved')
                                    <span class="badge badge-completed"
                                        style="background:#dcfce7;color:#166534;">Approved</span>
                                @else
                                    <span class="badge badge-inactive"
                                        style="background:#fee2e2;color:#991b1b;">Rejected</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size:12px;">{{ $c->admin_notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
