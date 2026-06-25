<?php

namespace App\Http\Controllers;

use App\Models\ReportType;
use Illuminate\Http\Request;

class ReportTypeController extends Controller
{
    public function index()
    {
        $reportTypes = ReportType::ordered()->get();

        return view('admin.report-types.index', compact('reportTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:report_types,name',
        ]);

        ReportType::create([
            'name' => $request->input('name'),
            'is_active' => true,
            'sort_order' => ReportType::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Report/Task type added.');
    }

    public function update(Request $request, ReportType $reportType)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:report_types,name,' . $reportType->id,
        ]);

        $reportType->update(['name' => $request->input('name')]);

        return back()->with('success', 'Report/Task type updated.');
    }

    public function toggle(ReportType $reportType)
    {
        $reportType->update(['is_active' => !$reportType->is_active]);

        return back()->with('success', 'Status updated.');
    }

    public function destroy(ReportType $reportType)
    {
        if ($reportType->taskLogs()->exists()) {
            return back()->with('error', 'Cannot delete a report type that already has task logs. Deactivate it instead.');
        }

        $reportType->delete();

        return back()->with('success', 'Report/Task type deleted.');
    }
}
