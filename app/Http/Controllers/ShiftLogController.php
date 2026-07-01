<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftLogController extends Controller
{
    /** Clock in — start a new shift. */
    public function clockIn(Request $request)
    {
        $user = $request->user();

        $openLog = LoginLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('id')
            ->first();

        if ($openLog) {
            return back()->with('error', 'You are already on shift. Please time out first.');
        }

        LoginLog::create([
            'user_id'   => $user->id,
            'work_date' => Carbon::now()->format('Y-m-d'), // anchor date = day shift started
            'login_at'  => Carbon::now(),
        ]);

        return back()->with('status', 'Clocked in.');
    }

    /** Clock out — end the current open shift, regardless of what calendar day it is now. */
    public function clockOut(Request $request)
    {
        $user = $request->user();

        $openLog = LoginLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('id')
            ->first();

        if (!$openLog) {
            return back()->with('error', 'No active shift found.');
        }

        $openLog->update(['logout_at' => Carbon::now()]);

        return back()->with('status', 'Clocked out.');
    }
}
