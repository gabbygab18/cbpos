<?php

namespace App\Http\Controllers;

use App\Models\PipRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PipRecordController extends Controller
{
    public function index(Request $request)
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $query = PipRecord::with('member', 'creator')->latest();
        if ($request->member_id) $query->where('member_id', $request->member_id);
        $pips = $query->paginate(20)->withQueryString();
        return view('admin.pip.index', compact('pips', 'members'));
    }

    public function create()
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        return view('admin.pip.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'           => 'required|exists:users,id',
            'category'            => 'nullable|string|max:100',
            'client_name'         => 'nullable|string|max:100',
            'team_leader'         => 'nullable|string|max:100',
            'manager'             => 'nullable|string|max:100',
            'offense_occurrence'  => 'nullable|string|max:100',
            'due_date'            => 'nullable|date',
            'engagement'          => 'nullable|string',
            'reinforce'           => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
        ]);
        $data['created_by'] = Auth::id();
        PipRecord::create($data);
        return redirect()->route('admin.pip.index')->with('success', 'PIP record saved.');
    }

    public function show(PipRecord $pip)
    {
        return view('admin.pip.show', compact('pip'));
    }

    public function edit(PipRecord $pip)
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        return view('admin.pip.edit', compact('pip', 'members'));
    }

    public function update(Request $request, PipRecord $pip)
    {
        $data = $request->validate([
            'member_id'           => 'required|exists:users,id',
            'category'            => 'nullable|string|max:100',
            'client_name'         => 'nullable|string|max:100',
            'team_leader'         => 'nullable|string|max:100',
            'manager'             => 'nullable|string|max:100',
            'offense_occurrence'  => 'nullable|string|max:100',
            'due_date'            => 'nullable|date',
            'engagement'          => 'nullable|string',
            'reinforce'           => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
        ]);
        $pip->update($data);
        return redirect()->route('admin.pip.show', $pip)->with('success', 'PIP record updated.');
    }

    public function destroy(PipRecord $pip)
    {
        $pip->delete();
        return redirect()->route('admin.pip.index')->with('success', 'PIP record deleted.');
    }
}
