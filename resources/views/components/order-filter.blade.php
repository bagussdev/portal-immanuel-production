@props(['current' => ''])

@php
    $link = function (string $value) use ($current) {
        $query = request()->query();
        unset($query['page'], $query['sort'], $query['direction']);

        if ($current === $value) {
            unset($query['order']);
        } else {
            $query['order'] = $value;
        }

        return url()->current().($query ? '?'.http_build_query($query) : '');
    };
@endphp

<div {{ $attributes->class('ip-card flex flex-wrap items-center justify-between gap-3 p-3 sm:px-4') }}>
    <span class="text-xs font-extrabold uppercase tracking-[.16em] text-slate-400">Urutan</span>
    <div class="grid grid-cols-2 gap-2">
        @foreach(['latest' => 'Terbaru', 'oldest' => 'Terlama'] as $value => $label)
            <a href="{{ $link($value) }}" class="inline-flex min-h-9 items-center justify-center rounded-xl border px-4 py-2 text-xs font-extrabold transition {{ $current === $value ? 'border-sky-500 bg-sky-50 text-sky-800 dark:border-red-500 dark:bg-red-500/10 dark:text-red-300' : 'border-sky-100 bg-white text-slate-600 hover:border-sky-300 dark:border-white/10 dark:bg-white/[.04] dark:text-slate-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>
