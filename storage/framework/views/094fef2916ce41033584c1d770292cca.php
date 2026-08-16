<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <section class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-sky-100 bg-white shadow-[0_32px_90px_-35px_rgba(15,23,42,.35)] lg:grid-cols-[.88fr_1.12fr]">
        <aside class="relative hidden min-h-[620px] overflow-hidden bg-gradient-to-br from-slate-950 via-sky-950 to-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-500 via-red-500 to-sky-500"></div>
            <div class="absolute -right-28 -top-24 h-80 w-80 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="absolute -bottom-28 -left-24 h-80 w-80 rounded-full bg-red-600/15 blur-3xl"></div>

            <a href="<?php echo e(route('login')); ?>" class="relative inline-flex w-fit items-center gap-3 rounded-2xl border border-white/10 bg-black/40 px-4 py-3">
                <img src="<?php echo e(asset('assets/brand/immanuel-production-white-logo.png')); ?>" alt="Immanuel Production" class="h-16 w-24 object-contain">
                <span><strong class="block text-xs tracking-wide">PORTAL IMMANUEL</strong><span class="text-[9px] font-bold tracking-[.2em] text-slate-400">PRODUCTION</span></span>
            </a>

            <div class="relative">
                <span class="inline-flex rounded-full border border-sky-300/20 bg-sky-400/10 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[.2em] text-sky-200">Pemulihan akun</span>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight">Akses kembali akunmu dengan aman.</h1>
                <p class="mt-4 text-sm leading-7 text-slate-300">Kami akan mengirim tautan khusus ke email yang terdaftar. Password lama tidak pernah ditampilkan atau dikirim.</p>

                <div class="mt-8 space-y-3">
                    <?php $__currentLoopData = ['Tautan hanya dapat digunakan satu kali', 'Masa berlaku dibatasi selama 60 menit', 'Abaikan email jika kamu tidak meminta reset']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[.05] px-4 py-3 text-xs font-semibold text-slate-200">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300">&#10003;</span>
                            <?php echo e($item); ?>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <p class="relative text-[11px] font-semibold text-slate-500">Portal Immanuel Production &bull; Akses aman</p>
        </aside>

        <div class="flex min-h-[590px] items-center px-6 py-10 sm:px-12 lg:px-16">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-8 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-3 py-2 lg:hidden">
                        <img src="<?php echo e(asset('assets/brand/immanuel-production-white-logo.png')); ?>" alt="Immanuel Production" class="h-11 w-16 object-contain">
                        <span class="text-[10px] font-extrabold tracking-wide text-white">PORTAL IMMANUEL</span>
                    </div>
                    <a href="<?php echo e(route('login')); ?>" class="ml-auto inline-flex items-center gap-2 text-xs font-extrabold text-slate-500 transition hover:text-sky-700">
                        <span aria-hidden="true">&larr;</span> Kembali ke login
                    </a>
                </div>

                <p class="ip-kicker">Lupa password</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Minta tautan pemulihan</h2>
                <p class="mt-3 text-sm leading-6 text-slate-500">Masukkan email akun kerja. Kami akan mengirimkan tautan untuk membuat password baru.</p>

                <?php if(session('status')): ?>
                    <div class="ip-alert-success mt-6 flex items-start gap-3" role="status">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">&#10003;</span>
                        <div><p class="font-extrabold">Email berhasil diproses</p><p class="mt-0.5 text-xs leading-5"><?php echo e(session('status')); ?></p></div>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="ip-alert-error mt-6" role="alert">
                        <p class="font-extrabold">Tautan belum dapat dikirim</p>
                        <p class="mt-1 text-xs leading-5"><?php echo e($errors->first()); ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('password.email')); ?>" class="mt-7 space-y-5" onsubmit="showFullScreenLoader();">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="email" class="ip-label">Email akun</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18v12H3zM3 7l9 7 9-7" /></svg>
                            </span>
                            <input id="email" class="ip-input min-h-12 pl-12" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="email" placeholder="nama@immanuel.com">
                        </div>
                    </div>

                    <button type="submit" class="ip-btn-primary min-h-12 w-full text-[15px]">
                        Kirim tautan reset
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </button>
                </form>

                <div class="mt-6 rounded-2xl border border-sky-100 bg-sky-50/70 p-4 text-xs leading-5 text-slate-500">
                    <strong class="block text-slate-700">Belum menerima email?</strong>
                    Periksa folder Spam, pastikan alamat email benar, lalu tunggu satu menit sebelum mencoba kembali.
                </div>
            </div>
        </div>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>