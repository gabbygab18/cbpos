@extends('layouts.app')

@section('title', 'Leave Types')

@section('content')
    <div class="page-head">
        <div>
            <h1>Leave Types</h1>
            <p class="subtitle">Define the types of leave available to your team members.</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('create-modal')">
            <iconify-icon icon="material-symbols:add" width="17" height="17"></iconify-icon>
            Add Leave Type
        </button>
    </div>

    <div class="card">
        @if ($leaveTypes->isEmpty())
            <div class="empty-state">No leave types yet. Create one above to get started.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Default Days</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaveTypes as $lt)
                        <tr>
                            <td>
                                <strong>{{ $lt->name }}</strong>
                                @if ($lt->description)
                                    <div class="text-muted text-sm">{{ Str::limit($lt->description, 60) }}</div>
                                @endif
                            </td>
                            <td><span class="mono" style="background:var(--bg); padding:2px 7px; border-radius:5px; font-size:12px;">{{ $lt->code }}</span></td>
                            <td>{{ $lt->default_days ? $lt->default_days . ' days' : '—' }}</td>
                            <td>
                                <span class="badge {{ $lt->is_paid ? 'badge-paid' : 'badge-unpaid' }}">
                                    {{ $lt->is_paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $lt->is_active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $lt->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:6px; justify-content:flex-end;">
                                    <button class="btn btn-ghost btn-sm"
                                        onclick="openEdit({{ $lt->id }}, '{{ addslashes($lt->name) }}', '{{ $lt->code }}', '{{ $lt->default_days }}', {{ $lt->is_paid ? 'true' : 'false' }}, '{{ addslashes($lt->description ?? '') }}')">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.leave-types.toggle', $lt) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm">
                                            {{ $lt->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @if (!$lt->leaveRequests()->exists())
                                        <form method="POST" action="{{ route('admin.leave-types.destroy', $lt) }}"
                                              onsubmit="return confirm('Delete this leave type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ── Create modal ── --}}
    <div class="modal-overlay" id="create-modal">
        <div class="modal-box">
            <h3>Add Leave Type</h3>
            <form method="POST" action="{{ route('admin.leave-types.store') }}">
                @csrf
                <div class="form-row">
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" placeholder="e.g. Vacation Leave" required maxlength="100">
                    </div>
                    <div class="field" style="flex: 0 0 120px;">
                        <label>Code</label>
                        <input type="text" name="code" placeholder="VL" required maxlength="20" style="text-transform:uppercase;"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label>Default Days / Year <span class="text-muted">(optional)</span></label>
                        <input type="number" name="default_days" min="1" max="365" placeholder="e.g. 15">
                    </div>
                    <div class="field" style="display:flex; align-items:center; gap:8px; padding-top:22px;">
                        <input type="checkbox" name="is_paid" id="is_paid_create" value="1" checked style="width:auto;">
                        <label for="is_paid_create" style="margin:0; font-weight:500;">Paid leave</label>
                    </div>
                </div>
                <div class="field">
                    <label>Description <span class="text-muted">(optional)</span></label>
                    <textarea name="description" rows="2" maxlength="500" placeholder="Brief description of this leave type"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('create-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Leave Type</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Edit modal ── --}}
    <div class="modal-overlay" id="edit-modal">
        <div class="modal-box">
            <h3>Edit Leave Type</h3>
            <form method="POST" id="edit-form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" id="edit-name" required maxlength="100">
                    </div>
                    <div class="field" style="flex: 0 0 120px;">
                        <label>Code</label>
                        <input type="text" name="code" id="edit-code" required maxlength="20"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label>Default Days / Year <span class="text-muted">(optional)</span></label>
                        <input type="number" name="default_days" id="edit-days" min="1" max="365">
                    </div>
                    <div class="field" style="display:flex; align-items:center; gap:8px; padding-top:22px;">
                        <input type="checkbox" name="is_paid" id="edit-paid" value="1" style="width:auto;">
                        <label for="edit-paid" style="margin:0; font-weight:500;">Paid leave</label>
                    </div>
                </div>
                <div class="field">
                    <label>Description <span class="text-muted">(optional)</span></label>
                    <textarea name="description" id="edit-desc" rows="2" maxlength="500"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('edit-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', (e) => { if (e.target === el) el.classList.remove('open'); });
    });

    function openEdit(id, name, code, days, isPaid, desc) {
        document.getElementById('edit-form').action = `/admin/leave-types/${id}`;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-code').value = code;
        document.getElementById('edit-days').value = days || '';
        document.getElementById('edit-paid').checked = isPaid;
        document.getElementById('edit-desc').value = desc;
        openModal('edit-modal');
    }

    // Auto-open create modal if there are errors
    @if ($errors->any())
        openModal('create-modal');
    @endif
</script>
@endpush
