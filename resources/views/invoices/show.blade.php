@php
    $priceGroupCounts = $invoice->items->whereNotNull('price_group')->countBy('price_group');
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
                        <a href="{{ route('invoices.index') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-bold text-sky-100 hover:text-white dark:text-slate-400 dark:hover:text-white">
                            <span aria-hidden="true">&larr;</span> Kembali ke invoice
                        </a>
                        <p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-sky-200 dark:text-red-400">Detail invoice</p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $invoice->invoice_number ?: 'DRAFT #'.$invoice->id }}</h1>
                        <p class="mt-2 text-sm text-sky-100/80 dark:text-slate-400">
                            {{ $invoice->client?->name ?: 'Client belum diisi' }} &middot; {{ $invoice->event_name ?: 'Tanpa nama acara' }}
                        </p>
                        <span class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-bold text-white">
                            Tipe event: {{ $invoice->workFlowLabel() }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$invoice->status" />
                        @can('adddp')@if(in_array($invoice->status,['unpaid','partial','overdue','overpaid']) && !$invoice->resolved_at)<form method="POST" action="{{ route('invoices.complete',$invoice) }}" onsubmit="return confirmAndLoad('Selesaikan invoice dan catat pelunasan sisanya?')">@csrf<input type="hidden" name="paid_at" value="{{ today()->format('Y-m-d') }}"><button class="ip-btn bg-emerald-500 text-white hover:bg-emerald-600">Selesaikan</button></form>@endif @endcan

                        @if ($invoice->invoice_number)
                            <a target="_blank" href="{{ route('invoices.export.pdf', $invoice) }}" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">
                                Lihat PDF
                            </a>
                            @if($invoice->fieldJob)
                                <a href="{{ route('field-jobs.show', $invoice->fieldJob) }}" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">
                                    Pekerjaan {{ $invoice->fieldJob->job_number }}
                                </a>
                            @endif
                        @endif

                        @can('editinvoice')
                            @if ($invoice->status !== 'void')
                                <a href="{{ route('invoices.edit', $invoice) }}" class="ip-btn bg-white text-slate-950 hover:bg-sky-50">
                                    Edit invoice
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                <div class="ip-card p-4 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total tagihan</p>
                    <p class="mt-3 min-h-7 text-base font-extrabold text-slate-950 dark:text-white sm:text-xl">{{ $rupiahOrBlank($invoice->grand_total) }}</p>
                </div>
                <div class="ip-card p-4 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Sudah dibayar</p>
                    <p class="mt-3 min-h-7 text-base font-extrabold text-emerald-600 dark:text-emerald-400 sm:text-xl">{{ $rupiahOrBlank($invoice->total_paid) }}</p>
                </div>
                <div class="ip-card border-rose-100 bg-rose-50/70 p-4 dark:border-red-500/20 dark:bg-red-950/20 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500 dark:text-red-400">Sisa tagihan</p>
                    <p class="mt-3 min-h-7 text-base font-extrabold text-rose-700 dark:text-red-300 sm:text-xl">{{ $rupiahOrBlank($invoice->balance_due) }}</p>
                </div>
                <div class="ip-card p-4 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Jatuh tempo</p>
                    <p class="mt-3 text-sm font-extrabold text-slate-900 dark:text-white sm:text-lg">{{ optional($invoice->due_date)->translatedFormat('d F Y') ?: '-' }}</p>
                </div>
            </div>

            @if ($invoice->status === 'draft')
                @can('issueinvoice')
                    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-500/30 dark:bg-amber-950/20">
                        <div class="flex gap-3">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-400 font-extrabold text-amber-950">!</span>
                            <div class="min-w-0 flex-1">
                                <h2 class="font-extrabold text-amber-950 dark:text-amber-100">Invoice masih draft</h2>
                                <p class="mt-1 text-sm leading-6 text-amber-700 dark:text-amber-300">Periksa item dan harga. Nomor resmi baru dibuat saat invoice diterbitkan.</p>
                                <form method="POST" action="{{ route('invoices.issue', $invoice) }}" class="mt-4 flex flex-wrap items-end gap-3">
                                    @csrf
                                    <label class="text-xs font-bold text-amber-950 dark:text-amber-100">Tanggal terbit
                                        <input type="date" name="issue_date" value="{{ today()->format('Y-m-d') }}" required class="ip-input mt-1">
                                    </label>
                                    <label class="text-xs font-bold text-amber-950 dark:text-amber-100">Jatuh tempo
                                        <input type="date" name="due_date" value="{{ optional($invoice->event_date)->format('Y-m-d') }}" class="ip-input mt-1">
                                    </label>
                                    <button class="ip-btn-dark">Terbitkan invoice</button>
                                </form>
                            </div>
                        </div>
                    </section>
                @endcan
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),380px]">
                <x-responsive-disclosure kicker="Rincian" title="Item invoice" description="{{ $invoice->items->count() }} item pekerjaan" :mobile-open="true" content-class="p-0">
                    @include('documents._location-items', ['document' => $invoice])
                </x-responsive-disclosure>

                <aside class="rounded-2xl bg-sky-950 p-5 text-white shadow-xl dark:bg-[#0b0c0f]">
                    <p class="text-[11px] font-extrabold uppercase tracking-[.18em] text-sky-300 dark:text-red-400">Perhitungan</p>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between text-slate-400"><dt>Subtotal</dt><dd class="font-bold text-white">{{ $rupiahOrBlank($invoice->subtotal) }}</dd></div>
                        @if ((int) $invoice->discount_value > 0)<div class="flex justify-between text-slate-400"><dt>Diskon @if ($invoice->discount_percent !== null) ({{ $invoice->discount_percent }}%) @endif</dt><dd class="font-bold text-sky-300 dark:text-red-300">- {{ $rupiahOrBlank($invoice->discount_value) }}</dd></div>@endif
                        @if ((int) $invoice->tax_value > 0)<div class="flex justify-between text-slate-400"><dt>Potongan pajak @if ($invoice->tax_percent !== null) ({{ $invoice->tax_percent }}%) @endif</dt><dd class="font-bold text-sky-300 dark:text-red-300">- {{ $rupiahOrBlank($invoice->tax_value) }}</dd></div>@endif
                        <div class="flex justify-between border-t border-white/10 pt-4"><dt class="font-bold">Total</dt><dd class="text-xl font-extrabold">{{ $rupiahOrBlank($invoice->grand_total) }}</dd></div>
                    </dl>
                    <div class="mt-6 space-y-2 border-t border-white/10 pt-5 text-xs text-slate-400">
                        <p><strong class="text-slate-200">Loading:</strong> {{ optional($invoice->loading_date)->translatedFormat('d M Y H:i') ?: '-' }}</p>
                        <p><strong class="text-slate-200">Acara:</strong> {{ optional($invoice->event_date)->translatedFormat('d M Y') ?: '-' }}</p>
                        <p><strong class="text-slate-200">Bongkar:</strong> {{ optional($invoice->bongkaran_date)->translatedFormat('d M Y H:i') ?: '-' }}</p>
                    </div>
                </aside>
            </div>

            @include('documents._bank-detail-card', ['bankDetail' => $invoice->bankDetail])

            <x-responsive-disclosure
                kicker="Pembayaran bertahap"
                title="Riwayat transfer"
                description="{{ $invoice->payments->whereNull('voided_at')->count() }} pembayaran aktif"
                content-class="p-0"
            >
                <div class="space-y-3 p-4 md:hidden">
                    @forelse ($invoice->payments->sortBy('paid_at') as $payment)
                        <article class="rounded-2xl border border-sky-100 p-4 {{ $payment->voided_at ? 'opacity-55' : 'bg-sky-50/45' }} dark:border-white/10 dark:bg-white/[.025]">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white">{{ $payment->method }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $payment->paid_at->translatedFormat('d M Y') }} &middot; {{ $payment->reference ?: 'Tanpa referensi' }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold {{ $payment->voided_at ? 'line-through text-slate-400' : 'text-emerald-600 dark:text-emerald-400' }}">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-sky-100 pt-3 text-xs dark:border-white/10">
                                <span class="text-slate-500">Penerima: <strong class="text-slate-700 dark:text-slate-300">{{ $payment->receiver?->name ?: '-' }}</strong></span>
                                <span class="flex items-center gap-3">
                                    @if ($payment->attachment)
                                        <a class="font-bold text-sky-700 dark:text-sky-300" href="{{ route('invoices.payments.attachment', [$invoice, $payment]) }}">Bukti</a>
                                    @endif
                                    @if (! $payment->voided_at)
                                        @can('voidpayment')
                                            <form method="POST" action="{{ route('invoices.payments.void', [$invoice, $payment]) }}" onsubmit="const r=prompt('Alasan pembatalan pembayaran:');if(!r)return false;this.querySelector('[name=reason]').value=r">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="reason">
                                                <button class="font-bold text-red-600 dark:text-red-400">Batalkan</button>
                                            </form>
                                        @endcan
                                    @else
                                        <x-status-badge status="void" size="small" />
                                    @endif
                                </span>
                            </div>
                        </article>
                    @empty
                        <p class="py-8 text-center text-sm text-slate-500">Belum ada pembayaran.</p>
                    @endforelse
                </div>
                <div class="ip-table-wrap hidden md:block">
                    <table class="ip-table min-w-[800px]">
                        <thead><tr><th>Tanggal</th><th>Metode</th><th>Referensi</th><th class="text-right">Nominal</th><th>Penerima</th><th class="text-right">Aksi</th></tr></thead>
                        <tbody>
                            @forelse ($invoice->payments->sortBy('paid_at') as $payment)
                                <tr class="{{ $payment->voided_at ? 'opacity-50' : '' }}">
                                    <td>{{ $payment->paid_at->translatedFormat('d M Y') }}</td>
                                    <td class="font-bold">{{ $payment->method }}</td>
                                    <td>{{ $payment->reference ?: '-' }}</td>
                                    <td class="text-right font-extrabold {{ $payment->voided_at ? 'line-through' : 'text-emerald-600 dark:text-emerald-400' }}">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>{{ $payment->receiver?->name ?: '-' }}</td>
                                    <td class="text-right">
                                        @if ($payment->attachment)
                                            <a class="mr-2 font-bold text-sky-700 hover:text-sky-900 dark:text-slate-300 dark:hover:text-white" href="{{ route('invoices.payments.attachment', [$invoice, $payment]) }}">Bukti</a>
                                        @endif
                                        @if (! $payment->voided_at)
                                            @can('voidpayment')
                                                <form method="POST" action="{{ route('invoices.payments.void', [$invoice, $payment]) }}" class="inline" onsubmit="const r=prompt('Alasan pembatalan pembayaran:');if(!r)return false;this.querySelector('[name=reason]').value=r">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="reason">
                                                    <button class="font-bold text-red-600 dark:text-red-400">Batalkan</button>
                                                </form>
                                            @endcan
                                        @else
                                            <x-status-badge status="void" size="small" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-12 text-center text-slate-500">Belum ada pembayaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-responsive-disclosure>

            @if (! in_array($invoice->status, ['draft', 'void']))
                @can('adddp')
                    <x-responsive-disclosure
                        kicker="Pembayaran"
                        title="Catat pembayaran berikutnya"
                        description="DP dapat dicatat beberapa kali dan langsung mengurangi sisa tagihan."
                        class="border-emerald-200 bg-emerald-50/70 dark:border-emerald-500/30 dark:bg-emerald-950/20"
                    >
                        <form method="POST" enctype="multipart/form-data" action="{{ route('invoices.payments.store', $invoice) }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                            @csrf
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Tanggal<input type="date" name="paid_at" value="{{ today()->format('Y-m-d') }}" required class="ip-input mt-1"></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Nominal<input name="amount" required inputmode="numeric" class="ip-input mt-1" placeholder="Rp 0"></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Metode<select name="method" class="ip-input mt-1"><option>Transfer</option><option>Tunai</option><option>Giro</option><option>Lainnya</option></select></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Referensi<input name="reference" class="ip-input mt-1"></label>
                            <label class="text-xs font-bold text-emerald-900 dark:text-emerald-100">Bukti<input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="mt-2 block w-full text-xs"></label>
                            <button class="ip-btn min-h-11 self-end bg-emerald-600 text-white hover:bg-emerald-700">Tambah pembayaran</button>
                        </form>
                    </x-responsive-disclosure>
                @endcan
            @endif

            @if ($invoice->status !== 'void')
                @can('voidinvoice')
                    <form method="POST" action="{{ route('invoices.void', $invoice) }}" class="flex justify-end" onsubmit="const r=prompt('Alasan VOID invoice:');if(!r)return false;this.querySelector('[name=reason]').value=r">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="reason">
                        <button class="ip-btn-danger">Void invoice</button>
                    </form>
                @endcan
            @endif
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
