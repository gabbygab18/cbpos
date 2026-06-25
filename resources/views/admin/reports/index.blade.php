@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="page-head">
        <div>
            <h1>Handling Time Reports</h1>
            <p class="subtitle">Filter by date range and member to monitor total handling time.</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="form-row" style="align-items:flex-end;">
            <div class="field">
                <label for="from">From</label>
                <input type="date" name="from" id="from" value="{{ $from }}">
            </div>
            <div class="field">
                <label for="to">To</label>
                <input type="date" name="to" id="to" value="{{ $to }}">
            </div>
            <div class="field">
                <label for="user_id">Member</label>
                <select name="user_id" id="user_id">
                    <option value="">All members</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" {{ (string) $selectedMemberId === (string) $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="flex: 0 0 auto;">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    @if ($byMember->isEmpty())
        <div class="card"><div class="empty-state">No task logs found for this range.</div></div>
    @endif

    @foreach ($byMember as $row)
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 4px;">
                <h2 style="margin:0;">{{ $row['user']->name }}</h2>
                <a href="{{ route('admin.reports.member-detail', $row['user']->id) }}?from={{ $from }}&to={{ $to }}" class="btn btn-ghost btn-sm">Open timesheet</a>
            </div>
            <p class="text-muted" style="font-size:13px; margin: 0 0 14px;">
                {{ $row['task_count'] }} tasks &middot; {{ \App\Support\Duration::decimalHours($row['total_minutes']) }} total
            </p>
        </div>
    @endforeach
@endsection
