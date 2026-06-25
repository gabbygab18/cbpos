@extends('layouts.app')
@section('title', 'Coaching Log')

@section('content')
    <div class="page-head">
        <div>
            <h1>Coaching Log</h1>
            <p class="subtitle">{{ $coaching->member->name }} — {{ $coaching->coaching_type }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.coaching.edit', $coaching) }}" class="btn btn-ghost">Edit</a>
            <a href="{{ route('admin.coaching.index') }}" class="btn btn-ghost">← Back</a>
        </div>
    </div>

    <div class="card" style="padding:24px;max-width:720px;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Member</p>
                <p><strong>{{ $coaching->member->name }}</strong></p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Coaching Type</p>
                <p><span class="badge badge-active">{{ $coaching->coaching_type }}</span></p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Week</p>
                <p>{{ $coaching->week_number ?? '—' }}</p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Session Date</p>
                <p>{{ $coaching->session_date ? $coaching->session_date->format('M d, Y') : '—' }}</p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Logged On</p>
                <p>{{ $coaching->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Created By</p>
                <p>{{ $coaching->creator->name }}</p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Acknowledgment</p>
                @if ($coaching->acknowledged_at)
                    <p><span class="badge badge-active" style="background:#dcfce7;color:#16a34a;">Acknowledged</span></p>
                @else
                    <p><span class="badge" style="background:#fef3c7;color:#b45309;">Not yet acknowledged</span></p>
                @endif
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Acknowledged Date</p>
                <p>{{ $coaching->acknowledged_at ? $coaching->acknowledged_at->setTimezone('Asia/Manila')->format('M d, Y g:i A') : '—' }}
                </p>
            </div>
        </div>

        @if ($coaching->discussion_points)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Discussion Points</p>
                <p style="white-space:pre-line;">{{ $coaching->discussion_points }}</p>
            </div>
        @endif

        @if ($coaching->action_items)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Action Items</p>
                <p style="white-space:pre-line;">{{ $coaching->action_items }}</p>
            </div>
        @endif

        @if ($coaching->notes)
            <div style="border-top:1px solid var(--border);padding-top:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Notes</p>
                <p style="white-space:pre-line;">{{ $coaching->notes }}</p>
            </div>
        @endif

        <div
            style="border-top:1px solid var(--border);padding-top:16px;margin-top:16px;display:flex;justify-content:flex-end;">
            <form action="{{ route('admin.coaching.destroy', $coaching) }}" method="POST"
                onsubmit="return confirm('Delete this coaching log?')">
                @csrf @method('DELETE')
                <button class="btn btn-ghost" style="color:var(--red);">Delete</button>
            </form>
        </div>
    </div>
@endsection
