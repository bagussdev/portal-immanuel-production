<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'value', 'icon', 'color' => 'bg-yellow-300', 'href' => null]));

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

foreach (array_filter((['title', 'value', 'icon', 'color' => 'bg-yellow-300', 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $content = <<<HTML
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-3 sm:p-4 w-full hover:shadow-xl transition duration-300 ease-in-out">
            <div class="flex justify-between items-center gap-3 sm:gap-4 mb-2 sm:mb-3">
                <h2 class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 truncate">{$title}</h2>
                <div class="{$color} rounded-2xl w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center shrink-0">
                    {$icon}
                </div>
            </div>
            <p class="text-2xl sm:text-4xl font-bold text-gray-900 dark:text-white truncate">{$value}</p>
        </div>
    HTML;
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>" class="block w-full">
        <?php echo $content; ?>

    </a>
<?php else: ?>
    <?php echo $content; ?>

<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\components\dashboard\stat-card.blade.php ENDPATH**/ ?>