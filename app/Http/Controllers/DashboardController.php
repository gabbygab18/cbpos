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

        $logs = TaskLog::with(['reportType', 'facility'])
            ->where('user_id', $user->id)
            ->forWorkDate($today)
            ->orderByDesc('started_at')
            ->get();

        $runningLog  = $logs->firstWhere('status', TaskLog::STATUS_RUNNING);
        $totalMinutes = $logs->where('status', TaskLog::STATUS_COMPLETED)->sum('duration_minutes');

        // Today's shift (login log for current session)
        $shiftLog = LoginLog::where('user_id', $user->id)
            ->forDate($today)
            ->latest('id')
            ->first();

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
        $date = $request->query('date') ?: Carbon::now()->format('Y-m-d');

        $members = User::where('role', User::ROLE_MEMBER)
            ->orderBy('name')
            ->get();

        $logsByUser = TaskLog::with(['reportType', 'facility'])
            ->forWorkDate($date)
            ->orderBy('started_at')
            ->get()
            ->groupBy('user_id');

        // Shift logs for today (latest per user)
        $shiftsByUser = LoginLog::forDate($date)
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->sortByDesc('id')->first());

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

        return view('admin.dashboard', [
            'summaries' => $summaries,
            'date'      => $date,
        ]);
    }
}
