@extends('layouts.app')
@section('title', 'Coaching Log')

@section('content')
    <div class="page-head">
        <div>
            <h1>Coaching Log</h1>
            <p class="subtitle">{{ $coaching->coaching_type }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('member.coaching.index') }}" class="btn btn-ghost">← Back</a>
        </div>
    </div>

    <div class="card" style="padding:24px;max-width:720px;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
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
                <p>{{ $coaching->session_date ? $coaching->session_date->setTimezone('Asia/Manila')->format('M d, Y') : '—' }}
                </p>
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
                    Logged By</p>
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

        @if ($coaching->root_cause_coachee)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Root Cause — Coachee</p>
                <p style="white-space:pre-line;">{{ $coaching->root_cause_coachee }}</p>
            </div>
        @endif

        @if ($coaching->coach_comments)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Coach Comments</p>
                <p style="white-space:pre-line;">{{ $coaching->coach_comments }}</p>
            </div>
        @endif

        @if ($coaching->tools)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Tools</p>
                <p style="white-space:pre-line;">{{ $coaching->tools }}</p>
            </div>
        @endif

        @if ($coaching->coachee_action_plan)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Coachee Action Plan</p>
                <p style="white-space:pre-line;">{{ $coaching->coachee_action_plan }}</p>
            </div>
        @endif

        @if ($coaching->coach_commitment)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Coach Commitment</p>
                <p style="white-space:pre-line;">{{ $coaching->coach_commitment }}</p>
            </div>
        @endif

        @if ($coaching->duration)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Duration</p>
                <p style="white-space:pre-line;">{{ $coaching->duration }}</p>
            </div>
        @endif

        @if ($coaching->targets)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Targets</p>
                <p style="white-space:pre-line;">{{ $coaching->targets }}</p>
            </div>
        @endif

        @if ($coaching->employee_combined_text)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Employee Combined Text</p>
                <p style="white-space:pre-line;">{{ $coaching->employee_combined_text }}</p>
            </div>
        @endif

        @if ($coaching->client_classification)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Client Classification</p>
                <p style="white-space:pre-line;">{{ $coaching->client_classification }}</p>
            </div>
        @endif

        @if (!$coaching->acknowledged_at)
            <div
                style="border-top:1px solid var(--border);padding-top:16px;margin-top:16px;display:flex;justify-content:flex-end;">
                <form action="{{ route('member.coaching.acknowledge', $coaching) }}" method="POST"
                    onsubmit="return confirm('Acknowledge this coaching log? This cannot be undone.')">
                    @csrf
                    <button class="btn btn-primary">Acknowledge</button>
                </form>
            </div>
        @endif
    </div>
@endsection
