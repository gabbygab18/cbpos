<?php

namespace App\Http\Controllers;

use App\Models\OrgChartNode;
use Illuminate\Http\Request;

class OrgChartController extends Controller
{
    // ── Colors available in the form ──────────────────────
    private const COLORS = [
        'blue',
        'teal',
        'amber',
        'pink',
        'green',
        'gray',
        'purple',
        'coral',
        'red',
    ];

    // ─────────────────────────────────────────────────────
    // READ
    // ─────────────────────────────────────────────────────

    public function index()
    {
        $roots = OrgChartNode::with('childrenRecursive')
            ->roots()
            ->get();

        $allNodes = OrgChartNode::orderBy('sort_order')
            ->get(['id', 'parent_id', 'sort_order'])
            ->toJson();

        return view('admin.orgchart.index', compact('roots', 'allNodes'));
    }

    // ─────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────

    public function create()
    {
        $nodes = OrgChartNode::selectOptions();
        $colors = self::COLORS;

        return view('admin.orgchart.create', compact('nodes', 'colors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:org_chart_nodes,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'facilities' => 'nullable|string',
            'color' => 'required|in:' . implode(',', self::COLORS),
            'sort_order' => 'nullable|integer',
        ]);

        $data['facilities'] = $this->parseFacilities($data['facilities'] ?? null);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        OrgChartNode::create($data);

        return redirect()->route('admin.orgchart.index')
            ->with('success', 'Node added successfully.');
    }

    // ─────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────

    public function edit(OrgChartNode $orgchart)
    {
        $nodes = OrgChartNode::selectOptions($orgchart->id);
        $colors = self::COLORS;

        return view('admin.orgchart.edit', compact('orgchart', 'nodes', 'colors'));
    }

    public function update(Request $request, OrgChartNode $orgchart)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:org_chart_nodes,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'facilities' => 'nullable|string',
            'color' => 'required|in:' . implode(',', self::COLORS),
            'sort_order' => 'nullable|integer',
        ]);

        $data['facilities'] = $this->parseFacilities($data['facilities'] ?? null);
        $data['sort_order'] = $data['sort_order'] ?? $orgchart->sort_order;

        $orgchart->update($data);

        return redirect()->route('admin.orgchart.index')
            ->with('success', 'Node updated.');
    }

    // ─────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────

    public function destroy(OrgChartNode $orgchart)
    {
        // Re-parent children to this node's parent before deleting
        OrgChartNode::where('parent_id', $orgchart->id)
            ->update(['parent_id' => $orgchart->parent_id]);

        $orgchart->delete();

        return redirect()->route('admin.orgchart.index')
            ->with('success', 'Node deleted. Its children were re-parented.');
    }

    // ─────────────────────────────────────────────────────
    // DRAG REORDER (AJAX)
    // ─────────────────────────────────────────────────────

    /**
     * Accepts: { nodes: [ {id, parent_id, sort_order}, ... ] }
     * Updates parent_id + sort_order for each node in one batch.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'nodes' => 'required|array',
            'nodes.*.id' => 'required|exists:org_chart_nodes,id',
            'nodes.*.parent_id' => 'nullable|exists:org_chart_nodes,id',
            'nodes.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->nodes as $item) {
            OrgChartNode::where('id', $item['id'])->update([
                'parent_id' => $item['parent_id'] ?? null,
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────

    /** Convert newline- or comma-separated textarea input into a JSON array. */
    private function parseFacilities(?string $raw): ?array
    {
        if (!$raw || !trim($raw))
            return null;

        $lines = preg_split('/[\r\n,]+/', $raw);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        return array_values($lines) ?: null;
    }
}
