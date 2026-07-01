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

        $log      = $correction->loginLog;
        $tz       = 'Asia/Manila';
        $workDate = $log->work_date->format('Y-m-d');

        // Resolve the corrected login time first — it's the anchor point.
        $newLoginAt = $correction->requested_login_at
            ? Carbon::parse($workDate . ' ' . $correction->requested_login_at, $tz)
            : $log->login_at->copy()->setTimezone($tz);

        // Resolve logout relative to login. If it lands at or before login,
        // the shift crosses midnight — push it to the next calendar day.
        if ($correction->requested_logout_at) {
            $newLogoutAt = Carbon::parse($workDate . ' ' . $correction->requested_logout_at, $tz);

            if ($newLogoutAt->lessThanOrEqualTo($newLoginAt)) {
                $newLogoutAt->addDay();
            }

            $log->logout_at = $newLogoutAt->utc();
        }

        $log->login_at = $newLoginAt->utc();
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
