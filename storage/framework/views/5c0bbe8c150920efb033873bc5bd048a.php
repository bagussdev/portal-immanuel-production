<?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if (isset($component)) { $__componentOriginala98942d6121e3029513e1b771eecbd7e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala98942d6121e3029513e1b771eecbd7e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notifications.item','data' => ['n' => $n]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notifications.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['n' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($n)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala98942d6121e3029513e1b771eecbd7e)): ?>
<?php $attributes = $__attributesOriginala98942d6121e3029513e1b771eecbd7e; ?>
<?php unset($__attributesOriginala98942d6121e3029513e1b771eecbd7e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala98942d6121e3029513e1b771eecbd7e)): ?>
<?php $component = $__componentOriginala98942d6121e3029513e1b771eecbd7e; ?>
<?php unset($__componentOriginala98942d6121e3029513e1b771eecbd7e); ?>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\notifications\_rows.blade.php ENDPATH**/ ?>