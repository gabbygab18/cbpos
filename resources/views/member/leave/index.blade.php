@extends('layouts.app')

@section('title', 'My Leaves')

@section('content')
    <div class="page-head">
        <div>
            <h1>My Leaves</h1>
            <p class="subtitle">Track your leave requests and their status.</p>
        </div>
        <a href="{{ route('member.leaves.create') }}" class="btn btn-primary">
            <iconify-icon icon="material-symbols:add" width="17" height="17"></iconify-icon>
            File New Leave
        </a>
    </div>

    @if ($leaves->isEmpty())
        <div class="card">
            <div class="empty-state">
                <iconify-icon icon="material-symbols:event-available-outline" width="36" height="36" style="color:var(--border); display:block; margin: 0 auto 10px;"></iconify-icon>
                You haven't filed any leave requests yet.
                <br>
                <a href="{{ route('member.leaves.create') }}" style="margin-top:10px; display:inline-block;">File your first leave →</a>
            </div>
        </div>
    @else
        <div class="card">
            <table>
                <thead>
                    <tr>
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
                    @foreach ($leaves as $lr)
                        <tr>
                            <td>
                                <strong>{{ optional($lr->leaveType)->name ?? '—' }}</strong>
                                @if ($lr->leaveType)
                                    <div class="text-muted text-sm">{{ $lr->leaveType->is_paid ? 'Paid' : 'Unpaid' }}</div>
                                @endif
                            </td>
                            <td class="nowrap">
                                {{ $lr->start_date->format('M j, Y') }}
                                @if (!$lr->start_date->equalTo($lr->end_date))
                                    <br><span class="text-muted text-sm">to {{ $lr->end_date->format('M j, Y') }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($lr->total_days, 0) }}d</td>
                            <td style="max-width:180px;">
                                <span style="font-size:12.5px;">{{ Str::limit($lr->reason, 70) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $lr->status }}">{{ ucfirst($lr->status) }}</span>
                                @if ($lr->status === 'rejected' && $lr->rejection_reason)
                                    <div class="text-muted text-sm" style="margin-top:3px;" title="{{ $lr->rejection_reason }}">
                                        "{{ Str::limit($lr->rejection_reason, 40) }}"
                                    </div>
                                @endif
                                @if ($lr->status === 'approved' && $lr->admin_notes)
                                    <div class="text-muted text-sm" style="margin-top:3px;">
                                        {{ Str::limit($lr->admin_notes, 40) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-muted nowrap" style="font-size:12px;">
                                {{ $lr->created_at->format('M j, Y') }}
                            </td>
                            <td>
                                @if ($lr->status === 'pending')
                                    <form method="POST" action="{{ route('member.leaves.cancel', $lr) }}"
                                          onsubmit="return confirm('Cancel this leave request?');">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
