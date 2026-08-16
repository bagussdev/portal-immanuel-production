@foreach($quotations as $quotation)
<tr data-id="{{ $quotation->id }}">
    <td><a href="{{ route('quotations.show',$quotation) }}" class="font-extrabold text-sky-700 hover:text-sky-900 dark:text-white dark:hover:text-red-400">{{ $quotation->quotation_number }}</a></td>
    <td><p class="font-bold text-slate-900 dark:text-white">{{ $quotation->client?->name ?: 'Client manual' }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $quotation->event_name ?: 'Tanpa nama acara' }}</p></td>
    <td class="whitespace-nowrap">{{ optional($quotation->event_date)->format('d/m/Y') ?: '-' }}</td>
    <td><x-status-badge :status="$quotation->status" /></td>
    <td class="whitespace-nowrap text-right font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($quotation->grand_total,0,',','.') }}</td>
    <td class="text-right"><a href="{{ route('quotations.show',$quotation) }}" class="inline-flex rounded-lg bg-sky-50 px-3 py-2 font-bold text-sky-700 hover:bg-sky-100 dark:bg-white/[.06] dark:text-red-400 dark:hover:bg-white/10">Lihat detail</a></td>
</tr>
@endforeach
