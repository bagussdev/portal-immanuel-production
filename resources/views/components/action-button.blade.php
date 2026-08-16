@props(['type' => 'submit', 'color' => 'gray', 'text' => 'Action', 'href' => null, 'dense' => false])
@php
    $sizeClass = $dense
        ? 'min-h-8 px-2 py-1 text-[11px]'
        : 'min-h-9 px-2.5 py-1.5 text-xs sm:px-3';
    $baseClass = "inline-flex $sizeClass items-center justify-center whitespace-nowrap rounded-lg font-extrabold transition focus:outline-none focus:ring-2 focus:ring-offset-2";
    $colorClass = match ($color) {
        'green' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500',
        'red' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'yellow' => 'bg-amber-400 text-amber-950 hover:bg-amber-500 focus:ring-amber-400',
        'blue', 'purple' => 'bg-slate-950 text-white hover:bg-slate-800 focus:ring-slate-700',
        default => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',
    };
@endphp
@if ($href)<a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClass $colorClass"]) }}>{{ $text }}</a>@else<button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClass $colorClass"]) }}>{{ $text }}</button>@endif
