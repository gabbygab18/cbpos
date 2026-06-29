<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrectionRequest;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $corrections = AttendanceCorrectionRequest::where('user_id', $request->user()->id)
            ->with('loginLog')
            ->latest()
            ->get();

        return view('member.corrections.index', compact('corrections'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $logs = LoginLog::where('user_id', $user->id)
            ->orderByDesc('work_date')
            ->take(30)
            ->get();

        return view('member.corrections.create', compact('logs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'login_log_id'        => 'required|exists:login_logs,id',
            'requested_login_at'  => 'nullable|date_format:H:i',
            'requested_logout_at' => 'nullable|date_format:H:i',
            'reason'              => 'required|string|max:1000',
        ]);

        $log = LoginLog::where('id', $data['login_log_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Must request at least one correction
        if (empty($data['requested_login_at']) && empty($data['requested_logout_at'])) {
            return back()->withErrors(['requested_login_at' => 'Please provide at least one corrected time.'])->withInput();
        }

        // One pending correction per employee at a time
        $exists = AttendanceCorrectionRequest::where('user_id', $request->user()->id)
            ->pending()
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have a pending correction request. Please wait for it to be resolved before submitting another.')->withInput();
        }

        AttendanceCorrectionRequest::create([
            'user_id'             => $request->user()->id,
            'login_log_id'        => $log->id,
            'work_date'           => $log->work_date,
            'requested_login_at'  => $data['requested_login_at'] ?? null,
            'requested_logout_at' => $data['requested_logout_at'] ?? null,
            'reason'              => $data['reason'],
        ]);

        return redirect()->route('member.corrections.index')
            ->with('success', 'Correction request submitted.');
    }
}
