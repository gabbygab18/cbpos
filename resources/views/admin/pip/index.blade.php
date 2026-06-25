@extends('layouts.app')
@section('title', 'PIP Records')

@section('content')
    <div class="page-head">
        <div>
            <h1>PIP Records</h1>
            <p class="subtitle">Performance Improvement Plan records for team members.</p>
        </div>
        <a href="{{ route('admin.pip.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add PIP Record
        </a>
    </div>

    <div class="card" style="margin-bottom:16px;padding:16px 20px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--slate);display:block;margin-bottom:4px;">Filter by
                    Member</label>
                <select name="member_id" onchange="this.form.submit()"
                    style="border:1px solid var(--border);border-radius:8px;padding:6px 12px;font-size:13px;">
                    <option value="">All Members</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            @if (request('member_id'))
                <a href="{{ route('admin.pip.index') }}" class="btn btn-ghost" style="font-size:13px;">Clear</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($pips->isEmpty())
            <div class="empty-state">No PIP records yet.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Category</th>
                        <th>Client</th>
                        <th>Due Date</th>
                        <th>Created By</th>
                        <th>Date Added</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pips as $pip)
                        <tr>
                            <td><strong>{{ $pip->member->name }}</strong></td>
                            <td>{{ $pip->category ?? '—' }}</td>
                            <td>{{ $pip->client_name ?? '—' }}</td>
                            <td>{{ $pip->due_date ? $pip->due_date->format('M d, Y') : '—' }}</td>
                            <td>{{ $pip->creator->name }}</td>
                            <td>{{ $pip->created_at->format('M d, Y') }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.pip.show', $pip) }}" class="btn btn-ghost"
                                    style="font-size:12px;">View</a>
                                <a href="{{ route('admin.pip.edit', $pip) }}" class="btn btn-ghost"
                                    style="font-size:12px;">Edit</a>
                                <form action="{{ route('admin.pip.destroy', $pip) }}" method="POST"
                                    style="display:inline;" onsubmit="return confirm('Delete this PIP record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost" style="font-size:12px;color:var(--red);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:16px 20px;">{{ $pips->links() }}</div>
        @endif
    </div>
@endsection
