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
<div class="mx-auto max-w-6xl space-y-6">
    <header class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-sky-700 via-sky-800 to-slate-950 p-6 text-white shadow-xl dark:from-[#11151e] dark:via-[#0b0c0f] dark:to-black sm:p-8"><div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-sky-300/20 blur-3xl dark:bg-red-600/25"></div><div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between"><div><p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-sky-200 dark:text-red-400"><?php echo e($armada->type); ?> &middot; <?php echo e($armada->year); ?></p><h1 class="mt-2 text-2xl font-extrabold sm:text-3xl"><?php echo e($armada->name); ?></h1><p class="mt-2 text-sm text-sky-100/75 dark:text-slate-400"><?php echo e($armada->nomor_polisi); ?> &middot; <?php echo e($armada->brand); ?> <?php echo e($armada->model); ?></p></div><div class="flex flex-wrap gap-2"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $armada->document_status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($armada->document_status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('samsatarmada')): ?><button onclick="openSamsatModal(<?php echo e($armada->id); ?>)" class="ip-btn bg-sky-500 text-white hover:bg-sky-400 dark:bg-red-600 dark:hover:bg-red-700">Catat perpanjangan</button><?php endif; ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editarmada')): ?><a href="<?php echo e(route('armada.edit',$armada)); ?>" class="ip-btn bg-white text-slate-950">Edit kendaraan</a><?php endif; ?></div></div></header>

    <div class="grid gap-6 lg:grid-cols-[1fr,390px]">
        <section class="ip-card ip-card-body"><div class="flex items-center justify-between"><div><p class="ip-kicker">Kendaraan</p><h2 class="mt-1 ip-section-title">Data operasional</h2></div><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $armada->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($armada->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></div><dl class="mt-6 grid gap-5 sm:grid-cols-2"><?php $__currentLoopData = ['Lokasi'=>$armada->location?->name,'Penanggung jawab'=>$armada->user?->name,'Nomor rangka'=>$armada->nomor_rangka,'Nomor mesin'=>$armada->nomor_mesin]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400"><?php echo e($label); ?></dt><dd class="mt-1 font-bold text-slate-800"><?php echo e($value ?: '-'); ?></dd></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></dl></section>
        <section class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 to-white p-5 shadow-soft dark:border-white/10 dark:from-[#11151e] dark:to-[#0b0c0f]"><p class="ip-kicker">Samsat</p><div class="mt-3 flex items-center justify-between gap-3"><h2 class="text-lg font-extrabold text-slate-950 dark:text-white">Masa berlaku STNK</h2><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $armada->document_status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($armada->document_status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></div><p class="mt-6 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white"><?php echo e(optional($armada->stnk_expired)->translatedFormat('d M Y') ?: '-'); ?></p><p class="mt-2 text-xs font-semibold text-slate-500">Gunakan “Catat perpanjangan” setelah proses Samsat selesai.</p><?php if($armada->stnk_attachment): ?><a href="<?php echo e(route('armada.stnk.attachment',$armada)); ?>" class="mt-5 inline-flex text-sm font-extrabold text-sky-700 dark:text-red-400">Unduh dokumen terbaru &rarr;</a><?php endif; ?></section>
    </div>

    <section class="ip-card"><div class="border-b border-slate-100 px-5 py-4"><p class="ip-kicker">Riwayat</p><h2 class="mt-1 ip-section-title">Perpanjangan STNK</h2></div><div class="ip-table-wrap"><table class="ip-table min-w-[760px]"><thead><tr><th>Diproses</th><th>Berlaku lama</th><th>Berlaku baru</th><th class="text-right">Biaya</th><th>Dicatat oleh</th><th class="text-right">Bukti</th></tr></thead><tbody><?php $__empty_1 = true; $__currentLoopData = $armada->renewals->sortByDesc('processed_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $renewal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><?php echo e($renewal->processed_at->format('d/m/Y')); ?></td><td><?php echo e(optional($renewal->previous_expired_at)->format('d/m/Y') ?: '-'); ?></td><td class="font-extrabold text-slate-900"><?php echo e($renewal->new_expired_at->format('d/m/Y')); ?></td><td class="text-right font-bold">Rp <?php echo e(number_format($renewal->cost,0,',','.')); ?></td><td><?php echo e($renewal->creator?->name); ?></td><td class="text-right"><?php if($renewal->attachment): ?><a href="<?php echo e(route('armada.renewals.attachment',[$armada,$renewal])); ?>" class="font-bold text-red-600">Unduh</a><?php else: ?> - <?php endif; ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="6" class="py-12 text-center text-slate-500">Belum ada riwayat perpanjangan.</td></tr><?php endif; ?></tbody></table></div></section>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\armada\show.blade.php ENDPATH**/ ?>