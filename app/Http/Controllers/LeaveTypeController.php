<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::orderBy('name')->get();
        return view('admin.leave-types.index', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'required|string|max:20|unique:leave_types,code',
            'default_days' => 'nullable|integer|min:1|max:365',
            'is_paid'      => 'boolean',
            'description'  => 'nullable|string|max:500',
        ]);

        $data['is_paid']    = $request->boolean('is_paid');
        $data['is_active']  = true;

        LeaveType::create($data);

        return back()->with('success', 'Leave type created.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'required|string|max:20|unique:leave_types,code,' . $leaveType->id,
            'default_days' => 'nullable|integer|min:1|max:365',
            'is_paid'      => 'boolean',
            'description'  => 'nullable|string|max:500',
        ]);

        $data['is_paid'] = $request->boolean('is_paid');

        $leaveType->update($data);

        return back()->with('success', 'Leave type updated.');
    }

    public function toggle(LeaveType $leaveType)
    {
        $leaveType->update(['is_active' => !$leaveType->is_active]);
        return back()->with('success', 'Leave type status updated.');
    }

    public function destroy(LeaveType $leaveType)
    {
        if ($leaveType->leaveRequests()->exists()) {
            return back()->with('error', 'Cannot delete a leave type that has existing requests.');
        }

        $leaveType->delete();
        return back()->with('success', 'Leave type deleted.');
    }
}
