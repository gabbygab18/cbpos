@extends('layouts.app')
@section('title', 'Org Chart')

@push('styles')
    <style>
        /* ── Wrapper ── */
        .oc-wrap {
            padding: 32px 24px;
            overflow-x: auto;
            min-height: 300px;
        }

        /* ── Each level is a flex column centered ── */
        .oc-level {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── Vertical connector from parent card down ── */
        .oc-v-line {
            width: 2px;
            height: 20px;
            background: var(--border);
            flex-shrink: 0;
        }

        /* ── Horizontal bar spanning children ── */
        .oc-h-bar-wrap {
            position: relative;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .oc-h-bar {
            height: 2px;
            background: var(--border);
            position: absolute;
            top: 0;
        }

        /* ── Children row — key: tighter gap, allow wrap ── */
        .oc-children-row {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            justify-content: center;
            flex-wrap: wrap;
            /* wrap onto new rows if too wide */
            max-width: 960px;
            /* cap total spread */
        }

        /* ── Single child column ── */
        .oc-child-col {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── Drop stub line from h-bar down to card ── */
        .oc-drop-line {
            width: 2px;
            height: 20px;
            background: var(--border);
            flex-shrink: 0;
        }

        /* ── Node card — narrower, more compact ── */
        .oc-card {
            border-radius: 10px;
            padding: 8px 12px;
            text-align: center;
            border: 1.5px solid;
            width: 150px;
            /* fixed width — no sprawl */
            cursor: grab;
            transition: box-shadow .18s, transform .15s, opacity .15s;
            position: relative;
            user-select: none;
            box-sizing: border-box;
        }

        .oc-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12);
            transform: translateY(-1px);
        }

        .oc-card.dragging {
            opacity: .4;
            transform: scale(.96);
            cursor: grabbing;
        }

        .oc-card.drag-over {
            box-shadow: 0 0 0 3px var(--teal);
            transform: scale(1.02);
        }

        .oc-name {
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1.35;
            word-break: break-word;
        }

        .oc-title {
            font-size: 10px;
            margin-top: 2px;
            opacity: .7;
            line-height: 1.3;
            word-break: break-word;
        }

        .oc-facs {
            font-size: 9.5px;
            margin-top: 5px;
            opacity: .65;
            line-height: 1.5;
            text-align: left;
            border-top: 1px dashed currentColor;
            padding-top: 4px;
            word-break: break-word;
        }

        /* Color ramps */
        .oc-blue {
            background: #E6F1FB;
            border-color: #85B7EB;
            color: #0C447C;
        }

        .oc-teal {
            background: #E1F5EE;
            border-color: #5DCAA5;
            color: #085041;
        }

        .oc-amber {
            background: #FAEEDA;
            border-color: #EF9F27;
            color: #633806;
        }

        .oc-pink {
            background: #FBEAF0;
            border-color: #ED93B1;
            color: #4B1528;
        }

        .oc-green {
            background: #EAF3DE;
            border-color: #97C459;
            color: #27500A;
        }

        .oc-gray {
            background: #F1EFE8;
            border-color: #B4B2A9;
            color: #2C2C2A;
        }

        .oc-purple {
            background: #EEEDFE;
            border-color: #AFA9EC;
            color: #3C3489;
        }

        .oc-coral {
            background: #FAECE7;
            border-color: #F0997B;
            color: #4A1B0C;
        }

        .oc-red {
            background: #FCEBEB;
            border-color: #F09595;
            color: #791F1F;
        }

        /* ── Inline actions (edit / delete) ── */
        .oc-actions {
            position: absolute;
            top: 4px;
            right: 4px;
            display: flex;
            gap: 2px;
            opacity: 0;
            transition: opacity .15s;
        }

        .oc-card:hover .oc-actions {
            opacity: 1;
        }

        .oc-actions a,
        .oc-actions button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            background: rgba(255, 255, 255, .75);
            border: 1px solid rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: 10px;
            color: inherit;
            text-decoration: none;
            transition: background .12s;
            font-family: inherit;
            padding: 0;
        }

        .oc-actions a:hover,
        .oc-actions button:hover {
            background: #fff;
        }

        .oc-del {
            color: var(--red) !important;
        }

        /* ── Empty state ── */
        .oc-empty {
            text-align: center;
            padding: 48px 20px;
            color: var(--slate);
            font-size: 13.5px;
        }

        /* ── Save banner ── */
        #save-banner {
            display: none;
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--teal);
            color: #fff;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .2);
            z-index: 999;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }

        #save-banner.visible {
            display: flex;
        }

        #save-banner .sb-btn {
            background: rgba(255, 255, 255, .2);
            border: 1px solid rgba(255, 255, 255, .35);
            color: #fff;
            border-radius: 6px;
            padding: 5px 14px;
            cursor: pointer;
            font-size: 12.5px;
            font-family: inherit;
            font-weight: 600;
            transition: background .15s;
        }

        #save-banner .sb-btn:hover {
            background: rgba(255, 255, 255, .35);
        }

        #save-banner .sb-discard {
            background: transparent;
            border-color: rgba(255, 255, 255, .2);
        }

        /* ── Zoom controls ── */
        #oc-zoom-controls {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            background: var(--card, #fff);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 6px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .1);
            z-index: 998;
        }

        #oc-zoom-controls button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: transparent;
            cursor: pointer;
            font-size: 13px;
            color: var(--text, #333);
            font-family: inherit;
            transition: background .12s;
        }

        #oc-zoom-controls button:hover {
            background: var(--hover, #f3f3f3);
        }

        #oc-zoom-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--slate, #888);
            min-width: 36px;
            text-align: center;
        }

        /* Scaler: centers tree and adjusts for zoom */
        #oc-scaler {
            display: flex;
            justify-content: center;
            padding-bottom: 24px;
        }

        #oc-tree {
            transform-origin: top center;
            transition: transform .18s ease;
        }
    </style>
@endpush

@section('content')
    <div class="page-head">
        <div>
            <h1>Org Chart</h1>
            <p class="subtitle">Drag a card onto another to reassign it. Use edit to change details.</p>
        </div>
        <a href="{{ route('admin.orgchart.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Node
        </a>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
        <div class="oc-wrap">
            @if ($roots->isEmpty())
                <div class="oc-empty">
                    No nodes yet. <a href="{{ route('admin.orgchart.create') }}">Add the first one.</a>
                </div>
            @else
                <div id="oc-scaler">
                    <div id="oc-tree" data-nodes="{{ $allNodes }}">
                        @foreach ($roots as $root)
                            @include('admin.orgchart._node', ['node' => $root])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Floating save banner --}}
    <div id="save-banner">
        <span>Unsaved changes</span>
        <button class="sb-btn" onclick="saveReorder()">Save order</button>
        <button class="sb-btn sb-discard" onclick="location.reload()">Discard</button>
    </div>

    {{-- Zoom controls --}}
    <div id="oc-zoom-controls">
        <button onclick="zoomIn()" title="Zoom in"><i class="bi bi-plus-lg"></i></button>
        <span id="oc-zoom-label">100%</span>
        <button onclick="zoomOut()" title="Zoom out"><i class="bi bi-dash-lg"></i></button>
        <button onclick="zoomReset()" title="Reset zoom"><i class="bi bi-arrows-fullscreen"></i></button>
    </div>
@endsection

@push('scripts')
    <script>
        const REORDER_URL = '{{ route('admin.orgchart.reorder') }}';
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let dragSrc = null;
        let changed = false;

        /* ── Drag events ── */
        function initDrag() {
            document.querySelectorAll('.oc-card[draggable]').forEach(card => {
                card.addEventListener('dragstart', onDragStart);
                card.addEventListener('dragend', onDragEnd);
                card.addEventListener('dragover', onDragOver);
                card.addEventListener('dragleave', onDragLeave);
                card.addEventListener('drop', onDrop);
            });
        }

        function onDragStart(e) {
            dragSrc = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.id);
        }

        function onDragEnd() {
            this.classList.remove('dragging');
            document.querySelectorAll('.oc-card').forEach(c => c.classList.remove('drag-over'));
            dragSrc = null;
        }

        function onDragOver(e) {
            e.preventDefault();
            if (dragSrc && this !== dragSrc) this.classList.add('drag-over');
            return false;
        }

        function onDragLeave() {
            this.classList.remove('drag-over');
        }

        function onDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.oc-card').forEach(c => c.classList.remove('drag-over'));

            if (!dragSrc || this === dragSrc) return;

            const dragId = parseInt(dragSrc.dataset.id);
            const targetId = parseInt(this.dataset.id);

            if (isDescendant(dragId, targetId)) {
                alert('Cannot move a node into its own child.');
                return;
            }

            dragSrc.dataset.parentId = targetId;

            const dragCol = dragSrc.closest('.oc-child-col') || dragSrc.closest('.oc-level');
            const targetLvl = this.closest('.oc-level');

            let childRow = targetLvl.querySelector(':scope > .oc-children-row');
            if (!childRow) {
                const vLine = document.createElement('div');
                vLine.className = 'oc-v-line';

                const hWrap = document.createElement('div');
                hWrap.className = 'oc-h-bar-wrap';
                const hBar = document.createElement('div');
                hBar.className = 'oc-h-bar';
                hWrap.appendChild(hBar);

                childRow = document.createElement('div');
                childRow.className = 'oc-children-row';

                targetLvl.appendChild(vLine);
                targetLvl.appendChild(hWrap);
                targetLvl.appendChild(childRow);
            }

            let colToMove;
            if (dragCol && dragCol.classList.contains('oc-child-col')) {
                colToMove = dragCol;
            } else {
                colToMove = document.createElement('div');
                colToMove.className = 'oc-child-col';
                const dl = document.createElement('div');
                dl.className = 'oc-drop-line';
                colToMove.appendChild(dl);
                colToMove.appendChild(dragSrc.closest('.oc-level') || dragSrc.parentElement);
            }

            const oldRow = colToMove.parentElement;
            colToMove.remove();
            childRow.appendChild(colToMove);

            if (oldRow && oldRow.classList.contains('oc-children-row') && !oldRow.children.length) {
                oldRow.previousElementSibling?.remove();
                oldRow.previousElementSibling?.remove();
                oldRow.remove();
            }

            recalcHBars();
            markChanged();
        }

        /* ── Recalculate horizontal bar widths ── */
        function recalcHBars() {
            document.querySelectorAll('.oc-children-row').forEach(row => {
                const cols = Array.from(row.children);
                if (!cols.length) return;

                const rowRect = row.getBoundingClientRect();
                const firstRect = cols[0].getBoundingClientRect();
                const lastRect = cols[cols.length - 1].getBoundingClientRect();

                const left = (firstRect.left + firstRect.width / 2) - rowRect.left;
                const right = (lastRect.left + lastRect.width / 2) - rowRect.left;
                const width = Math.max(0, right - left);

                const hWrap = row.previousElementSibling;
                if (hWrap && hWrap.classList.contains('oc-h-bar-wrap')) {
                    const bar = hWrap.querySelector('.oc-h-bar');
                    if (bar) {
                        bar.style.left = left + 'px';
                        bar.style.width = width + 'px';
                    }
                }
            });
        }

        function isDescendant(rootId, candidateId) {
            const rootCard = document.querySelector(`.oc-card[data-id="${rootId}"]`);
            const candidateCard = document.querySelector(`.oc-card[data-id="${candidateId}"]`);
            if (!rootCard || !candidateCard) return false;
            return rootCard.closest('.oc-level')?.contains(candidateCard) ?? false;
        }

        function markChanged() {
            changed = true;
            document.getElementById('save-banner').classList.add('visible');
        }

        function saveReorder() {
            const nodes = [];
            document.querySelectorAll('.oc-card[data-id]').forEach((card, idx) => {
                nodes.push({
                    id: parseInt(card.dataset.id),
                    parent_id: card.dataset.parentId ? parseInt(card.dataset.parentId) : null,
                    sort_order: idx,
                });
            });

            fetch(REORDER_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        nodes
                    }),
                })
                .then(r => {
                    if (!r.ok) throw new Error('Server error ' + r.status);
                    return r.json();
                })
                .then(() => {
                    changed = false;
                    document.getElementById('save-banner').classList.remove('visible');
                    location.reload();
                })
                .catch(err => alert('Save failed: ' + err.message));
        }

        window.addEventListener('beforeunload', e => {
            if (changed) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            initDrag();
            requestAnimationFrame(() => setTimeout(recalcHBars, 50));
        });
        window.addEventListener('resize', recalcHBars);

        /* ── Zoom ── */
        let currentZoom = 1;
        const ZOOM_STEP = 0.15;
        const ZOOM_MIN = 0.3;
        const ZOOM_MAX = 2;

        function applyZoom() {
            const tree = document.getElementById('oc-tree');
            const scaler = document.getElementById('oc-scaler');

            tree.style.transform = `scale(${currentZoom})`;

            const naturalH = tree.scrollHeight;
            scaler.style.height = (naturalH * currentZoom) + 'px';

            const naturalW = tree.scrollWidth;
            const scaledW = naturalW * currentZoom;
            const margin = Math.max(0, (naturalW - scaledW) / 2);
            tree.style.marginLeft = margin + 'px';
            tree.style.marginRight = margin + 'px';

            document.getElementById('oc-zoom-label').textContent =
                Math.round(currentZoom * 100) + '%';
        }

        function zoomIn() {
            currentZoom = Math.min(ZOOM_MAX, +(currentZoom + ZOOM_STEP).toFixed(2));
            applyZoom();
        }

        function zoomOut() {
            currentZoom = Math.max(ZOOM_MIN, +(currentZoom - ZOOM_STEP).toFixed(2));
            applyZoom();
        }

        function zoomReset() {
            currentZoom = 1;
            applyZoom();
        }

        document.querySelector('.oc-wrap').addEventListener('wheel', e => {
            if (!e.ctrlKey) return;
            e.preventDefault();
            e.deltaY < 0 ? zoomIn() : zoomOut();
        }, {
            passive: false
        });
    </script>
@endpush
