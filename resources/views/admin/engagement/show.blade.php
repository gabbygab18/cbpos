@extends('layouts.app')
@section('title', 'Engagement Record')

@section('content')
    <div class="page-head">
        <div>
            <h1>Engagement Record</h1>
            <p class="subtitle">{{ $engagement->member->name }} — {{ $engagement->type_label }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.engagement.edit', $engagement) }}" class="btn btn-ghost">Edit</a>
            <a href="{{ route('admin.engagement.index') }}" class="btn btn-ghost">← Back</a>
        </div>
    </div>

    <div class="card" style="padding:24px;max-width:720px;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Employee</p>
                <p><strong>{{ $engagement->member->name }}</strong></p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Type</p>
                <p><span class="badge badge-active">{{ $engagement->type_label }}</span></p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Date</p>
                <p>{{ $engagement->record_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Title</p>
                <p>{{ $engagement->title }}</p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Score</p>
                <p>{{ $engagement->score !== null ? number_format($engagement->score, 2) : '—' }}</p>
            </div>
            <div>
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:4px;">
                    Added By</p>
                <p>{{ $engagement->creator->name }}</p>
            </div>
        </div>
        @if ($engagement->notes)
            <div style="border-top:1px solid var(--border);padding-top:16px;">
                <p
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:8px;">
                    Notes</p>
                <p style="white-space:pre-line;">{{ $engagement->notes }}</p>
            </div>
        @endif
    </div>
@endsection
