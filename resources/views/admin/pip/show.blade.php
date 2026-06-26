@extends('layouts.app')
@section('title', 'PIP Record')

@section('content')
    <div class="page-head">
        <div>
            <h1>PIP Record</h1>
            <p class="subtitle">{{ $pip->member->name }}{{ $pip->category ? ' — ' . $pip->category : '' }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.pip.edit', $pip) }}" class="btn btn-ghost">Edit</a>
            <a href="{{ route('admin.pip.index') }}" class="btn btn-ghost">← Back</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div class="card" style="padding:20px;grid-column:1/-1;">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Employee</p>
                    <p><strong>{{ $pip->member->name }}</strong></p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Category</p>
                    <p>{{ $pip->category ?? '—' }}</p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Due Date</p>
                    <p>{{ $pip->due_date ? $pip->due_date->format('M d, Y') : '—' }}</p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Client</p>
                    <p>{{ $pip->client_name ?? '—' }}</p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Team Leader</p>
                    <p>{{ $pip->team_leader ?? '—' }}</p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Manager</p>
                    <p>{{ $pip->manager ?? '—' }}</p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Offense / Occurrence</p>
                    <p>{{ $pip->offense_occurrence ?? '—' }}</p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Created By</p>
                    <p>{{ $pip->creator->name }}</p>
                </div>
                <div>
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                        Date Added</p>
                    <p>{{ $pip->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        @if ($pip->engagement)
            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:12px;">
                    Engagement</h3>
                <p style="white-space:pre-line;">{{ $pip->engagement }}</p>
            </div>
        @endif

        @if ($pip->reinforce)
            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:12px;">
                    Reinforce</h3>
                <p style="white-space:pre-line;">{{ $pip->reinforce }}</p>
            </div>
        @endif

        @if ($pip->areas_for_improvement)
            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:12px;">
                    Areas for Improvement</h3>
                <p style="white-space:pre-line;">{{ $pip->areas_for_improvement }}</p>
            </div>
        @endif

    </div>
@endsection
