@extends('layouts.app')
@section('title', 'Attendance Correction Requests')
@section('content')

    <div class="page-head">
        <div>
            <h1>Attendance Corrections</h1>
            <p class="subtitle">Review and approve employee time correction requests.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    {{-- ── Pending ── --}}
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="margin-bottom:16px;">
            <h2>
                <iconify-icon icon="material-symbols:pending-actions" width="18" height="18"
                    style="vertical-align:-3px;margin-right:6px;"></iconify-icon>
                Pending Requests
            </h2>
            <span class="badge badge-inactive" style="background:#fef9c3;color:#854d0e;">
                {{ $pending->count() }} pending
            </span>
        </div>

        @if ($pending->isEmpty())
            <div class="empty-state">No pending correction requests.</div>
        @else
            <table class="compact">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Original</th>
                        <th>Requested</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pending as $c)
                        @php
                            $tz = 'Asia/Manila';
                            $log = $c->loginLog;
                        @endphp
                        <tr>
                            <td><strong>{{ $c->user->name }}</strong></td>
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
                            <td style="font-size:13px; max-width:200px;">{{ $c->reason }}</td>
                            <td>
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                    {{-- Approve --}}
                                    <form method="POST" action="{{ route('admin.corrections.approve', $c) }}"
                                        onsubmit="return confirmAction(this, 'approve')">
                                        @csrf
                                        <input type="hidden" name="admin_notes" class="admin-notes-input" value="">
                                        <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                    </form>
                                    {{-- Reject --}}
                                    <form method="POST" action="{{ route('admin.corrections.reject', $c) }}"
                                        onsubmit="return confirmAction(this, 'reject')">
                                        @csrf
                                        <input type="hidden" name="admin_notes" class="admin-notes-input" value="">
                                        <button type="submit" class="btn btn-ghost btn-sm"
                                            style="color:var(--red, #dc2626);">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ── Resolved ── --}}
    <div class="card">
        <div class="card-header" style="margin-bottom:16px;">
            <h2>
                <iconify-icon icon="material-symbols:history" width="18" height="18"
                    style="vertical-align:-3px;margin-right:6px;"></iconify-icon>
                Recent Resolved
            </h2>
        </div>

        @if ($resolved->isEmpty())
            <div class="empty-state">No resolved requests yet.</div>
        @else
            <table class="compact">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th>Reviewed By</th>
                        <th>Admin Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resolved as $c)
                        <tr>
                            <td><strong>{{ $c->user->name }}</strong></td>
                            <td class="nowrap">{{ $c->work_date->format('M j, Y') }}</td>
                            <td class="mono nowrap" style="font-size:12px;">
                                @if ($c->requested_login_at)
                                    In: {{ \Carbon\Carbon::parse($c->requested_login_at)->format('g:i A') }}<br>
                                @endif
                                @if ($c->requested_logout_at)
                                    Out: {{ \Carbon\Carbon::parse($c->requested_logout_at)->format('g:i A') }}
                                @endif
                            </td>
                            <td>
                                @if ($c->status === 'approved')
                                    <span class="badge" style="background:#dcfce7;color:#166534;">Approved</span>
                                @else
                                    <span class="badge" style="background:#fee2e2;color:#991b1b;">Rejected</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size:12px;">
                                {{ optional($c->reviewer)->name ?? '—' }}<br>
                                <span
                                    style="font-size:11px;">{{ $c->reviewed_at?->setTimezone('Asia/Manila')->format('M j, g:i A') }}</span>
                            </td>
                            <td class="text-muted" style="font-size:12px;">{{ $c->admin_notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @push('scripts')
        <script>
            function confirmAction(form, type) {
                const notes = prompt(
                    type === 'approve' ?
                    'Add a note (optional):' :
                    'Reason for rejection (optional):'
                );
                if (notes === null) return false; // cancelled
                form.querySelector('.admin-notes-input').value = notes;
                return true;
            }
        </script>
    @endpush

@endsection
