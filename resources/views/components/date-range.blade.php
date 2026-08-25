@props(['start' => null, 'end' => null])

@php
    $from = $start ? \Illuminate\Support\Carbon::parse($start) : null;
    $until = $end ? \Illuminate\Support\Carbon::parse($end) : null;

    if (!$from) {
        $formatted = '-';
        $title = 'Tanggal belum diatur';
    } elseif (!$until || $from->isSameDay($until)) {
        $formatted = $from->format('d/m/Y');
        $title = $from->locale('id')->translatedFormat('d F Y');
    } elseif ($from->format('mY') === $until->format('mY')) {
        $formatted = $from->format('d').'–'.$until->format('d/m/Y');
        $title = $from->locale('id')->translatedFormat('d').'–'.$until->locale('id')->translatedFormat('d F Y');
    } elseif ($from->format('Y') === $until->format('Y')) {
        $formatted = $from->format('d/m').'–'.$until->format('d/m/Y');
        $title = $from->locale('id')->translatedFormat('d F').'–'.$until->locale('id')->translatedFormat('d F Y');
    } else {
        $formatted = $from->format('d/m/Y').'–'.$until->format('d/m/Y');
        $title = $from->locale('id')->translatedFormat('d F Y').'–'.$until->locale('id')->translatedFormat('d F Y');
    }
@endphp

<span title="{{ $title }}" {{ $attributes }}>{{ $formatted }}</span>
