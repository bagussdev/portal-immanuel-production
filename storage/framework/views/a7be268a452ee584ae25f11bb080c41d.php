<?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr data-id="<?php echo e($quotation->id); ?>">
    <td><a href="<?php echo e(route('quotations.show',$quotation)); ?>" class="font-extrabold text-sky-700 hover:text-sky-900 dark:text-white dark:hover:text-red-400"><?php echo e($quotation->quotation_number); ?></a></td>
    <td><p class="font-bold text-slate-900 dark:text-white"><?php echo e($quotation->client?->name ?: 'Client manual'); ?></p><p class="mt-0.5 text-xs text-slate-500"><?php echo e($quotation->event_name ?: 'Tanpa nama acara'); ?></p></td>
    <td class="whitespace-nowrap"><?php echo e(optional($quotation->event_date)->format('d/m/Y') ?: '-'); ?></td>
    <td><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?></td>
    <td class="whitespace-nowrap text-right font-extrabold text-slate-900 dark:text-white">Rp <?php echo e(number_format($quotation->grand_total,0,',','.')); ?></td>
    <td class="text-right"><a href="<?php echo e(route('quotations.show',$quotation)); ?>" class="inline-flex rounded-lg bg-sky-50 px-3 py-2 font-bold text-sky-700 hover:bg-sky-100 dark:bg-white/[.06] dark:text-red-400 dark:hover:bg-white/10">Lihat detail</a></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\quotations\_rows.blade.php ENDPATH**/ ?>