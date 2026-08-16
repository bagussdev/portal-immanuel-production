<?php
    // Koleksi bisa datang sebagai $expenses (polling) atau $rows (render awal)
    $collection = isset($rows) ? $rows : $expenses ?? collect();
    $canAct = $allowActions ?? ($locks['can_add'] ?? false); // fallback aman
?>

<?php $__currentLoopData = $collection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr data-id="<?php echo e($row->id); ?>">
        <td class="px-4 py-3">
            <?php echo e(($baseOffset ?? 0) + $loop->iteration); ?>

        </td>
        <td class="px-4 py-3 text-left font-medium">
            <?php echo e($row->expense_number); ?>

        </td>
        <td class="px-4 py-3">
            <?php echo e(optional($row->expense_date)->format('d/m/Y')); ?>

        </td>
        <td class="px-4 py-3 text-left">
            <?php echo e($row->name); ?>

        </td>
        <td class="px-4 py-3">
            <?php echo e(number_format((int) $row->qty, 0, ',', '.')); ?>

        </td>
        <td class="px-4 py-3 font-semibold">
            <?php echo e('Rp ' . number_format((int) $row->total, 0, ',', '.')); ?>

        </td>
        <td class="px-4 py-3 text-left">
            <?php echo e($row->creator->name ?? '-'); ?>

        </td>
        <td class="px-4 py-3">
            <div class="flex justify-center items-center gap-1">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editexpenses')): ?>
                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['href' => route('expenses.edit', $row),'onclick' => 'showFullScreenLoader();','class' => ''.e($canAct ? '' : 'pointer-events-none opacity-50').'','color' => 'green','text' => 'Edit','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('expenses.edit', $row)),'onclick' => 'showFullScreenLoader();','class' => ''.e($canAct ? '' : 'pointer-events-none opacity-50').'','color' => 'green','text' => 'Edit','dense' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('deleteexpenses')): ?>
                    <form action="<?php echo e(route('expenses.destroy', $row)); ?>" method="POST"
                        onsubmit="return confirmAndLoad('Hapus expense ini?');"
                        class="<?php echo e($canAct ? '' : 'pointer-events-none opacity-50'); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['color' => 'red','text' => 'Hapus','dense' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'red','text' => 'Hapus','dense' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\expenses\_rows.blade.php ENDPATH**/ ?>