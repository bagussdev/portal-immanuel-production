@props(['kind', 'zoomMax' => 3])
@php
    $label = $kind === 'profile' ? 'profil' : 'KTP';
    $state = $kind === 'profile' ? 'profile' : 'ktp';
@endphp

<div x-show="{{ $state }}Preview" class="mt-3 space-y-3 rounded-2xl border border-sky-100 bg-sky-50/60 p-3 dark:border-white/10 dark:bg-white/[.025]">
    <label class="block text-[11px] font-bold text-slate-500">
        Geser horizontal
        <input type="range" min="0" max="100" step="1" name="{{ $kind }}_crop_x" x-model.number="{{ $state }}CropX" @input="{{ $state }}TransformChanged = true; $nextTick(() => renderPreview('{{ $state }}'))" class="mt-1 w-full accent-sky-600">
    </label>
    <label class="block text-[11px] font-bold text-slate-500">
        Geser vertikal
        <input type="range" min="0" max="100" step="1" name="{{ $kind }}_crop_y" x-model.number="{{ $state }}CropY" @input="{{ $state }}TransformChanged = true; $nextTick(() => renderPreview('{{ $state }}'))" class="mt-1 w-full accent-sky-600">
    </label>
    <label class="block text-[11px] font-bold text-slate-500">
        Zoom <span class="float-right tabular-nums" x-text="Number({{ $state }}Zoom).toFixed(1) + '×'"></span>
        <input type="range" min="1" max="{{ $zoomMax }}" step="0.1" name="{{ $kind }}_zoom" x-model.number="{{ $state }}Zoom" @input="{{ $state }}TransformChanged = true; $nextTick(() => renderPreview('{{ $state }}'))" class="mt-1 w-full accent-sky-600">
    </label>
    <input type="hidden" name="{{ $kind }}_transform_changed" :value="{{ $state }}TransformChanged ? 1 : 0">
    <p class="text-[10px] leading-4 text-slate-400">Atur posisi {{ $label }} sampai pas di dalam bingkai.</p>
</div>
