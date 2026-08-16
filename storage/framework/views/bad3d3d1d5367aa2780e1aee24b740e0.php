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
            <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="ip-kicker">Data operasional</p><h1 class="ip-title">Detail rekening</h1><p class="ip-subtitle">Pilih profil ini saat membuat quotation atau invoice. Kolom yang kosong tidak akan dicetak.</p></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('createbankdetail')): ?><a href="<?php echo e(route('bank-details.create')); ?>" class="ip-btn-primary">+ Tambah rekening</a><?php endif; ?>
            </header>

            <form method="GET" class="ip-card flex flex-col gap-3 p-4 sm:flex-row">
                <input name="search" value="<?php echo e($search); ?>" class="ip-input flex-1" placeholder="Cari nama, bank, atau nomor rekening">
                <button class="ip-btn-dark">Cari</button>
                <?php if($search): ?><a href="<?php echo e(route('bank-details.index')); ?>" class="ip-btn-secondary">Reset</a><?php endif; ?>
            </form>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <?php $__empty_1 = true; $__currentLoopData = $bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bankDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="ip-card ip-card-body flex flex-col">
                        <div class="flex items-start justify-between gap-3">
                            <div><p class="ip-kicker"><?php echo e($bankDetail->bank_name ?: 'Rekening belum lengkap'); ?></p><h2 class="mt-1 text-xl font-extrabold text-slate-950 dark:text-white"><?php echo e($bankDetail->label); ?></h2></div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide <?php echo e($bankDetail->active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400'); ?>"><?php echo e($bankDetail->active ? 'Aktif' : 'Nonaktif'); ?></span>
                        </div>
                        <dl class="mt-5 flex-1 space-y-3 text-sm">
                            <?php $__currentLoopData = ['Atas nama' => $bankDetail->account_name, 'No. rekening' => $bankDetail->account_number, 'Email' => $bankDetail->email, 'No. HP' => $bankDetail->phone, 'NPWP' => $bankDetail->npwp]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400"><?php echo e($label); ?></dt><dd class="mt-0.5 break-words font-semibold text-slate-700 dark:text-slate-300"><?php echo e($value ?: 'Belum diisi'); ?></dd></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </dl>
                        <div class="mt-5 flex gap-2 border-t border-sky-100 pt-4 dark:border-white/10">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editbankdetail')): ?><a href="<?php echo e(route('bank-details.edit', $bankDetail)); ?>" class="ip-btn-secondary flex-1">Edit</a><?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('deletebankdetail')): ?>
                                <form method="POST" action="<?php echo e(route('bank-details.destroy', $bankDetail)); ?>" onsubmit="return confirmAndLoad('Hapus detail rekening ini?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="ip-btn-danger">Hapus</button></form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="ip-card ip-card-body text-center text-slate-500 md:col-span-2 xl:col-span-3">Belum ada detail rekening.</div>
                <?php endif; ?>
            </div>
            <?php echo e($bankDetails->links()); ?>

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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/bank-details/index.blade.php ENDPATH**/ ?>