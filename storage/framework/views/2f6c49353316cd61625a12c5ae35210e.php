<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['route', 'perPage' => 5, 'search' => '', 'items', 'showPagination' => true]));

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

foreach (array_filter((['route', 'perPage' => 5, 'search' => '', 'items', 'showPagination' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use Illuminate\Pagination\LengthAwarePaginator;
?>

<?php if($showPagination): ?>
    <div class="mt-6 px-4 pb-3 flex flex-col sm:flex-row justify-between items-center gap-4">

        
        <?php if($items instanceof LengthAwarePaginator): ?>
            <div>
                <?php echo e($items->appends(['per_page' => $perPage, 'search' => $search])->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-sm text-gray-500 dark:text-gray-400 italic">
                Showing all <?php echo e($items->count()); ?> records.
            </div>
        <?php endif; ?>

        
        <div class="flex items-center gap-4 flex-wrap justify-end">
            <form method="GET" action="<?php echo e(route($route)); ?>" onsubmit="showFullScreenLoader();">
                <input type="hidden" name="search" value="<?php echo e($search); ?>">
                <div class="flex items-center gap-1">
                    <label for="per_page" class="text-sm text-gray-600 dark:text-gray-300">Show</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()"
                        class="text-sm w-16 px-2 py-1 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500">
                        <option value="5" <?php echo e($perPage == 5 ? 'selected' : ''); ?>>5</option>
                        <option value="10" <?php echo e($perPage == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="20" <?php echo e($perPage == 20 ? 'selected' : ''); ?>>20</option>
                        <option value="all" <?php echo e($perPage == 'all' ? 'selected' : ''); ?>>All</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">per page</span>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\components\per-page-selector.blade.php ENDPATH**/ ?>