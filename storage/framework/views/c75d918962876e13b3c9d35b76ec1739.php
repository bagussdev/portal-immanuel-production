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

        
        <div class="mb-6">
            <a href="<?php echo e(route('payroll.index', ['month' => $payroll->month, 'year' => $payroll->year])); ?>"
                onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <?php
            /** @var \Illuminate\Support\Collection $baseItems */
            /** @var \Illuminate\Support\Collection $deductionItems */
            $baseItems = $baseItems ?? collect(); // dari controller
            $deductionItems = $deductionItems ?? collect();

            $baseTotal = (int) ($baseItems->sum('amount') ?? 0);
            $dedTotal = (int) ($deductionItems->sum('amount') ?? 0);
            $net = $baseTotal - $dedTotal;
            $idr = fn($n) => number_format((int) $n, 0, ',', '.');
        ?>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            
            <div class="mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Payroll Details</h2>
                <div class="text-sm text-gray-500">
                    Employee:
                    <span class="font-medium text-gray-700 dark:text-gray-200"><?php echo e($payroll->user->name); ?></span>
                </div>
                <div class="text-sm text-gray-500">
                    Period:
                    <?php echo e(\Carbon\Carbon::create()->month($payroll->period->month)->locale('id')->translatedFormat('F')); ?>

                    <?php echo e($payroll->period->year); ?>

                </div>
            </div>

            
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-gray-500 text-xs">Base</div>
                    <div class="font-semibold text-blue-600">Rp <?php echo e($idr($baseTotal)); ?></div>
                </div>
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-gray-500 text-xs">Deduction</div>
                    <div class="font-semibold text-red-600">Rp <?php echo e($idr($dedTotal)); ?></div>
                </div>
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-gray-500 text-xs">Net Total</div>
                    <div class="font-bold">Rp <?php echo e($idr($net)); ?></div>
                </div>
            </div>

            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <div class="lg:col-span-7">
                    <div class="rounded-lg border dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 font-medium text-gray-700 dark:text-gray-200">
                            Rincian Komponen
                        </div>
                        <div class="p-4">
                            
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Base</div>
                                    <div class="text-xs text-gray-500">
                                        Total: <span class="font-semibold text-blue-600">Rp
                                            <?php echo e($idr($baseTotal)); ?></span>
                                    </div>
                                </div>

                                <?php if($baseItems->count()): ?>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="text-left text-gray-500">
                                                <tr class="border-b dark:border-gray-700">
                                                    <th class="py-2 pr-2">No</th>
                                                    <th class="py-2 pr-2">Nama</th>
                                                    <th class="py-2 pr-2 text-right">Jumlah (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-800 dark:text-gray-100">
                                                <?php $__currentLoopData = $baseItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr class="border-b last:border-0 dark:border-gray-700">
                                                        <td class="py-2 pr-2 align-top"><?php echo e($i + 1); ?></td>
                                                        <td class="py-2 pr-2 align-top"><?php echo e($it->name ?: 'Gaji Pokok'); ?>

                                                        </td>
                                                        <td class="py-2 pr-2 align-top text-right">Rp
                                                            <?php echo e($idr($it->amount)); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-sm text-gray-500">Tidak ada komponen base.</div>
                                <?php endif; ?>
                            </div>

                            <hr class="my-4 border-gray-200 dark:border-gray-700">

                            
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Deductions</div>
                                    <div class="text-xs text-gray-500">
                                        Total: <span class="font-semibold text-red-600">Rp <?php echo e($idr($dedTotal)); ?></span>
                                    </div>
                                </div>

                                <?php if($deductionItems->count()): ?>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="text-left text-gray-500">
                                                <tr class="border-b dark:border-gray-700">
                                                    <th class="py-2 pr-2">No</th>
                                                    <th class="py-2 pr-2">Nama</th>
                                                    <th class="py-2 pr-2 text-right">Jumlah (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-800 dark:text-gray-100">
                                                <?php $__currentLoopData = $deductionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr class="border-b last:border-0 dark:border-gray-700">
                                                        <td class="py-2 pr-2 align-top"><?php echo e($i + 1); ?></td>
                                                        <td class="py-2 pr-2 align-top"><?php echo e($it->name); ?></td>
                                                        <td class="py-2 pr-2 align-top text-right">Rp
                                                            <?php echo e($idr($it->amount)); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-sm text-gray-500">Tidak ada potongan.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-5">
                    <div class="rounded-lg border dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 font-medium text-gray-700 dark:text-gray-200">
                            Ringkasan Pembayaran
                        </div>
                        <div class="p-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Total Base</span>
                                <span class="font-semibold">Rp <?php echo e($idr($baseTotal)); ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Total Potongan</span>
                                <span class="font-semibold text-red-600">- Rp <?php echo e($idr($dedTotal)); ?></span>
                            </div>
                            <hr class="border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between text-base">
                                <span class="text-gray-700 dark:text-gray-200 font-medium">Diterima (Net)</span>
                                <span class="font-bold">Rp <?php echo e($idr($net)); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg border dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 font-medium text-gray-700 dark:text-gray-200">
                            Catatan
                        </div>
                        <div class="p-4 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">
                            <?php echo e($payroll->notes ?: '—'); ?>

                        </div>
                    </div>
                </div>
            </div>

            
            <div class="mt-6 flex">
                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['as' => 'a','href' => ''.e(route('payroll.slip.pdf', $payroll)).'','target' => '_blank','text' => 'Export PDF','color' => 'yellow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['as' => 'a','href' => ''.e(route('payroll.slip.pdf', $payroll)).'','target' => '_blank','text' => 'Export PDF','color' => 'yellow']); ?>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/payroll/show.blade.php ENDPATH**/ ?>