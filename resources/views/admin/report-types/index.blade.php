@extends('layouts.app')

@section('title', 'Report Types')

@section('content')
    <div class="page-head">
        <div>
            <h1>Report / Task Types</h1>
            <p class="subtitle">This list fills the Type of Report dropdown members see when stamping a task.</p>
        </div>
    </div>

    <div class="card">
        <h2>Add report type</h2>
        <form method="POST" action="{{ route('admin.report-types.store') }}" class="form-row" style="align-items:flex-end;">
            @csrf
            <div class="field">
                <label>Report / task name</label>
                <input type="text" name="name" placeholder="e.g. Daily Skilled Notes" required>
            </div>
            <div class="field" style="flex:0 0 auto;">
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>All report types ({{ $reportTypes->count() }})</h2>
        <table>
            <thead>
                <tr><th>Name</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($reportTypes as $type)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('admin.report-types.update', $type) }}" style="display:flex; gap:8px;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $type->name }}" style="max-width:260px;">
                                <button type="submit" class="btn btn-ghost btn-sm">Save</button>
                            </form>
                        </td>
                        <td><span class="badge badge-{{ $type->is_active ? 'active' : 'inactive' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td style="display:flex; gap:6px;">
                            <form method="POST" action="{{ route('admin.report-types.toggle', $type) }}">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm">{{ $type->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.report-types.destroy', $type) }}" onsubmit="return confirm('Delete this report type?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
