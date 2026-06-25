@extends('layouts.app')
@section('title', 'Edit Node')

@section('content')
    <div class="page-head">
        <div>
            <h1>Edit Node</h1>
            <p class="subtitle">{{ $orgchart->name }} — {{ $orgchart->title }}</p>
        </div>
        <a href="{{ route('admin.orgchart.index') }}" class="btn btn-ghost">← Back</a>
    </div>

    <form action="{{ route('admin.orgchart.update', $orgchart) }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Position Info
                </h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $orgchart->name) }}"
                            required>
                    </div>
                    <div>
                        <label class="form-label">Title / Role *</label>
                        <input type="text" name="title" class="form-control"
                            value="{{ old('title', $orgchart->title) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Reports To (Parent)</label>
                        <select name="parent_id" class="form-control">
                            <option value="">— Top level / no parent —</option>
                            @foreach ($nodes as $id => $nodeName)
                                <option value="{{ $id }}"
                                    {{ old('parent_id', $orgchart->parent_id) == $id ? 'selected' : '' }}>
                                    {{ $nodeName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Card Color</label>
                        <select name="color" class="form-control">
                            @foreach ($colors as $c)
                                <option value="{{ $c }}"
                                    {{ old('color', $orgchart->color) == $c ? 'selected' : '' }}>
                                    {{ ucfirst($c) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                            value="{{ old('sort_order', $orgchart->sort_order) }}" min="0">
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Facility Assignments
                </h3>
                <label class="form-label">Facilities (one per line, or comma-separated)</label>
                <textarea name="facilities" class="form-control" rows="5">{{ old('facilities', $orgchart->facilities ? implode("\n", $orgchart->facilities) : '') }}</textarea>
                <p class="form-hint">Leave blank if this person has no assigned facilities.</p>
            </div>

        </div>

        <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('admin.orgchart.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
@endsection
