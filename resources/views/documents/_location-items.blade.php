@php
    $locations = $document->locations;
    $money = fn($value) => (int) $value > 0 ? 'Rp '.number_format((int) $value, 0, ',', '.') : '';
@endphp
<div class="space-y-4 p-4 sm:p-5">
    @forelse($locations as $location)
        @php($showUnitPrice = $location->items->contains(fn($item) => (int) $item->unit_price > 0))
        <section class="overflow-hidden rounded-2xl border border-sky-100 dark:border-white/10">
            <header class="bg-sky-50/70 p-4 dark:bg-white/[.04]">
                <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-sky-600">Lokasi</p><h3 class="mt-1 font-extrabold text-slate-900 dark:text-white">{{ $location->name ?: 'Lokasi belum ditentukan' }}</h3></div>
                <p class="mt-2 text-xs text-slate-500">Loading: {{ optional($location->loading_date)->translatedFormat('d M Y, H:i') ?: '-' }}@if($location->teardown_date) &middot; Bongkar: {{ optional($location->teardown_date)->translatedFormat('d M Y, H:i') }}@endif</p>
            </header>
            <div class="ip-table-wrap"><table class="ip-table min-w-[620px]"><thead><tr><th>Item</th><th>Qty</th><th>Panjang</th>@if($showUnitPrice)<th class="text-right">Harga satuan</th>@endif<th class="text-right">Total</th></tr></thead><tbody>
                @foreach($location->items as $item)<tr><td class="font-bold text-slate-900 dark:text-white">{{ $item->item_name }}</td><td>{{ (float) $item->qty }}</td><td>{{ filled($item->length) && (float)$item->length > 0 ? (float)$item->length : '' }}</td>@if($showUnitPrice)<td class="text-right">{{ $money($item->unit_price) }}</td>@endif<td class="text-right font-extrabold">{{ $money($item->total) }}</td></tr>@endforeach
            </tbody></table></div>
        </section>
    @empty
        <p class="py-8 text-center text-sm text-slate-500">Belum ada rincian lokasi.</p>
    @endforelse
</div>
