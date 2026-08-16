<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginal060abe2a9b4511e378911474e77b046d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal060abe2a9b4511e378911474e77b046d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginala2b0fa968b944f36eb1fd78215b6c473 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala2b0fa968b944f36eb1fd78215b6c473 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-information','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert-information'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala2b0fa968b944f36eb1fd78215b6c473)): ?>
<?php $attributes = $__attributesOriginala2b0fa968b944f36eb1fd78215b6c473; ?>
<?php unset($__attributesOriginala2b0fa968b944f36eb1fd78215b6c473); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala2b0fa968b944f36eb1fd78215b6c473)): ?>
<?php $component = $__componentOriginala2b0fa968b944f36eb1fd78215b6c473; ?>
<?php unset($__componentOriginala2b0fa968b944f36eb1fd78215b6c473); ?>
<?php endif; ?>

        <div class="mt-4">
            <a href="<?php echo e(route('equipment.index')); ?>" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="flex justify-between items-center mt-4 w-full max-w-full">
            <h2 class="font-bold text-xl sm:text-2xl">
                Equipment Detail
            </h2>
        </div>

        <hr class="h-[3px] my-4 bg-gray-200 border-0 dark:bg-gray-700 w-full">

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-sm text-gray-700 dark:text-gray-300">
            
            <div class="block md:hidden space-y-4">
                <?php
                    $fields = [
                        'Name' => $equipment->name,
                        'Brand' => $equipment->brand ?? '-',
                        'Model' => $equipment->model ?? '-',
                        'Serial Number' => $equipment->serial_number ?? '-',
                        'Qty' => $equipment->qty,
                        'Status' => ucfirst($equipment->status),
                        'Location' => $equipment->gudang->name ?? '-',
                        'Created By' => $equipment->createdBy->name ?? '-',
                    ];
                ?>
                <div class="grid grid-cols-1 gap-3">
                    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex">
                            <div class="w-40 font-medium"><?php echo e($label); ?></div>
                            <div class="flex-1">: <?php echo nl2br(e($value)); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex">
                        <div class="w-40 font-medium">Photo</div>
                        <div class="flex-1">
                            <?php if($equipment->photo): ?>
                                <img src="<?php echo e(asset('storage/equipments/' . $equipment->photo)); ?>"
                                    class="w-full max-w-xs rounded-md shadow mt-2" alt="Photo">
                            <?php else: ?>
                                <p class="italic text-gray-400">No Photo Found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="hidden md:grid grid-cols-1 md:grid-cols-4 gap-6">
                <div><span class="font-medium">Name :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($equipment->name); ?></div>
                </div>
                <div><span class="font-medium">Brand :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($equipment->brand ?? '-'); ?></div>
                </div>
                <div><span class="font-medium">Model :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($equipment->model ?? '-'); ?></div>
                </div>
                <div><span class="font-medium">Serial Number :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">
                        <?php echo e($equipment->serial_number ?? '-'); ?></div>
                </div>
                <div><span class="font-medium">Qty :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($equipment->qty); ?></div>
                </div>
                <div><span class="font-medium">Status :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e(ucfirst($equipment->status)); ?>

                    </div>
                </div>
                <div><span class="font-medium">Location :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">
                        <?php echo e($equipment->gudang->name ?? '-'); ?></div>
                </div>
                <div><span class="font-medium">Created By :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">
                        <?php echo e($equipment->createdBy->name ?? '-'); ?></div>
                </div>
                <div><span class="font-medium">Photo :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1 text-center"
                        id="equipment-photo-viewer">
                        <?php if($equipment->photo): ?>
                            <img src="<?php echo e(asset('storage/' . $equipment->photo)); ?>"
                                class="w-full h-auto max-h-48 object-contain mx-auto rounded shadow cursor-pointer"
                                alt="Photo">
                        <?php else: ?>
                            <p class="italic text-gray-400">No Photo Found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="mt-6">
                <p class="font-semibold">Notes :</p>
                <div class="bg-blue-50 dark:bg-blue-900 rounded px-4 py-3 mt-1">
                    <?php echo e($equipment->notes ?? '-'); ?>

                </div>
            </div>
        </div>

        
        <?php $__env->startPush('scripts'); ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const container = document.getElementById('equipment-photo-viewer');
                    if (container) {
                        new Viewer(container, {
                            inline: false,
                            toolbar: true,
                            movable: true,
                            zoomable: true,
                            scalable: true,
                            transition: true,
                        });
                    }
                });
            </script>
        <?php $__env->stopPush(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal060abe2a9b4511e378911474e77b046d)): ?>
<?php $attributes = $__attributesOriginal060abe2a9b4511e378911474e77b046d; ?>
<?php unset($__attributesOriginal060abe2a9b4511e378911474e77b046d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal060abe2a9b4511e378911474e77b046d)): ?>
<?php $component = $__componentOriginal060abe2a9b4511e378911474e77b046d; ?>
<?php unset($__componentOriginal060abe2a9b4511e378911474e77b046d); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\equipment\show.blade.php ENDPATH**/ ?>