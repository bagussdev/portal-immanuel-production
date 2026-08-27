<x-app-layout><x-dashboard.sidebar><x-alert-information />
<div class="ip-page">
    <header class="ip-page-header"><div><p class="ip-kicker">Penjualan</p><h1 class="ip-title">Quotation</h1><p class="ip-subtitle">Penawaran client dan status persetujuan.</p></div>@can('createquotation')<a href="{{ route('quotations.create') }}" class="ip-btn-primary">+ Buat quotation</a>@endcan</header>
    <div class="inline-flex w-fit rounded-xl border border-sky-100 bg-white p-1 dark:border-white/10 dark:bg-white/[.04]"><a href="{{ route('quotations.index') }}" class="rounded-lg px-4 py-2 text-sm font-extrabold {{ !$history ? 'bg-sky-700 text-white dark:bg-red-600' : 'text-slate-500' }}">Aktif</a><a href="{{ route('quotations.index',['history'=>1]) }}" class="rounded-lg px-4 py-2 text-sm font-extrabold {{ $history ? 'bg-sky-700 text-white dark:bg-red-600' : 'text-slate-500' }}">History</a></div>
    <form class="ip-card flex flex-col gap-3 p-4 sm:flex-row"><input name="search" value="{{ $search }}" placeholder="Cari nomor, client, atau acara" class="ip-input flex-1"><select name="status" class="ip-input sm:w-52"><option value="">Semua status</option>@foreach(($history ? ['approved'=>'Disetujui','rejected'=>'Ditolak','cancelled'=>'Dibatalkan'] : ['draft'=>'Draft','sent'=>'Dikirim']) as $value=>$label)<option value="{{ $value }}" @selected($status===$value)>{{ $label }}</option>@endforeach</select>@if($history)<input type="hidden" name="history" value="1">@endif @if($order)<input type="hidden" name="order" value="{{ $order }}">@endif<button class="ip-btn-dark">Terapkan filter</button></form>
    <x-order-filter :current="$order" />
    <div class="space-y-3 md:hidden">
        @forelse($quotations as $quotation)
            <x-responsive-disclosure
                :title="$quotation->quotation_number"
                :description="($quotation->client?->name ?: 'Client manual').' · '.($quotation->event_name ?: 'Tanpa nama acara')"
                content-class="p-4"
            >
                <x-slot name="meta"><x-status-badge :status="$quotation->status" /></x-slot>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Tanggal quotation</dt><dd class="mt-1 text-xs font-extrabold text-slate-800 dark:text-slate-200"><x-date-range :start="$quotation->quotation_date ?: $quotation->created_at" /></dd></div>
                    <div class="rounded-xl bg-slate-50 p-3 dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Tanggal event</dt><dd class="mt-1 text-xs font-extrabold text-slate-800 dark:text-slate-200"><x-date-range :start="$quotation->event_date" :end="$quotation->event_end_date" /></dd></div>
                    <div class="rounded-xl bg-sky-50 p-3 text-right dark:bg-white/[.04]"><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Total</dt><dd class="mt-1 font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($quotation->grand_total,0,',','.') }}</dd></div>
                </dl>
                <a href="{{ route('quotations.show',$quotation) }}" class="ip-btn-primary mt-4 w-full">Lihat detail quotation</a>
                @can('deletequotation')
                    @if(! $quotation->invoice)
                        <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" onsubmit="return confirmAndLoad('Hapus quotation ini dari daftar?')" class="mt-2">@csrf @method('DELETE')<button class="ip-btn-danger w-full">Hapus</button></form>
                    @endif
                @endcan
            </x-responsive-disclosure>
        @empty
            <div class="ip-card p-10 text-center text-sm text-slate-500">Belum ada quotation. Mulai dari penawaran pertama.</div>
        @endforelse
    </div>
    <div class="ip-card">
        <div class="ip-table-wrap hidden md:block"><table class="ip-table min-w-[1120px]"><thead><tr><th>Nomor</th><th>Client</th><th>Acara</th><th>Tgl quotation</th><th>Tgl event</th><th>Status</th><th class="text-right">Total</th><th class="text-right">Aksi</th></tr></thead><tbody>@forelse($quotations as $quotation)@include('quotations._rows',['quotations'=>collect([$quotation])])@empty<tr><td colspan="8" class="py-14 text-center text-slate-500">Belum ada quotation. Mulai dari penawaran pertama.</td></tr>@endforelse</tbody></table></div>
        <div class="border-t border-slate-200 p-4 dark:border-white/10">{{ $quotations->links() }}</div>
    </div>
</div>
</x-dashboard.sidebar></x-app-layout>
