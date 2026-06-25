<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /* ── Admin: list all requests ── */
    public function adminIndex(Request $request)
    {
        $status      = $request->query('status', 'pending');
        $month       = $request->query('month');        // expected format: "YYYY-MM"
        $leaveTypeId = $request->query('leave_type');
        $memberId    = $request->query('member');

        $requests = LeaveRequest::with(['user', 'leaveType', 'reviewedBy'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($month, function ($q) use ($month) {
                [$year, $monthNum] = explode('-', $month);
                $q->where(function ($q) use ($year, $monthNum) {
                    $q->whereYear('start_date', $year)->whereMonth('start_date', $monthNum)
                      ->orWhere(function ($q2) use ($year, $monthNum) {
                          $q2->whereYear('end_date', $year)->whereMonth('end_date', $monthNum);
                      });
                });
            })
            ->when($leaveTypeId, fn ($q) => $q->where('leave_type_id', $leaveTypeId))
            ->when($memberId, fn ($q) => $q->where('user_id', $memberId))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $pendingCount = LeaveRequest::pending()->count();

        $leaveTypes = LeaveType::orderBy('name')->get();

        // Members who have at least one leave request, for the dropdown.
        // Uses a subquery instead of a relation, since User doesn't define leaveRequests().
        $members = User::whereIn('id', LeaveRequest::select('user_id')->distinct())
            ->orderBy('name')
            ->get();

        // Rolling list of months for the dropdown (11 back, 6 forward from current month).
        // Adjust the range if your data goes further back than that.
        $monthOptions = [];
        $cursor = Carbon::now()->startOfMonth()->subMonths(11);
        for ($i = 0; $i < 18; $i++) {
            $monthOptions[$cursor->format('Y-m')] = $cursor->format('F Y');
            $cursor->addMonth();
        }

        return view('admin.leave-requests.index', compact(
            'requests',
            'status',
            'pendingCount',
            'month',
            'leaveTypeId',
            'memberId',
            'leaveTypes',
            'members',
            'monthOptions'
        ));
    }

    /* ── Admin: approve ── */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        if (!$leaveRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $data = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $leaveRequest->update([
            'status'      => LeaveRequest::STATUS_APPROVED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => Carbon::now(),
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        return back()->with('success', 'Leave request approved.');
    }

    /* ── Admin: reject ── */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if (!$leaveRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $data = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest->update([
            'status'           => LeaveRequest::STATUS_REJECTED,
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => Carbon::now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    /* ── Member: show file-leave form ── */
    public function create()
    {
        $leaveTypes = LeaveType::active()->orderBy('name')->get();
        return view('member.leave.create', compact('leaveTypes'));
    }

    /* ── Member: store new leave request ── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date|after_or_equal:today',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'required|string|max:1000',
        ]);

        // Check for overlapping requests for this user
        $overlap = LeaveRequest::where('user_id', $request->user()->id)
            ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                  ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('start_date', '<=', $data['start_date'])
                         ->where('end_date', '>=', $data['end_date']);
                  });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'You already have a leave request that overlaps with these dates.'])->withInput();
        }

        LeaveRequest::create([
            'user_id'       => $request->user()->id,
            'leave_type_id' => $data['leave_type_id'],
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'total_days'    => LeaveRequest::calcDays($data['start_date'], $data['end_date']),
            'reason'        => $data['reason'],
            'status'        => LeaveRequest::STATUS_PENDING,
        ]);

        return redirect()->route('member.leaves.index')->with('success', 'Leave request filed. Please wait for admin approval.');
    }

    /* ── Member: view their own leaves ── */
    public function memberIndex(Request $request)
    {
        $leaves = LeaveRequest::with('leaveType')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return view('member.leave.index', compact('leaves'));
    }

    /* ── Member: cancel a pending request ── */
    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== $request->user()->id) {
            abort(403);
        }

        if (!$leaveRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $leaveRequest->update(['status' => LeaveRequest::STATUS_CANCELLED]);

        return back()->with('success', 'Leave request cancelled.');
    }
}
