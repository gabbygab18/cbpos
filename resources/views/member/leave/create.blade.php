@extends('layouts.app')

@section('title', 'File Leave')

@section('content')
    <div class="page-head">
        <div>
            <h1>File a Leave</h1>
            <p class="subtitle">Submit a leave request for admin approval.</p>
        </div>
        <a href="{{ route('member.leaves.index') }}" class="btn btn-ghost">
            &larr; My Leaves
        </a>
    </div>

    <div class="card" style="max-width: 560px;">
        <form method="POST" action="{{ route('member.leaves.store') }}">
            @csrf
            <div class="field">
                <label for="leave_type_id">Leave Type <span style="color:var(--red);">*</span></label>
                @if ($leaveTypes->isEmpty())
                    <p class="text-muted" style="font-size:13px; margin-top:4px;">No leave types are set up yet. Please contact your administrator.</p>
                @else
                    <select name="leave_type_id" id="leave_type_id" required>
                        <option value="">Select type of leave&hellip;</option>
                        @foreach ($leaveTypes as $lt)
                            <option value="{{ $lt->id }}" {{ old('leave_type_id') == $lt->id ? 'selected' : '' }}>
                                {{ $lt->name }} ({{ $lt->is_paid ? 'Paid' : 'Unpaid' }})
                                @if ($lt->default_days) — {{ $lt->default_days }}d/yr @endif
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="start_date">Start Date <span style="color:var(--red);">*</span></label>
                    <input type="date" name="start_date" id="start_date"
                           value="{{ old('start_date') }}"
                           min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="field">
                    <label for="end_date">End Date <span style="color:var(--red);">*</span></label>
                    <input type="date" name="end_date" id="end_date"
                           value="{{ old('end_date') }}"
                           min="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <p class="form-hint" id="days-preview" style="margin-top: -8px; margin-bottom: 14px;"></p>

            <div class="field">
                <label for="reason">Reason <span style="color:var(--red);">*</span></label>
                <textarea name="reason" id="reason" rows="4"
                          placeholder="Please explain the reason for your leave request…"
                          required maxlength="1000">{{ old('reason') }}</textarea>
                <p class="form-hint">Be clear and specific. Your admin will review this.</p>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary" {{ $leaveTypes->isEmpty() ? 'disabled' : '' }}>
                    <iconify-icon icon="material-symbols:send-outline" width="16" height="16"></iconify-icon>
                    Submit Leave Request
                </button>
                <a href="{{ route('member.leaves.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const startInput = document.getElementById('start_date');
    const endInput   = document.getElementById('end_date');
    const preview    = document.getElementById('days-preview');

    function updatePreview() {
        const s = startInput.value;
        const e = endInput.value;
        if (s && e && e >= s) {
            const start = new Date(s);
            const end   = new Date(e);
            const days  = Math.round((end - start) / 86400000) + 1;
            preview.textContent = `${days} calendar day${days !== 1 ? 's' : ''} selected.`;
        } else {
            preview.textContent = '';
        }
        // Keep end_date min = start_date
        if (s) endInput.min = s;
    }

    startInput.addEventListener('change', updatePreview);
    endInput.addEventListener('change', updatePreview);
    updatePreview();
</script>
@endpush
