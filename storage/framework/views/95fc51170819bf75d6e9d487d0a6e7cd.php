<?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr data-id="<?php echo e($invoice->id); ?>">
    <td><a href="<?php echo e(route('invoices.show',$invoice)); ?>" class="font-extrabold text-sky-700 hover:text-sky-900 dark:text-white dark:hover:text-red-400"><?php echo e($invoice->invoice_number ?: 'DRAFT #'.$invoice->id); ?></a></td>
    <td><p class="font-bold text-slate-900 dark:text-white"><?php echo e($invoice->client?->name ?: 'Client manual'); ?></p><p class="mt-0.5 text-xs text-slate-500"><?php echo e($invoice->event_name ?: 'Tanpa nama acara'); ?></p></td>
    <td><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?></td>
    <td class="whitespace-nowrap text-right"><?php echo e((int) $invoice->grand_total > 0 ? 'Rp '.number_format($invoice->grand_total,0,',','.') : ''); ?></td>
    <td class="whitespace-nowrap text-right font-bold text-emerald-600"><?php echo e((int) $invoice->total_paid > 0 ? 'Rp '.number_format($invoice->total_paid,0,',','.') : ''); ?></td>
    <td class="whitespace-nowrap text-right font-extrabold text-slate-900 dark:text-white"><?php echo e((int) $invoice->balance_due > 0 ? 'Rp '.number_format($invoice->balance_due,0,',','.') : ''); ?></td>
    <td class="text-right"><a href="<?php echo e(route('invoices.show',$invoice)); ?>" class="inline-flex rounded-lg bg-sky-50 px-3 py-2 font-bold text-sky-700 hover:bg-sky-100 dark:bg-white/[.06] dark:text-red-400 dark:hover:bg-white/10">Lihat detail</a></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\invoices\_rows.blade.php ENDPATH**/ ?>