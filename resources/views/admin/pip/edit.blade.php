@extends('layouts.app')
@section('title', 'Edit PIP Record')

@section('content')
    <div class="page-head">
        <div>
            <h1>Edit PIP Record</h1>
            <p class="subtitle">{{ $pip->member->name }}</p>
        </div>
        <a href="{{ route('admin.pip.show', $pip) }}" class="btn btn-ghost">← Back</a>
    </div>

    <form action="{{ route('admin.pip.update', $pip) }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Basic Info</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">Member *</label>
                        <select name="member_id" class="form-control" required>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}" {{ $pip->member_id == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="{{ $pip->category }}">
                    </div>
                    <div>
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control"
                            value="{{ $pip->due_date?->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Personnel</h3>
                <div style="display:grid;gap:12px;">
                    <div><label class="form-label">Client Name</label><input type="text" name="client_name"
                            class="form-control" value="{{ $pip->client_name }}"></div>
                    <div><label class="form-label">Team Leader</label><input type="text" name="team_leader"
                            class="form-control" value="{{ $pip->team_leader }}"></div>
                    <div><label class="form-label">Manager</label><input type="text" name="manager" class="form-control"
                            value="{{ $pip->manager }}"></div>
                    <div><label class="form-label">Offense / Occurrence</label><input type="text"
                            name="offense_occurrence" class="form-control" value="{{ $pip->offense_occurrence }}"></div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Plan Details</h3>
                <div style="display:grid;gap:12px;">
                    <div><label class="form-label">Engagement</label>
                        <textarea name="engagement" class="form-control" rows="3">{{ $pip->engagement }}</textarea>
                    </div>
                    <div><label class="form-label">Reinforce</label>
                        <textarea name="reinforce" class="form-control" rows="3">{{ $pip->reinforce }}</textarea>
                    </div>
                    <div><label class="form-label">Areas for Improvement</label>
                        <textarea name="areas_for_improvement" class="form-control" rows="3">{{ $pip->areas_for_improvement }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('admin.pip.show', $pip) }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
@endsection
