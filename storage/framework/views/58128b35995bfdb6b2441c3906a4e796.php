<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['n']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['n']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<tr data-id="<?php echo e($n->id); ?>"
    class="border-t dark:border-gray-700 <?php echo e($n->read_at ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800'); ?>">
    <td class="px-4 py-2">#<?php echo e($n->id); ?></td>
    <td class="px-4 py-2"><?php echo e($n->type); ?></td>
    <td class="px-4 py-2 max-w-[260px] truncate"><?php echo e($n->title ?? data_get($n->data, 'title')); ?></td>
    <td class="px-4 py-2 max-w-[380px] truncate"><?php echo e($n->message ?? data_get($n->data, 'message')); ?></td>
    <td class="px-4 py-2"><?php echo e($n->created_at->format('Y-m-d H:i')); ?></td>
    <td class="px-4 py-2">
        <?php if(is_null($n->read_at)): ?>
            <span class="inline-flex items-center gap-1 text-xs text-blue-700 dark:text-blue-300">
                <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span> Unread
            </span>
        <?php else: ?>
            <span class="text-xs text-gray-500">Read</span>
        <?php endif; ?>
    </td>
    <td class="px-4 py-2">
        <?php if(is_null($n->read_at)): ?>
            <form method="POST" action="<?php echo e(route('notifications.read', $n)); ?>"
                onsubmit="return confirmAndLoad('Mark as read?')">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['text' => 'Mark read','color' => 'green','type' => 'submit','dense' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'Mark read','color' => 'green','type' => 'submit','dense' => 'true']); ?>
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
        <?php else: ?>
            <span class="text-xs text-gray-400">—</span>
        <?php endif; ?>
    </td>
</tr>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\components\notifications\index-row.blade.php ENDPATH**/ ?>