<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f0f9ff">
    <title>{{ config('app.name', 'Portal Immanuel Production') }}</title>

    <script>
        (() => {
            const theme = localStorage.getItem('ip-theme') || 'light';
            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.dataset.theme = theme;
        })();
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>[x-cloak] { display: none !important; }</style>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.5/viewer.min.css" rel="stylesheet" />
    <script defer src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.5/viewer.min.js"></script>
</head>
<body class="min-h-full font-sans antialiased text-slate-900 dark:text-slate-100">
    <x-loading-overlay />
    {{ $slot }}

    @stack('scripts')
    <script>
        function confirmAndLoad(message) {
            const confirmed = window.confirm(message);
            if (confirmed && typeof window.showFullScreenLoader === 'function') {
                window.showFullScreenLoader();
            }
            return confirmed;
        }
    </script>
</body>
</html>
