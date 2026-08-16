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
            <a href="<?php echo e(route('payroll.index', ['month' => $month, 'year' => $year])); ?>" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            
            <div class="mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Create Payroll</h2>
                <div class="text-sm text-gray-500">
                    Period: <?php echo e(\Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F')); ?>

                    <?php echo e($year); ?>

                </div>
            </div>

            <form method="POST" action="<?php echo e(route('payroll.store')); ?>"
                onsubmit="return confirmAndLoad('Apakah Anda Yakin Membuat Gaji?')">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="month" value="<?php echo e($month); ?>">
                <input type="hidden" name="year" value="<?php echo e($year); ?>">

                
                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Employee</label>
                    <select name="user_id" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">— Select —</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php if(old('user_id') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-red-600 text-xs mt-1"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Base</label>
                        <button type="button" id="btnAddBase"
                            class="text-xs sm:text-sm px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Add Row
                        </button>
                    </div>

                    <div id="baseList" class="space-y-2 overflow-x-auto w-full">
                        
                    </div>
                    <?php $__errorArgs = ['bases'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-red-600 text-xs mt-1"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deduction</label>
                        <button type="button" id="btnAddDed"
                            class="text-xs sm:text-sm px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Add Row
                        </button>
                    </div>

                    <div id="dedList" class="space-y-2 overflow-x-auto w-full">
                        
                    </div>
                    <?php $__errorArgs = ['deductions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-red-600 text-xs mt-1"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3 rounded border dark:border-gray-700">
                        <div class="text-gray-500 text-xs">Base</div>
                        <div id="sumBase" class="font-semibold text-blue-600">0</div>
                    </div>
                    <div class="p-3 rounded border dark:border-gray-700">
                        <div class="text-gray-500 text-xs">Deduction</div>
                        <div id="sumDed" class="font-semibold text-red-600">0</div>
                    </div>
                    <div class="p-3 rounded border dark:border-gray-700">
                        <div class="text-gray-500 text-xs">Net Total</div>
                        <div id="sumNet" class="font-bold">0</div>
                    </div>
                </div>

                
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?php echo e(old('notes')); ?></textarea>
                </div>

                
                <div class="flex gap-2">
                    <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'submit','text' => 'Save','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','text' => 'Save','color' => 'blue']); ?>
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
            </form>
        </div>

        
        <template id="tplBaseRow">
            <div
                class="base-row flex flex-nowrap items-center gap-2 border rounded-md p-2 dark:border-gray-700 w-full overflow-x-auto">
                
                <input type="text" name="bases[name][]" placeholder="Nama komponen gaji (mis. Gaji Pokok/Tunjangan)"
                    class="flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />

                
                <input type="text" name="bases[amount][]" placeholder="0"
                    class="rp flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-right" />

                
                <button type="button" aria-label="Remove"
                    class="btnDelBase shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 dark:border-gray-600
                        hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600">
                    ×
                </button>
            </div>
        </template>

        
        <template id="tplDedRow">
            <div
                class="ded-row flex flex-nowrap items-center gap-2 border rounded-md p-2 dark:border-gray-700 w-full overflow-x-auto">
                <input type="text" name="deductions[name][]" placeholder="Nama potongan"
                    class="flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                <input type="text" name="deductions[amount][]" placeholder="0"
                    class="rp flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-right" />
                <button type="button" aria-label="Remove"
                    class="btnDelRow shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 dark:border-gray-600
                        hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600">
                    ×
                </button>
            </div>
        </template>

        
        <script>
            (function() {
                const q = sel => document.querySelector(sel);
                const qa = sel => Array.from(document.querySelectorAll(sel));

                const formatRp = (num) => {
                    num = Number.isFinite(num) ? num : 0;
                    return num.toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    });
                };
                const unformatRp = (s) => {
                    if (!s) return 0;
                    s = String(s).replace(/[^\d]/g, '');
                    return parseInt(s || '0', 10);
                };

                function bindRpInput(el) {
                    if (el.dataset.bound === '1') return;
                    el.dataset.bound = '1';
                    el.addEventListener('input', () => {
                        el.value = formatRp(unformatRp(el.value));
                        computeSummary();
                    });
                    el.addEventListener('blur', () => {
                        el.value = formatRp(unformatRp(el.value));
                        computeSummary();
                    });
                }

                function computeSummary() {
                    const bases = qa('input[name="bases[amount][]"]').reduce((sum, e) => sum + unformatRp(e.value), 0);
                    const deds = qa('input[name="deductions[amount][]"]').reduce((sum, e) => sum + unformatRp(e.value), 0);
                    q('#sumBase').textContent = formatRp(bases);
                    q('#sumDed').textContent = formatRp(deds);
                    q('#sumNet').textContent = formatRp(bases - deds);
                }

                function addBaseRow(name = '', amount = '') {
                    const tpl = q('#tplBaseRow');
                    const list = q('#baseList');
                    const node = tpl.content.cloneNode(true);
                    list.appendChild(node);

                    const row = list.lastElementChild;
                    const nameEl = row.querySelector('input[name="bases[name][]"]');
                    const amtEl = row.querySelector('input[name="bases[amount][]"]');
                    const btn = row.querySelector('.btnDelBase');

                    nameEl.value = name || '';
                    if (amount) {
                        const raw = String(amount).replace(/[^\d]/g, '');
                        amtEl.value = formatRp(parseInt(raw || '0', 10));
                    }

                    bindRpInput(amtEl);
                    btn.addEventListener('click', () => {
                        row.remove();
                        computeSummary();
                    });
                    computeSummary();
                }

                function addDedRow(name = '', amount = '') {
                    const tpl = q('#tplDedRow');
                    const list = q('#dedList');
                    const node = tpl.content.cloneNode(true);
                    list.appendChild(node);

                    const row = list.lastElementChild;
                    const nameEl = row.querySelector('input[name="deductions[name][]"]');
                    const amtEl = row.querySelector('input[name="deductions[amount][]"]');
                    const btn = row.querySelector('.btnDelRow');

                    nameEl.value = name || '';
                    if (amount) {
                        const raw = String(amount).replace(/[^\d]/g, '');
                        amtEl.value = formatRp(parseInt(raw || '0', 10));
                    }

                    bindRpInput(amtEl);
                    btn.addEventListener('click', () => {
                        row.remove();
                        computeSummary();
                    });
                    computeSummary();
                }

                // tombol add
                const btnAddBase = q('#btnAddBase');
                if (btnAddBase) btnAddBase.addEventListener('click', () => addBaseRow());

                const btnAddDed = q('#btnAddDed');
                if (btnAddDed) btnAddDed.addEventListener('click', () => addDedRow());

                // restore old inputs jika validasi error
                <?php
                    $oldBaseNames = old('bases.name', []);
                    $oldBaseAmts = old('bases.amount', []);
                    $oldDedNames = old('deductions.name', []);
                    $oldDedAmts = old('deductions.amount', []);
                ?>

                if (<?php echo json_encode(!empty($oldBaseNames), 15, 512) ?>) {
                    <?php $__currentLoopData = $oldBaseNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $nm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        addBaseRow(<?php echo json_encode($nm, 15, 512) ?>, <?php echo json_encode($oldBaseAmts[$i] ?? '', 15, 512) ?>);
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                } else {
                    // seed 1 baris gaji pokok
                    addBaseRow('Gaji Pokok', '');
                }

                if (<?php echo json_encode(!empty($oldDedNames), 15, 512) ?>) {
                    <?php $__currentLoopData = $oldDedNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $nm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        addDedRow(<?php echo json_encode($nm, 15, 512) ?>, <?php echo json_encode($oldDedAmts[$i] ?? '', 15, 512) ?>);
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                }

                // bind semua .rp diawal
                qa('.rp').forEach(bindRpInput);
                computeSummary();
            })();
        </script>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\payroll\create.blade.php ENDPATH**/ ?>