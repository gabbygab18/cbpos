@extends('layouts.app')
@section('title', 'Request Attendance Correction')
@section('content')

    <div class="page-head">
        <div>
            <h1>Request Attendance Correction</h1>
            <p class="subtitle">Submit a correction for an incorrect time in or time out.</p>
        </div>
        <a href="{{ route('member.corrections.index') }}" class="btn btn-ghost btn-sm">Back</a>
    </div>

    @if (session('error'))
        <div class="alert alert-error" style="margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('member.corrections.store') }}">
            @csrf

            <div class="field" style="margin-bottom:16px;">
                <label for="login_log_id">Select Shift to Correct</label>
                <select name="login_log_id" id="login_log_id" required>
                    <option value="">Choose a shift date…</option>
                    @foreach ($logs as $log)
                        <option value="{{ $log->id }}" {{ old('login_log_id') == $log->id ? 'selected' : '' }}>
                            {{ $log->work_date->format('D, M j, Y') }}
                            — In: {{ $log->login_at->setTimezone('Asia/Manila')->format('g:i A') }}
                            @if ($log->logout_at)
                                · Out: {{ $log->logout_at->setTimezone('Asia/Manila')->format('g:i A') }}
                            @else
                                · Out: —
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('login_log_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row" style="margin-bottom:16px;">
                <div class="field">
                    <label for="requested_login_at">
                        Corrected Time In
                        <span class="text-muted">(leave blank if unchanged)</span>
                    </label>
                    <input type="time" name="requested_login_at" id="requested_login_at"
                        value="{{ old('requested_login_at') }}">
                    @error('requested_login_at')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="requested_logout_at">
                        Corrected Time Out
                        <span class="text-muted">(leave blank if unchanged)</span>
                    </label>
                    <input type="time" name="requested_logout_at" id="requested_logout_at"
                        value="{{ old('requested_logout_at') }}">
                    @error('requested_logout_at')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="field" style="margin-bottom:20px;">
                <label for="reason">Reason for Correction <span style="color:red;">*</span></label>
                <textarea name="reason" id="reason" rows="3" required
                    placeholder="e.g. Forgot to time in, system error, etc.">{{ old('reason') }}</textarea>
                @error('reason')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Submit Correction Request</button>
        </form>
    </div>
@endsection
