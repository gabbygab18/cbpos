@extends('layouts.app')

@section('title', 'Facilities')

@section('content')
    <div class="page-head">
        <div>
            <h1>Facilities</h1>
            <p class="subtitle">This list fills the Facility dropdown members see when stamping a task.</p>
        </div>
    </div>

    <div class="card">
        <h2>Add facility</h2>
        <form method="POST" action="{{ route('admin.facilities.store') }}" class="form-row" style="align-items:flex-end;">
            @csrf
            <div class="field">
                <label>Facility name</label>
                <input type="text" name="name" placeholder="e.g. Great Neck" required>
            </div>
            <div class="field" style="flex:0 0 auto;">
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>All facilities ({{ $facilities->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($facilities as $facility)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('admin.facilities.update', $facility) }}"
                                style="display:flex; gap:8px;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $facility->name }}" style="max-width:260px;">
                                <button type="submit" class="btn btn-ghost btn-sm">Save</button>
                            </form>
                        </td>
                        <td><span
                                class="badge badge-{{ $facility->is_active ? 'active' : 'inactive' }}">{{ $facility->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td style="display:flex; gap:6px;">
                            <form method="POST" action="{{ route('admin.facilities.toggle', $facility) }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-ghost btn-sm">{{ $facility->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.facilities.destroy', $facility) }}"
                                onsubmit="return confirm('Delete this facility?');">
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
