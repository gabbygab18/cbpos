@extends('layouts.app')
@section('title', 'Add Org Chart Node')

@section('content')
    <div class="page-head">
        <div>
            <h1>Add Node</h1>
            <p class="subtitle">Add a person or position to the org chart.</p>
        </div>
        <a href="{{ route('admin.orgchart.index') }}" class="btn btn-ghost">← Back</a>
    </div>

    <form action="{{ route('admin.orgchart.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Position Info
                </h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                            placeholder="e.g. Arlene Fabay">
                    </div>
                    <div>
                        <label class="form-label">Title / Role *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required
                            placeholder="e.g. MDS Account Manager">
                    </div>
                    <div>
                        <label class="form-label">Reports To (Parent)</label>
                        <select name="parent_id" class="form-control">
                            <option value="">— Top level / no parent —</option>
                            @foreach ($nodes as $id => $nodeName)
                                <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                                    {{ $nodeName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Card Color</label>
                        <select name="color" class="form-control">
                            @foreach ($colors as $c)
                                <option value="{{ $c }}" {{ old('color', 'blue') == $c ? 'selected' : '' }}>
                                    {{ ucfirst($c) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}"
                            min="0">
                    </div>
                </div>
            </div>

            <div class="card" style="padding:20px;grid-column:1/-1;">
                <h3
                    style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--slate);margin-bottom:16px;">
                    Facility Assignments
                </h3>
                <label class="form-label">Facilities (one per line, or comma-separated)</label>
                <textarea name="facilities" class="form-control" rows="5" placeholder="Waterview&#10;Utica&#10;South Point">{{ old('facilities') }}</textarea>
                <p class="form-hint">Leave blank if this person has no assigned facilities.</p>
            </div>

        </div>

        <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('admin.orgchart.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Add Node</button>
        </div>
    </form>
@endsection
