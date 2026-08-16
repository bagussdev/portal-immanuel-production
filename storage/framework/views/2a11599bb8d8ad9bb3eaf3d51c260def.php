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

        <div class="mt-5 mb-4 text-xl font-bold text-gray-800 dark:text-white">Pembayaran</div>

        <?php
            $mode = $mode ?? request('mode', 'monthly');
            $month = $month ?? request('month', now()->format('Y-m'));
            $weekStart =
                $weekStart ?? request('week_start', now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString());
            $search = $search ?? request('search', '');
            $exactDate = $exactDate ?? request('exact_date', '');
            $perPage = $perPage ?? request('per_page', 5);
        ?>

        
        <form id="periodForm" method="GET" action="<?php echo e(route('payments.index')); ?>"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 lg:p-5 shadow mb-4 relative"
            onsubmit="showFullScreenLoader();">
            <input type="hidden" name="mode" id="modeInput" value="<?php echo e($mode); ?>">
            <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Periode</div>

                <div class="grid w-full grid-cols-3 gap-2 md:flex md:w-auto md:items-center">
                    <button type="button" id="prevBtn"
                        class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg border border-gray-300 px-2 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 sm:px-3 sm:py-2 sm:text-sm">‹
                        Prev</button>
                    <button type="button" id="nextBtn"
                        class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg border border-gray-300 px-2 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 sm:px-3 sm:py-2 sm:text-sm">Next
                        ›</button>

                    
                    <button type="button" id="filterToggleBtn"
                        class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-2 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 sm:gap-2 sm:px-3 sm:py-2 sm:text-sm">
                        <span>Filter</span>
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.11l3.71-3.92a.75.75 0 111.08 1.04l-4.24 4.44a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            
            <div id="filterPanel"
                class="hidden mt-3 grid grid-cols-12 gap-3 md:absolute md:right-4 md:top-14 md:w-[780px] md:bg-white md:dark:bg-gray-800 md:border md:border-gray-200 md:dark:border-gray-700 md:rounded-xl md:shadow-xl md:p-4 z-20">

                
                <div class="col-span-12 md:col-span-4">
                    <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Mode</label>
                    <div id="segmentedToggle"
                        class="inline-flex w-full p-1 rounded-2xl bg-gray-100 dark:bg-gray-700 shadow-inner">
                        <button type="button" data-mode="weekly"
                            class="seg-btn flex-1 px-3 py-1.5 rounded-xl text-sm font-medium transition <?php echo e($mode === 'weekly' ? 'bg-white dark:bg-gray-800 shadow text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'); ?>">
                            Weekly
                        </button>
                        <button type="button" data-mode="monthly"
                            class="seg-btn flex-1 px-3 py-1.5 rounded-xl text-sm font-medium transition <?php echo e($mode === 'monthly' ? 'bg-white dark:bg-gray-800 shadow text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'); ?>">
                            Monthly
                        </button>
                    </div>
                </div>

                
                <div class="col-span-12 md:col-span-8">
                    <div class="grid grid-cols-12 gap-3">
                        <div id="monthlyWrap"
                            class="col-span-12 sm:col-span-6 <?php echo e($mode === 'weekly' ? 'hidden' : ''); ?>">
                            <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Bulan</label>
                            <input id="monthInput" name="month" type="month" value="<?php echo e($month); ?>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div id="weeklyWrap" class="col-span-12 sm:col-span-6 <?php echo e($mode === 'weekly' ? '' : 'hidden'); ?>">
                            <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Mulai
                                Minggu</label>
                            <input id="weekInput" name="week_start" type="date" value="<?php echo e($weekStart); ?>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                
                <div class="col-span-12">
                    <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Pencarian</label>

                    
                    <div class="flex flex-row flex-wrap sm:flex-nowrap items-stretch gap-2">
                        <input name="search" id="searchInput" type="text" value="<?php echo e($search); ?>"
                            placeholder="Cari invoice no / client / notes / user"
                            class="flex-1 min-w-0 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                        <input name="exact_date" id="exactDateInput" type="date" value="<?php echo e($exactDate); ?>"
                            class="w-36 shrink-0 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        
                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['href' => route('payments.index'),'onclick' => 'showFullScreenLoader();','text' => 'Reset','color' => 'blue','dense' => true,'class' => 'basis-full sm:basis-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('payments.index')),'onclick' => 'showFullScreenLoader();','text' => 'Reset','color' => 'blue','dense' => true,'class' => 'basis-full sm:basis-auto']); ?>
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
                    </div>
                </div>
            </div>

            <div class="mt-3 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                Periode aktif:
                <span id="periodLabel"
                    class="font-medium text-gray-900 dark:text-white"><?php echo e($periodLabel ?? ''); ?></span>
            </div>
        </form>

        
        <div class="mb-4 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 sm:p-4">
                <div class="text-xs text-gray-500">Total Pembayaran</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    <?php echo e(number_format($totalAmount ?? 0, 0, ',', '.')); ?></div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 sm:p-4">
                <div class="text-xs text-gray-500">Jumlah Pembayaran</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    <?php echo e(number_format($count ?? 0, 0, ',', '.')); ?></div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 sm:p-4">
                <div class="text-xs text-gray-500">Rata-rata</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    <?php echo e(number_format($avgAmount ?? 0, 0, ',', '.')); ?></div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 sm:p-4">
                <div class="text-xs text-gray-500">Terbesar</div>
                <div class="text-sm text-gray-900 dark:text-white">
                    <?php echo e(isset($maxPayment['amount']) ? number_format($maxPayment['amount'], 0, ',', '.') : '0'); ?>

                </div>
                <div class="text-xs text-gray-500 mt-1">
                    <?php echo e(!empty($maxPayment) ? ($maxPayment['invoice_number'] ?? '') . ' • ' . ($maxPayment['client_name'] ?? '') : '—'); ?>

                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto" id="paymentsList">
                <table class="min-w-full text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-2 w-14 cursor-pointer sort" data-sort="no">No <span
                                    class="sort-icon"></span></th>
                            <th class="px-4 py-2 cursor-pointer sort" data-sort="invoice_number">Invoice No
                                <span class="sort-icon"></span>
                            </th>
                            <th class="px-4 py-2 cursor-pointer sort" data-sort="date">Tanggal <span
                                    class="sort-icon"></span></th>
                            <th class="px-4 py-2 cursor-pointer sort" data-sort="client_name">Client <span
                                    class="sort-icon"></span></th>
                            <th class="px-4 py-2 cursor-pointer sort" data-sort="amount">Amount <span
                                    class="sort-icon"></span></th>
                            <th class="px-4 py-2">Notes</th>
                            <th class="px-4 py-2 cursor-pointer sort" data-sort="received_by">Created By
                                <span class="sort-icon"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="list divide-y divide-gray-200 dark:divide-gray-700">
                        <?php
                            $baseIndex =
                                $items instanceof \Illuminate\Pagination\LengthAwarePaginator
                                    ? ($items->currentPage() - 1) * $items->perPage()
                                    : 0;
                        ?>
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $isoDate = !empty($it['paid_at'])
                                    ? \Carbon\Carbon::parse($it['paid_at'])->toDateString()
                                    : '';
                                $amountRaw = (int) ($it['amount'] ?? 0);
                                $percent = number_format((float) ($it['percent'] ?? 0), 0, ',', '.');
                            ?>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-2 no text-center"><?php echo e($baseIndex + $i + 1); ?></td>
                                <td class="px-4 py-2 invoice_number">
                                    <?php if(!empty($it['invoice_id'])): ?>
                                        <a class="text-indigo-600 hover:underline"
                                            href="<?php echo e(route('invoices.show', $it['invoice_id'])); ?>"><?php echo e($it['invoice_number']); ?></a>
                                    <?php else: ?>
                                        <?php echo e($it['invoice_number']); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="date"
                                        data-sort="<?php echo e($isoDate); ?>"><?php echo e($it['paid_at_human'] ?? ''); ?></span>
                                </td>
                                <td class="px-4 py-2 client_name"><?php echo e(trim($it['client_name'] ?? '') ?: '—'); ?></td>
                                <td class="px-4 py-2">
                                    <span class="amount" data-sort="<?php echo e($amountRaw); ?>"><?php echo e($percent); ?>% —
                                        <?php echo e(number_format($amountRaw, 0, ',', '.')); ?></span>
                                </td>
                                <td class="px-4 py-2"><?php echo e(trim($it['notes'] ?? '') ?: '—'); ?></td>
                                <td class="px-4 py-2 received_by"><?php echo e(trim($it['received_by'] ?? '') ?: '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($component)) { $__componentOriginal720c5d99204acad589a79c73de989541 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal720c5d99204acad589a79c73de989541 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page-selector','data' => ['route' => 'payments.index','perPage' => $perPage,'search' => $search,'items' => $items,'showPagination' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('payments.index'),'perPage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perPage),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($items),'showPagination' => true]); ?>
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
        </div>

        <?php $__env->startPush('scripts'); ?>
            <script src="https://cdn.jsdelivr.net/npm/list.js@2.3.1/dist/list.min.js"></script>
            <script>
                (function() {
                    const form = document.getElementById('periodForm');
                    const modeEl = document.getElementById('modeInput');
                    const monthEl = document.getElementById('monthInput');
                    const weekEl = document.getElementById('weekInput');
                    const searchEl = document.getElementById('searchInput');
                    const exactEl = document.getElementById('exactDateInput');

                    const monthlyWrap = document.getElementById('monthlyWrap');
                    const weeklyWrap = document.getElementById('weeklyWrap');

                    // === Filter dropdown logic ===
                    const toggleBtn = document.getElementById('filterToggleBtn');
                    const panel = document.getElementById('filterPanel');

                    function openPanel() {
                        panel?.classList.remove('hidden');
                    }

                    function closePanel() {
                        panel?.classList.add('hidden');
                    }

                    toggleBtn?.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (panel?.classList.contains('hidden')) openPanel();
                        else closePanel();
                    });
                    // close when clicking outside panel (bukan seluruh form)
                    document.addEventListener('click', (e) => {
                        if (!panel || panel.classList.contains('hidden')) return;
                        if (!panel.contains(e.target) && e.target !== toggleBtn) closePanel();
                    });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') closePanel();
                    });

                    // === Auto submit ===
                    function submitNow() {
                        form?.submit();
                    }

                    function pad(n) {
                        return String(n).padStart(2, '0');
                    }

                    document.querySelectorAll('#segmentedToggle [data-mode]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const m = btn.getAttribute('data-mode');
                            if (!m || m === modeEl.value) return;
                            modeEl.value = m;
                            monthlyWrap?.classList.toggle('hidden', m !== 'monthly');
                            weeklyWrap?.classList.toggle('hidden', m !== 'weekly');
                            submitNow();
                        });
                    });

                    function shift(dir) {
                        const m = modeEl.value || 'monthly';
                        if (m === 'monthly') {
                            const val = monthEl?.value || new Date().toISOString().slice(0, 7);
                            const [y, M] = val.split('-').map(Number);
                            const d = new Date(y || new Date().getFullYear(), (M ? M - 1 : 0), 1);
                            d.setMonth(d.getMonth() + (dir > 0 ? 1 : -1));
                            if (monthEl) monthEl.value = `${d.getFullYear()}-${pad(d.getMonth()+1)}`;
                        } else {
                            const val = weekEl?.value || new Date().toISOString().slice(0, 10);
                            const d = new Date(val);
                            d.setDate(d.getDate() + (dir > 0 ? 7 : -7));
                            if (weekEl) weekEl.value = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
                        }
                        submitNow();
                    }
                    document.getElementById('prevBtn')?.addEventListener('click', () => shift(-1));
                    document.getElementById('nextBtn')?.addEventListener('click', () => shift(1));

                    monthEl?.addEventListener('change', submitNow);
                    weekEl?.addEventListener('change', submitNow);

                    let t = null;

                    function onSearch() {
                        clearTimeout(t);
                        t = setTimeout(submitNow, 350);
                    }
                    searchEl?.addEventListener('input', onSearch);
                    exactEl?.addEventListener('change', submitNow);

                    // === List.js sorting (tri-state) ===
                    const list = new List('paymentsList', {
                        valueNames: [
                            'no', 'invoice_number',
                            {
                                name: 'date',
                                attr: 'data-sort'
                            },
                            'client_name',
                            {
                                name: 'amount',
                                attr: 'data-sort'
                            },
                            'received_by',
                        ]
                    });

                    const headers = document.querySelectorAll('#paymentsList thead th.sort');
                    const tbody = document.querySelector('#paymentsList tbody.list');
                    const originalHTML = tbody ? tbody.innerHTML : '';
                    let currentSort = {
                        el: null,
                        key: '',
                        order: ''
                    };

                    headers.forEach(h => {
                        h.addEventListener('click', () => {
                            const key = h.getAttribute('data-sort');
                            const icon = h.querySelector('.sort-icon');
                            document.querySelectorAll('#paymentsList .sort-icon').forEach(i => i.textContent =
                                '');

                            if (currentSort.el === h) {
                                if (currentSort.order === 'asc') {
                                    list.sort(key, {
                                        order: 'desc'
                                    });
                                    icon && (icon.textContent = '↓');
                                    currentSort.order = 'desc';
                                } else if (currentSort.order === 'desc') {
                                    tbody && (tbody.innerHTML = originalHTML);
                                    list.reIndex();
                                    currentSort = {
                                        el: null,
                                        key: '',
                                        order: ''
                                    };
                                }
                            } else {
                                list.sort(key, {
                                    order: 'asc'
                                });
                                icon && (icon.textContent = '↑');
                                currentSort = {
                                    el: h,
                                    key,
                                    order: 'asc'
                                };
                            }
                        });
                    });
                })();
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/payments/index.blade.php ENDPATH**/ ?>