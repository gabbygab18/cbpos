<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user || !$user->is_active) {
            return back()->withErrors([
                'email' => 'No active account found with that email.',
            ])->onlyInput('email');
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Record a fresh login stamp for today (members and admins both, for the work-hour log).
        // $today = Carbon::now()->format('Y-m-d');
        // $openLog = LoginLog::where('user_id', $user->id)
        //     ->where('work_date', $today)
        //     ->whereNull('logout_at')
        //     ->latest('id')
        //     ->first();

        // if (!$openLog) {
        //     LoginLog::create([
        //         'user_id' => $user->id,
        //         'work_date' => $today,
        //         'login_at' => Carbon::now(),
        //     ]);
        // }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // $today = Carbon::now()->format('Y-m-d');
            // $openLog = LoginLog::where('user_id', $user->id)
            //     ->where('work_date', $today)
            //     ->whereNull('logout_at')
            //     ->latest('id')
            //     ->first();

            // if ($openLog) {
            //     $openLog->update(['logout_at' => Carbon::now()]);
            // }

            // Auto-stop any task the member forgot to stop before logging out,
            // so handling-time totals don't run forever in the background.
            \App\Models\TaskLog::where('user_id', $user->id)
                ->where('status', \App\Models\TaskLog::STATUS_RUNNING)
                ->each(function ($log) {
                    $log->ended_at = Carbon::now();
                    $log->status = \App\Models\TaskLog::STATUS_COMPLETED;
                    $log->recalculateDuration();
                    $log->save();
                });
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
