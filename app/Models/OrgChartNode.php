<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrgChartNode extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'title',
        'facilities',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'facilities' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrgChartNode::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OrgChartNode::class, 'parent_id')->orderBy('sort_order');
    }

    // ── Recursive eager-load helper ────────────────────────

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    // ── Scope: only top-level nodes ────────────────────────

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    // ── Helpers ────────────────────────────────────────────

    /** Build a flat list of [id => name] suitable for a <select> parent picker,
     *  excluding the node itself and its descendants to prevent circular refs. */
    public static function selectOptions(?int $excludeId = null): array
    {
        $nodes = static::orderBy('sort_order')->get();

        // Collect all descendant IDs of $excludeId so we can exclude them
        $excluded = [];
        if ($excludeId) {
            $excluded[] = $excludeId;
            $queue = [$excludeId];
            while ($queue) {
                $current = array_shift($queue);
                $children = $nodes->where('parent_id', $current)->pluck('id')->toArray();
                $excluded = array_merge($excluded, $children);
                $queue    = array_merge($queue, $children);
            }
        }

        return $nodes
            ->whereNotIn('id', $excluded)
            ->pluck('name', 'id')
            ->toArray();
    }
}
