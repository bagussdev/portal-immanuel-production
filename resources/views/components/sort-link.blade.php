@props([
    'column',
    'label',
    'current' => '',
    'direction' => 'asc',
    'compact' => false,
])

@php
    $active = $current === $column;
    $nextDirection = !$active ? 'asc' : ($direction === 'asc' ? 'desc' : null);
    $query = request()->query();

    if ($nextDirection) {
        $query['sort'] = $column;
        $query['direction'] = $nextDirection;
    } else {
        unset($query['sort'], $query['direction']);
    }

    unset($query['page']);
    $url = url()->current().($query ? '?'.http_build_query($query) : '');
    $icon = !$active ? '↕' : ($direction === 'asc' ? '↑' : '↓');
    $state = !$active ? 'Urutan awal' : ($direction === 'asc' ? 'Naik' : 'Turun');
@endphp

<a href="{{ $url }}"
    aria-label="Urutkan {{ $label }}. {{ $state }}."
    title="Klik untuk mengubah urutan {{ strtolower($label) }}"
    {{ $attributes->class($compact
        ? 'inline-flex min-h-9 items-center justify-center gap-1.5 rounded-xl border px-3 py-2 text-xs font-extrabold transition '.($active ? 'border-sky-500 bg-sky-50 text-sky-800 dark:border-red-500 dark:bg-red-500/10 dark:text-red-300' : 'border-sky-100 bg-white text-slate-600 hover:border-sky-300 dark:border-white/10 dark:bg-white/[.04] dark:text-slate-300')
        : 'group inline-flex items-center gap-1.5 whitespace-nowrap font-inherit text-inherit') }}>
    <span>{{ $label }}</span>
    <span aria-hidden="true" class="text-[11px] {{ $active ? 'text-sky-600 dark:text-red-400' : 'text-slate-300 group-hover:text-slate-500' }}">{{ $icon }}</span>
</a>
