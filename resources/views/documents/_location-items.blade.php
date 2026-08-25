@php
    $locations = $document->locations;
    $money = fn($value) => (int) $value > 0 ? 'Rp '.number_format((int) $value, 0, ',', '.') : '';
@endphp
<div class="space-y-4 p-4 sm:p-5">
    @forelse($locations as $location)
        @php($showUnitPrice = $location->items->contains(fn($item) => (int) $item->unit_price > 0))
        <details data-location-disclosure data-responsive-disclosure data-mobile-open="false" class="ip-disclosure overflow-hidden rounded-2xl border border-sky-100 dark:border-white/10">
            <summary class="flex min-h-16 cursor-pointer list-none items-center gap-3 bg-sky-50/70 p-4 outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500 dark:bg-white/[.04]">
                <span class="min-w-0 flex-1">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-sky-600">Lokasi</span>
                    <span class="mt-1 block truncate font-extrabold text-slate-900 dark:text-white">{{ $location->name ?: 'Lokasi belum ditentukan' }}</span>
                    <span class="mt-1 block text-xs text-slate-500">{{ $location->items->count() }} item &middot; Loading: {{ optional($location->loading_date)->translatedFormat('d M Y, H:i') ?: '-' }}@if($location->teardown_date) &middot; Bongkar: {{ optional($location->teardown_date)->translatedFormat('d M Y, H:i') }}@endif</span>
                </span>
                <span class="ip-disclosure-action" aria-hidden="true"><span class="hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-400 sm:inline">Detail</span><svg class="ip-disclosure-chevron h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg></span>
            </summary>
            <div class="ip-table-wrap"><table class="ip-table min-w-[620px]"><thead><tr><th>Item</th><th>Qty</th><th>Panjang</th>@if($showUnitPrice)<th class="text-right">Harga satuan</th>@endif<th class="text-right">Total</th></tr></thead><tbody>
                @foreach($location->items as $item)<tr><td class="font-bold text-slate-900 dark:text-white">{{ $item->item_name }}</td><td>{{ (float) $item->qty }}</td><td>{{ filled($item->length) && (float)$item->length > 0 ? (float)$item->length : '' }}</td>@if($showUnitPrice)<td class="whitespace-nowrap text-right tabular-nums">{{ $money($item->unit_price) }}</td>@endif<td class="whitespace-nowrap text-right font-extrabold tabular-nums">{{ $money($item->total) }}</td></tr>@endforeach
            </tbody></table></div>
        </details>
    @empty
        <p class="py-8 text-center text-sm text-slate-500">Belum ada rincian lokasi.</p>
    @endforelse
</div>
