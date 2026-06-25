@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
    <div class="page-head">
        <div>
            <h1>Leave Requests</h1>
            <p class="subtitle">Review and approve leave requests from team members.</p>
        </div>
    </div>

    {{-- ── Filter tabs ── --}}
    <div class="filter-tabs">
        @foreach ([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'all' => 'All',
        ] as $value => $label)
            <a href="{{ route('admin.leave-requests.index', array_merge(request()->query(), ['status' => $value])) }}"
                class="filter-tab {{ $status === $value ? 'active' : '' }}">
                {{ $label }}
                @if ($value === 'pending' && $pendingCount > 0)
                    <span
                        style="background:#e05555;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:20px;margin-left:4px;line-height:1.6;display:inline-block;">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- ── Secondary filters: month / leave type / member ── --}}
    <style>
        .filter-bar {
            display: flex;
            gap: 16px;
            align-items: flex-end;
            flex-wrap: wrap;
            background: #fff;
            border: 1px solid #e3e8ee;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        .filter-bar .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 0;
            min-width: 170px;
        }

        .filter-bar .field label {
            font-size: 12px;
            font-weight: 600;
            color: var(--slate, #5b6472);
            white-space: nowrap;
        }

        .filter-bar select {
            width: 100%;
            box-sizing: border-box;
            padding: 8px 32px 8px 12px;
            font-size: 13.5px;
            color: #1f2937;
            background-color: #fff;
            border: 1px solid #d6dce3;
            border-radius: 7px;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235b6472' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
            cursor: pointer;
        }

        .filter-bar select:focus {
            outline: none;
            border-color: #2f9e8f;
            box-shadow: 0 0 0 3px rgba(47, 158, 143, 0.12);
        }

        .filter-bar .clear-filters {
            font-size: 13px;
            padding: 8px 14px;
        }

        @media (max-width: 640px) {
            .filter-bar {
                padding: 14px;
            }

            .filter-bar .field {
                min-width: 140px;
                flex: 1 1 140px;
            }
        }
    </style>
    <form method="GET" action="{{ route('admin.leave-requests.index') }}" id="extra-filters-form" class="filter-bar">
        {{-- preserve the current status tab when these filters submit --}}
        <input type="hidden" name="status" value="{{ $status }}">

        <div class="field">
            <label for="filter-month">Month</label>
            <select name="month" id="filter-month" onchange="document.getElementById('extra-filters-form').submit()">
                <option value="">All months</option>
                @foreach ($monthOptions as $value => $label)
                    <option value="{{ $value }}" {{ (string) $month === (string) $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="filter-leave-type">Leave Type</label>
            <select name="leave_type" id="filter-leave-type"
                onchange="document.getElementById('extra-filters-form').submit()">
                <option value="">All types</option>
                @foreach ($leaveTypes as $leaveType)
                    <option value="{{ $leaveType->id }}"
                        {{ (string) $leaveTypeId === (string) $leaveType->id ? 'selected' : '' }}>
                        {{ $leaveType->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="filter-member">Member</label>
            <select name="member" id="filter-member" onchange="document.getElementById('extra-filters-form').submit()">
                <option value="">All members</option>
                @foreach ($members as $member)
                    <option value="{{ $member->id }}"
                        {{ (string) $memberId === (string) $member->id ? 'selected' : '' }}>
                        {{ $member->name }}
                    </option>
                @endforeach
            </select>
        </div>

        @if ($month || $leaveTypeId || $memberId)
            <div class="field" style="min-width:0;">
                <a href="{{ route('admin.leave-requests.index', ['status' => $status]) }}"
                    class="btn btn-ghost btn-sm clear-filters">
                    Clear filters
                </a>
            </div>
        @endif
    </form>

    <div class="card">
        @if ($requests->isEmpty())
            <div class="empty-state">No {{ $status !== 'all' ? $status : '' }} leave requests.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Leave Type</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Filed</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $lr)
                        <tr>
                            <td><strong>{{ $lr->user->name }}</strong></td>
                            <td>
                                {{ optional($lr->leaveType)->name ?? '—' }}
                                @if ($lr->leaveType)
                                    <span class="badge {{ $lr->leaveType->is_paid ? 'badge-paid' : 'badge-unpaid' }}"
                                        style="margin-left:4px;">
                                        {{ $lr->leaveType->is_paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                @endif
                            </td>
                            <td class="nowrap text-muted">
                                {{ $lr->start_date->format('M j') }}
                                @if (!$lr->start_date->equalTo($lr->end_date))
                                    &ndash; {{ $lr->end_date->format('M j, Y') }}
                                @else
                                    , {{ $lr->start_date->format('Y') }}
                                @endif
                            </td>
                            <td>{{ number_format($lr->total_days, 0) }}d</td>
                            <td style="max-width: 200px;">
                                <span
                                    style="white-space: normal; font-size:12.5px;">{{ Str::limit($lr->reason, 60) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $lr->status }}">{{ ucfirst($lr->status) }}</span>
                            </td>
                            <td class="text-muted nowrap" style="font-size:12px;">
                                {{ $lr->created_at->format('M j, Y') }}
                            </td>
                            <td>
                                @if ($lr->status === 'pending')
                                    <div style="display:flex; gap:5px;">
                                        <button class="btn btn-success btn-sm"
                                            onclick="openApprove({{ $lr->id }}, '{{ addslashes($lr->user->name) }}')">
                                            Approve
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="openReject({{ $lr->id }}, '{{ addslashes($lr->user->name) }}')">
                                            Reject
                                        </button>
                                    </div>
                                @elseif ($lr->reviewed_at)
                                    <span class="text-muted text-sm">
                                        by {{ optional($lr->reviewedBy)->name ?? '—' }}<br>
                                        {{ $lr->reviewed_at->format('M j') }}
                                    </span>
                                @else
                                    <span class="text-muted text-sm">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($requests->hasPages())
                <div style="margin-top:16px;">{{ $requests->appends(request()->query())->links() }}</div>
            @endif
        @endif
    </div>

    {{-- ── Approve modal ── --}}
    <div class="modal-overlay" id="approve-modal">
        <div class="modal-box">
            <h3>Approve Leave Request</h3>
            <p style="color:var(--slate); margin-bottom:16px; font-size:13.5px;">
                Approving leave for <strong id="approve-name"></strong>.
            </p>
            <form method="POST" id="approve-form">
                @csrf
                <div class="field">
                    <label>Admin Notes <span class="text-muted">(optional)</span></label>
                    <textarea name="admin_notes" rows="2" placeholder="Any notes for the member…"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('approve-modal')">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Reject modal ── --}}
    <div class="modal-overlay" id="reject-modal">
        <div class="modal-box">
            <h3>Reject Leave Request</h3>
            <p style="color:var(--slate); margin-bottom:16px; font-size:13.5px;">
                Rejecting leave for <strong id="reject-name"></strong>.
            </p>
            <form method="POST" id="reject-form">
                @csrf
                <div class="field">
                    <label>Reason for Rejection <span style="color:var(--red);">*</span></label>
                    <textarea name="rejection_reason" rows="3" placeholder="Explain why this leave is being rejected…" required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('reject-modal')">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', (e) => {
                if (e.target === el) el.classList.remove('open');
            });
        });

        function openApprove(id, name) {
            document.getElementById('approve-form').action = `/admin/leave-requests/${id}/approve`;
            document.getElementById('approve-name').textContent = name;
            openModal('approve-modal');
        }

        function openReject(id, name) {
            document.getElementById('reject-form').action = `/admin/leave-requests/${id}/reject`;
            document.getElementById('reject-name').textContent = name;
            openModal('reject-modal');
        }
    </script>
@endpush
