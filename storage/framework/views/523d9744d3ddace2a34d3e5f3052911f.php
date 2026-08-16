<?php
    use App\Models\FieldJobStage;

    $canManage = auth()->user()->canManageAllFieldJobs();
    $hasTeardown = $fieldJob->stages->contains('type', FieldJobStage::TYPE_TEARDOWN);
    $formatQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
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
        <div class="mx-auto max-w-6xl space-y-6">
            <header class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-sky-700 via-sky-800 to-slate-950 p-6 text-white shadow-xl dark:from-[#11151e] dark:via-[#0b0c0f] dark:to-black sm:p-8">
                <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-sky-300/20 blur-3xl dark:bg-red-600/25"></div>
                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div class="min-w-0">
                        <a href="<?php echo e(route('field-jobs.index')); ?>" class="mb-4 inline-flex items-center gap-2 text-xs font-bold text-sky-100 hover:text-white">&larr; Kembali ke pekerjaan</a>
                        <p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-sky-200 dark:text-red-400"><?php echo e($fieldJob->job_number); ?></p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl"><?php echo e($fieldJob->event_name ?: $fieldJob->client_name); ?></h1>
                        <p class="mt-2 text-sm text-sky-100/80"><?php echo e($fieldJob->client_name); ?><?php echo e($fieldJob->location ? ' · '.$fieldJob->location : ''); ?></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $fieldJob->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldJob->status)]); ?>
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
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('invoicemenu')): ?>
                            <?php if($fieldJob->relationLoaded('invoice') && $fieldJob->invoice): ?>
                                <a href="<?php echo e(route('invoices.show', $fieldJob->invoice)); ?>" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">Buka invoice</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Informasi event','title' => 'Jadwal & lokasi','description' => 'Ringkasan waktu pelaksanaan dan catatan pekerjaan.','mobileOpen' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Informasi event','title' => 'Jadwal & lokasi','description' => 'Ringkasan waktu pelaksanaan dan catatan pekerjaan.','mobile-open' => true]); ?>
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Hari acara</p><p class="mt-1 font-bold text-slate-900 dark:text-white"><?php echo e(optional($fieldJob->event_date)->translatedFormat('d F Y') ?: '-'); ?></p></div>
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Loading</p><p class="mt-1 font-bold text-slate-900 dark:text-white"><?php echo e(optional($fieldJob->loading_date)->translatedFormat('d M Y, H:i') ?: '-'); ?></p></div>
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Bongkar</p><p class="mt-1 font-bold text-slate-900 dark:text-white"><?php echo e(optional($fieldJob->teardown_date)->translatedFormat('d M Y, H:i') ?: 'Tidak diperlukan'); ?></p></div>
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Lokasi</p><p class="mt-1 font-bold text-slate-900 dark:text-white"><?php echo e($fieldJob->location ?: '-'); ?></p></div>
                </div>
                <?php if($fieldJob->notes): ?>
                    <div class="mt-5 rounded-xl bg-sky-50 p-4 dark:bg-white/[.04]"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Catatan pekerjaan</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-300"><?php echo e($fieldJob->notes); ?></p></div>
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

            <?php $__currentLoopData = $fieldJob->stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $stageItems = $fieldJob->items;
                    $isAssigned = $stage->assignees->contains('id', auth()->id());
                    $canAct = $canManage || $isAssigned;
                    $teardownStage = $stage->type === FieldJobStage::TYPE_INSTALL ? $fieldJob->stages->firstWhere('type', FieldJobStage::TYPE_TEARDOWN) : null;
                    $copyTeamToTeardown = $teardownStage && ($teardownStage->assignees->isEmpty() || $teardownStage->assignees->pluck('id')->sort()->values()->all() === $stage->assignees->pluck('id')->sort()->values()->all());
                ?>

                <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['id' => 'stage-'.e($stage->id).'','kicker' => 'Tahap pekerjaan','title' => ''.e($stage->label()).'','description' => ''.e(optional($stage->scheduled_at)->translatedFormat('l, d F Y · H:i') ?: 'Jadwal belum diatur').'','mobileOpen' => $stage->status === FieldJobStage::STATUS_IN_PROGRESS,'contentClass' => 'p-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stage-'.e($stage->id).'','kicker' => 'Tahap pekerjaan','title' => ''.e($stage->label()).'','description' => ''.e(optional($stage->scheduled_at)->translatedFormat('l, d F Y · H:i') ?: 'Jadwal belum diatur').'','mobile-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stage->status === FieldJobStage::STATUS_IN_PROGRESS),'content-class' => 'p-0']); ?>
                     <?php $__env->slot('meta', null, []); ?> <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $stage->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stage->status)]); ?>
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
                    <div class="grid gap-6 p-5 lg:grid-cols-[minmax(0,1fr),360px]">
                        <div class="space-y-6">
                            <div>
                                <h3 class="ip-section-title">Detail yang dikerjakan</h3>
                                <div class="mt-3 divide-y divide-sky-100 rounded-xl border border-sky-100 dark:divide-white/10 dark:border-white/10">
                                    <?php $__currentLoopData = $stageItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                                            <p class="font-bold text-slate-900 dark:text-white"><?php echo e($item->item_name); ?></p>
                                            <p class="shrink-0 text-sm font-semibold text-slate-500"><?php echo e($formatQty($item->qty)); ?><?php echo e($item->length ? ' × '.$formatQty($item->length).' m' : ''); ?></p>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-3"><h3 class="ip-section-title">Foto hasil</h3><span class="text-xs font-bold text-slate-400"><?php echo e($stage->photos->count()); ?> foto</span></div>
                                <?php if($stage->photos->isNotEmpty()): ?>
                                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                        <?php $__currentLoopData = $stage->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <figure class="group relative overflow-hidden rounded-xl border border-sky-100 bg-slate-100 dark:border-white/10 dark:bg-white/[.04]">
                                                <a href="<?php echo e(route('field-jobs.stages.photos.show', [$fieldJob, $stage, $photo])); ?>" target="_blank">
                                                    <img src="<?php echo e(route('field-jobs.stages.photos.show', [$fieldJob, $stage, $photo])); ?>" alt="Foto <?php echo e($stage->label()); ?>" loading="lazy" class="aspect-square w-full object-cover transition duration-300 group-hover:scale-105">
                                                </a>
                                                <figcaption class="p-2.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                    <p class="truncate font-bold text-slate-700 dark:text-slate-200"><?php echo e($photo->uploader?->name ?: 'Pengguna'); ?></p>
                                                    <p><?php echo e($photo->created_at->translatedFormat('d M Y, H:i')); ?></p>
                                                    <?php if($photo->caption): ?><p class="mt-1 line-clamp-2"><?php echo e($photo->caption); ?></p><?php endif; ?>
                                                    <?php if($canManage || ((int) $photo->uploaded_by === (int) auth()->id() && $isAssigned)): ?>
                                                        <form method="POST" action="<?php echo e(route('field-jobs.stages.photos.destroy', [$fieldJob, $stage, $photo])); ?>" class="mt-2" onsubmit="return confirmAndLoad('Hapus foto ini?')">
                                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                            <button class="font-extrabold text-red-600 hover:text-red-800">Hapus</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </figcaption>
                                            </figure>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-3 rounded-xl border border-dashed border-sky-200 p-7 text-center text-sm font-semibold text-slate-400 dark:border-white/10">Belum ada foto hasil.</div>
                                <?php endif; ?>
                            </div>

                            <?php if($canAct && $stage->status !== FieldJobStage::STATUS_COMPLETED): ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('uploadfieldjobphotos')): ?>
                                    <form method="POST" enctype="multipart/form-data" action="<?php echo e(route('field-jobs.stages.photos.store', [$fieldJob, $stage])); ?>" class="rounded-xl border border-sky-100 bg-sky-50/60 p-4 dark:border-white/10 dark:bg-white/[.025]">
                                        <?php echo csrf_field(); ?>
                                        <label class="ip-label">Ambil atau pilih foto (maksimal 8)</label>
                                        <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" capture="environment" multiple required class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-sky-600 file:px-4 file:py-2.5 file:font-bold file:text-white hover:file:bg-sky-700 dark:text-slate-300 dark:file:bg-red-600">
                                        <label class="mt-3 block"><span class="ip-label">Catatan foto (opsional)</span><input name="caption" maxlength="255" class="ip-input" placeholder="Contoh: Hasil panggung sisi depan"></label>
                                        <p class="mt-2 text-xs leading-5 text-slate-400">Foto dikompres otomatis dan hanya dapat dilihat anggota yang berhak.</p>
                                        <button class="ip-btn-primary mt-3">Unggah foto</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <aside class="space-y-5">
                            <div class="rounded-xl border border-sky-100 p-4 dark:border-white/10">
                                <div class="flex items-center justify-between gap-3"><h3 class="ip-section-title">Tim bertugas</h3><span class="text-xs font-bold text-slate-400"><?php echo e($stage->assignees->count()); ?> orang</span></div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <?php $__empty_1 = true; $__currentLoopData = $stage->assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <span class="rounded-full bg-sky-100 px-3 py-1.5 text-xs font-bold text-sky-800 dark:bg-white/[.07] dark:text-slate-200"><?php echo e($member->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <p class="text-sm font-semibold text-amber-600">Belum ada anggota yang ditugaskan.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if($canManage): ?>
                                <form method="POST" action="<?php echo e(route('field-jobs.stages.assignments', [$fieldJob, $stage])); ?>" class="rounded-xl border border-sky-100 p-4 dark:border-white/10">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <h3 class="ip-section-title">Atur anggota</h3>
                                    <div class="mt-3 max-h-56 space-y-2 overflow-y-auto pr-1">
                                        <?php $__currentLoopData = $teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex cursor-pointer items-center gap-3 rounded-lg p-2 hover:bg-sky-50 dark:hover:bg-white/[.04]">
                                                <input type="checkbox" name="assignee_ids[]" value="<?php echo e($member->id); ?>" <?php if($stage->assignees->contains('id', $member->id)): echo 'checked'; endif; ?> class="rounded border-sky-200 text-sky-600 focus:ring-sky-500">
                                                <span class="min-w-0"><strong class="block truncate text-sm text-slate-800 dark:text-slate-200"><?php echo e($member->name); ?></strong><small class="uppercase text-slate-400"><?php echo e($member->role?->name); ?></small></span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php if($stage->type === FieldJobStage::TYPE_INSTALL && $hasTeardown): ?>
                                        <label class="mt-3 flex items-start gap-2 rounded-lg bg-sky-50 p-3 text-xs font-semibold text-slate-600 dark:bg-white/[.04] dark:text-slate-300">
                                            <input type="hidden" name="copy_to_teardown" value="0"><input type="checkbox" name="copy_to_teardown" value="1" <?php if($copyTeamToTeardown): echo 'checked'; endif; ?> class="mt-0.5 rounded border-sky-200 text-sky-600">
                                            <span>Gunakan tim yang sama untuk tahap Bongkar</span>
                                        </label>
                                    <?php endif; ?>
                                    <button class="ip-btn-secondary mt-3 w-full">Simpan penugasan</button>
                                </form>
                            <?php endif; ?>

                            <?php if($canAct): ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('updatefieldjobstatus')): ?>
                                    <div class="rounded-xl bg-sky-950 p-4 text-white dark:bg-black">
                                        <h3 class="font-extrabold">Perbarui progres</h3>
                                        <p class="mt-1 text-xs leading-5 text-slate-400">Minimal satu foto diperlukan sebelum pekerjaan dapat diselesaikan.</p>
                                        <?php if($stage->status === FieldJobStage::STATUS_PENDING): ?>
                                            <form method="POST" action="<?php echo e(route('field-jobs.stages.update', [$fieldJob, $stage])); ?>" class="mt-3"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><input type="hidden" name="status" value="in_progress"><button class="ip-btn w-full bg-white text-slate-950 hover:bg-slate-100">Mulai pekerjaan</button></form>
                                        <?php elseif($stage->status === FieldJobStage::STATUS_IN_PROGRESS): ?>
                                            <form method="POST" action="<?php echo e(route('field-jobs.stages.update', [$fieldJob, $stage])); ?>" class="mt-3"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><input type="hidden" name="status" value="completed"><button class="ip-btn w-full bg-emerald-500 text-white hover:bg-emerald-600">Tandai selesai</button></form>
                                        <?php elseif($canManage): ?>
                                            <form method="POST" action="<?php echo e(route('field-jobs.stages.update', [$fieldJob, $stage])); ?>" class="mt-3"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><input type="hidden" name="status" value="pending"><button class="ip-btn w-full bg-white/10 text-white hover:bg-white/20">Buka kembali</button></form>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </aside>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\field-jobs\show.blade.php ENDPATH**/ ?>