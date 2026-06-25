<?php

namespace App\Http\Controllers;

use App\Models\TaskLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskLogController extends Controller
{
    /**
     * Member starts a new task stamp: pick Type of Report + Facility, time starts now.
     */
    public function start(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'report_type_id' => 'required|exists:report_types,id',
            'facility_id' => 'required|exists:facilities,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $alreadyRunning = TaskLog::where('user_id', $user->id)
            ->where('status', TaskLog::STATUS_RUNNING)
            ->exists();

        if ($alreadyRunning) {
            return back()->with('error', 'You already have a task in progress. Stop it before starting a new one.');
        }

        $now = Carbon::now();

        TaskLog::create([
            'user_id' => $user->id,
            'report_type_id' => $request->input('report_type_id'),
            'facility_id' => $request->input('facility_id'),
            'work_date' => $now->format('Y-m-d'),
            'started_at' => $now,
            'status' => TaskLog::STATUS_RUNNING,
            'notes' => $request->input('notes'),
            'created_by' => $user->id,
        ]);

        return back()->with('success', 'Task started. Timer is running.');
    }

    /**
     * Member stops the currently running task.
     */
    public function stop(Request $request, TaskLog $taskLog)
    {
        $user = $request->user();

        if ($taskLog->user_id !== $user->id) {
            abort(403);
        }

        if (!$taskLog->isRunning()) {
            return back()->with('error', 'That task is already completed.');
        }

        $taskLog->ended_at = Carbon::now();
        $taskLog->status = TaskLog::STATUS_COMPLETED;
        $taskLog->recalculateDuration();
        $taskLog->save();

        return back()->with('success', 'Task stopped. ' . $taskLog->formatted_duration . ' logged.');
    }

    /**
     * Member cancels/deletes a task they started by mistake (only while still running,
     * and only same-day, so the handling-time history stays trustworthy).
     */
    public function destroy(Request $request, TaskLog $taskLog)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $taskLog->delete();
            return back()->with('success', 'Task log deleted.');
        }

        if ($taskLog->user_id !== $user->id || !$taskLog->isRunning()) {
            abort(403, 'Only an admin can remove a completed task log.');
        }

        $taskLog->delete();

        return back()->with('success', 'Task removed.');
    }

    /**
     * Admin-only: edit the work date / start time / end time of a task log,
     * and reassign report type or facility if it was logged wrong.
     */
    public function update(Request $request, TaskLog $taskLog)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            abort(403, 'Only an admin can edit logged time and date.');
        }

        $data = $request->validate([
            'report_type_id' => 'required|exists:report_types,id',
            'facility_id' => 'required|exists:facilities,id',
            'work_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $workDate = $data['work_date'];
        $startedAt = Carbon::parse($workDate . ' ' . $data['start_time']);
        $endedAt = $data['end_time'] ? Carbon::parse($workDate . ' ' . $data['end_time']) : null;

        if ($endedAt && $endedAt->lessThan($startedAt)) {
            return back()->withErrors(['end_time' => 'End time cannot be before start time.']);
        }

        $taskLog->report_type_id = $data['report_type_id'];
        $taskLog->facility_id = $data['facility_id'];
        $taskLog->work_date = $workDate;
        $taskLog->started_at = $startedAt;
        $taskLog->ended_at = $endedAt;
        $taskLog->status = $endedAt ? TaskLog::STATUS_COMPLETED : TaskLog::STATUS_RUNNING;
        $taskLog->notes = $data['notes'] ?? $taskLog->notes;
        $taskLog->recalculateDuration();
        $taskLog->last_edited_by = $user->id;
        $taskLog->time_edited_at = Carbon::now();
        $taskLog->save();

        return back()->with('success', 'Task log updated.');
    }
}
