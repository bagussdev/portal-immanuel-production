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
    <section class="grid w-full overflow-hidden rounded-[2rem] border border-sky-100 bg-white shadow-[0_32px_90px_-35px_rgba(15,23,42,.35)] lg:grid-cols-[1.08fr_.92fr]">
        <div class="relative hidden min-h-[680px] overflow-hidden bg-gradient-to-br from-slate-950 via-sky-950 to-slate-950 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-500 via-red-500 to-sky-500"></div>
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-red-600/15 blur-3xl"></div>
            <div class="absolute inset-0 opacity-[.035]" style="background-image:linear-gradient(rgba(255,255,255,.7) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.7) 1px,transparent 1px);background-size:34px 34px"></div>

            <div class="relative">
                <div class="inline-flex items-center gap-4 rounded-2xl border border-white/10 bg-black/50 px-5 py-4 shadow-2xl backdrop-blur">
                    <img src="<?php echo e(asset('assets/brand/immanuel-production-white-logo.png')); ?>" alt="Immanuel Production" class="h-20 w-32 object-contain">
                    <div><p class="text-base font-extrabold tracking-[.08em] text-white">PORTAL IMMANUEL</p><p class="mt-1 text-[11px] font-bold tracking-[.24em] text-slate-400">PRODUCTION</p></div>
                </div>
            </div>

            <div class="relative max-w-xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-sky-300/20 bg-sky-400/10 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[.22em] text-sky-200"><i class="h-1.5 w-1.5 rounded-full bg-sky-400"></i>Production workspace</span>
                <h1 class="mt-5 text-4xl font-extrabold leading-[1.12] tracking-tight">Pekerjaan lebih tertata.<br><span class="text-sky-300">Tim bergerak lebih tenang.</span></h1>
                <p class="mt-5 max-w-lg text-sm leading-7 text-slate-300">Kelola quotation, invoice, jadwal, armada, dan penggajian dalam satu alur yang jelas serta aman.</p>
                <div class="mt-8 grid grid-cols-3 gap-3">
                    <?php $__currentLoopData = [['Dokumen','Rapi'],['Jadwal','Terpantau'],['Akses','Terkontrol']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$value]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-2xl border border-white/10 bg-white/[.06] p-4 backdrop-blur"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400"><?php echo e($label); ?></p><p class="mt-1 text-sm font-extrabold text-white"><?php echo e($value); ?></p></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="relative flex items-center justify-between text-[11px] font-semibold text-slate-500"><span>Portal Immanuel Production</span><span>Akses aman</span></div>
        </div>

        <div class="flex min-h-[620px] items-center bg-white px-6 py-10 sm:px-12 lg:px-16" x-data="{ showPassword: false }">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-9 inline-flex items-center gap-3 rounded-2xl bg-slate-950 px-4 py-3 shadow-lg lg:hidden"><img src="<?php echo e(asset('assets/brand/immanuel-production-white-logo.png')); ?>" alt="Immanuel Production" class="h-14 w-20 object-contain"><span class="text-left"><strong class="block text-xs tracking-wide text-white">PORTAL IMMANUEL</strong><span class="text-[9px] font-bold tracking-[.2em] text-slate-400">PRODUCTION</span></span></div>

                <p class="ip-kicker">Selamat datang kembali</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Masuk ke Portal Immanuel</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan akun kerja sesuai peranmu untuk melanjutkan.</p>

                <?php if (isset($component)) { $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-session-status','data' => ['class' => 'mt-6 ip-alert-success','status' => session('status')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth-session-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-6 ip-alert-success','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('status'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $attributes = $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $component = $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-900 shadow-sm" role="alert">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.4 2.6 18a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 4.4a2 2 0 0 0-3.4 0Z"/></svg>
                            </span>
                            <div><p class="font-extrabold">Login belum berhasil</p><p class="mt-1 text-sm font-medium leading-5 text-red-700"><?php echo e($errors->first()); ?></p></div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login')); ?>" class="mt-7 space-y-5" onsubmit="showFullScreenLoader();">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="email" class="ip-label">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18v12H3zM3 7l9 7 9-7"/></svg></span>
                            <input id="email" class="ip-input min-h-12 pl-12" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username" placeholder="nama@immanuel.com">
                        </div>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between"><label for="password" class="ip-label !mb-0">Password</label><?php if(Route::has('password.request')): ?><a class="text-xs font-extrabold text-sky-700 hover:text-sky-900" href="<?php echo e(route('password.request')); ?>">Lupa password?</a><?php endif; ?></div>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 10V8a6 6 0 0 1 12 0v2M5 10h14v11H5z"/></svg></span>
                            <input id="password" class="ip-input min-h-12 px-12" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 hover:text-sky-700" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg x-show="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                <svg x-cloak x-show="showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="m3 3 18 18M10.7 6.1A11 11 0 0 1 12 6c6.5 0 10 6 10 6a14 14 0 0 1-2.1 2.7M6.4 6.4C3.6 8.2 2 12 2 12s3.5 6 10 6c1.8 0 3.3-.5 4.6-1.2"/></svg>
                            </button>
                        </div>
                    </div>

                    <label for="remember_me" class="flex cursor-pointer items-center gap-3 rounded-xl bg-sky-50/70 px-3 py-2.5 text-sm font-semibold text-slate-600"><input id="remember_me" type="checkbox" class="rounded border-sky-300 text-sky-600 focus:ring-sky-500" name="remember">Ingat saya di perangkat ini</label>

                    <button type="submit" class="ip-btn-primary min-h-12 w-full text-[15px]">Masuk ke portal<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                </form>

                <p class="mt-7 text-center text-xs leading-5 text-slate-400">Akun belum aktif atau lupa akses?<br><strong class="font-bold text-slate-600">Hubungi Master untuk bantuan.</strong></p>
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
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\auth\login.blade.php ENDPATH**/ ?>