@extends('layouts.app')
@section('title', 'New PIP Record')

@section('content')
    <div class="page-head">
        <div>
            <h1>New PIP Record</h1>
            <p class="subtitle">Create a Performance Improvement Plan record.</p>
        </div>
        <a href="{{ route('admin.pip.index') }}" class="btn btn-ghost">← Back</a>
    </div>

    <form action="{{ route('admin.pip.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Basic Info</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">Member *</label>
                        <select name="member_id" class="form-control" required>
                            <option value="">Select member</option>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                            placeholder="e.g. Attendance, Performance">
                    </div>
                    <div>
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Personnel</h3>
                <div style="display:grid;gap:12px;">
                    <div>
                        <label class="form-label">Client Name</label>
                        <input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}">
                    </div>
                    <div>
                        <label class="form-label">Team Leader</label>
                        <input type="text" name="team_leader" class="form-control" value="{{ old('team_leader') }}">
                    </div>
                    <div>
                        <label class="form-label">Manager</label>
                        <input type="text" name="manager" class="form-control" value="{{ old('manager') }}">
                    </div>
                    <div>
                        <label class="form-label">Offense / Occurrence</label>
                        <input type="text" name="offense_occurrence" class="form-control"
                            value="{{ old('offense_occurrence') }}">
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Plan Details</h3>
                <div style="display:grid;gap:12px;">
                    <div>
                        <label class="form-label">Engagement</label>
                        <textarea name="engagement" class="form-control" rows="3">{{ old('engagement') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Reinforce</label>
                        <textarea name="reinforce" class="form-control" rows="3">{{ old('reinforce') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Areas for Improvement</label>
                        <textarea name="areas_for_improvement" class="form-control" rows="3">{{ old('areas_for_improvement') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('admin.pip.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Save PIP Record</button>
        </div>
    </form>
@endsection
