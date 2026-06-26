@extends('layouts.app')
@section('title', 'New Engagement Record')

@section('content')
    <div class="page-head">
        <div>
            <h1>New Engagement Record</h1>
            <p class="subtitle">Log a KPI, scorecard, meeting, feedback, or recognition entry.</p>
        </div>
        <a href="{{ route('admin.engagement.index') }}" class="btn btn-ghost">← Back</a>
    </div>

    <form action="{{ route('admin.engagement.store') }}" method="POST">
        @csrf
        <div class="card" style="padding:24px;max-width:720px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <label class="form-label">Employee *</label>
                    <select name="member_id" class="form-control" required>
                        <option value="">Select member</option>
                        @foreach ($members as $m)
                            <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Record Type *</label>
                    <select name="record_type" class="form-control" required>
                        <option value="">Select type</option>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}" {{ old('record_type') == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required
                        placeholder="e.g. June KPI Review, Q2 Scorecard">
                </div>
                <div>
                    <label class="form-label">Record Date *</label>
                    <input type="date" name="record_date" class="form-control"
                        value="{{ old('record_date', date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Score <span style="color:var(--slate);font-weight:400;">(0–100,
                            optional)</span></label>
                    <input type="number" name="score" class="form-control" value="{{ old('score') }}" min="0"
                        max="100" step="0.01" placeholder="e.g. 95.5">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('admin.engagement.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Record</button>
            </div>
        </div>
    </form>
@endsection
