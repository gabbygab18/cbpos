<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index()
    {
        $members = User::orderByDesc('role')->orderBy('name')->get();

        return view('admin.members.index', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,member',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Account created.');
    }

    public function update(Request $request, User $member)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($member->id)],
            'role' => 'required|in:admin,member',
            'password' => 'nullable|string|min:6',
        ]);

        $member->name = $data['name'];
        $member->email = $data['email'];
        $member->role = $data['role'];

        if (!empty($data['password'])) {
            $member->password = Hash::make($data['password']);
        }

        $member->save();

        return back()->with('success', 'Account updated.');
    }

    public function toggle(Request $request, User $member)
    {
        if ($member->id === $request->user()->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $member->update(['is_active' => !$member->is_active]);

        return back()->with('success', 'Account status updated.');
    }
}
