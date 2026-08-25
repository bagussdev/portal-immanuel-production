@foreach($quotations as $quotation)
<tr data-id="{{ $quotation->id }}">
    <td><a href="{{ route('quotations.show',$quotation) }}" class="font-extrabold text-sky-700 hover:text-sky-900 dark:text-white dark:hover:text-red-400">{{ $quotation->quotation_number }}</a></td>
    <td><p class="font-bold text-slate-900 dark:text-white">{{ $quotation->client?->name ?: 'Client manual' }}</p></td>
    <td class="max-w-[220px]"><p class="truncate text-sm font-semibold text-slate-700 dark:text-slate-300" title="{{ $quotation->event_name }}">{{ $quotation->event_name ?: '-' }}</p></td>
    <td class="whitespace-nowrap text-xs font-bold text-slate-600 dark:text-slate-300"><x-date-range :start="$quotation->quotation_date ?: $quotation->created_at" /></td>
    <td class="whitespace-nowrap text-xs font-bold text-slate-600 dark:text-slate-300"><x-date-range :start="$quotation->event_date" :end="$quotation->event_end_date" /></td>
    <td><x-status-badge :status="$quotation->status" /></td>
    <td class="whitespace-nowrap text-right font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($quotation->grand_total,0,',','.') }}</td>
    <td class="text-right"><a href="{{ route('quotations.show',$quotation) }}" class="inline-flex rounded-lg bg-sky-50 px-3 py-2 font-bold text-sky-700 hover:bg-sky-100 dark:bg-white/[.06] dark:text-red-400 dark:hover:bg-white/10">Lihat detail</a></td>
</tr>
@endforeach
