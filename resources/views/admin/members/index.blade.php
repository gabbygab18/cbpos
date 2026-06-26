@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    <div class="page-head">
        <div>
            <h1>Employees</h1>
            <p class="subtitle">3 admins can edit logged time/date; members can only start and stop their own tasks.</p>
        </div>
    </div>

    <div class="card">
        <h2>Add account</h2>
        <form method="POST" action="{{ route('admin.members.store') }}" class="form-row"
            style="align-items:flex-end; flex-wrap:wrap;">
            @csrf
            <div class="field" style="min-width:160px;">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="field" style="min-width:200px;">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="field" style="min-width:150px;">
                <label>Password</label>
                <input type="text" name="password" required>
            </div>
            <div class="field" style="min-width:120px;">
                <label>Role</label>
                <select name="role" required>
                    <option value="member">Employee</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="field" style="flex:0 0 auto;">
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>All accounts ({{ $members->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td class="text-muted">{{ $member->email }}</td>
                        <td><span class="badge badge-{{ $member->role }}">{{ ucfirst($member->role) }}</span></td>
                        <td>
                            <span
                                class="badge badge-{{ $member->is_active ? 'active' : 'inactive' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td style="display:flex; gap:6px;">
                            <button type="button" class="btn btn-ghost btn-sm"
                                onclick="openEditMember({{ $member->id }}, '{{ $member->name }}', '{{ $member->email }}', '{{ $member->role }}')">Edit</button>
                            @if ($member->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.members.toggle', $member) }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-ghost btn-sm">{{ $member->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="edit-modal">
        <div class="modal-box">
            <h3>Edit account</h3>
            <form method="POST" id="edit-form">
                @csrf
                @method('PUT')
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" id="edit-name" required>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" id="edit-email" required>
                </div>
                <div class="field">
                    <label>Role</label>
                    <select name="role" id="edit-role" required>
                        <option value="member">Employee</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="field">
                    <label>New password <span class="text-muted">(leave blank to keep current)</span></label>
                    <input type="text" name="password">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeEditMember()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openEditMember(id, name, email, role) {
            document.getElementById('edit-form').action = `/admin/members/${id}`;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-role').value = role;
            document.getElementById('edit-modal').classList.add('open');
        }

        function closeEditMember() {
            document.getElementById('edit-modal').classList.remove('open');
        }
    </script>
@endpush
