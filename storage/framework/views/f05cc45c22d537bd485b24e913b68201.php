<?php
    $priceGroupCounts = $quotation->items->whereNotNull('price_group')->countBy('price_group');
    $renderedPriceGroups = [];
    $rupiahOrBlank = fn ($value) => (int) $value > 0 ? 'Rp ' . number_format((int) $value, 0, ',', '.') : '';
?>
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

        <div class="ip-page">
            <header class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-sky-700 via-sky-800 to-slate-950 p-6 text-white shadow-xl dark:from-[#11151e] dark:via-[#0b0c0f] dark:to-black sm:p-8">
                <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-sky-300/20 blur-3xl dark:bg-red-600/25"></div>
                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <a href="<?php echo e(route('quotations.index')); ?>" class="mb-4 inline-flex items-center gap-2 text-xs font-bold text-sky-100 hover:text-white dark:text-slate-400 dark:hover:text-white"><span aria-hidden="true">&larr;</span> Kembali ke quotation</a>
                        <p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-sky-200 dark:text-red-400">Detail quotation</p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl"><?php echo e($quotation->quotation_number); ?></h1>
                        <p class="mt-2 text-sm text-sky-100/80 dark:text-slate-400"><?php echo e($quotation->client?->name ?: 'Client belum diisi'); ?> &middot; <?php echo e($quotation->event_name ?: 'Tanpa nama acara'); ?></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $quotation->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quotation->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                        <a href="<?php echo e(route('quotations.export.pdf', $quotation)); ?>" target="_blank" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">Lihat PDF</a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editquotation')): ?>
                            <?php if($quotation->status !== 'approved'): ?>
                                <a href="<?php echo e(route('quotations.edit', $quotation)); ?>" class="ip-btn bg-white text-slate-950 hover:bg-sky-50">Edit quotation</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <?php if($quotation->invoice): ?>
                <a href="<?php echo e(route('invoices.show', $quotation->invoice)); ?>" class="flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-950/20 dark:text-emerald-100">
                    <span><strong class="block">Invoice draft sudah dibuat</strong><span class="mt-1 block text-sm text-emerald-700 dark:text-emerald-300">Lanjutkan penyesuaian item atau harga di invoice.</span></span><span class="text-xl" aria-hidden="true">&rarr;</span>
                </a>
            <?php endif; ?>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),380px]">
                <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Rincian','title' => 'Rincian pekerjaan','description' => ''.e($quotation->items->count()).' item quotation','mobileOpen' => true,'contentClass' => 'p-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Rincian','title' => 'Rincian pekerjaan','description' => ''.e($quotation->items->count()).' item quotation','mobile-open' => true,'content-class' => 'p-0']); ?>
                    <div class="ip-table-wrap">
                        <table class="ip-table min-w-[680px]">
                            <thead><tr><th>Item</th><th>Qty</th><th>Panjang</th><th class="text-right">Harga / total</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $quotation->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="font-bold text-slate-900 dark:text-white"><?php echo e($item->item_name); ?></td>
                                        <td><?php echo e((float) $item->qty > 0 ? (float) $item->qty : ''); ?></td>
                                        <td><?php echo e((float) $item->length > 0 ? (float) $item->length : ''); ?></td>
                                        <?php if($item->price_group): ?>
                                            <?php if(! isset($renderedPriceGroups[$item->price_group])): ?>
                                                <?php ($renderedPriceGroups[$item->price_group] = true); ?>
                                                <td rowspan="<?php echo e($priceGroupCounts[$item->price_group]); ?>" class="border-l border-sky-100 text-right align-middle font-extrabold text-slate-900 dark:border-white/10 dark:text-white"><?php echo e($rupiahOrBlank($item->total)); ?></td>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <td class="text-right font-extrabold text-slate-900 dark:text-white"><?php echo e($rupiahOrBlank($item->total)); ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="4" class="py-12 text-center text-slate-500">Belum ada item quotation.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if($quotation->description): ?>
                        <div class="m-5 rounded-xl bg-sky-50 p-4 dark:bg-white/[.04]"><p class="text-xs font-extrabold uppercase tracking-wide text-slate-400">Catatan</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-300"><?php echo e($quotation->description); ?></p></div>
                    <?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0)): ?>
<?php $attributes = $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0; ?>
<?php unset($__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74affecb73a231f0dd28b1b2df1c10f0)): ?>
<?php $component = $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0; ?>
<?php unset($__componentOriginal74affecb73a231f0dd28b1b2df1c10f0); ?>
<?php endif; ?>

                <aside class="space-y-4">
                    <section class="rounded-2xl bg-sky-950 p-5 text-white shadow-xl dark:bg-[#0b0c0f]">
                        <p class="text-[11px] font-extrabold uppercase tracking-[.18em] text-sky-300 dark:text-red-400">Ringkasan</p>
                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex justify-between text-slate-400"><dt>Subtotal</dt><dd class="font-bold text-white"><?php echo e($rupiahOrBlank($quotation->subtotal)); ?></dd></div>
                            <?php if((int) $quotation->discount > 0): ?><div class="flex justify-between text-slate-400"><dt>Diskon</dt><dd class="font-bold text-sky-300 dark:text-red-300">- <?php echo e($rupiahOrBlank($quotation->discount)); ?></dd></div><?php endif; ?>
                            <?php if((int) $quotation->tax_value > 0): ?><div class="flex justify-between text-slate-400"><dt>Potongan pajak</dt><dd class="font-bold text-sky-300 dark:text-red-300">- <?php echo e($rupiahOrBlank($quotation->tax_value)); ?></dd></div><?php endif; ?>
                            <div class="flex justify-between border-t border-white/10 pt-4"><dt class="font-bold">Total</dt><dd class="text-xl font-extrabold"><?php echo e($rupiahOrBlank($quotation->grand_total)); ?></dd></div>
                        </dl>
                    </section>
                    <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Operasional','title' => 'Jadwal','description' => 'Lokasi, acara, loading, dan bongkar.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Operasional','title' => 'Jadwal','description' => 'Lokasi, acara, loading, dan bongkar.']); ?>
                        <dl class="space-y-3 text-sm">
                            <?php $__currentLoopData = ['Lokasi' => $quotation->location_event, 'Acara' => optional($quotation->event_date)->translatedFormat('d F Y'), 'Loading' => optional($quotation->loading_date)->translatedFormat('d F Y H:i'), 'Bongkar' => optional($quotation->bongkaran_date)->translatedFormat('d F Y H:i')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400"><?php echo e($label); ?></dt><dd class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300"><?php echo e($value ?: '-'); ?></dd></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </dl>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0)): ?>
<?php $attributes = $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0; ?>
<?php unset($__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74affecb73a231f0dd28b1b2df1c10f0)): ?>
<?php $component = $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0; ?>
<?php unset($__componentOriginal74affecb73a231f0dd28b1b2df1c10f0); ?>
<?php endif; ?>
                    <?php echo $__env->make('documents._bank-detail-card', ['bankDetail' => $quotation->bankDetail], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </aside>
            </div>

            <?php if(! $quotation->invoice && ! in_array($quotation->status, ['approved', 'cancelled'])): ?>
                <div class="flex flex-wrap justify-end gap-3">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editquotation')): ?>
                        <form method="POST" action="<?php echo e(route('quotations.cancel', $quotation)); ?>" onsubmit="return confirmAndLoad('Batalkan quotation ini?')"><?php echo csrf_field(); ?><button class="ip-btn-danger">Batalkan</button></form>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approvequotation')): ?>
                        <form method="POST" action="<?php echo e(route('quotations.acc', $quotation)); ?>" onsubmit="return confirmAndLoad('Setujui quotation dan buat invoice draft otomatis?')"><?php echo csrf_field(); ?><button class="ip-btn min-h-10 bg-emerald-600 text-white hover:bg-emerald-700">Setujui & buat invoice draft</button></form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\quotations\show.blade.php ENDPATH**/ ?>