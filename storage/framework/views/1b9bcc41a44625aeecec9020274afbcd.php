<?php
    $isInvoice = $kind === 'invoice';
    $loadedItems = $document->relationLoaded('items') ? $document->items : collect();
    $initialItems = old('items', $loadedItems->map(function ($item, $index) use ($loadedItems) {
        $previous = $index > 0 ? $loadedItems->get($index - 1) : null;
        return [
            'item_name' => $item->item_name,
            'qty' => (float) $item->qty,
            'length' => $item->length !== null ? (float) $item->length : '',
            'unit_price' => (int) $item->unit_price,
            'merge_price' => (bool) ($item->price_group && $previous?->price_group === $item->price_group),
        ];
    })->values()->all());
    if (!$initialItems) $initialItems = [['item_name' => '', 'qty' => 1, 'length' => '', 'unit_price' => '', 'merge_price' => false]];
    $discountAmount = $isInvoice ? ($document->discount_value ?? 0) : ($document->discount ?? 0);
    $discountMode = old('discount_mode', $document->discount_percent !== null ? 'percent' : 'amount');
    $taxMode = old('tax_mode', $document->tax_percent !== null ? 'percent' : 'amount');
    $workFlow = $isInvoice ? old('work_flow', $document->work_flow ?: 'install_teardown') : null;
?>

<form method="POST" action="<?php echo e($action); ?>" id="documentForm" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php if($method !== 'POST'): ?> <?php echo method_field($method); ?> <?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Informasi utama','title' => 'Client & acara','description' => 'Nama client baru akan tersimpan otomatis.','mobileOpen' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Informasi utama','title' => 'Client & acara','description' => 'Nama client baru akan tersimpan otomatis.','mobile-open' => true]); ?>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="block text-sm font-medium text-slate-700">Nama client
                <input name="client_name" list="clientSuggestions" required value="<?php echo e(old('client_name', $document->client?->name)); ?>" class="ip-input mt-1" placeholder="Ketik nama client">
                <datalist id="clientSuggestions"><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($client->name); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></datalist>
                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('client_name'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('client_name')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
            </label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nama acara
                <input name="event_name" value="<?php echo e(old('event_name', $document->event_name)); ?>" class="ip-input mt-1" placeholder="Contoh: Wedding Andi & Sinta">
            </label>
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

    <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Operasional','title' => ''.e($isInvoice ? 'Jadwal & tipe event' : 'Jadwal pekerjaan').'','description' => 'Buka saat perlu mengatur lokasi, tanggal, dan alur tim lapangan.','mobileOpen' => $errors->hasAny(['location_event', 'event_date', 'loading_date', 'bongkaran_date', 'work_flow'])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Operasional','title' => ''.e($isInvoice ? 'Jadwal & tipe event' : 'Jadwal pekerjaan').'','description' => 'Buka saat perlu mengatur lokasi, tanggal, dan alur tim lapangan.','mobile-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->hasAny(['location_event', 'event_date', 'loading_date', 'bongkaran_date', 'work_flow']))]); ?>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Lokasi
                <input name="location_event" value="<?php echo e(old('location_event', $document->location_event)); ?>" class="ip-input mt-1" placeholder="Lokasi pekerjaan">
            </label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Tanggal acara
                <input type="date" name="event_date" value="<?php echo e(old('event_date', optional($document->event_date)->format('Y-m-d'))); ?>" class="ip-input mt-1">
            </label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Jadwal loading
                <input type="datetime-local" name="loading_date" value="<?php echo e(old('loading_date', optional($document->loading_date)->format('Y-m-d\TH:i'))); ?>" class="ip-input mt-1">
            </label>
            <label id="bongkaranField" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Jadwal bongkar
                <input type="datetime-local" name="bongkaran_date" value="<?php echo e(old('bongkaran_date', optional($document->bongkaran_date)->format('Y-m-d\TH:i'))); ?>" class="ip-input mt-1">
            </label>
            <?php if($isInvoice): ?>
                <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-sky-100 bg-sky-50/60 p-4 dark:border-white/10 dark:bg-white/[.035]">
                    <div class="mb-3">
                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">Tipe pekerjaan event</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Satu pilihan berlaku untuk seluruh invoice dan menentukan tahap yang diterima tim lapangan.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <?php $__currentLoopData = [
                            'install_teardown' => ['Pasang & Bongkar', 'Tahap pasang/loading dan tahap bongkar.'],
                            'install_only' => ['Pasang saja', 'Satu tahap pasang tanpa bongkar.'],
                            'one_way' => ['Sekali jalan', 'Satu tahap untuk transport atau pengiriman.'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => [$label, $description]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="work_flow" value="<?php echo e($value); ?>" class="peer sr-only" <?php if($workFlow === $value): echo 'checked'; endif; ?>>
                                <span class="block h-full rounded-xl border border-slate-200 bg-white p-3 transition peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:ring-2 peer-checked:ring-sky-500/15 dark:border-white/10 dark:bg-white/[.04] dark:peer-checked:border-sky-400 dark:peer-checked:bg-sky-500/10">
                                    <span class="block text-sm font-extrabold text-slate-900 dark:text-white"><?php echo e($label); ?></span>
                                    <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400"><?php echo e($description); ?></span>
                                </span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('work_flow'),'class' => 'mt-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('work_flow')),'class' => 'mt-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                </div>
            <?php endif; ?>
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

    <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Pembayaran','title' => 'Detail rekening','description' => 'Opsional. Informasi rekening terpilih akan muncul di detail dan PDF.','mobileOpen' => $errors->has('bank_detail_id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Pembayaran','title' => 'Detail rekening','description' => 'Opsional. Informasi rekening terpilih akan muncul di detail dan PDF.','mobile-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('bank_detail_id'))]); ?>
        <label class="block max-w-2xl text-sm font-medium text-slate-700 dark:text-slate-200">Rekening tujuan
            <select name="bank_detail_id" class="ip-input mt-1">
                <option value="">Tanpa detail rekening</option>
                <?php $__currentLoopData = $bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bankDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($bankDetail->id); ?>" <?php if((string) old('bank_detail_id', $document->bank_detail_id) === (string) $bankDetail->id): echo 'selected'; endif; ?>>
                        <?php echo e($bankDetail->label); ?><?php echo e($bankDetail->bank_name ? ' - ' . $bankDetail->bank_name : ''); ?><?php echo e($bankDetail->account_number ? ' (' . $bankDetail->account_number . ')' : ' - belum lengkap'); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('bank_detail_id'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('bank_detail_id')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
        </label>
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

    <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Rincian','title' => 'Item pekerjaan','description' => 'Geser tabel ke kanan atau kiri untuk mengisi seluruh kolom.','mobileOpen' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Rincian','title' => 'Item pekerjaan','description' => 'Geser tabel ke kanan atau kiri untuk mengisi seluruh kolom.','mobile-open' => true]); ?>
        <div class="mb-4 flex justify-end">
            <button type="button" id="addItem" class="ip-btn border border-sky-200 bg-sky-50 text-sky-700 shadow-sm hover:bg-sky-100 dark:border-white/10 dark:bg-white/[.06] dark:text-white dark:hover:bg-white/10">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Tambah item
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[960px] w-full text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="pb-2 w-20">Qty</th><th class="pb-2">Nama item</th><th class="pb-2 w-28">Panjang</th><th class="pb-2 w-60">Harga</th><th class="pb-2 w-44 text-right">Total baris</th><th class="w-12"></th></tr>
                </thead>
                <tbody id="itemRows" class="divide-y divide-slate-100 dark:divide-slate-700"></tbody>
            </table>
        </div>
        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('items'),'class' => 'mt-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('items')),'class' => 'mt-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
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

    <section class="grid gap-6 lg:grid-cols-[1fr,420px]">
        <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Keterangan','title' => 'Catatan tambahan','description' => 'Buka hanya bila ada informasi untuk client atau tim lapangan.','mobileOpen' => $errors->hasAny(['notes', 'description', 'operational_notes', 'change_reason'])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Keterangan','title' => 'Catatan tambahan','description' => 'Buka hanya bila ada informasi untuk client atau tim lapangan.','mobile-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->hasAny(['notes', 'description', 'operational_notes', 'change_reason']))]); ?>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Catatan
                <textarea name="<?php echo e($isInvoice ? 'notes' : 'description'); ?>" rows="5" class="ip-input mt-1" placeholder="Catatan untuk client atau tim internal"><?php echo e(old($isInvoice ? 'notes' : 'description', $isInvoice ? $document->notes : $document->description)); ?></textarea>
            </label>
            <?php if($isInvoice): ?>
                <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Catatan khusus tim lapangan
                    <textarea name="operational_notes" rows="4" class="ip-input mt-1" placeholder="Instruksi teknis tanpa harga, pembayaran, atau informasi keuangan"><?php echo e(old('operational_notes', $document->operational_notes)); ?></textarea>
                    <span class="mt-1 block text-xs text-slate-400">Hanya catatan ini yang disalin ke halaman Mandor dan User.</span>
                </label>
            <?php endif; ?>
            <?php if($isInvoice && $document->exists && $document->status !== 'draft'): ?>
                <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Alasan perubahan
                    <input name="change_reason" value="<?php echo e(old('change_reason')); ?>" class="ip-input mt-1" placeholder="Opsional, tetapi disarankan untuk audit">
                </label>
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

        <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Perhitungan','title' => 'Ringkasan nilai','description' => 'Diskon, pajak, dan total invoice.','mobileOpen' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Perhitungan','title' => 'Ringkasan nilai','description' => 'Diskon, pajak, dan total invoice.','mobile-open' => true]); ?>
            <div class="space-y-4">
                <div class="flex min-h-6 justify-between gap-4 text-sm text-slate-500 dark:text-slate-400"><span>Subtotal</span><strong id="summarySubtotal" class="text-slate-950 dark:text-white"></strong></div>
                <div class="grid grid-cols-[minmax(0,1fr)_72px_minmax(0,1.15fr)] items-end gap-2">
                    <label class="min-w-0 text-xs font-semibold text-slate-500 dark:text-slate-400">Diskon
                        <select name="discount_mode" id="discountMode" class="ip-input mt-1 min-w-0 !py-2 text-xs"><option value="percent" <?php if($discountMode === 'percent'): echo 'selected'; endif; ?>>Persen</option><option value="amount" <?php if($discountMode === 'amount'): echo 'selected'; endif; ?>>Nominal</option></select>
                    </label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percent" id="discountPercent" value="<?php echo e(old('discount_percent', $document->discount_percent)); ?>" class="ip-input min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]" placeholder="%">
                    <input name="discount_value" id="discountValue" value="<?php echo e(old('discount_value', $discountAmount ?: '')); ?>" class="ip-input money min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]" placeholder="Rp">
                </div>
                <div class="grid grid-cols-[minmax(0,1fr)_72px_minmax(0,1.15fr)] items-end gap-2">
                    <label class="min-w-0 text-xs font-semibold text-slate-500 dark:text-slate-400">Potongan pajak
                        <select name="tax_mode" id="taxMode" class="ip-input mt-1 min-w-0 !py-2 text-xs"><option value="percent" <?php if($taxMode === 'percent'): echo 'selected'; endif; ?>>Persen</option><option value="amount" <?php if($taxMode === 'amount'): echo 'selected'; endif; ?>>Nominal</option></select>
                    </label>
                    <input type="number" step="0.01" min="0" max="100" name="tax_percent" id="taxPercent" value="<?php echo e(old('tax_percent', $document->tax_percent)); ?>" class="ip-input min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]" placeholder="%">
                    <input name="tax_value" id="taxValue" value="<?php echo e(old('tax_value', $document->tax_value ?: '')); ?>" class="ip-input money min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]" placeholder="Rp">
                </div>
                <div class="flex min-h-12 items-end justify-between gap-4 border-t border-sky-100 pt-4 dark:border-white/10"><span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Total tagihan</span><strong id="summaryGrand" class="text-2xl font-extrabold text-sky-700 dark:text-red-400"></strong></div>
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
    </section>

    <div class="sticky bottom-3 z-20 flex flex-wrap justify-end gap-3 rounded-2xl border border-sky-100 bg-white/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur md:static md:border-0 md:bg-transparent md:p-0 md:shadow-none dark:border-white/10 dark:bg-[#11151e]/95 md:dark:bg-transparent">
        <a href="<?php echo e($cancelUrl); ?>" class="ip-btn-secondary">Batal</a>
        <button type="submit" class="ip-btn-primary px-6"><?php echo e($submitLabel); ?></button>
    </div>
</form>

<template id="itemTemplate">
    <tr class="item-row">
        <td class="py-3 pr-2"><input type="number" min="0.01" step="0.01" data-name="qty" class="qty ip-input" required></td>
        <td class="py-3 pr-2"><input data-name="item_name" class="ip-input" required></td>
        <td class="py-3 pr-2"><input type="number" min="0" step="0.01" data-name="length" class="length ip-input" placeholder="Opsional"></td>
        <td class="py-3 pr-2">
            <input data-name="unit_price" class="unit-price money ip-input text-right" required placeholder="Rp">
            <label class="mt-2 flex items-start gap-2 text-[11px] font-semibold leading-4 text-slate-500 dark:text-slate-400">
                <input type="checkbox" value="1" data-name="merge_price" class="merge-price mt-0.5 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                <span>Gabung harga dengan item di atas</span>
            </label>
        </td>
        <td class="py-3 pr-2 text-right font-extrabold row-total"></td>
        <td class="py-3"><button type="button" class="remove-row flex h-9 w-9 items-center justify-center rounded-xl border border-red-100 bg-red-50 text-lg font-bold text-red-600 hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300" aria-label="Hapus item">&times;</button></td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const initial = <?php echo json_encode($initialItems, 15, 512) ?>;
    const rows = document.getElementById('itemRows');
    const template = document.getElementById('itemTemplate');
    const money = value => new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(Number(value) || 0)));
    const raw = value => Number(String(value ?? '').replace(/[^0-9]/g, '')) || 0;
    let index = 0;

    function addRow(data = {}) {
        const fragment = template.content.cloneNode(true);
        const row = fragment.querySelector('.item-row');
        row.querySelectorAll('[data-name]').forEach(input => {
            input.name = `items[${index}][${input.dataset.name}]`;
            if (input.type === 'checkbox') input.checked = Boolean(Number(data[input.dataset.name] ?? 0) || data[input.dataset.name] === true);
            else input.value = data[input.dataset.name] ?? '';
        });
        const price = row.querySelector('.unit-price');
        price.value = raw(data.unit_price) > 0 ? money(raw(data.unit_price)) : '';
        row.querySelector('.remove-row').addEventListener('click', () => {
            if (rows.children.length > 1) row.remove();
            calculate();
        });
        row.querySelectorAll('input').forEach(input => input.addEventListener('input', calculate));
        row.querySelector('.merge-price').addEventListener('change', calculate);
        price.addEventListener('input', () => { price.value = money(raw(price.value)); calculate(); });
        rows.appendChild(row);
        index++;
        calculate();
    }

    function calculate() {
        let subtotal = 0;
        const allRows = [...rows.querySelectorAll('.item-row')];
        allRows.forEach((row, rowIndex) => {
            const merge = row.querySelector('.merge-price');
            if (rowIndex === 0) {
                merge.checked = false;
                merge.disabled = true;
            } else {
                merge.disabled = false;
            }
            const isMerged = rowIndex > 0 && merge.checked;
            const priceInput = row.querySelector('.unit-price');
            priceInput.readOnly = isMerged;
            priceInput.classList.toggle('bg-sky-50', isMerged);
            if (isMerged) priceInput.value = '0';

            const qty = Number(row.querySelector('.qty').value) || 0;
            const length = Number(row.querySelector('.length').value) || 1;
            const price = raw(priceInput.value);
            const hasMergedFollower = rowIndex + 1 < allRows.length && allRows[rowIndex + 1].querySelector('.merge-price').checked;
            const total = isMerged ? 0 : (hasMergedFollower ? price : qty * length * price);
            subtotal += total;
            row.querySelector('.row-total').textContent = total > 0 ? `Rp ${money(total)}` : (isMerged ? 'Harga digabung' : '');
        });
        const dMode = document.getElementById('discountMode').value;
        const tMode = document.getElementById('taxMode').value;
        const discount = dMode === 'percent' ? subtotal * ((Number(document.getElementById('discountPercent').value) || 0) / 100) : raw(document.getElementById('discountValue').value);
        const afterDiscount = Math.max(subtotal - discount, 0);
        const tax = tMode === 'percent' ? afterDiscount * ((Number(document.getElementById('taxPercent').value) || 0) / 100) : raw(document.getElementById('taxValue').value);
        document.getElementById('summarySubtotal').textContent = subtotal > 0 ? `Rp ${money(subtotal)}` : '';
        const grand = Math.max(afterDiscount - tax, 0);
        document.getElementById('summaryGrand').textContent = grand > 0 ? `Rp ${money(grand)}` : '';
        document.getElementById('discountPercent').disabled = dMode !== 'percent';
        document.getElementById('discountValue').disabled = dMode !== 'amount';
        document.getElementById('taxPercent').disabled = tMode !== 'percent';
        document.getElementById('taxValue').disabled = tMode !== 'amount';
    }

    initial.forEach(addRow);
    document.getElementById('addItem').addEventListener('click', () => addRow({qty: 1}));
    ['discountMode','taxMode','discountPercent','discountValue','taxPercent','taxValue'].forEach(id => document.getElementById(id).addEventListener('input', calculate));
    document.querySelectorAll('.money').forEach(input => input.addEventListener('input', () => { input.value = money(raw(input.value)); calculate(); }));

    const workFlowInputs = [...document.querySelectorAll('input[name="work_flow"]')];
    const teardownField = document.getElementById('bongkaranField');
    function syncWorkFlowFields() {
        if (!teardownField || workFlowInputs.length === 0) return;
        const selected = workFlowInputs.find(input => input.checked)?.value;
        const teardownInput = teardownField.querySelector('input');
        const needsTeardown = selected === 'install_teardown';
        teardownField.classList.toggle('hidden', !needsTeardown);
        teardownInput.disabled = !needsTeardown;
    }
    workFlowInputs.forEach(input => input.addEventListener('change', syncWorkFlowFields));
    syncWorkFlowFields();
    calculate();
});
</script>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/documents/_form.blade.php ENDPATH**/ ?>