
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

        <?php
            // supaya komponen per-page dapet nilai konsisten
            $search = request('search');
            $perPage = (int) ($perPage ?? ($items->perPage() ?? 10));
        ?>

        <div
            class="mb-4 sm:mt-5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-xl font-bold text-gray-800 dark:text-white">
            <div>Notifications</div>

            
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto text-sm">
                <form method="GET" action="<?php echo e(route('notifications.index')); ?>" id="filterForm"
                    class="flex items-center gap-2 flex-1 min-w-0">
                    <div class="shrink-0">
                        <?php if (isset($component)) { $__componentOriginal882efe807f9a6518d3ff9d28ce551b05 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal882efe807f9a6518d3ff9d28ce551b05 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-filter-dropdown','data' => ['action' => route('notifications.index'),'startDate' => request('start_date'),'endDate' => request('end_date'),'formId' => 'filterForm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-filter-dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('notifications.index')),'startDate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('start_date')),'endDate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('end_date')),'formId' => 'filterForm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal882efe807f9a6518d3ff9d28ce551b05)): ?>
<?php $attributes = $__attributesOriginal882efe807f9a6518d3ff9d28ce551b05; ?>
<?php unset($__attributesOriginal882efe807f9a6518d3ff9d28ce551b05); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal882efe807f9a6518d3ff9d28ce551b05)): ?>
<?php $component = $__componentOriginal882efe807f9a6518d3ff9d28ce551b05; ?>
<?php unset($__componentOriginal882efe807f9a6518d3ff9d28ce551b05); ?>
<?php endif; ?>
                    </div>

                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search..."
                        class="flex-1 min-w-0 w-full sm:w-48 text-xs sm:text-sm px-3 py-2 rounded-md
                           border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                           focus:ring-purple-500" />

                    <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'submit','class' => 'px-3 py-2 text-xs sm:text-sm rounded-md','text' => 'Search','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'px-3 py-2 text-xs sm:text-sm rounded-md','text' => 'Search','color' => 'blue']); ?>
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

                
                <form method="POST" action="<?php echo e(route('notifications.readAll')); ?>"
                    onsubmit="return confirmAndLoad('Tandai semua notifikasi sebagai terbaca?')">
                    <?php echo csrf_field(); ?>
                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['text' => 'Mark all read','color' => 'green','class' => 'basis-full sm:basis-auto w-full sm:w-auto justify-center text-xs sm:text-sm px-3 py-2','type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'Mark all read','color' => 'green','class' => 'basis-full sm:basis-auto w-full sm:w-auto justify-center text-xs sm:text-sm px-3 py-2','type' => 'submit']); ?>
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
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full text-sm whitespace-nowrap min-w-[900px]">
                    <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-2 text-left w-28">Type</th>
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2 text-left hidden sm:table-cell">Message</th>
                            <th class="px-4 py-2 text-left w-40">Created</th>
                            <th class="px-4 py-2 text-left w-24">Status</th>
                            <th class="px-4 py-2 text-left w-24">Action</th>
                        </tr>
                    </thead>
                    <tbody id="notifIndexBody">
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr data-id="<?php echo e($n->id); ?>"
                                class="border-t dark:border-gray-700 <?php echo e($n->read_at ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800'); ?>">
                                <td class="px-4 py-2"><?php echo e($n->type); ?></td>
                                <td class="px-4 py-2 max-w-[260px] truncate">
                                    <?php echo e($n->title ?? (data_get($n->data, 'title') ?? ucfirst(str_replace('_', ' ', $n->type)))); ?>

                                </td>
                                <td class="px-4 py-2 max-w-[380px] truncate hidden sm:table-cell">
                                    <?php echo e($n->message ?? data_get($n->data, 'message')); ?>

                                </td>
                                <td class="px-4 py-2"><?php echo e($n->created_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-4 py-2">
                                    <?php if(is_null($n->read_at)): ?>
                                        <span
                                            class="inline-flex items-center gap-1 text-xs text-blue-700 dark:text-blue-300">
                                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span> Unread
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-500">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2">
                                    <?php if(is_null($n->read_at)): ?>
                                        <form method="POST" action="<?php echo e(route('notifications.read', $n)); ?>"
                                            onsubmit="return confirmAndLoad('Mark this as read?')">
                                            <?php echo csrf_field(); ?>
                                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['text' => 'Mark','color' => 'green','type' => 'submit','dense' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'Mark','color' => 'green','type' => 'submit','dense' => 'true']); ?>
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
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada notifikasi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="px-3 py-2 text-xs text-gray-500 border-t dark:border-gray-700 flex items-center gap-2">
                <span id="idxStatusDot" class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span>Polling aktif</span>
                <span class="mx-1">•</span>
                <span>Last update: <span id="idxLastUpdate">—</span></span>
            </div>
        </div>

        
        <?php if (isset($component)) { $__componentOriginal720c5d99204acad589a79c73de989541 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal720c5d99204acad589a79c73de989541 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page-selector','data' => ['route' => 'notifications.index','perPage' => $perPage,'search' => $search,'items' => $items]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('notifications.index'),'perPage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perPage),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($items)]); ?>
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

<script>
    (() => {
        const body = document.getElementById('notifIndexBody'); // ← PENTING: id ada
        const idxDot = document.getElementById('idxStatusDot');
        const idxLast = document.getElementById('idxLastUpdate');

        function visibleIds() {
            return Array.from(body.querySelectorAll('tr[data-id]')).map(tr => parseInt(tr.dataset.id, 10));
        }

        function updateStamp() {
            idxLast.textContent = new Date().toLocaleTimeString('id-ID', {
                hour12: false
            });
        }

        let latestTs = <?php echo json_encode($latestTs, 15, 512) ?>;
        let backoff = 5000,
            MIN = 5000,
            MAX = 30000,
            timer = null;

        async function poll() {
            if (document.hidden) {
                idxDot.classList.replace('bg-green-500', 'bg-yellow-500');
                return schedule();
            }
            idxDot.classList.replace('bg-yellow-500', 'bg-green-500');

            try {
                const url = new URL(<?php echo json_encode(route('notifications.index.sync.changes'), 15, 512) ?>, window.location.origin);
                url.searchParams.set('since', latestTs);

                // kirim filter yang sama
                const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
                for (const [k, v] of params) url.searchParams.set(k, v);

                for (const id of visibleIds()) url.searchParams.append('visible[]', id);

                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('sync failed');
                const data = await res.json();

                latestTs = data.latest_ts || latestTs;

                // delete first
                (data.deleted || []).forEach(id => {
                    const tr = body.querySelector(`tr[data-id="${id}"]`);
                    if (tr) tr.remove();
                });

                const need = Array.from(new Set([...(data.created || []), ...(data.updated || [])]));
                if (need.length) {
                    const rowsUrl = new URL(<?php echo json_encode(route('notifications.index.rows'), 15, 512) ?>, window.location.origin);
                    // kirim filter sama juga
                    const params2 = new URLSearchParams(new FormData(document.getElementById('filterForm')));
                    for (const [k, v] of params2) rowsUrl.searchParams.set(k, v);
                    need.forEach(id => rowsUrl.searchParams.append('ids[]', id));

                    const html = await (await fetch(rowsUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })).text();
                    const tmp = document.createElement('tbody');
                    tmp.innerHTML = html.trim();

                    // replace/insert
                    tmp.querySelectorAll('tr[data-id]').forEach(newTr => {
                        const id = newTr.getAttribute('data-id');
                        const old = body.querySelector(`tr[data-id="${id}"]`);
                        if (old) old.replaceWith(newTr);
                        else body.prepend(newTr);
                    });
                }

                updateStamp();
                backoff = Math.max(MIN, Math.floor(backoff * 0.8));
            } catch (e) {
                backoff = Math.min(MAX, Math.floor(backoff * 1.6));
            } finally {
                schedule();
            }
        }

        function schedule() {
            if (timer) clearTimeout(timer);
            timer = setTimeout(poll, backoff);
        }
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                backoff = MIN;
                poll();
            }
        });
        schedule();
    })();
</script>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\notifications\index.blade.php ENDPATH**/ ?>