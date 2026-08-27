<x-app-layout><x-dashboard.sidebar><x-alert-information />
<div class="ip-page">
    <header class="ip-page-header"><div><p class="ip-kicker">Keuangan</p><h1 class="ip-title">Invoice</h1><p class="ip-subtitle">Invoice dan pembayaran client.</p></div>@can('createinvoice')<a href="{{ route('invoices.create') }}" class="ip-btn-primary">+ Buat invoice draft</a>@endcan</header>
    <div class="inline-flex w-fit rounded-xl border border-sky-100 bg-white p-1 dark:border-white/10 dark:bg-white/[.04]"><a href="{{ route('invoices.index') }}" class="rounded-lg px-4 py-2 text-sm font-extrabold {{ !$history ? 'bg-sky-700 text-white dark:bg-red-600' : 'text-slate-500' }}">Aktif</a><a href="{{ route('invoices.index',['history'=>1]) }}" class="rounded-lg px-4 py-2 text-sm font-extrabold {{ $history ? 'bg-sky-700 text-white dark:bg-red-600' : 'text-slate-500' }}">History</a></div>
    <form class="ip-card flex flex-col gap-3 p-4 sm:flex-row"><input name="search" value="{{ $search }}" placeholder="Cari nomor, client, acara, atau lokasi" class="ip-input flex-1"><select name="status" class="ip-input sm:w-56"><option value="">Semua status</option>@foreach(($history ? ['paid'=>'Lunas','overpaid'=>'Lebih bayar selesai','void'=>'Void'] : ['draft'=>'Draft','unpaid'=>'Belum Dibayar','partial'=>'Sebagian','overpaid'=>'Kelebihan','overdue'=>'Terlambat']) as $value=>$label)<option value="{{ $value }}" @selected($status===$value)>{{ $label }}</option>@endforeach</select>@if($history)<input type="hidden" name="history" value="1">@endif @if($order)<input type="hidden" name="order" value="{{ $order }}">@endif<button class="ip-btn-dark">Terapkan filter</button></form>
    <x-order-filter :current="$order" />
    <div class="space-y-3 md:hidden">
        @forelse($invoices as $invoice)
            @php
                $locationLabel = $invoice->locations->pluck('name')->filter()->unique()->implode(', ') ?: ($invoice->location_event ?: '-');
            @endphp
            <x-responsive-disclosure
                :title="$invoice->invoice_number ?: 'DRAFT #'.$invoice->id"
                :description="($invoice->client?->name ?: 'Client manual').' · '.($invoice->event_name ?: 'Tanpa nama acara')"
                content-class="p-4"
            >
                <x-slot name="meta"><x-status-badge :status="$invoice->status" /></x-slot>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Tanggal invoice</dt><dd class="mt-1 text-xs font-extrabold text-slate-800 dark:text-slate-200"><x-date-range :start="$invoice->issue_date ?: $invoice->created_at" /></dd></div>
                    <div class="rounded-xl bg-slate-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Tanggal event</dt><dd class="mt-1 text-xs font-extrabold text-slate-800 dark:text-slate-200"><x-date-range :start="$invoice->event_date" :end="$invoice->event_end_date" /></dd></div>
                    <div class="col-span-2 rounded-xl bg-slate-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Lokasi</dt><dd class="mt-1 text-xs font-extrabold text-slate-800 dark:text-slate-200">{{ $locationLabel }}</dd></div>
                    <div class="rounded-xl bg-sky-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Tagihan</dt><dd class="mt-1 font-extrabold text-slate-900 dark:text-white">{{ (int) $invoice->grand_total > 0 ? 'Rp '.number_format($invoice->grand_total,0,',','.') : '-' }}</dd></div>
                    <div class="rounded-xl bg-rose-50 p-3 dark:bg-red-950/20"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-rose-400">Sisa</dt><dd class="mt-1 font-extrabold text-rose-700 dark:text-red-300">{{ (int) $invoice->balance_due > 0 ? 'Rp '.number_format($invoice->balance_due,0,',','.') : '-' }}</dd></div>
                    <div class="col-span-2 rounded-xl bg-emerald-50 p-3 dark:bg-emerald-950/20">
                        <dt class="text-[10px] font-extrabold uppercase tracking-wide text-emerald-500">Pembayaran</dt>
                        <dd class="mt-2 space-y-1.5">
                            @forelse($invoice->payments as $payment)
                                <span class="flex items-center justify-between gap-3 text-xs font-extrabold text-emerald-700 dark:text-emerald-300"><span>{{ $payment->reference ?: 'Pembayaran '.($loop->iteration) }}</span><span class="whitespace-nowrap">Rp {{ number_format($payment->amount,0,',','.') }}</span></span>
                            @empty
                                <span class="text-xs font-bold text-slate-400">Belum ada pembayaran</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>
                <a href="{{ route('invoices.show',$invoice) }}" class="ip-btn-primary mt-4 w-full">Lihat detail invoice</a>
                @can('adddp')@if(in_array($invoice->status,['unpaid','partial','overdue','overpaid']) && !$invoice->resolved_at)<form method="POST" action="{{ route('invoices.complete',$invoice) }}" onsubmit="return confirmAndLoad('Selesaikan invoice ini?')" class="mt-2">@csrf<input type="hidden" name="paid_at" value="{{ today()->format('Y-m-d') }}"><button class="ip-btn w-full bg-emerald-600 text-white">Selesaikan</button></form>@endif @endcan
                @can('deleteinvoice')
                    @if(in_array($invoice->status, ['draft', 'void']))
                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirmAndLoad('Hapus invoice ini dari daftar?')" class="mt-2">@csrf @method('DELETE')<button class="ip-btn-danger w-full">Hapus</button></form>
                    @endif
                @endcan
            </x-responsive-disclosure>
        @empty
            <div class="ip-card p-10 text-center text-sm text-slate-500">Belum ada invoice.</div>
        @endforelse
    </div>
    <div class="ip-card">
        <div class="ip-table-wrap hidden md:block"><table class="ip-table min-w-[1500px]"><thead><tr><th>Nomor</th><th>Client</th><th>Acara</th><th>Lokasi</th><th>Tgl invoice</th><th>Tgl event</th><th>Status</th><th class="text-right">Tagihan</th><th>Pembayaran</th><th class="text-right">Sisa</th><th class="text-right">Aksi</th></tr></thead><tbody>@forelse($invoices as $invoice)@include('invoices._rows',['invoices'=>collect([$invoice])])@empty<tr><td colspan="11" class="py-14 text-center text-slate-500">Belum ada invoice.</td></tr>@endforelse</tbody></table></div>
        <div class="border-t border-slate-200 p-4 dark:border-white/10">{{ $invoices->links() }}</div>
    </div>
</div>
</x-dashboard.sidebar></x-app-layout>
