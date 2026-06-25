<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::ordered()->get();

        return view('admin.facilities.index', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:facilities,name',
        ]);

        Facility::create([
            'name' => $request->input('name'),
            'is_active' => true,
            'sort_order' => Facility::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Facility added.');
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:facilities,name,' . $facility->id,
        ]);

        $facility->update(['name' => $request->input('name')]);

        return back()->with('success', 'Facility updated.');
    }

    public function toggle(Facility $facility)
    {
        $facility->update(['is_active' => !$facility->is_active]);

        return back()->with('success', 'Facility status updated.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->taskLogs()->exists()) {
            return back()->with('error', 'Cannot delete a facility that already has task logs. Deactivate it instead.');
        }

        $facility->delete();

        return back()->with('success', 'Facility deleted.');
    }
}
