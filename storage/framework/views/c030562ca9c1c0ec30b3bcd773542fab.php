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

        <div class="mt-4 px-4">
            <a href="<?php echo e(route('client.index')); ?>" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="flex justify-between items-center mt-4 w-full max-w-full px-4">
            <h2 class="font-bold text-xl sm:text-2xl">Client Detail</h2>
        </div>

        <hr class="h-[3px] my-4 bg-gray-200 border-0 dark:bg-gray-700 w-full">

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-sm text-gray-700 dark:text-gray-300">
            
            <div class="block sm:hidden space-y-4">
                <?php
                    $fields = [
                        'Name' => $client->name,
                        'Company' => $client->company ?? '-',
                        'Email' => $client->email ?? '-',
                        'Phone' => $client->phone ?? '-',
                    ];
                ?>
                <div class="grid grid-cols-1 gap-3">
                    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex">
                            <div class="w-32 font-medium"><?php echo e($label); ?></div>
                            <div class="flex-1">: <?php echo nl2br(e($value)); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="hidden sm:grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div><span class="font-medium">Name :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($client->name); ?></div>
                </div>
                <div><span class="font-medium">Company :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($client->company ?? '-'); ?></div>
                </div>
                <div><span class="font-medium">Email :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($client->email ?? '-'); ?></div>
                </div>
                <div><span class="font-medium">Phone :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1"><?php echo e($client->phone ?? '-'); ?></div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">List Invoice</h2>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                            <tr>
                                <th class="px-4 py-2 text-left">Invoice No</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $invNo = $inv->invoice_number ?? 'INV-' . $inv->id;
                                    $invDate = \Illuminate\Support\Carbon::parse($inv->created_at)->format('d M Y');
                                    $invTot = (float) ($inv->grand_total ?? 0);
                                ?>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">
                                        <a href="<?php echo e(route('invoices.show', $inv->id)); ?>"
                                            onclick="showFullScreenLoader();"
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                            <?php echo e($invNo); ?>

                                        </a>
                                    </td>
                                    <td class="px-4 py-2"><?php echo e($invDate); ?></td>
                                    <td class="px-4 py-2">Rp <?php echo e(number_format($invTot, 0, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No
                                        invoice found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <?php echo e($invoices->onEachSide(1)->withQueryString()->links()); ?>

                    </div>
                </div>

                
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">List Quotation</h2>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                            <tr>
                                <th class="px-4 py-2 text-left">Quotation No</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Total (Sisa)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $quoNo = $quo->quotation_number ?? 'QUO-' . $quo->id;
                                    $quoDate = \Illuminate\Support\Carbon::parse($quo->created_at)->format('d M Y');
                                    $quoTot = (float) ($quo->grand_total ?? 0);
                                ?>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">
                                        <a href="<?php echo e(route('quotations.show', $quo->id)); ?>"
                                            onclick="showFullScreenLoader();"
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                            <?php echo e($quoNo); ?>

                                        </a>
                                    </td>
                                    <td class="px-4 py-2"><?php echo e($quoDate); ?></td>
                                    <td class="px-4 py-2">Rp <?php echo e(number_format($quoTot, 0, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No
                                        quotation found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <?php echo e($quotations->onEachSide(1)->withQueryString()->links()); ?>

                    </div>
                </div>
            </div>
        </div>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\client\show.blade.php ENDPATH**/ ?>