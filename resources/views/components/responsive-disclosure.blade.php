@props([
    'title',
    'kicker' => null,
    'description' => null,
    'mobileOpen' => false,
    'contentClass' => 'p-4 sm:p-5',
])

<details
    data-responsive-disclosure
    data-mobile-open="{{ $mobileOpen ? 'true' : 'false' }}"
    {{ $attributes->class(['ip-card ip-disclosure']) }}
>
    <summary class="flex min-h-16 cursor-pointer list-none items-center gap-3 px-4 py-3 outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500 sm:px-5">
        <span class="min-w-0 flex-1">
            @if ($kicker)
                <span class="ip-kicker block">{{ $kicker }}</span>
            @endif
            <span class="block text-base font-extrabold tracking-tight text-slate-900 dark:text-white {{ $kicker ? 'mt-1' : '' }}">{{ $title }}</span>
            @if ($description)
                <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $description }}</span>
            @endif
        </span>
        @isset($meta)
            <span class="shrink-0">{{ $meta }}</span>
        @endisset
        <span class="ip-disclosure-action" aria-hidden="true">
            <span class="hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-400 sm:inline">Detail</span>
            <svg class="ip-disclosure-chevron h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            </svg>
        </span>
    </summary>
    <div class="border-t border-sky-100 dark:border-white/10 {{ $contentClass }}">
        {{ $slot }}
    </div>
</details>
