<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginal060abe2a9b4511e378911474e77b046d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal060abe2a9b4511e378911474e77b046d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginala2b0fa968b944f36eb1fd78215b6c473 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala2b0fa968b944f36eb1fd78215b6c473 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-information','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert-information'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala2b0fa968b944f36eb1fd78215b6c473)): ?>
<?php $attributes = $__attributesOriginala2b0fa968b944f36eb1fd78215b6c473; ?>
<?php unset($__attributesOriginala2b0fa968b944f36eb1fd78215b6c473); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala2b0fa968b944f36eb1fd78215b6c473)): ?>
<?php $component = $__componentOriginala2b0fa968b944f36eb1fd78215b6c473; ?>
<?php unset($__componentOriginala2b0fa968b944f36eb1fd78215b6c473); ?>
<?php endif; ?>

        <div
            class="mb-4 sm:mt-5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 text-xl font-bold text-gray-800 dark:text-white">
            <div>Equipment Management</div>

            
            <div class="flex flex-row gap-2 sm:gap-3 items-start sm:items-center text-sm">
                <form method="GET" action="<?php echo e(route('equipment.index')); ?>" class="flex gap-2 items-center"
                    onsubmit="showFullScreenLoader();">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search..."
                        class="text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 w-36 sm:w-44" />
                    <input type="hidden" name="per_page" value="<?php echo e($perPage ?? 5); ?>">

                    <button type="submit"
                        class="text-xs sm:text-sm px-3 py-1.5 sm:px-3 sm:py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Search
                    </button>
                </form>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('createequipment')): ?>
                    <a href="<?php echo e(route('equipment.create')); ?>" onclick="showFullScreenLoader();"
                        class="text-xs sm:text-sm px-3 py-1.5 sm:px-4 sm:py-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-md focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700 text-center">
                        Add Equipment
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <hr class="h-[3px] my-8 bg-gray-200 border-0 dark:bg-gray-700 w-full">

        
        <div class="w-full overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden shadow rounded-lg" id="equipmentList" data-list>
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm text-center text-gray-700 dark:text-gray-300">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-400 whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="no">No <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="name">Name <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="brand">Brand <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="model">Model <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="serial">S/N <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="qty">Qty <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="createdby">Created By <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="status">Status <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="location">Location <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>

                        <?php
                            $seedTs = \Carbon\Carbon::parse($latestTs ?? now())
                                ->subSeconds(2)
                                ->toIso8601String();
                        ?>

                        <tbody id="equipBody"
                            class="list bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 whitespace-nowrap"
                            data-last-ts="<?php echo e($seedTs); ?>" data-base-offset="<?php echo e($baseOffset ?? 0); ?>"
                            data-changes-url="<?php echo e(route('equipment.sync.changes', request()->only('search'))); ?>"
                            data-rows-url="<?php echo e(route('equipment.rows')); ?>">
                            <?php $__empty_1 = true; $__currentLoopData = $equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr data-id="<?php echo e($item->id); ?>">
                                    <td class="px-4 py-3 text-left no">
                                        <?php echo e($loop->iteration + ($equipment instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($equipment->currentPage() - 1) * $equipment->perPage() : 0)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-left name"><?php echo e($item->name); ?></td>
                                    <td class="px-4 py-3 brand"><?php echo e($item->brand ?? '-'); ?></td>
                                    <td class="px-4 py-3 model"><?php echo e($item->model ?? '-'); ?></td>
                                    <td class="px-4 py-3 serial"><?php echo e($item->serial_number ?? '-'); ?></td>
                                    <td class="px-4 py-3 qty"><?php echo e($item->qty); ?></td>
                                    <td class="px-4 py-3 createdby"><?php echo e($item->createdBy->name ?? '-'); ?></td>
                                    <td class="px-4 py-3 status">
                                        <?php if($item->status === 'baik'): ?>
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800">Baik</span>
                                        <?php else: ?>
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-md bg-red-100 text-red-700">Rusak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 location"><?php echo e($item->gudang->name ?? '-'); ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-row items-center justify-center gap-1">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editequipment')): ?>
                                                <a href="<?php echo e(route('equipment.edit', $item->id)); ?>"
                                                    onclick="showFullScreenLoader();">
                                                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['text' => 'Edit','color' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'Edit','color' => 'green']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('deleteequipment')): ?>
                                                <form action="<?php echo e(route('equipment.destroy', $item->id)); ?>" method="POST"
                                                    onsubmit="return confirmAndLoad('Are you sure to delete equipment?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['text' => 'Delete','color' => 'red']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'Delete','color' => 'red']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                                                </form>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('equipment.show', $item->id)); ?>"
                                                onclick="showFullScreenLoader();">
                                                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['text' => 'Details','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'Details','color' => 'blue']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No equipment found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        
        <div id="pollFooter"
            class="mt-2 mb-4 flex items-center justify-between text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 px-4">
            <span class="inline-flex items-center">
                <span id="pollDot" class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                <span id="pollLabel">Polling active</span>
            </span>
            <span>Last update: <span id="lastUpdatedAt">—</span></span>
        </div>

        <?php if (isset($component)) { $__componentOriginal720c5d99204acad589a79c73de989541 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal720c5d99204acad589a79c73de989541 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page-selector','data' => ['route' => 'equipment.index','perPage' => $perPage,'search' => $search,'items' => $equipment]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('equipment.index'),'perPage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perPage),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($equipment)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal720c5d99204acad589a79c73de989541)): ?>
<?php $attributes = $__attributesOriginal720c5d99204acad589a79c73de989541; ?>
<?php unset($__attributesOriginal720c5d99204acad589a79c73de989541); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal720c5d99204acad589a79c73de989541)): ?>
<?php $component = $__componentOriginal720c5d99204acad589a79c73de989541; ?>
<?php unset($__componentOriginal720c5d99204acad589a79c73de989541); ?>
<?php endif; ?>

        <?php $__env->startPush('scripts'); ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // ===== Init List.js =====
                    const list = new List('equipmentList', {
                        valueNames: ['no', 'name', 'brand', 'serial', 'qty', 'createdby', 'status', 'location',
                            'model'
                        ],
                    });
                    window.equipmentList = list;

                    const sortHeaders = document.querySelectorAll('.sort');
                    const sortStates = {};

                    function applySortIcon(header, state) {
                        const icon = header.querySelector('.sort-icon');
                        icon.textContent = state === 1 ? '↑' : (state === 2 ? '↓' : '');
                    }

                    function reapplyCurrentSort() {
                        for (const h of sortHeaders) {
                            const key = h.getAttribute('data-sort');
                            const st = sortStates[key] ?? 0;
                            if (st === 1) {
                                list.sort(key, {
                                    order: 'asc'
                                });
                                break;
                            }
                            if (st === 2) {
                                list.sort(key, {
                                    order: 'desc'
                                });
                                break;
                            }
                        }
                    }
                    sortHeaders.forEach(header => {
                        const key = header.getAttribute('data-sort');
                        sortStates[key] = 0;
                        header.addEventListener('click', function() {
                            sortStates[key] = (sortStates[key] + 1) % 3;
                            sortHeaders.forEach(h => {
                                if (h !== header) {
                                    const ok = h.getAttribute('data-sort');
                                    sortStates[ok] = 0;
                                    applySortIcon(h, 0);
                                }
                            });
                            applySortIcon(header, sortStates[key]);
                            if (sortStates[key] === 1) list.sort(key, {
                                order: 'asc'
                            });
                            else if (sortStates[key] === 2) list.sort(key, {
                                order: 'desc'
                            });
                            else list.sort('', {
                                order: 'asc'
                            });
                        });
                    });

                    // ===== Polling delta per-row =====
                    const tbody = document.getElementById('equipBody');
                    if (!tbody) return;

                    // footer UI
                    const pollDot = document.getElementById('pollDot');
                    const pollLbl = document.getElementById('pollLabel');
                    const lastLbl = document.getElementById('lastUpdatedAt');

                    function setPollUI(mode) {
                        if (!pollDot || !pollLbl) return;
                        if (mode === 'active') {
                            pollDot.className = 'inline-block w-2 h-2 rounded-full bg-green-500 mr-2';
                            pollLbl.textContent = 'Polling active';
                        } else if (mode === 'paused') {
                            pollDot.className = 'inline-block w-2 h-2 rounded-full bg-gray-400 mr-2';
                            pollLbl.textContent = 'Paused (tab hidden)';
                        } else {
                            pollDot.className = 'inline-block w-2 h-2 rounded-full bg-amber-500 mr-2';
                            pollLbl.textContent = 'Idle';
                        }
                    }

                    function updateLastUpdated() {
                        if (lastLbl) lastLbl.textContent = new Date().toLocaleString('id-ID');
                    }

                    let lastTs = tbody.dataset.lastTs || new Date().toISOString();
                    let baseOffset = parseInt(tbody.dataset.baseOffset || '0', 10);
                    const changesUrl = tbody.dataset.changesUrl;
                    const rowsUrl = tbody.dataset.rowsUrl;

                    let timer = null,
                        idle = 0;
                    const baseInterval = 10000; // 10s — ubah jika perlu
                    const maxInterval = 60000; // 60s

                    function nextInterval() {
                        return Math.min(baseInterval + idle * 10000, maxInterval);
                    }

                    function schedule(ms, reason = '') {
                        clearTimeout(timer);
                        console.debug(
                            `[Equipment] Polling ${reason ? '('+reason+') ' : ''}→ next in ${(ms/1000).toFixed(1)}s`);
                        timer = setTimeout(tick, ms);
                    }

                    function renumber() {
                        const rows = tbody.querySelectorAll('tr[data-id]');
                        let i = 0;
                        rows.forEach(tr => {
                            const cell = tr.querySelector('.no');
                            if (cell) cell.textContent = (baseOffset + (++i)).toString();
                        });
                        list.reIndex();
                        reapplyCurrentSort();
                    }

                    async function tick() {
                        try {
                            if (document.hidden) {
                                setPollUI('paused');
                                console.debug('[Equipment] Tab hidden → pause polling');
                                schedule(maxInterval, 'hidden');
                                return;
                            }
                            setPollUI('active');
                            console.debug('[Equipment] Tick start', {
                                since: lastTs
                            });

                            const u = new URL(changesUrl, window.location.origin);
                            u.searchParams.set('since', lastTs);

                            // KIRIM daftar ID yang sedang terlihat → backend bisa hitung "deleted" untuk hard delete
                            tbody.querySelectorAll('tr[data-id]').forEach(tr => {
                                u.searchParams.append('visible[]', tr.getAttribute('data-id'));
                            });

                            const res = await fetch(u.toString(), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            if (!res.ok) throw new Error('changes fetch failed');
                            const j = await res.json();

                            const {
                                latest_ts,
                                created = [],
                                updated = [],
                                deleted = []
                            } = j || {};
                            let changed = false;

                            // deleted: hapus baris yang hilang di DB
                            if (deleted.length) {
                                deleted.forEach(id => {
                                    const row = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (row) row.remove();
                                });
                                changed = true;
                            }

                            // created + updated → ambil partial rows (HTML)
                            const need = [...new Set([...updated, ...created])];
                            if (need.length) {
                                const ru = new URL(rowsUrl, window.location.origin);
                                need.forEach(id => ru.searchParams.append('ids[]', id));
                                const html = await (await fetch(ru.toString(), {
                                    headers: {
                                        'Accept': 'text/html'
                                    }
                                })).text();

                                const tpl = document.createElement('template');
                                tpl.innerHTML = html.trim();
                                const fresh = Array.from(tpl.content.querySelectorAll('tr[data-id]'));

                                fresh.forEach(newRow => {
                                    const id = newRow.getAttribute('data-id');
                                    const old = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (old) old.replaceWith(newRow);
                                    else tbody.insertBefore(newRow, tbody.firstChild);
                                });
                                changed = true;
                                console.debug('[Equipment] Applied changes', {
                                    created: created.length,
                                    updated: updated.length,
                                    deleted: deleted.length
                                });
                            } else {
                                console.debug('[Equipment] No changes');
                            }

                            if (latest_ts) lastTs = latest_ts;
                            updateLastUpdated();

                            if (changed) {
                                renumber();
                                idle = 0;
                                setPollUI('active');
                                schedule(nextInterval(), 'after-change');
                            } else {
                                idle++;
                                setPollUI('idle');
                                schedule(nextInterval(), 'no-change');
                            }
                        } catch (e) {
                            console.error('[Equipment] Polling error:', e);
                            setPollUI('idle');
                            schedule(maxInterval, 'error');
                        }
                    }

                    // Resume cepat saat tab kembali aktif
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) {
                            console.debug('[Equipment] Tab visible → resume now');
                            idle = 0;
                            setPollUI('active');
                            schedule(200, 'resume-visible');
                        }
                    });

                    // Instant highlight ketika balik dari create (?highlight=ID)
                    async function tryInstantHighlight() {
                        const params = new URLSearchParams(location.search);
                        const highlightId = params.get('highlight');
                        if (!highlightId) return;
                        try {
                            const ru = new URL(rowsUrl, window.location.origin);
                            ru.searchParams.append('ids[]', highlightId);
                            const html = await (await fetch(ru.toString(), {
                                headers: {
                                    'Accept': 'text/html'
                                }
                            })).text();
                            const tpl = document.createElement('template');
                            tpl.innerHTML = html.trim();
                            const newRow = tpl.content.querySelector('tr[data-id]');
                            if (newRow) {
                                const old = tbody.querySelector(`tr[data-id="${highlightId}"]`);
                                if (old) old.replaceWith(newRow);
                                else tbody.insertBefore(newRow, tbody.firstChild);
                                renumber();
                                newRow.classList.add('animate-pulse');
                                setTimeout(() => newRow.classList.remove('animate-pulse'), 1200);
                                console.debug('[Equipment] Instant highlight appended for ID', highlightId);
                            }
                        } catch (e) {
                            console.error('[Equipment] highlight fetch failed', e);
                        }
                    }

                    // Start
                    updateLastUpdated();
                    tryInstantHighlight();
                    schedule(baseInterval, 'init');
                });
            </script>
        <?php $__env->stopPush(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal060abe2a9b4511e378911474e77b046d)): ?>
<?php $attributes = $__attributesOriginal060abe2a9b4511e378911474e77b046d; ?>
<?php unset($__attributesOriginal060abe2a9b4511e378911474e77b046d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal060abe2a9b4511e378911474e77b046d)): ?>
<?php $component = $__componentOriginal060abe2a9b4511e378911474e77b046d; ?>
<?php unset($__componentOriginal060abe2a9b4511e378911474e77b046d); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\equipment\index.blade.php ENDPATH**/ ?>