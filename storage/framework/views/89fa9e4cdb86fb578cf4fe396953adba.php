<form method="POST" action="<?php echo e($action); ?>" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php if($method !== 'POST'): ?> <?php echo method_field($method); ?> <?php endif; ?>

    <section class="ip-card ip-card-body">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nama pilihan <span class="text-red-500">*</span>
                <input name="label" required maxlength="100" value="<?php echo e(old('label', $bankDetail->label)); ?>" class="ip-input mt-2" placeholder="Contoh: Sugito">
                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('label'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('label')),'class' => 'mt-2']); ?>
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
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Email
                <input type="email" name="email" value="<?php echo e(old('email', $bankDetail->email)); ?>" class="ip-input mt-2" placeholder="nama@email.com">
                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('email'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('email')),'class' => 'mt-2']); ?>
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
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nama bank
                <input name="bank_name" value="<?php echo e(old('bank_name', $bankDetail->bank_name)); ?>" class="ip-input mt-2" placeholder="Contoh: BCA">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Atas nama rekening
                <input name="account_name" value="<?php echo e(old('account_name', $bankDetail->account_name)); ?>" class="ip-input mt-2" placeholder="Nama pemilik rekening">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nomor rekening
                <input name="account_number" inputmode="numeric" value="<?php echo e(old('account_number', $bankDetail->account_number)); ?>" class="ip-input mt-2" placeholder="Nomor rekening">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nomor HP
                <input name="phone" value="<?php echo e(old('phone', $bankDetail->phone)); ?>" class="ip-input mt-2" placeholder="08...">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">NPWP <span class="font-normal text-slate-400">(boleh kosong)</span>
                <input name="npwp" value="<?php echo e(old('npwp', $bankDetail->npwp)); ?>" class="ip-input mt-2" placeholder="Kosongkan bila tidak digunakan">
            </label>
            <label class="flex items-center gap-3 self-end rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm font-bold text-slate-700 dark:border-white/10 dark:bg-white/[.04] dark:text-slate-200">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" <?php if(old('active', $bankDetail->active ?? true)): echo 'checked'; endif; ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                Aktif dan dapat dipilih di dokumen
            </label>
        </div>
        <label class="mt-5 block text-sm font-bold text-slate-700 dark:text-slate-200">Catatan internal
            <textarea name="notes" rows="3" class="ip-input mt-2" placeholder="Opsional"><?php echo e(old('notes', $bankDetail->notes)); ?></textarea>
        </label>
    </section>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="<?php echo e(route('bank-details.index')); ?>" class="ip-btn-secondary">Batal</a>
        <button class="ip-btn-primary px-6"><?php echo e($submitLabel); ?></button>
    </div>
</form>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/bank-details/_form.blade.php ENDPATH**/ ?>