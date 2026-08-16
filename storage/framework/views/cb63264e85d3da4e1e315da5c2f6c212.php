<?php if($bankDetail): ?>
    <?php if (isset($component)) { $__componentOriginal74affecb73a231f0dd28b1b2df1c10f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74affecb73a231f0dd28b1b2df1c10f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-disclosure','data' => ['kicker' => 'Detail rekening','title' => ''.e($bankDetail->label).'','description' => 'Buka untuk melihat rekening tujuan pembayaran.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-disclosure'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['kicker' => 'Detail rekening','title' => ''.e($bankDetail->label).'','description' => 'Buka untuk melihat rekening tujuan pembayaran.']); ?>
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <?php $__currentLoopData = ['Email' => $bankDetail->email, 'Bank' => $bankDetail->bank_name, 'Atas nama' => $bankDetail->account_name, 'No. rekening' => $bankDetail->account_number, 'NPWP' => $bankDetail->npwp, 'No. HP' => $bankDetail->phone]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(filled($value)): ?>
                    <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400"><?php echo e($label); ?></dt><dd class="mt-1 break-words font-bold text-slate-800 dark:text-slate-200"><?php echo e($value); ?></dd></div>
                <?php endif; ?>
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
<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/documents/_bank-detail-card.blade.php ENDPATH**/ ?>