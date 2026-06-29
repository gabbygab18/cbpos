<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectionRequest;
use App\Models\LoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceCorrectionController extends Controller
{
    public function index()
    {
        $pending = AttendanceCorrectionRequest::with(['user', 'loginLog'])
            ->pending()
            ->latest()
            ->get();

        $resolved = AttendanceCorrectionRequest::with(['user', 'loginLog', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at')
            ->take(20)
            ->get();

        return view('admin.corrections.index', compact('pending', 'resolved'));
    }

    public function approve(Request $request, AttendanceCorrectionRequest $correction)
    {
        $data = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $log = $correction->loginLog;
        $tz  = 'Asia/Manila';

        // Apply the corrected times to the actual login_log
        if ($correction->requested_login_at) {
            $log->login_at = Carbon::parse(
                $log->work_date->format('Y-m-d') . ' ' . $correction->requested_login_at,
                $tz
            )->utc();
        }

        if ($correction->requested_logout_at) {
            $log->logout_at = Carbon::parse(
                $log->work_date->format('Y-m-d') . ' ' . $correction->requested_logout_at,
                $tz
            )->utc();
        }

        $log->save();

        $correction->update([
            'status'      => 'approved',
            'reviewed_by' => $request->user()->id,
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Correction approved and shift updated.');
    }

    public function reject(Request $request, AttendanceCorrectionRequest $correction)
    {
        $data = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $correction->update([
            'status'      => 'rejected',
            'reviewed_by' => $request->user()->id,
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Correction request rejected.');
    }
}
