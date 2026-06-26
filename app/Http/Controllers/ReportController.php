<?php

namespace App\Http\Controllers;

use App\Models\TaskLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Admin report: summary of handling time per member over a date range,
     * with drill-down into each member's individual task stamps.
     */
    public function index(Request $request)
    {
        $from = $request->query('from') ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = $request->query('to') ?: Carbon::now()->format('Y-m-d');
        $memberId = $request->query('user_id');

        $members = User::where('role', User::ROLE_EMPLOYEE)->orderBy('name')->get();

        $query = TaskLog::with(['user', 'reportType', 'facility'])
            ->whereBetween('work_date', [$from, $to]);

        if ($memberId) {
            $query->where('user_id', $memberId);
        }

        $logs = $query->orderBy('work_date')->orderBy('started_at')->get();

        $byMember = $logs->groupBy('user_id')->map(function ($group) {
            $completed = $group->where('status', TaskLog::STATUS_COMPLETED);

            return [
                'user' => $group->first()->user,
                'task_count' => $group->count(),
                'total_minutes' => $completed->sum('duration_minutes'),
                'logs' => $group,
            ];
        })->sortBy(fn ($row) => $row['user']->name);

        return view('admin.reports.index', [
            'byMember' => $byMember,
            'members' => $members,
            'from' => $from,
            'to' => $to,
            'selectedMemberId' => $memberId,
        ]);
    }

    /**
     * One member's printable timesheet (per the "work hour summary" Arlene asked for),
     * grouped by day with type of report, facility, time range, and duration.
     */
    public function memberDetail(Request $request, User $member)
    {
        $from = $request->query('from') ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = $request->query('to') ?: Carbon::now()->format('Y-m-d');

        $logs = TaskLog::with(['reportType', 'facility'])
            ->where('user_id', $member->id)
            ->whereBetween('work_date', [$from, $to])
            ->orderBy('work_date')
            ->orderBy('started_at')
            ->get()
            ->groupBy(fn ($log) => $log->work_date->format('Y-m-d'));

        $grandTotalMinutes = $logs->flatten()
            ->where('status', TaskLog::STATUS_COMPLETED)
            ->sum('duration_minutes');

        return view('admin.reports.member-detail', [
            'member' => $member,
            'logsByDate' => $logs,
            'from' => $from,
            'to' => $to,
            'grandTotalMinutes' => $grandTotalMinutes,
        ]);
    }
}
