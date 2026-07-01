<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\TaskLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->adminOverview($request);
        }

        return $this->memberToday($user);
    }

    private function memberToday(User $user)
    {
        $today = Carbon::now()->format('Y-m-d');

        // Today's logs, PLUS any running task regardless of which day it started
        // (so an overnight task doesn't vanish from view once midnight passes).
        $logs = TaskLog::with(['reportType', 'facility'])
            ->where('user_id', $user->id)
            ->forWorkDateOrRunning($today)
            ->orderByDesc('started_at')
            ->get();

        $runningLog   = $logs->firstWhere('status', TaskLog::STATUS_RUNNING);
        $totalMinutes = $logs->where('status', TaskLog::STATUS_COMPLETED)->sum('duration_minutes');

        // Prefer an open shift regardless of start date; fall back to today's shift log.
        $shiftLog = LoginLog::where('user_id', $user->id)
            ->open()
            ->latest('id')
            ->first();

        if (!$shiftLog) {
            $shiftLog = LoginLog::where('user_id', $user->id)
                ->forDate($today)
                ->latest('id')
                ->first();
        }

        return view('member.dashboard', [
            'logs'         => $logs,
            'runningLog'   => $runningLog,
            'totalMinutes' => $totalMinutes,
            'taskCount'    => $logs->count(),
            'reportTypes'  => \App\Models\ReportType::active()->ordered()->get(),
            'facilities'   => \App\Models\Facility::active()->ordered()->get(),
            'today'        => $today,
            'shiftLog'     => $shiftLog,
        ]);
    }

    private function adminOverview(Request $request)
    {
        $date    = $request->query('date') ?: Carbon::now()->format('Y-m-d');
        $isToday = $date === Carbon::now()->format('Y-m-d');

        $members = User::where('role', User::ROLE_EMPLOYEE)
            ->orderBy('name')
            ->get();

        // For "today", also catch running tasks that started on a previous date.
        // For any other (historical) date, keep strict work_date filtering.
        $logsByUser = TaskLog::with(['reportType', 'facility'])
            ->when(
                $isToday,
                fn ($q) => $q->forWorkDateOrRunning($date),
                fn ($q) => $q->forWorkDate($date)
            )
            ->orderBy('started_at')
            ->get()
            ->groupBy('user_id');

        $shiftsByUser = LoginLog::forDate($date)
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->sortByDesc('id')->first());

        // For "today", also surface shifts that are still open even if they
        // started on a previous calendar date (overnight shifts).
        if ($isToday) {
            $openShifts = LoginLog::open()
                ->get()
                ->groupBy('user_id')
                ->map(fn ($rows) => $rows->sortByDesc('id')->first());

            foreach ($openShifts as $userId => $shift) {
                $shiftsByUser[$userId] = $shift;
            }
        }

        $summaries = $members->map(function ($member) use ($logsByUser, $shiftsByUser) {
            $logs      = $logsByUser->get($member->id, collect());
            $completed = $logs->where('status', TaskLog::STATUS_COMPLETED);
            $shift     = $shiftsByUser->get($member->id);

            return [
                'user'          => $member,
                'logs'          => $logs,
                'task_count'    => $logs->count(),
                'total_minutes' => $completed->sum('duration_minutes'),
                'has_running'   => $logs->contains('status', TaskLog::STATUS_RUNNING),
                'shift'         => $shift,
            ];
        });

        $attendanceLogs = LoginLog::with('user')
            ->forDate($date)
            ->orderBy('login_at')
            ->get();

        return view('admin.dashboard', [
            'summaries'      => $summaries,
            'date'           => $date,
            'attendanceLogs' => $attendanceLogs,
        ]);
    }
}
