<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'submit', 'color' => 'gray', 'text' => 'Action', 'href' => null, 'dense' => false]));

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

foreach (array_filter((['type' => 'submit', 'color' => 'gray', 'text' => 'Action', 'href' => null, 'dense' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $sizeClass = $dense
        ? 'min-h-8 px-2 py-1 text-[11px]'
        : 'min-h-9 px-2.5 py-1.5 text-xs sm:px-3';
    $baseClass = "inline-flex $sizeClass items-center justify-center whitespace-nowrap rounded-lg font-extrabold transition focus:outline-none focus:ring-2 focus:ring-offset-2";
    $colorClass = match ($color) {
        'green' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500',
        'red' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'yellow' => 'bg-amber-400 text-amber-950 hover:bg-amber-500 focus:ring-amber-400',
        'blue', 'purple' => 'bg-slate-950 text-white hover:bg-slate-800 focus:ring-slate-700',
        default => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',
    };
?>
<?php if($href): ?><a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => "$baseClass $colorClass"])); ?>><?php echo e($text); ?></a><?php else: ?><button type="<?php echo e($type); ?>" <?php echo e($attributes->merge(['class' => "$baseClass $colorClass"])); ?>><?php echo e($text); ?></button><?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/components/action-button.blade.php ENDPATH**/ ?>