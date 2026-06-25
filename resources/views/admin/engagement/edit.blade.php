@extends('layouts.app')
@section('title', 'Edit Engagement Record')

@section('content')
    <div class="page-head">
        <div>
            <h1>Edit Engagement Record</h1>
            <p class="subtitle">{{ $engagement->member->name }}</p>
        </div>
        <a href="{{ route('admin.engagement.show', $engagement) }}" class="btn btn-ghost">← Back</a>
    </div>

    <form action="{{ route('admin.engagement.update', $engagement) }}" method="POST">
        @csrf @method('PUT')
        <div class="card" style="padding:24px;max-width:720px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <label class="form-label">Member *</label>
                    <select name="member_id" class="form-control" required>
                        @foreach ($members as $m)
                            <option value="{{ $m->id }}" {{ $engagement->member_id == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Record Type *</label>
                    <select name="record_type" class="form-control" required>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}" {{ $engagement->record_type == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ $engagement->title }}" required>
                </div>
                <div>
                    <label class="form-label">Record Date *</label>
                    <input type="date" name="record_date" class="form-control"
                        value="{{ $engagement->record_date->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="form-label">Score <span style="color:var(--slate);font-weight:400;">(0–100,
                            optional)</span></label>
                    <input type="number" name="score" class="form-control" value="{{ $engagement->score }}"
                        min="0" max="100" step="0.01">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="4">{{ $engagement->notes }}</textarea>
                </div>
            </div>
            <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('admin.engagement.show', $engagement) }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
@endsection
