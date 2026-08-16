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
<?php endif; ?><?php if (isset($component)) { $__componentOriginalb668d8452d8402a934cc2be0dce69da0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb668d8452d8402a934cc2be0dce69da0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.samsat-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('samsat-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb668d8452d8402a934cc2be0dce69da0)): ?>
<?php $attributes = $__attributesOriginalb668d8452d8402a934cc2be0dce69da0; ?>
<?php unset($__attributesOriginalb668d8452d8402a934cc2be0dce69da0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb668d8452d8402a934cc2be0dce69da0)): ?>
<?php $component = $__componentOriginalb668d8452d8402a934cc2be0dce69da0; ?>
<?php unset($__componentOriginalb668d8452d8402a934cc2be0dce69da0); ?>
<?php endif; ?>
<div class="ip-page">
    <header class="ip-page-header"><div><p class="ip-kicker">Operasional</p><h1 class="ip-title">Armada & Samsat</h1><p class="ip-subtitle">Pantau kendaraan dan masa berlaku dokumen tanpa alur administrasi yang rumit.</p></div><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('createarmada')): ?><a href="<?php echo e(route('armada.create')); ?>" class="ip-btn-primary">+ Tambah kendaraan</a><?php endif; ?></header>
    <div class="grid gap-4 sm:grid-cols-3">
        <a href="<?php echo e(route('armada.index',['document_status'=>'overdue'])); ?>" class="group rounded-2xl border border-red-200 bg-gradient-to-br from-red-50 to-white p-5 shadow-sm hover:-translate-y-0.5"><p class="text-[11px] font-extrabold uppercase tracking-wider text-red-600">Terlambat</p><p class="mt-3 text-3xl font-extrabold text-red-700"><?php echo e($stats['overdue']); ?></p><p class="mt-1 text-xs text-red-500">Perlu ditangani sekarang</p></a>
        <a href="<?php echo e(route('armada.index',['document_status'=>'due_soon'])); ?>" class="group rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm hover:-translate-y-0.5"><p class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">Jatuh tempo &le; 30 hari</p><p class="mt-3 text-3xl font-extrabold text-amber-700"><?php echo e($stats['due_soon']); ?></p><p class="mt-1 text-xs text-amber-600">Siapkan perpanjangan</p></a>
        <a href="<?php echo e(route('armada.index',['document_status'=>'safe'])); ?>" class="group rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm hover:-translate-y-0.5"><p class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">Dokumen aman</p><p class="mt-3 text-3xl font-extrabold text-emerald-700"><?php echo e($stats['safe']); ?></p><p class="mt-1 text-xs text-emerald-600">Belum perlu tindakan</p></a>
    </div>
    <form class="ip-card flex gap-3 p-4"><input name="search" value="<?php echo e($search); ?>" placeholder="Cari nama kendaraan, nomor polisi, atau merek" class="ip-input flex-1"><button class="ip-btn-dark">Cari</button></form>
    <div class="ip-card"><div class="ip-table-wrap"><table class="ip-table min-w-[940px]"><thead><tr><th>Kendaraan</th><th>Lokasi / PIC</th><th>Status kendaraan</th><th>Status Samsat</th><th>Berlaku sampai</th><th class="text-right">Aksi</th></tr></thead><tbody><?php $__empty_1 = true; $__currentLoopData = $armadas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $armada): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php echo $__env->make('armada._rows',['armadas'=>collect([$armada])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="6" class="py-14 text-center text-slate-500">Belum ada kendaraan.</td></tr><?php endif; ?></tbody></table></div><div class="border-t border-slate-200 p-4"><?php echo e($armadas->links()); ?></div></div>
</div>
<script>function openSamsatModal(id){const form=document.getElementById('samsatForm');form.action=<?php echo json_encode(url('/armada'), 15, 512) ?>+'/'+id+'/samsat';const modal=document.getElementById('samsatModal');modal.classList.remove('hidden');modal.classList.add('flex')}function closeSamsatModal(){const modal=document.getElementById('samsatModal');modal.classList.add('hidden');modal.classList.remove('flex')}</script>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\armada\index.blade.php ENDPATH**/ ?>