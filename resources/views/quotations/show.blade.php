@php
    $priceGroupCounts = $quotation->items->whereNotNull('price_group')->countBy('price_group');
    $renderedPriceGroups = [];
    $rupiahOrBlank = fn ($value) => (int) $value > 0 ? 'Rp ' . number_format((int) $value, 0, ',', '.') : '';
@endphp
<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="ip-page">
            <header class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-sky-700 via-sky-800 to-slate-950 p-6 text-white shadow-xl dark:from-[#11151e] dark:via-[#0b0c0f] dark:to-black sm:p-8">
                <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-sky-300/20 blur-3xl dark:bg-red-600/25"></div>
                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <a href="{{ route('quotations.index') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-bold text-sky-100 hover:text-white dark:text-slate-400 dark:hover:text-white"><span aria-hidden="true">&larr;</span> Kembali ke quotation</a>
                        <p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-sky-200 dark:text-red-400">Detail quotation</p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $quotation->quotation_number }}</h1>
                        <p class="mt-2 text-sm text-sky-100/80 dark:text-slate-400">{{ $quotation->client?->name ?: 'Client belum diisi' }} &middot; {{ $quotation->event_name ?: 'Tanpa nama acara' }}</p>
                        <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-bold text-white">
                            <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1">Quotation: <x-date-range :start="$quotation->quotation_date ?: $quotation->created_at" class="ml-1" /></span>
                            <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1">Event: <x-date-range :start="$quotation->event_date" :end="$quotation->event_end_date" class="ml-1" /></span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$quotation->status" />
                        <a href="{{ route('quotations.export.pdf', [$quotation, $quotation->pdfFilename()]) }}" target="_blank" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">Lihat PDF</a>
                        <a href="{{ route('quotations.export.pdf', [$quotation, $quotation->pdfFilename(), 'download' => 1]) }}" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">Unduh PDF</a>
                        @can('editquotation')
                            @if ($quotation->status !== 'approved')
                                <a href="{{ route('quotations.edit', $quotation) }}" class="ip-btn bg-white text-slate-950 hover:bg-sky-50">Edit quotation</a>
                            @endif
                        @endcan
                    </div>
                </div>
            </header>

            @if ($quotation->invoice)
                <a href="{{ route('invoices.show', $quotation->invoice) }}" class="flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-950/20 dark:text-emerald-100">
                    <span><strong class="block">Invoice draft sudah dibuat</strong><span class="mt-1 block text-sm text-emerald-700 dark:text-emerald-300">Lanjutkan penyesuaian item atau harga di invoice.</span></span><span class="text-xl" aria-hidden="true">&rarr;</span>
                </a>
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),380px]">
                <x-responsive-disclosure kicker="Rincian" title="Rincian pekerjaan" description="{{ $quotation->items->count() }} item quotation" :mobile-open="true" content-class="p-0">
                    @include('documents._location-items', ['document' => $quotation])
                    @if ($quotation->description)
                        <div class="m-5 rounded-xl bg-sky-50 p-4 dark:bg-white/[.04]"><p class="text-xs font-extrabold uppercase tracking-wide text-slate-400">Catatan</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-300">{{ $quotation->description }}</p></div>
                    @endif
                </x-responsive-disclosure>

                <aside class="space-y-4">
                    <section class="rounded-2xl bg-sky-950 p-5 text-white shadow-xl dark:bg-[#0b0c0f]">
                        <p class="text-[11px] font-extrabold uppercase tracking-[.18em] text-sky-300 dark:text-red-400">Ringkasan</p>
                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex justify-between text-slate-400"><dt>Subtotal</dt><dd class="font-bold text-white">{{ $rupiahOrBlank($quotation->subtotal) }}</dd></div>
                            @if ((int) $quotation->discount > 0)<div class="flex justify-between text-slate-400"><dt>Diskon</dt><dd class="font-bold text-sky-300 dark:text-red-300">- {{ $rupiahOrBlank($quotation->discount) }}</dd></div>@endif
                            @if ((int) $quotation->tax_value > 0)<div class="flex justify-between text-slate-400"><dt>Potongan pajak</dt><dd class="font-bold text-sky-300 dark:text-red-300">- {{ $rupiahOrBlank($quotation->tax_value) }}</dd></div>@endif
                            <div class="flex justify-between border-t border-white/10 pt-4"><dt class="font-bold">Total</dt><dd class="text-xl font-extrabold">{{ $rupiahOrBlank($quotation->grand_total) }}</dd></div>
                        </dl>
                    </section>
                    <x-responsive-disclosure kicker="Operasional" title="Jadwal" description="Lokasi, acara, loading, dan bongkar.">
                        <dl class="space-y-3 text-sm">
                            <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Lokasi</dt><dd class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300">{{ $quotation->location_event ?: '-' }}</dd></div>
                            <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Acara</dt><dd class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300"><x-date-range :start="$quotation->event_date" :end="$quotation->event_end_date" /></dd></div>
                            <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Loading</dt><dd class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300">{{ optional($quotation->loading_date)->translatedFormat('d F Y H:i') ?: '-' }}</dd></div>
                            <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Bongkar</dt><dd class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300">{{ optional($quotation->bongkaran_date)->translatedFormat('d F Y H:i') ?: '-' }}</dd></div>
                        </dl>
                    </x-responsive-disclosure>
                    @include('documents._bank-detail-card', ['bankDetail' => $quotation->bankDetail])
                </aside>
            </div>

            @if (! $quotation->invoice)
                <div class="flex flex-wrap justify-end gap-3">
                    @if (! in_array($quotation->status, ['approved', 'cancelled']))
                        @can('editquotation')
                            <form method="POST" action="{{ route('quotations.cancel', $quotation) }}" onsubmit="return confirmAndLoad('Batalkan quotation ini?')">@csrf<button class="ip-btn-danger">Batalkan</button></form>
                        @endcan
                        @can('approvequotation')
                            <form method="POST" action="{{ route('quotations.acc', $quotation) }}" onsubmit="return confirmAndLoad('Setujui quotation dan buat invoice draft otomatis?')">@csrf<button class="ip-btn min-h-10 bg-emerald-600 text-white hover:bg-emerald-700">Setujui & buat invoice draft</button></form>
                        @endcan
                    @endif
                    @can('deletequotation')
                        <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" onsubmit="return confirmAndLoad('Hapus quotation ini dari daftar?')">@csrf @method('DELETE')<button class="ip-btn-danger">Hapus quotation</button></form>
                    @endcan
                </div>
            @endif
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
