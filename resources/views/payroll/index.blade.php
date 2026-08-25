<x-app-layout><x-dashboard.sidebar><x-alert-information />
@php
    $periodName = \Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F').' '.$year;
    $periodStatus = $locks['period_exists'] ? strtolower($period->status) : 'unknown';
    $pct = $stats['total'] > 0 ? round($stats['paid'] / $stats['total'] * 100) : 0;
@endphp
<div class="ip-page">
    <header class="ip-page-header"><div><p class="ip-kicker">Keuangan tim</p><h1 class="ip-title">Penggajian</h1><p class="ip-subtitle">Slip dan pembayaran per periode.</p></div><div class="flex flex-wrap items-center gap-2">@if($locks['period_exists'])<x-status-badge :status="$periodStatus" />@else<x-status-badge status="unknown" label="Belum dibuka" />@endif<span class="text-sm font-extrabold text-slate-900">{{ $periodName }}</span></div></header>

    <section class="ip-card p-4">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <form method="GET" action="{{ route('payroll.index') }}" class="grid flex-1 gap-3 sm:grid-cols-[160px,110px,1fr,auto]" onsubmit="showFullScreenLoader();">
                <select name="month" class="ip-input">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m===$month)>{{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}</option>@endfor</select>
                <input type="number" name="year" value="{{ $year }}" class="ip-input">
                <input name="search" value="{{ $search }}" placeholder="Cari nama karyawan" class="ip-input"><input type="hidden" name="per_page" value="{{ $perPage }}">
                <button class="ip-btn-dark">Tampilkan</button>
            </form>
            <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                @can('addpayroll')<a href="{{ route('payroll.create',['month'=>$month,'year'=>$year]) }}" onclick="showFullScreenLoader();" class="ip-btn-primary w-full sm:w-auto {{ $locks['can_add'] ? '' : 'pointer-events-none opacity-50' }}">+ Tambah slip</a>@endcan
                @can('paypayroll')@if($locks['period_exists'] && in_array($periodStatus,['open','reopen'],true) && $stats['draft'] > 0)<form action="{{ route('payroll.period.pay-all',$period) }}?month={{ $month }}&year={{ $year }}" method="POST" onsubmit="return confirmAndLoad('Bayar semua {{ $stats['draft'] }} slip draft pada periode ini?')">@csrf @method('PATCH')<button class="ip-btn w-full bg-emerald-600 text-white hover:bg-emerald-700 sm:w-auto">Bayar semua</button></form>@endif @endcan
                @can('managepayroll')
                    @if($locks['can_open'])<form action="{{ route('payroll.period.open') }}" method="POST" onsubmit="return confirmAndLoad('Buka periode penggajian ini?')">@csrf<input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="year" value="{{ $year }}"><button class="ip-btn-primary w-full sm:w-auto">Buka periode</button></form>@endif
                    @if($locks['period_exists'] && in_array($periodStatus,['open','reopen'],true))<form action="{{ route('payroll.period.close',$period) }}?month={{ $month }}&year={{ $year }}" method="POST" onsubmit="return confirmAndLoad('Tutup periode ini? Semua slip harus sudah dibayar.')">@csrf @method('PATCH')<button class="ip-btn-danger w-full sm:w-auto">Tutup periode</button></form>@endif
                    @if($locks['can_reopen'])<form action="{{ route('payroll.period.reopen',$period) }}?month={{ $month }}&year={{ $year }}" method="POST" onsubmit="return confirmAndLoad('Buka kembali periode ini?')">@csrf @method('PATCH')<button class="ip-btn-secondary w-full sm:w-auto">Buka kembali</button></form>@endif
                @endcan
            </div>
        </div>
        @if(!$locks['period_exists'])<p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs font-semibold leading-5 text-amber-700">@if($locks['is_current'])Periode bulan berjalan belum dibuka. Master atau Admin dapat membukanya untuk mulai menginput slip.@elseif($locks['is_past'])Periode lampau hanya bisa digunakan apabila sebelumnya pernah dibuat lalu ditutup.@else Periode mendatang belum dapat dibuka.@endif</p>@endif
    </section>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        <div class="ip-card p-4 sm:p-5"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 sm:text-[11px]">Komponen gaji</p><p class="mt-3 break-words text-base font-extrabold text-slate-950 sm:text-xl">Rp {{ number_format($stats['base'],0,',','.') }}</p></div>
        <div class="ip-card p-4 sm:p-5"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 sm:text-[11px]">Potongan</p><p class="mt-3 break-words text-base font-extrabold text-red-600 sm:text-xl">Rp {{ number_format($stats['ded'],0,',','.') }}</p></div>
        <div class="ip-card bg-slate-950 p-4 text-white sm:p-5"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 sm:text-[11px]">Total bersih</p><p class="mt-3 break-words text-base font-extrabold sm:text-xl">Rp {{ number_format($stats['net'],0,',','.') }}</p></div>
        <div class="ip-card p-4 sm:p-5"><div class="flex items-center justify-between gap-2"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 sm:text-[11px]">Progress bayar</p><strong class="text-xs text-emerald-600 sm:text-sm">{{ $stats['paid'] }}/{{ $stats['total'] }}</strong></div><div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-emerald-500" style="width:{{ $pct }}%"></div></div><p class="mt-2 text-xs font-semibold text-slate-400">{{ $pct }}% selesai</p></div>
    </div>

    <div class="ip-card" id="payrollList" data-month="{{ $month }}" data-year="{{ $year }}" data-search="{{ $search }}" data-changes-url="{{ route('payroll.sync.changes') }}" data-rows-url="{{ route('payroll.rows') }}" data-latest-ts="{{ $latestTs ?? '' }}">
        <div class="ip-table-wrap"><table class="ip-table min-w-[900px]"><thead><tr><th>No</th><th>Karyawan</th><th class="text-right">Gaji</th><th class="text-right">Potongan</th><th class="text-right">Bersih</th><th>Status</th><th class="text-center">Aksi</th></tr></thead><tbody id="payroll_tbody">
            @forelse($rows as $row)@include('payroll._rows',['rows'=>collect([$row]),'rowNumber'=>$loop->iteration+($rows->currentPage()-1)*$rows->perPage()])@empty<tr><td colspan="7" class="py-14 text-center text-slate-500">@if(!$locks['period_exists'])Periode belum dibuka.@else Belum ada slip gaji pada periode ini.@endif</td></tr>@endforelse
        </tbody></table></div><div class="border-t border-slate-200 p-4"><x-per-page-selector :route="'payroll.index'" :perPage="$perPage" :search="$search" :items="$rows" /></div>
    </div>
</div>

@push('scripts')<script>
document.addEventListener('DOMContentLoaded',()=>{const box=document.getElementById('payrollList');if(!box)return;const tbody=document.getElementById('payroll_tbody'),changesUrl=box.dataset.changesUrl,rowsUrl=box.dataset.rowsUrl,month=box.dataset.month,year=box.dataset.year,search=box.dataset.search||'';let latestTs=box.dataset.latestTs||'';if(!changesUrl||!rowsUrl||!latestTs)return;const ids=()=>Array.from(tbody.querySelectorAll('tr[data-id]')).map(tr=>tr.dataset.id).filter(Boolean);const renumber=()=>{let i=0;tbody.querySelectorAll('tr[data-id]').forEach(tr=>{const cell=tr.querySelector('td:first-child');if(cell)cell.textContent=String(++i)})};async function tick(){try{const params=new URLSearchParams({since:latestTs,month,year,search});ids().forEach(id=>params.append('visible[]',id));const res=await fetch(`${changesUrl}?${params}`,{headers:{'X-Requested-With':'XMLHttpRequest'}});if(!res.ok)return;const data=await res.json();latestTs=data.latest_ts||latestTs;(data.deleted||[]).forEach(id=>tbody.querySelector(`tr[data-id="${id}"]`)?.remove());const need=[...new Set([...(data.created||[]),...(data.updated||[])])];if(!need.length){renumber();return}const p=new URLSearchParams({month,year});need.forEach(id=>p.append('ids[]',id));const htmlRes=await fetch(`${rowsUrl}?${p}`,{headers:{'X-Requested-With':'XMLHttpRequest'}});if(!htmlRes.ok)return;const temp=document.createElement('tbody');temp.innerHTML=(await htmlRes.text()).trim();Array.from(temp.children).forEach(newTr=>{const old=tbody.querySelector(`tr[data-id="${newTr.dataset.id}"]`);old?old.replaceWith(newTr):tbody.prepend(newTr)});renumber()}catch(e){}}setInterval(tick,6000)});
</script>@endpush
</x-dashboard.sidebar></x-app-layout>
