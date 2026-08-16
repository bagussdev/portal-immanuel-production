<x-app-layout><x-dashboard.sidebar><x-alert-information />
<div class="ip-page">
    <header class="ip-page-header"><div><p class="ip-kicker">Keuangan</p><h1 class="ip-title">Invoice</h1><p class="ip-subtitle">Edit invoice secara fleksibel dan catat pembayaran bertahap sebanyak yang dibutuhkan.</p></div>@can('createinvoice')<a href="{{ route('invoices.create') }}" class="ip-btn-primary">+ Buat invoice draft</a>@endcan</header>
    <form class="ip-card flex flex-col gap-3 p-4 sm:flex-row"><input name="search" value="{{ $search }}" placeholder="Cari nomor, client, atau acara" class="ip-input flex-1"><select name="status" class="ip-input sm:w-56"><option value="">Semua status</option>@foreach(['draft'=>'Draft','unpaid'=>'Belum Dibayar','partial'=>'Sebagian','paid'=>'Lunas','overpaid'=>'Kelebihan','overdue'=>'Terlambat','void'=>'Void'] as $value=>$label)<option value="{{ $value }}" @selected($status===$value)>{{ $label }}</option>@endforeach</select><button class="ip-btn-dark">Terapkan filter</button></form>
    <div class="space-y-3 md:hidden">
        @forelse($invoices as $invoice)
            <x-responsive-disclosure
                title="{{ $invoice->invoice_number ?: 'DRAFT #'.$invoice->id }}"
                description="{{ $invoice->client?->name ?: 'Client manual' }} · {{ $invoice->event_name ?: 'Tanpa nama acara' }}"
                content-class="p-4"
            >
                <x-slot name="meta"><x-status-badge :status="$invoice->status" /></x-slot>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-sky-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Tagihan</dt><dd class="mt-1 font-extrabold text-slate-900 dark:text-white">{{ (int) $invoice->grand_total > 0 ? 'Rp '.number_format($invoice->grand_total,0,',','.') : '-' }}</dd></div>
                    <div class="rounded-xl bg-rose-50 p-3 dark:bg-red-950/20"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-rose-400">Sisa</dt><dd class="mt-1 font-extrabold text-rose-700 dark:text-red-300">{{ (int) $invoice->balance_due > 0 ? 'Rp '.number_format($invoice->balance_due,0,',','.') : '-' }}</dd></div>
                    <div class="col-span-2 rounded-xl bg-emerald-50 p-3 dark:bg-emerald-950/20"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-emerald-500">Sudah dibayar</dt><dd class="mt-1 font-extrabold text-emerald-700 dark:text-emerald-300">{{ (int) $invoice->total_paid > 0 ? 'Rp '.number_format($invoice->total_paid,0,',','.') : '-' }}</dd></div>
                </dl>
                <a href="{{ route('invoices.show',$invoice) }}" class="ip-btn-primary mt-4 w-full">Lihat detail invoice</a>
            </x-responsive-disclosure>
        @empty
            <div class="ip-card p-10 text-center text-sm text-slate-500">Belum ada invoice.</div>
        @endforelse
    </div>
    <div class="ip-card">
        <div class="ip-table-wrap hidden md:block"><table class="ip-table min-w-[980px]"><thead><tr><th>Nomor</th><th>Client / Acara</th><th>Status</th><th class="text-right">Tagihan</th><th class="text-right">Dibayar</th><th class="text-right">Sisa</th><th class="text-right">Aksi</th></tr></thead><tbody>@forelse($invoices as $invoice)@include('invoices._rows',['invoices'=>collect([$invoice])])@empty<tr><td colspan="7" class="py-14 text-center text-slate-500">Belum ada invoice.</td></tr>@endforelse</tbody></table></div>
        <div class="border-t border-slate-200 p-4 dark:border-white/10">{{ $invoices->links() }}</div>
    </div>
</div>
</x-dashboard.sidebar></x-app-layout>
