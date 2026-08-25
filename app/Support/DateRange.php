<?php

namespace App\Support;

use Carbon\CarbonInterface;

class DateRange
{
    public static function format(?CarbonInterface $start, ?CarbonInterface $end, bool $short = false): string
    {
        if (! $start) return '-';
        if (! $end || $start->isSameDay($end)) return $short ? $start->format('d/m/Y') : $start->translatedFormat('d F Y');
        if ($start->year !== $end->year) return $start->translatedFormat('d F Y').' - '.$end->translatedFormat('d F Y');
        if ($start->month !== $end->month) return $start->translatedFormat('d F').' - '.$end->translatedFormat('d F Y');

        return $start->format('d').' - '.$end->translatedFormat('d F Y');
    }
}
