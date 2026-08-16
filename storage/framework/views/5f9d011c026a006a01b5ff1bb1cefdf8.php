<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f0f9ff">
    <title><?php echo $__env->yieldContent('code'); ?> &mdash; Portal Immanuel Production</title>
    <script>
        (() => {
            const theme = localStorage.getItem('ip-theme') || 'light';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,600,700,800&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="flex min-h-full items-center justify-center bg-sky-50 p-5 font-sans text-slate-900 dark:bg-[#080b12] dark:text-white">
    <main class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-sky-100 bg-white p-6 text-center shadow-2xl shadow-sky-900/10 dark:border-white/10 dark:bg-[#11151e] dark:shadow-black/30 sm:p-10">
        <div class="mx-auto flex h-16 w-fit items-center gap-3 rounded-2xl bg-[#0b0c0f] px-4">
            <img src="<?php echo e(asset('assets/brand/immanuel-production-legacy-logo.png')); ?>" alt="Immanuel Production" class="h-12 w-12 object-contain">
            <span class="text-left"><strong class="block text-xs tracking-wide text-white">PORTAL IMMANUEL</strong><span class="text-[9px] font-bold tracking-[.2em] text-slate-400">PRODUCTION</span></span>
        </div>
        <p class="mt-8 text-xs font-extrabold uppercase tracking-[.28em] text-sky-600 dark:text-red-400">Kode <?php echo $__env->yieldContent('code'); ?></p>
        <h1 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl"><?php echo $__env->yieldContent('title'); ?></h1>
        <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400"><?php echo $__env->yieldContent('message'); ?></p>
        <div class="mt-7 flex flex-col justify-center gap-2 sm:flex-row">
            <button type="button" onclick="history.back()" class="ip-btn-secondary">Kembali</button>
            <a href="<?php echo e(route('dashboard')); ?>" class="ip-btn-primary">Ke dashboard</a>
        </div>
    </main>
</body>
</html>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/errors/layout.blade.php ENDPATH**/ ?>