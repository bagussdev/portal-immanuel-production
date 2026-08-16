<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php if (isset($component)) { $__componentOriginal060abe2a9b4511e378911474e77b046d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal060abe2a9b4511e378911474e77b046d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php if (isset($component)) { $__componentOriginala2b0fa968b944f36eb1fd78215b6c473 = $component; } ?>
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
<?php
    $periodName = \Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F').' '.$year;
    $periodStatus = $locks['period_exists'] ? strtolower($period->status) : 'unknown';
    $pct = $stats['total'] > 0 ? round($stats['paid'] / $stats['total'] * 100) : 0;
?>
<div class="ip-page">
    <header class="ip-page-header"><div><p class="ip-kicker">Keuangan tim</p><h1 class="ip-title">Penggajian</h1><p class="ip-subtitle">Input slip, tandai pembayaran langsung, lalu tutup periode setelah seluruh gaji selesai.</p></div><div class="flex flex-wrap items-center gap-2"><?php if($locks['period_exists']): ?><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $periodStatus]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($periodStatus)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?><?php else: ?><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => 'unknown','label' => 'Belum dibuka']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => 'unknown','label' => 'Belum dibuka']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?><?php endif; ?><span class="text-sm font-extrabold text-slate-900"><?php echo e($periodName); ?></span></div></header>

    <section class="ip-card p-4">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <form method="GET" action="<?php echo e(route('payroll.index')); ?>" class="grid flex-1 gap-3 sm:grid-cols-[160px,110px,1fr,auto]" onsubmit="showFullScreenLoader();">
                <select name="month" class="ip-input"><?php for($m=1;$m<=12;$m++): ?><option value="<?php echo e($m); ?>" <?php if($m===$month): echo 'selected'; endif; ?>><?php echo e(\Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F')); ?></option><?php endfor; ?></select>
                <input type="number" name="year" value="<?php echo e($year); ?>" class="ip-input">
                <input name="search" value="<?php echo e($search); ?>" placeholder="Cari nama karyawan" class="ip-input"><input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                <button class="ip-btn-dark">Tampilkan</button>
            </form>
            <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('addpayroll')): ?><a href="<?php echo e(route('payroll.create',['month'=>$month,'year'=>$year])); ?>" onclick="showFullScreenLoader();" class="ip-btn-primary w-full sm:w-auto <?php echo e($locks['can_add'] ? '' : 'pointer-events-none opacity-50'); ?>">+ Tambah slip</a><?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('managepayroll')): ?>
                    <?php if($locks['can_open']): ?><form action="<?php echo e(route('payroll.period.open')); ?>" method="POST" onsubmit="return confirmAndLoad('Buka periode penggajian ini?')"><?php echo csrf_field(); ?><input type="hidden" name="month" value="<?php echo e($month); ?>"><input type="hidden" name="year" value="<?php echo e($year); ?>"><button class="ip-btn-primary w-full sm:w-auto">Buka periode</button></form><?php endif; ?>
                    <?php if($locks['period_exists'] && in_array($periodStatus,['open','reopen'],true)): ?><form action="<?php echo e(route('payroll.period.close',$period)); ?>?month=<?php echo e($month); ?>&year=<?php echo e($year); ?>" method="POST" onsubmit="return confirmAndLoad('Tutup periode ini? Semua slip harus sudah dibayar.')"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="ip-btn-danger w-full sm:w-auto">Tutup periode</button></form><?php endif; ?>
                    <?php if($locks['can_reopen']): ?><form action="<?php echo e(route('payroll.period.reopen',$period)); ?>?month=<?php echo e($month); ?>&year=<?php echo e($year); ?>" method="POST" onsubmit="return confirmAndLoad('Buka kembali periode ini?')"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="ip-btn-secondary w-full sm:w-auto">Buka kembali</button></form><?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if(!$locks['period_exists']): ?><p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs font-semibold leading-5 text-amber-700"><?php if($locks['is_current']): ?>Periode bulan berjalan belum dibuka. Master atau Admin dapat membukanya untuk mulai menginput slip.<?php elseif($locks['is_past']): ?>Periode lampau hanya bisa digunakan apabila sebelumnya pernah dibuat lalu ditutup.<?php else: ?> Periode mendatang belum dapat dibuka.<?php endif; ?></p><?php endif; ?>
    </section>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        <div class="ip-card p-4 sm:p-5"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 sm:text-[11px]">Komponen gaji</p><p class="mt-3 break-words text-base font-extrabold text-slate-950 sm:text-xl">Rp <?php echo e(number_format($stats['base'],0,',','.')); ?></p></div>
        <div class="ip-card p-4 sm:p-5"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 sm:text-[11px]">Potongan</p><p class="mt-3 break-words text-base font-extrabold text-red-600 sm:text-xl">Rp <?php echo e(number_format($stats['ded'],0,',','.')); ?></p></div>
        <div class="ip-card bg-slate-950 p-4 text-white sm:p-5"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 sm:text-[11px]">Total bersih</p><p class="mt-3 break-words text-base font-extrabold sm:text-xl">Rp <?php echo e(number_format($stats['net'],0,',','.')); ?></p></div>
        <div class="ip-card p-4 sm:p-5"><div class="flex items-center justify-between gap-2"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 sm:text-[11px]">Progress bayar</p><strong class="text-xs text-emerald-600 sm:text-sm"><?php echo e($stats['paid']); ?>/<?php echo e($stats['total']); ?></strong></div><div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-emerald-500" style="width:<?php echo e($pct); ?>%"></div></div><p class="mt-2 text-xs font-semibold text-slate-400"><?php echo e($pct); ?>% selesai</p></div>
    </div>

    <div class="ip-card" id="payrollList" data-month="<?php echo e($month); ?>" data-year="<?php echo e($year); ?>" data-search="<?php echo e($search); ?>" data-changes-url="<?php echo e(route('payroll.sync.changes')); ?>" data-rows-url="<?php echo e(route('payroll.rows')); ?>" data-latest-ts="<?php echo e($latestTs ?? ''); ?>">
        <div class="ip-table-wrap"><table class="ip-table min-w-[900px]"><thead><tr><th>No</th><th>Karyawan</th><th class="text-right">Gaji</th><th class="text-right">Potongan</th><th class="text-right">Bersih</th><th>Status</th><th class="text-center">Aksi</th></tr></thead><tbody id="payroll_tbody">
            <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php echo $__env->make('payroll._rows',['rows'=>collect([$row]),'rowNumber'=>$loop->iteration+($rows->currentPage()-1)*$rows->perPage()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="7" class="py-14 text-center text-slate-500"><?php if(!$locks['period_exists']): ?>Periode belum dibuka.<?php else: ?> Belum ada slip gaji pada periode ini.<?php endif; ?></td></tr><?php endif; ?>
        </tbody></table></div><div class="border-t border-slate-200 p-4"><?php if (isset($component)) { $__componentOriginal720c5d99204acad589a79c73de989541 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal720c5d99204acad589a79c73de989541 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page-selector','data' => ['route' => 'payroll.index','perPage' => $perPage,'search' => $search,'items' => $rows]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('payroll.index'),'perPage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perPage),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rows)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal720c5d99204acad589a79c73de989541)): ?>
<?php $attributes = $__attributesOriginal720c5d99204acad589a79c73de989541; ?>
<?php unset($__attributesOriginal720c5d99204acad589a79c73de989541); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal720c5d99204acad589a79c73de989541)): ?>
<?php $component = $__componentOriginal720c5d99204acad589a79c73de989541; ?>
<?php unset($__componentOriginal720c5d99204acad589a79c73de989541); ?>
<?php endif; ?></div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?><script>
document.addEventListener('DOMContentLoaded',()=>{const box=document.getElementById('payrollList');if(!box)return;const tbody=document.getElementById('payroll_tbody'),changesUrl=box.dataset.changesUrl,rowsUrl=box.dataset.rowsUrl,month=box.dataset.month,year=box.dataset.year,search=box.dataset.search||'';let latestTs=box.dataset.latestTs||'';if(!changesUrl||!rowsUrl||!latestTs)return;const ids=()=>Array.from(tbody.querySelectorAll('tr[data-id]')).map(tr=>tr.dataset.id).filter(Boolean);const renumber=()=>{let i=0;tbody.querySelectorAll('tr[data-id]').forEach(tr=>{const cell=tr.querySelector('td:first-child');if(cell)cell.textContent=String(++i)})};async function tick(){try{const params=new URLSearchParams({since:latestTs,month,year,search});ids().forEach(id=>params.append('visible[]',id));const res=await fetch(`${changesUrl}?${params}`,{headers:{'X-Requested-With':'XMLHttpRequest'}});if(!res.ok)return;const data=await res.json();latestTs=data.latest_ts||latestTs;(data.deleted||[]).forEach(id=>tbody.querySelector(`tr[data-id="${id}"]`)?.remove());const need=[...new Set([...(data.created||[]),...(data.updated||[])])];if(!need.length){renumber();return}const p=new URLSearchParams({month,year});need.forEach(id=>p.append('ids[]',id));const htmlRes=await fetch(`${rowsUrl}?${p}`,{headers:{'X-Requested-With':'XMLHttpRequest'}});if(!htmlRes.ok)return;const temp=document.createElement('tbody');temp.innerHTML=(await htmlRes.text()).trim();Array.from(temp.children).forEach(newTr=>{const old=tbody.querySelector(`tr[data-id="${newTr.dataset.id}"]`);old?old.replaceWith(newTr):tbody.prepend(newTr)});renumber()}catch(e){}}setInterval(tick,6000)});
</script><?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal060abe2a9b4511e378911474e77b046d)): ?>
<?php $attributes = $__attributesOriginal060abe2a9b4511e378911474e77b046d; ?>
<?php unset($__attributesOriginal060abe2a9b4511e378911474e77b046d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal060abe2a9b4511e378911474e77b046d)): ?>
<?php $component = $__componentOriginal060abe2a9b4511e378911474e77b046d; ?>
<?php unset($__componentOriginal060abe2a9b4511e378911474e77b046d); ?>
<?php endif; ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\payroll\index.blade.php ENDPATH**/ ?>