<?php

namespace App\Http\Controllers;

use App\Models\CoachingLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoachingLogController extends Controller
{
    public function index(Request $request)
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $query = CoachingLog::with('member', 'creator')->latest();
        if ($request->member_id)
            $query->where('member_id', $request->member_id);
        $logs = $query->paginate(20)->withQueryString();
        return view('admin.coaching.index', compact('logs', 'members'));
    }

    public function create()
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        return view('admin.coaching.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:users,id',
            'coaching_type' => 'required|string|max:100',
            'week_number' => 'nullable|string|max:50',
            'session_date' => 'required|date',
            'root_cause_coachee' => 'nullable|string',
            'coach_comments' => 'nullable|string',
            'tools' => 'nullable|string|max:255',
            'coachee_action_plan' => 'nullable|string',
            'coach_commitment' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'targets' => 'nullable|string',
            'employee_combined_text' => 'nullable|string',
            'client_classification' => 'nullable|string|max:100',
        ]);
        $data['created_by'] = Auth::id();
        $coaching = CoachingLog::create($data);

        \App\Models\Notification::create([
            'user_id' => $coaching->member_id,
            'type' => 'coaching_log',
            'title' => 'New coaching log',
            'message' => 'A new ' . $coaching->coaching_type . ' coaching log was added for ' . $coaching->session_date->format('M d, Y') . '. Please review and acknowledge.',
            'notifiable_type' => CoachingLog::class,
            'notifiable_id' => $coaching->id,
        ]);

        return redirect()->route('admin.coaching.index')->with('success', 'Coaching log saved.');
    }

    public function show(CoachingLog $coaching)
    {
        return view('admin.coaching.show', compact('coaching'));
    }

    public function edit(CoachingLog $coaching)
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        return view('admin.coaching.edit', compact('coaching', 'members'));
    }

    public function update(Request $request, CoachingLog $coaching)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:users,id',
            'coaching_type' => 'required|string|max:100',
            'week_number' => 'nullable|string|max:50',
            'session_date' => 'required|date',
            'root_cause_coachee' => 'nullable|string',
            'coach_comments' => 'nullable|string',
            'tools' => 'nullable|string|max:255',
            'coachee_action_plan' => 'nullable|string',
            'coach_commitment' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'targets' => 'nullable|string',
            'employee_combined_text' => 'nullable|string',
            'client_classification' => 'nullable|string|max:100',
        ]);
        $coaching->update($data);
        return redirect()->route('admin.coaching.show', $coaching)->with('success', 'Coaching log updated.');
    }

    public function destroy(CoachingLog $coaching)
    {
        $coaching->delete();
        return redirect()->route('admin.coaching.index')->with('success', 'Coaching log deleted.');
    }

    /** Member: list their own coaching logs. */
    public function memberIndex(Request $request)
    {
        $logs = CoachingLog::with('creator')
            ->where('member_id', $request->user()->id)
            ->latest('session_date')
            ->paginate(20);

        return view('member.coaching.index', compact('logs'));
    }

    /** Member: view a single coaching log (read-only). */
    public function memberShow(Request $request, CoachingLog $coaching)
    {
        abort_unless($coaching->member_id === $request->user()->id, 403);

        return view('member.coaching.show', compact('coaching'));
    }

    /** Member: acknowledge a coaching log. */
    public function acknowledge(Request $request, CoachingLog $coaching)
    {
        abort_unless($coaching->member_id === $request->user()->id, 403);

        if (!$coaching->acknowledged_at) {
            $coaching->update(['acknowledged_at' => now()]);
        }

        return redirect()->route('member.coaching.show', $coaching)->with('success', 'Coaching log acknowledged.');
    }
}
