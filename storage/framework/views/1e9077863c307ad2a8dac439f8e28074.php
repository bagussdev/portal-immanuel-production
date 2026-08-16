<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'kicker' => null,
    'description' => null,
    'mobileOpen' => false,
    'contentClass' => 'p-4 sm:p-6',
]));

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

foreach (array_filter(([
    'title',
    'kicker' => null,
    'description' => null,
    'mobileOpen' => false,
    'contentClass' => 'p-4 sm:p-6',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<details
    data-responsive-disclosure
    data-mobile-open="<?php echo e($mobileOpen ? 'true' : 'false'); ?>"
    <?php echo e($attributes->class(['ip-card ip-disclosure'])); ?>

>
    <summary class="flex min-h-[68px] cursor-pointer list-none items-center gap-3 px-4 py-4 outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500 sm:px-6">
        <span class="min-w-0 flex-1">
            <?php if($kicker): ?>
                <span class="ip-kicker block"><?php echo e($kicker); ?></span>
            <?php endif; ?>
            <span class="block text-base font-extrabold tracking-tight text-slate-900 dark:text-white <?php echo e($kicker ? 'mt-1' : ''); ?>"><?php echo e($title); ?></span>
            <?php if($description): ?>
                <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400"><?php echo e($description); ?></span>
            <?php endif; ?>
        </span>
        <?php if(isset($meta)): ?>
            <span class="shrink-0"><?php echo e($meta); ?></span>
        <?php endif; ?>
        <span class="ip-disclosure-action" aria-hidden="true">
            <span class="hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-400 sm:inline">Detail</span>
            <svg class="ip-disclosure-chevron h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            </svg>
        </span>
    </summary>
    <div class="border-t border-sky-100 dark:border-white/10 <?php echo e($contentClass); ?>">
        <?php echo e($slot); ?>

    </div>
</details>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\components\responsive-disclosure.blade.php ENDPATH**/ ?>