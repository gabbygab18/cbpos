@extends('layouts.app')
@section('title', 'Engagement & KPIs')

@section('content')
    <div class="page-head">
        <div>
            <h1>Engagement & KPIs</h1>
            <p class="subtitle">Track team meetings, feedback, KPIs, scorecards, and more.</p>
        </div>
        <a href="{{ route('admin.engagement.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Record
        </a>
    </div>

    <div class="card" style="margin-bottom:16px;padding:16px 20px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label
                    style="font-size:12px;font-weight:600;color:var(--slate);display:block;margin-bottom:4px;">Employee</label>
                <select name="member_id" onchange="this.form.submit()"
                    style="border:1px solid var(--border);border-radius:8px;padding:6px 12px;font-size:13px;">
                    <option value="">All Employees</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label
                    style="font-size:12px;font-weight:600;color:var(--slate);display:block;margin-bottom:4px;">Type</label>
                <select name="record_type" onchange="this.form.submit()"
                    style="border:1px solid var(--border);border-radius:8px;padding:6px 12px;font-size:13px;">
                    <option value="">All Types</option>
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}" {{ request('record_type') == $key ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if (request('member_id') || request('record_type'))
                <a href="{{ route('admin.engagement.index') }}" class="btn btn-ghost" style="font-size:13px;">Clear</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($records->isEmpty())
            <div class="empty-state">No engagement records yet.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Score</th>
                        <th>Date</th>
                        <th>Added By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td><strong>{{ $record->member->name }}</strong></td>
                            <td>
                                <span class="badge badge-active" style="font-size:11px;">{{ $record->type_label }}</span>
                            </td>
                            <td>{{ $record->title }}</td>
                            <td>{{ $record->score !== null ? number_format($record->score, 1) : '—' }}</td>
                            <td>{{ $record->record_date->format('M d, Y') }}</td>
                            <td>{{ $record->creator->name }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.engagement.show', $record) }}" class="btn btn-ghost"
                                    style="font-size:12px;">View</a>
                                <a href="{{ route('admin.engagement.edit', $record) }}" class="btn btn-ghost"
                                    style="font-size:12px;">Edit</a>
                                <form action="{{ route('admin.engagement.destroy', $record) }}" method="POST"
                                    style="display:inline;" onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost" style="font-size:12px;color:var(--red);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:16px 20px;">{{ $records->links() }}</div>
        @endif
    </div>
@endsection
