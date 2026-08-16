<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php if (isset($component)) { $__componentOriginal060abe2a9b4511e378911474e77b046d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal060abe2a9b4511e378911474e77b046d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php if (isset($component)) { $__componentOriginala2b0fa968b944f36eb1fd78215b6c473 = $component; } ?>
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
    <header class="ip-page-header"><div><p class="ip-kicker">Penjualan</p><h1 class="ip-title">Quotation</h1><p class="ip-subtitle">Penawaran yang disetujui otomatis menjadi invoice draft dan tetap dapat disesuaikan.</p></div><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('createquotation')): ?><a href="<?php echo e(route('quotations.create')); ?>" class="ip-btn-primary">+ Buat quotation</a><?php endif; ?></header>
    <form class="ip-card flex flex-col gap-3 p-4 sm:flex-row"><input name="search" value="<?php echo e($search); ?>" placeholder="Cari nomor, client, atau acara" class="ip-input flex-1"><select name="status" class="ip-input sm:w-52"><option value="">Semua status</option><?php $__currentLoopData = ['draft'=>'Draft','sent'=>'Dikirim','approved'=>'Disetujui','cancelled'=>'Dibatalkan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($value); ?>" <?php if($status===$value): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><button class="ip-btn-dark">Terapkan filter</button></form>
    <div class="space-y-3 md:hidden">
        <?php $__empty_1 = true; $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['title' => ''.e($quotation->quotation_number).'','description' => ''.e($quotation->client?->name ?: 'Client manual').' · '.e($quotation->event_name ?: 'Tanpa nama acara').'','contentClass' => 'p-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($quotation->quotation_number).'','description' => ''.e($quotation->client?->name ?: 'Client manual').' · '.e($quotation->event_name ?: 'Tanpa nama acara').'','content-class' => 'p-4']); ?>
                 <?php $__env->slot('meta', null, []); ?> <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?> <?php $__env->endSlot(); ?>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-sky-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Tanggal acara</dt><dd class="mt-1 font-bold text-slate-800 dark:text-slate-200"><?php echo e(optional($quotation->event_date)->format('d/m/Y') ?: '-'); ?></dd></div>
                    <div class="rounded-xl bg-sky-50 p-3 text-right dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Total</dt><dd class="mt-1 font-extrabold text-slate-900 dark:text-white">Rp <?php echo e(number_format($quotation->grand_total,0,',','.')); ?></dd></div>
                </dl>
                <a href="<?php echo e(route('quotations.show',$quotation)); ?>" class="ip-btn-primary mt-4 w-full">Lihat detail quotation</a>
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="ip-card p-10 text-center text-sm text-slate-500">Belum ada quotation. Mulai dari penawaran pertama.</div>
        <?php endif; ?>
    </div>
    <div class="ip-card">
        <div class="ip-table-wrap hidden md:block"><table class="ip-table min-w-[860px]"><thead><tr><th>Nomor</th><th>Client / Acara</th><th>Tanggal</th><th>Status</th><th class="text-right">Total</th><th class="text-right">Aksi</th></tr></thead><tbody><?php $__empty_1 = true; $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php echo $__env->make('quotations._rows',['quotations'=>collect([$quotation])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="6" class="py-14 text-center text-slate-500">Belum ada quotation. Mulai dari penawaran pertama.</td></tr><?php endif; ?></tbody></table></div>
        <div class="border-t border-slate-200 p-4 dark:border-white/10"><?php echo e($quotations->links()); ?></div>
    </div>
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
<?php endif; ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\quotations\index.blade.php ENDPATH**/ ?>