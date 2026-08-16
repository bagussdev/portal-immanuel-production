<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f0f9ff">
    <title>{{ config('app.name', 'Portal Immanuel Production') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-900">
    <x-loading-overlay />
    <main class="relative min-h-screen overflow-hidden bg-gradient-to-br from-sky-50 via-white to-slate-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-sky-300/35 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-red-200/30 blur-3xl"></div>
        <div class="relative mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-6xl items-center justify-center">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
