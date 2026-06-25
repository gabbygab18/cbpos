<?php

namespace App\Http\Controllers;

use App\Models\EngagementRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EngagementRecordController extends Controller
{
    public function index(Request $request)
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $query = EngagementRecord::with('member', 'creator')->latest('record_date');
        if ($request->member_id) $query->where('member_id', $request->member_id);
        if ($request->record_type) $query->where('record_type', $request->record_type);
        $records = $query->paginate(20)->withQueryString();
        $types = EngagementRecord::TYPES;
        return view('admin.engagement.index', compact('records', 'members', 'types'));
    }

    public function create()
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $types = EngagementRecord::TYPES;
        return view('admin.engagement.create', compact('members', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'   => 'required|exists:users,id',
            'record_type' => 'required|in:' . implode(',', array_keys(EngagementRecord::TYPES)),
            'title'       => 'required|string|max:255',
            'notes'       => 'nullable|string',
            'score'       => 'nullable|numeric|min:0|max:100',
            'record_date' => 'required|date',
        ]);
        $data['created_by'] = Auth::id();
        EngagementRecord::create($data);
        return redirect()->route('admin.engagement.index')->with('success', 'Record saved.');
    }

    public function show(EngagementRecord $engagement)
    {
        return view('admin.engagement.show', compact('engagement'));
    }

    public function edit(EngagementRecord $engagement)
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $types = EngagementRecord::TYPES;
        return view('admin.engagement.edit', compact('engagement', 'members', 'types'));
    }

    public function update(Request $request, EngagementRecord $engagement)
    {
        $data = $request->validate([
            'member_id'   => 'required|exists:users,id',
            'record_type' => 'required|in:' . implode(',', array_keys(EngagementRecord::TYPES)),
            'title'       => 'required|string|max:255',
            'notes'       => 'nullable|string',
            'score'       => 'nullable|numeric|min:0|max:100',
            'record_date' => 'required|date',
        ]);
        $engagement->update($data);
        return redirect()->route('admin.engagement.show', $engagement)->with('success', 'Record updated.');
    }

    public function destroy(EngagementRecord $engagement)
    {
        $engagement->delete();
        return redirect()->route('admin.engagement.index')->with('success', 'Record deleted.');
    }
}
