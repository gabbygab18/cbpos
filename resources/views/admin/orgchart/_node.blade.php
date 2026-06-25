{{-- Recursive node partial --}}
{{-- @include('admin.orgchart._node', ['node' => $node]) --}}

<div class="oc-level">
    {{-- Card --}}
    <div class="oc-card oc-{{ $node->color }}" draggable="true" data-id="{{ $node->id }}"
        data-parent-id="{{ $node->parent_id ?? '' }}">
        {{-- Edit / Delete --}}
        <div class="oc-actions">
            <a href="{{ route('admin.orgchart.edit', $node) }}" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('admin.orgchart.destroy', $node) }}" method="POST" style="display:contents;"
                onsubmit="return confirm('Delete {{ addslashes($node->name) }}? Children will be re-parented.')">
                @csrf @method('DELETE')
                <button type="submit" class="oc-del" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>

        <div class="oc-name">{{ $node->name }}</div>
        <div class="oc-title">{{ $node->title }}</div>

        @if ($node->facilities)
            <div class="oc-facs">
                @foreach ($node->facilities as $f)
                    • {{ $f }}<br>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Children --}}
    @if ($node->childrenRecursive->isNotEmpty())
        <div class="oc-v-line"></div>

        {{-- Horizontal bar width is calculated by JS after paint --}}
        <div class="oc-h-bar-wrap">
            <div class="oc-h-bar"></div>
        </div>

        <div class="oc-children-row">
            @foreach ($node->childrenRecursive as $child)
                <div class="oc-child-col">
                    <div class="oc-drop-line"></div>
                    @include('admin.orgchart._node', ['node' => $child])
                </div>
            @endforeach
        </div>
    @endif
</div>
