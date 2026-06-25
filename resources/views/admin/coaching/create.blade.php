@extends('layouts.app')
@section('title', 'New Coaching Log')

@section('content')
    <div class="page-head">
        <div>
            <h1>New Coaching Log</h1>
            <p class="subtitle">Record a coaching session.</p>
        </div>
        <a href="{{ route('admin.coaching.index') }}" class="btn btn-ghost">← Back</a>
    </div>

    <form action="{{ route('admin.coaching.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Basic Info</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">
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
                        <label class="form-label">Coaching Type *</label>
                        @php
                            $coachingTypes = [
                                'Employee Check-in',
                                'Incident Report',
                                'Performance Coaching',
                                'PIP Coaching',
                            ];
                        @endphp
                        <select name="coaching_type" class="form-control" required>
                            <option value="">Select type</option>
                            @foreach ($coachingTypes as $type)
                                <option value="{{ $type }}" {{ old('coaching_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Week Number</label>
                        <select name="week_number" class="form-control">
                            <option value="">Select week</option>
                            @foreach (['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'] as $week)
                                <option value="{{ $week }}" {{ old('week_number') == $week ? 'selected' : '' }}>
                                    {{ $week }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Session Date *</label>
                        <input type="date" name="session_date" class="form-control"
                            value="{{ old('session_date', \Carbon\Carbon::today()->toDateString()) }}" required>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Root Cause</h3>
                <div style="display:grid;gap:12px;">
                    <div>
                        <label class="form-label">Root Cause – Coachee</label>
                        <textarea name="root_cause_coachee" class="form-control" rows="3">{{ old('root_cause_coachee') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Coach Comments</label>
                        <textarea name="coach_comments" class="form-control" rows="3">{{ old('coach_comments') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Action Plan</h3>
                <div style="display:grid;gap:12px;">
                    <div>
                        <label class="form-label">Tools</label>
                        <input type="text" name="tools" class="form-control" value="{{ old('tools') }}">
                    </div>
                    <div>
                        <label class="form-label">Coachee Action Plan</label>
                        <textarea name="coachee_action_plan" class="form-control" rows="3">{{ old('coachee_action_plan') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Coach Commitment</label>
                        <textarea name="coach_commitment" class="form-control" rows="3">{{ old('coach_commitment') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Timeline</h3>
                <div style="display:grid;gap:12px;">
                    <div>
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control" value="{{ old('duration') }}"
                            placeholder="e.g. 30 minutes">
                    </div>
                    <div>
                        <label class="form-label">Targets</label>
                        <textarea name="targets" class="form-control" rows="3">{{ old('targets') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Acknowledgement</h3>
                <div style="display:grid;gap:12px;">
                    <div>
                        <label class="form-label">Employee Combined Text</label>
                        <textarea name="employee_combined_text" class="form-control" rows="3">{{ old('employee_combined_text') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Client Classification</label>
                        <input type="text" name="client_classification" class="form-control"
                            value="{{ old('client_classification') }}">
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('admin.coaching.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Coaching Log</button>
        </div>
    </form>
@endsection
