<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftLogController extends Controller
{
    /** Clock in — start a new shift for today. */
    public function clockIn(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::now()->format('Y-m-d');

        $openLog = LoginLog::where('user_id', $user->id)
            ->where('work_date', $today)
            ->whereNull('logout_at')
            ->latest('id')
            ->first();

        if (!$openLog) {
            LoginLog::create([
                'user_id'   => $user->id,
                'work_date' => $today,
                'login_at'  => Carbon::now(),
            ]);
        }

        return back()->with('status', 'Clocked in.');
    }

    /** Clock out — end the current open shift, if any. */
    public function clockOut(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::now()->format('Y-m-d');

        $openLog = LoginLog::where('user_id', $user->id)
            ->where('work_date', $today)
            ->whereNull('logout_at')
            ->latest('id')
            ->first();

        if ($openLog) {
            $openLog->update(['logout_at' => Carbon::now()]);
        }

        return back()->with('status', 'Clocked out.');
    }
}
