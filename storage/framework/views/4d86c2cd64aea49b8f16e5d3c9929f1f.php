<?php
    $priceGroupCounts = $invoice->items->whereNotNull('price_group')->countBy('price_group');
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
                        <a href="<?php echo e(route('invoices.index')); ?>" class="mb-4 inline-flex items-center gap-2 text-xs font-bold text-sky-100 hover:text-white dark:text-slate-400 dark:hover:text-white">
                            <span aria-hidden="true">&larr;</span> Kembali ke invoice
                        </a>
                        <p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-sky-200 dark:text-red-400">Detail invoice</p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl"><?php echo e($invoice->invoice_number ?: 'DRAFT #'.$invoice->id); ?></h1>
                        <p class="mt-2 text-sm text-sky-100/80 dark:text-slate-400">
                            <?php echo e($invoice->client?->name ?: 'Client belum diisi'); ?> &middot; <?php echo e($invoice->event_name ?: 'Tanpa nama acara'); ?>

                        </p>
                        <span class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-bold text-white">
                            Tipe event: <?php echo e($invoice->workFlowLabel()); ?>

                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $invoice->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->status)]); ?>
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

                        <?php if($invoice->invoice_number): ?>
                            <a target="_blank" href="<?php echo e(route('invoices.export.pdf', $invoice)); ?>" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">
                                Lihat PDF
                            </a>
                            <?php if($invoice->fieldJob): ?>
                                <a href="<?php echo e(route('field-jobs.show', $invoice->fieldJob)); ?>" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">
                                    Pekerjaan <?php echo e($invoice->fieldJob->job_number); ?>

                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editinvoice')): ?>
                            <?php if($invoice->status !== 'void'): ?>
                                <a href="<?php echo e(route('invoices.edit', $invoice)); ?>" class="ip-btn bg-white text-slate-950 hover:bg-sky-50">
                                    Edit invoice
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                <div class="ip-card p-4 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total tagihan</p>
                    <p class="mt-3 min-h-7 text-base font-extrabold text-slate-950 dark:text-white sm:text-xl"><?php echo e($rupiahOrBlank($invoice->grand_total)); ?></p>
                </div>
                <div class="ip-card p-4 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Sudah dibayar</p>
                    <p class="mt-3 min-h-7 text-base font-extrabold text-emerald-600 dark:text-emerald-400 sm:text-xl"><?php echo e($rupiahOrBlank($invoice->total_paid)); ?></p>
                </div>
                <div class="ip-card border-rose-100 bg-rose-50/70 p-4 dark:border-red-500/20 dark:bg-red-950/20 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500 dark:text-red-400">Sisa tagihan</p>
                    <p class="mt-3 min-h-7 text-base font-extrabold text-rose-700 dark:text-red-300 sm:text-xl"><?php echo e($rupiahOrBlank($invoice->balance_due)); ?></p>
                </div>
                <div class="ip-card p-4 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Jatuh tempo</p>
                    <p class="mt-3 text-sm font-extrabold text-slate-900 dark:text-white sm:text-lg"><?php echo e(optional($invoice->due_date)->translatedFormat('d F Y') ?: '-'); ?></p>
                </div>
            </div>

            <?php if($invoice->status === 'draft'): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('issueinvoice')): ?>
                    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-500/30 dark:bg-amber-950/20">
                        <div class="flex gap-3">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-400 font-extrabold text-amber-950">!</span>
                            <div class="min-w-0 flex-1">
                                <h2 class="font-extrabold text-amber-950 dark:text-amber-100">Invoice masih draft</h2>
                                <p class="mt-1 text-sm leading-6 text-amber-700 dark:text-amber-300">Periksa item dan harga. Nomor resmi baru dibuat saat invoice diterbitkan.</p>
                                <form method="POST" action="<?php echo e(route('invoices.issue', $invoice)); ?>" class="mt-4 flex flex-wrap items-end gap-3">
                                    <?php echo csrf_field(); ?>
                                    <label class="text-xs font-bold text-amber-950 dark:text-amber-100">Tanggal terbit
                                        <input type="date" name="issue_date" value="<?php echo e(today()->format('Y-m-d')); ?>" required class="ip-input mt-1">
                                    </label>
                                    <label class="text-xs font-bold text-amber-950 dark:text-amber-100">Jatuh tempo
                                        <input type="date" name="due_date" value="<?php echo e(optional($invoice->event_date)->format('Y-m-d')); ?>" class="ip-input mt-1">
                                    </label>
                                    <button class="ip-btn-dark">Terbitkan invoice</button>
                                </form>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),380px]">
                <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Rincian','title' => 'Item invoice','description' => ''.e($invoice->items->count()).' item pekerjaan','mobileOpen' => true,'contentClass' => 'p-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Rincian','title' => 'Item invoice','description' => ''.e($invoice->items->count()).' item pekerjaan','mobile-open' => true,'content-class' => 'p-0']); ?>
                    <div class="ip-table-wrap">
                        <table class="ip-table min-w-[680px]">
                            <thead><tr><th>Item</th><th>Qty</th><th>Panjang</th><th class="text-right">Harga / total</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                    <tr><td colspan="4" class="py-12 text-center text-slate-500">Belum ada item invoice.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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

                <aside class="rounded-2xl bg-sky-950 p-5 text-white shadow-xl dark:bg-[#0b0c0f]">
                    <p class="text-[11px] font-extrabold uppercase tracking-[.18em] text-sky-300 dark:text-red-400">Perhitungan</p>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between text-slate-400"><dt>Subtotal</dt><dd class="font-bold text-white"><?php echo e($rupiahOrBlank($invoice->subtotal)); ?></dd></div>
                        <?php if((int) $invoice->discount_value > 0): ?><div class="flex justify-between text-slate-400"><dt>Diskon <?php if($invoice->discount_percent !== null): ?> (<?php echo e($invoice->discount_percent); ?>%) <?php endif; ?></dt><dd class="font-bold text-sky-300 dark:text-red-300">- <?php echo e($rupiahOrBlank($invoice->discount_value)); ?></dd></div><?php endif; ?>
                        <?php if((int) $invoice->tax_value > 0): ?><div class="flex justify-between text-slate-400"><dt>Potongan pajak <?php if($invoice->tax_percent !== null): ?> (<?php echo e($invoice->tax_percent); ?>%) <?php endif; ?></dt><dd class="font-bold text-sky-300 dark:text-red-300">- <?php echo e($rupiahOrBlank($invoice->tax_value)); ?></dd></div><?php endif; ?>
                        <div class="flex justify-between border-t border-white/10 pt-4"><dt class="font-bold">Total</dt><dd class="text-xl font-extrabold"><?php echo e($rupiahOrBlank($invoice->grand_total)); ?></dd></div>
                    </dl>
                    <div class="mt-6 space-y-2 border-t border-white/10 pt-5 text-xs text-slate-400">
                        <p><strong class="text-slate-200">Loading:</strong> <?php echo e(optional($invoice->loading_date)->translatedFormat('d M Y H:i') ?: '-'); ?></p>
                        <p><strong class="text-slate-200">Acara:</strong> <?php echo e(optional($invoice->event_date)->translatedFormat('d M Y') ?: '-'); ?></p>
                        <p><strong class="text-slate-200">Bongkar:</strong> <?php echo e(optional($invoice->bongkaran_date)->translatedFormat('d M Y H:i') ?: '-'); ?></p>
                    </div>
                </aside>
            </div>

            <?php echo $__env->make('documents._bank-detail-card', ['bankDetail' => $invoice->bankDetail], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Pembayaran bertahap','title' => 'Riwayat transfer','description' => ''.e($invoice->payments->whereNull('voided_at')->count()).' pembayaran aktif','contentClass' => 'p-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Pembayaran bertahap','title' => 'Riwayat transfer','description' => ''.e($invoice->payments->whereNull('voided_at')->count()).' pembayaran aktif','content-class' => 'p-0']); ?>
                <div class="space-y-3 p-4 md:hidden">
                    <?php $__empty_1 = true; $__currentLoopData = $invoice->payments->sortBy('paid_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="rounded-2xl border border-sky-100 p-4 <?php echo e($payment->voided_at ? 'opacity-55' : 'bg-sky-50/45'); ?> dark:border-white/10 dark:bg-white/[.025]">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white"><?php echo e($payment->method); ?></p>
                                    <p class="mt-1 text-xs text-slate-500"><?php echo e($payment->paid_at->translatedFormat('d M Y')); ?> &middot; <?php echo e($payment->reference ?: 'Tanpa referensi'); ?></p>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold <?php echo e($payment->voided_at ? 'line-through text-slate-400' : 'text-emerald-600 dark:text-emerald-400'); ?>">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></p>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-sky-100 pt-3 text-xs dark:border-white/10">
                                <span class="text-slate-500">Penerima: <strong class="text-slate-700 dark:text-slate-300"><?php echo e($payment->receiver?->name ?: '-'); ?></strong></span>
                                <span class="flex items-center gap-3">
                                    <?php if($payment->attachment): ?>
                                        <a class="font-bold text-sky-700 dark:text-sky-300" href="<?php echo e(route('invoices.payments.attachment', [$invoice, $payment])); ?>">Bukti</a>
                                    <?php endif; ?>
                                    <?php if(! $payment->voided_at): ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('voidpayment')): ?>
                                            <form method="POST" action="<?php echo e(route('invoices.payments.void', [$invoice, $payment])); ?>" onsubmit="const r=prompt('Alasan pembatalan pembayaran:');if(!r)return false;this.querySelector('[name=reason]').value=r">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="reason">
                                                <button class="font-bold text-red-600 dark:text-red-400">Batalkan</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => 'void','size' => 'small']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => 'void','size' => 'small']); ?>
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
                                    <?php endif; ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="py-8 text-center text-sm text-slate-500">Belum ada pembayaran.</p>
                    <?php endif; ?>
                </div>
                <div class="ip-table-wrap hidden md:block">
                    <table class="ip-table min-w-[800px]">
                        <thead><tr><th>Tanggal</th><th>Metode</th><th>Referensi</th><th class="text-right">Nominal</th><th>Penerima</th><th class="text-right">Aksi</th></tr></thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $invoice->payments->sortBy('paid_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="<?php echo e($payment->voided_at ? 'opacity-50' : ''); ?>">
                                    <td><?php echo e($payment->paid_at->translatedFormat('d M Y')); ?></td>
                                    <td class="font-bold"><?php echo e($payment->method); ?></td>
                                    <td><?php echo e($payment->reference ?: '-'); ?></td>
                                    <td class="text-right font-extrabold <?php echo e($payment->voided_at ? 'line-through' : 'text-emerald-600 dark:text-emerald-400'); ?>">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                                    <td><?php echo e($payment->receiver?->name ?: '-'); ?></td>
                                    <td class="text-right">
                                        <?php if($payment->attachment): ?>
                                            <a class="mr-2 font-bold text-sky-700 hover:text-sky-900 dark:text-slate-300 dark:hover:text-white" href="<?php echo e(route('invoices.payments.attachment', [$invoice, $payment])); ?>">Bukti</a>
                                        <?php endif; ?>
                                        <?php if(! $payment->voided_at): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('voidpayment')): ?>
                                                <form method="POST" action="<?php echo e(route('invoices.payments.void', [$invoice, $payment])); ?>" class="inline" onsubmit="const r=prompt('Alasan pembatalan pembayaran:');if(!r)return false;this.querySelector('[name=reason]').value=r">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <input type="hidden" name="reason">
                                                    <button class="font-bold text-red-600 dark:text-red-400">Batalkan</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => 'void','size' => 'small']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => 'void','size' => 'small']); ?>
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
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="py-12 text-center text-slate-500">Belum ada pembayaran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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

            <?php if(! in_array($invoice->status, ['draft', 'void'])): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('adddp')): ?>
                    <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Pembayaran','title' => 'Catat pembayaran berikutnya','description' => 'DP dapat dicatat beberapa kali dan langsung mengurangi sisa tagihan.','class' => 'border-emerald-200 bg-emerald-50/70 dark:border-emerald-500/30 dark:bg-emerald-950/20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Pembayaran','title' => 'Catat pembayaran berikutnya','description' => 'DP dapat dicatat beberapa kali dan langsung mengurangi sisa tagihan.','class' => 'border-emerald-200 bg-emerald-50/70 dark:border-emerald-500/30 dark:bg-emerald-950/20']); ?>
                        <form method="POST" enctype="multipart/form-data" action="<?php echo e(route('invoices.payments.store', $invoice)); ?>" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                            <?php echo csrf_field(); ?>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Tanggal<input type="date" name="paid_at" value="<?php echo e(today()->format('Y-m-d')); ?>" required class="ip-input mt-1"></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Nominal<input name="amount" required inputmode="numeric" class="ip-input mt-1" placeholder="Rp 0"></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Metode<select name="method" class="ip-input mt-1"><option>Transfer</option><option>Tunai</option><option>Giro</option><option>Lainnya</option></select></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Referensi<input name="reference" class="ip-input mt-1"></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Bukti<input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="mt-2 block w-full text-xs"></label>
                            <button class="ip-btn min-h-11 self-end bg-emerald-600 text-white hover:bg-emerald-700">Tambah pembayaran</button>
                        </form>
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
                <?php endif; ?>
            <?php endif; ?>

            <?php if($invoice->status !== 'void'): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('voidinvoice')): ?>
                    <form method="POST" action="<?php echo e(route('invoices.void', $invoice)); ?>" class="flex justify-end" onsubmit="const r=prompt('Alasan VOID invoice:');if(!r)return false;this.querySelector('[name=reason]').value=r">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="reason">
                        <button class="ip-btn-danger">Void invoice</button>
                    </form>
                <?php endif; ?>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\invoices\show.blade.php ENDPATH**/ ?>