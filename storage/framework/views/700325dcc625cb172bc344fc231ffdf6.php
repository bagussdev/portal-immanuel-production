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

            
            <div class="mb-4 sm:mt-5 text-xl font-bold text-gray-800 dark:text-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <div>Pengeluaran</div>

                    
                    <div class="w-full sm:w-auto flex flex-col sm:flex-row sm:items-center gap-2">

                        
                        <form method="GET" action="<?php echo e(route('expenses.index')); ?>" id="filterForm"
                            onsubmit="showFullScreenLoader();" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">

                            <?php $curMonth = (int) ($month ?? now()->month); ?>

                            <select name="month"
                                class="text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo e($m); ?>" <?php if((int) $m === (int) $curMonth): echo 'selected'; endif; ?>>
                                        <?php echo e(\Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F')); ?>

                                    </option>
                                <?php endfor; ?>
                            </select>

                            <input type="number" name="year" value="<?php echo e($year ?? now()->year); ?>"
                                class="w-24 text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                            <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="Search..."
                                class="text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white w-36 sm:w-44">

                            <input type="hidden" name="per_page" value="<?php echo e($perPage ?? 10); ?>">

                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['color' => 'blue','type' => 'submit','text' => 'Terapkan','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'blue','type' => 'submit','text' => 'Terapkan','dense' => true]); ?>
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

                        
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto sm:justify-end">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('createexpenses')): ?>
                                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['href' => route('expenses.create', ['month' => $month, 'year' => $year]),'onclick' => 'showFullScreenLoader();','class' => ''.e($locks['can_add'] ? '' : 'pointer-events-none opacity-50').'','color' => 'blue','text' => 'Tambah','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('expenses.create', ['month' => $month, 'year' => $year])),'onclick' => 'showFullScreenLoader();','class' => ''.e($locks['can_add'] ? '' : 'pointer-events-none opacity-50').'','color' => 'blue','text' => 'Tambah','dense' => true]); ?>
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
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageexpenses')): ?>
                                
                                <?php if(($locks['period_exists'] ?? false) && ($locks['can_close'] ?? false) && ($period ?? null)): ?>
                                    <form
                                        action="<?php echo e(route('expenses.period.close', $period)); ?>?month=<?php echo e($month); ?>&year=<?php echo e($year); ?>"
                                        method="POST" onsubmit="return confirmAndLoad('Tutup periode ini (CLOSED)?');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['color' => 'red','text' => 'Tutup','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'red','text' => 'Tutup','dense' => true]); ?>
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

                                
                                <?php if(($locks['period_exists'] ?? false) && ($locks['can_reopen'] ?? false) && ($period ?? null)): ?>
                                    <form
                                        action="<?php echo e(route('expenses.period.reopen', $period)); ?>?month=<?php echo e($month); ?>&year=<?php echo e($year); ?>"
                                        method="POST"
                                        onsubmit="return confirmAndLoad('Reopen periode ini? Editing akan dibuka kembali.');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['color' => 'yellow','text' => 'Buka lagi','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'yellow','text' => 'Buka lagi','dense' => true]); ?>
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
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="flex flex-col gap-3 mb-4">
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        Period:
                    </span>
                    <span class="font-semibold">
                        <?php echo e(\Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F')); ?>

                        <?php echo e($year); ?>

                    </span>

                    <?php if($locks['period_exists'] ?? false): ?>
                        <?php
                            $pStatus = strtolower($period->status ?? '');
                            $pLabel = \Illuminate\Support\Str::of($pStatus)->title();
                            $badgeClasses = match ($pStatus) {
                                'open' => 'bg-green-100 text-green-800',
                                'reopen' => 'bg-yellow-100 text-yellow-800',
                                'closed' => 'bg-red-200 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        ?>
                        <span class="px-2 py-1 rounded text-xs <?php echo e($badgeClasses); ?>">
                            <?php echo e($pLabel); ?>

                        </span>
                    <?php else: ?>
                        <span class="px-2 py-1 rounded text-xs bg-rose-100 text-rose-800">
                            Not Opened
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (! ($locks['period_exists'] ?? false)): ?>
                    <div class="text-xs text-gray-600 dark:text-gray-300">
                        <?php if($locks['is_current'] ?? false): ?>
                            Periode bulan berjalan akan otomatis OPEN saat ada transaksi pertama.
                        <?php elseif($locks['is_past'] ?? false): ?>
                            Periode lampau tidak dapat di-Open. Gunakan <strong>Reopen</strong> jika period-nya sudah pernah
                            dibuat dan ditutup.
                        <?php else: ?>
                            Periode mendatang belum dapat dibuka sebelum waktunya.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            
            <?php
                $count = (int) ($stats['count'] ?? 0);
                $total = (int) ($stats['total'] ?? 0);
                $avg = (int) ($stats['avg'] ?? 0);
            ?>
            <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-gray-500 text-xs">Transactions</div>
                    <div class="font-semibold text-blue-600"><?php echo e(number_format($count, 0, ',', '.')); ?></div>
                </div>
                <div class="rounded border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-gray-500 text-xs">Total</div>
                    <div class="font-bold"><?php echo e('Rp ' . number_format($total, 0, ',', '.')); ?></div>
                </div>
                <div class="rounded border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-gray-500 text-xs">Avg / Txn</div>
                    <div class="font-semibold"><?php echo e('Rp ' . number_format($avg, 0, ',', '.')); ?></div>
                </div>
            </div>

            <hr class="h-[3px] mb-6 bg-gray-200 border-0 dark:bg-gray-700 w-full">

            
            <div class="w-full overflow-x-auto">
                <div class="min-w-full inline-block align-middle">
                    <div class="overflow-hidden shadow rounded-lg bg-white dark:bg-gray-800" id="expenseList"
                        data-month="<?php echo e($month); ?>" data-year="<?php echo e($year); ?>"
                        data-search="<?php echo e($search); ?>" data-changes-url="<?php echo e(route('expenses.sync.changes')); ?>"
                        data-rows-url="<?php echo e(route('expenses.rows')); ?>" data-latest-ts="<?php echo e($latestTs ?? ''); ?>">

                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3 text-left">Expense No</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3 text-left">Name</th>
                                    <th class="px-4 py-3">Qty</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3 text-left">Created By</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>

                            <tbody id="expenses_tbody"
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $canAct = $allowActions ?? false;
                                    ?>
                                    <tr data-id="<?php echo e($row->id); ?>">
                                        <td class="px-4 py-3">
                                            <?php echo e(($baseOffset ?? 0) + $loop->iteration); ?>

                                        </td>
                                        <td class="px-4 py-3 text-left font-medium"><?php echo e($row->expense_number); ?></td>
                                        <td class="px-4 py-3"><?php echo e(optional($row->expense_date)->format('d/m/Y')); ?></td>
                                        <td class="px-4 py-3 text-left"><?php echo e($row->name); ?></td>
                                        <td class="px-4 py-3"><?php echo e(number_format((int) $row->qty, 0, ',', '.')); ?></td>
                                        <td class="px-4 py-3 font-semibold">
                                            <?php echo e('Rp ' . number_format((int) $row->total, 0, ',', '.')); ?></td>
                                        <td class="px-4 py-3 text-left"><?php echo e($row->creator->name ?? '-'); ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center items-center gap-1">
                                                <?php $canAct = ($allowActions ?? ($locks['can_add'] ?? false)); ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editexpenses')): ?>
                                                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['href' => route('expenses.edit', $row),'onclick' => 'showFullScreenLoader();','class' => ''.e($canAct ? '' : 'pointer-events-none opacity-50').'','color' => 'green','text' => 'Edit','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('expenses.edit', $row)),'onclick' => 'showFullScreenLoader();','class' => ''.e($canAct ? '' : 'pointer-events-none opacity-50').'','color' => 'green','text' => 'Edit','dense' => true]); ?>
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
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('deleteexpenses')): ?>
                                                    <form action="<?php echo e(route('expenses.destroy', $row)); ?>" method="POST"
                                                        onsubmit="return confirmAndLoad('Hapus expense ini?');"
                                                        class="<?php echo e($canAct ? '' : 'pointer-events-none opacity-50'); ?>">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['color' => 'red','text' => 'Hapus','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'red','text' => 'Hapus','dense' => true]); ?>
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
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                            <?php if(!($locks['period_exists'] ?? false)): ?>
                                                <?php if($locks['is_current'] ?? false): ?>
                                                    Period akan otomatis OPEN saat ada transaksi pertama.
                                                <?php elseif($locks['is_past'] ?? false): ?>
                                                    Periode lampau tidak bisa di-Open. Reopen hanya tersedia bila period
                                                    sebelumnya sudah ada dan ditutup.
                                                <?php else: ?>
                                                    Belum ada data untuk periode ini.
                                                <?php endif; ?>
                                            <?php else: ?>
                                                Tidak ada data expense untuk periode ini.
                                            <?php endif; ?>
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
                    <span id="pollDot" class="inline-block w-2 h-2 rounded-full mr-2"></span>
                    <span id="pollLabel">Polling off</span>
                </span>
                <span>Last update: <span id="lastUpdatedAt">—</span></span>
            </div>

            
            <?php if (isset($component)) { $__componentOriginal720c5d99204acad589a79c73de989541 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal720c5d99204acad589a79c73de989541 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page-selector','data' => ['route' => 'expenses.index','perPage' => $perPage ?? 10,'search' => $search ?? '','items' => $rows]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('expenses.index'),'perPage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perPage ?? 10),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search ?? ''),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rows)]); ?>
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
                    document.addEventListener('DOMContentLoaded', () => {
                        const box = document.getElementById('expenseList');
                        if (!box) return;

                        const tbody = document.getElementById('expenses_tbody');
                        const changesUrl = box.dataset.changesUrl;
                        const rowsUrl = box.dataset.rowsUrl;
                        const month = box.dataset.month;
                        const year = box.dataset.year;
                        const search = box.dataset.search || '';
                        let latestTs = box.dataset.latestTs || '';

                        const pollDot = document.getElementById('pollDot');
                        const pollLbl = document.getElementById('pollLabel');
                        const lastLbl = document.getElementById('lastUpdatedAt');

                        function setFooter(mode, ts = '') {
                            if (pollDot && pollLbl) {
                                if (mode === 'active') {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-green-500';
                                    pollLbl.textContent = 'Polling active';
                                } else if (mode === 'error') {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-rose-500';
                                    pollLbl.textContent = 'Polling error, retrying…';
                                } else if (mode === 'paused') {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-gray-400';
                                    pollLbl.textContent = 'Polling paused';
                                } else {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-gray-400';
                                    pollLbl.textContent = 'Polling off';
                                }
                            }
                            if (lastLbl) {
                                if (!ts) lastLbl.textContent = '—';
                                else {
                                    try {
                                        lastLbl.textContent = new Date(ts).toLocaleString('id-ID');
                                    } catch {
                                        lastLbl.textContent = '—';
                                    }
                                }
                            }
                        }

                        // period belum ada (lampau) → polling off
                        if (!changesUrl || !rowsUrl || !latestTs) {
                            setFooter('off', '');
                            return;
                        }
                        setFooter('active', latestTs);

                        function visibleIds() {
                            return Array.from(tbody.querySelectorAll('tr[data-id]'))
                                .map(tr => tr.getAttribute('data-id'))
                                .filter(Boolean);
                        }

                        function renumber() {
                            let i = 0;
                            tbody.querySelectorAll('tr[data-id]').forEach(tr => {
                                const cell = tr.querySelector('td:first-child');
                                if (cell) cell.textContent = (++i).toString();
                            });
                        }

                        async function tick() {
                            try {
                                if (document.hidden) {
                                    setFooter('paused', latestTs);
                                    return;
                                }

                                const params = new URLSearchParams({
                                    since: latestTs,
                                    month,
                                    year,
                                    search
                                });
                                visibleIds().forEach(id => params.append('visible[]', id));

                                const res = await fetch(`${changesUrl}?${params.toString()}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (!res.ok) {
                                    setFooter('error', latestTs);
                                    return;
                                }

                                const data = await res.json();

                                if (data.latest_ts) {
                                    latestTs = data.latest_ts;
                                    setFooter('active', latestTs);
                                }

                                const need = [...new Set([...(data.created || []), ...(data.updated || [])])];
                                const del = data.deleted || [];

                                del.forEach(id => {
                                    const tr = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (tr) tr.remove();
                                });

                                if (need.length === 0) {
                                    return;
                                }

                                const p = new URLSearchParams({
                                    month,
                                    year
                                });
                                need.forEach(id => p.append('ids[]', id));

                                const htmlRes = await fetch(`${rowsUrl}?${p.toString()}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (!htmlRes.ok) return;

                                const html = await htmlRes.text();

                                const temp = document.createElement('tbody');
                                temp.innerHTML = html.trim();

                                Array.from(temp.children).forEach(newTr => {
                                    const id = newTr.getAttribute('data-id');
                                    const old = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (old) old.replaceWith(newTr);
                                    else tbody.prepend(newTr);
                                });

                                renumber();
                            } catch (_) {
                                setFooter('error', latestTs);
                            }
                        }

                        setInterval(tick, 6000);
                        document.addEventListener('visibilitychange', () => {
                            if (!document.hidden) setFooter('active', latestTs);
                        });
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\expenses\index.blade.php ENDPATH**/ ?>