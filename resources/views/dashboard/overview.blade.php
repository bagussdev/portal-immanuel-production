<x-app-layout><x-dashboard.sidebar><x-alert-information />
<div class="ip-page">
    <section class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-sky-700 via-sky-800 to-slate-950 px-6 py-6 text-white shadow-xl dark:from-[#11151e] dark:via-[#0b0c0f] dark:to-black sm:px-8 sm:py-7">
        <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full bg-sky-300/20 blur-3xl dark:bg-red-600/25"></div>
        <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div><p class="text-[11px] font-extrabold uppercase tracking-[.24em] text-sky-200 dark:text-red-400">Ringkasan hari ini</p><h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Selamat datang, {{ auth()->user()->name }}</h1></div>
            @can('fieldjobsmenu')<a href="{{ route('field-jobs.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-extrabold text-slate-950 hover:bg-red-50">Buka jadwal <span aria-hidden="true">&rarr;</span></a>@endcan
        </div>
    </section>

    @if($canFinance)
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('invoices.index',['status'=>'unpaid']) }}" class="ip-card p-5 hover:-translate-y-0.5"><div class="flex items-start justify-between"><p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Invoice terbuka</p><span class="rounded-lg bg-red-50 p-2 text-red-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z" /></svg></span></div><p class="mt-4 text-3xl font-extrabold text-slate-950">{{ $invoiceStats['open'] }}</p></a>
            <div class="ip-card p-5"><div class="flex items-start justify-between"><p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Sisa piutang</p><span class="rounded-lg bg-amber-50 p-2 text-amber-600">Rp</span></div><p class="mt-4 text-2xl font-extrabold tracking-tight text-slate-950">Rp {{ number_format($invoiceStats['receivable'],0,',','.') }}</p></div>
            <a href="{{ route('payments.index') }}" class="ip-card p-5 hover:-translate-y-0.5"><div class="flex items-start justify-between"><p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Masuk bulan ini</p><span class="rounded-lg bg-emerald-50 p-2 text-emerald-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" /></svg></span></div><p class="mt-4 text-2xl font-extrabold tracking-tight text-emerald-600">Rp {{ number_format($invoiceStats['paidThisMonth'],0,',','.') }}</p></a>
            <a href="{{ route('quotations.index',['status'=>'draft']) }}" class="ip-card p-5 hover:-translate-y-0.5"><div class="flex items-start justify-between"><p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Quotation draft</p><span class="rounded-lg bg-slate-100 p-2 text-slate-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4zM8 9h8M8 13h5" /></svg></span></div><p class="mt-4 text-3xl font-extrabold text-slate-950">{{ $invoiceStats['draftQuotations'] }}</p></a>
        </div>
    @endif

    <div class="grid gap-6 {{ $canFinance ? 'xl:grid-cols-[1.35fr_.65fr]' : '' }}">
        @can('fieldjobsmenu')
        <section class="ip-card ip-card-body">
            <div class="flex items-center justify-between gap-4"><div><p class="ip-kicker">Agenda</p><h2 class="mt-1 ip-section-title">Pasang & bongkar terdekat</h2></div><a href="{{ route('field-jobs.index') }}" class="text-xs font-extrabold text-red-600">Lihat jadwal</a></div>
            <div class="mt-5 divide-y divide-sky-100 dark:divide-white/[.07]">
                @forelse($upcomingStages as $stage)
                    <a href="{{ route('field-jobs.show',$stage->fieldJob) }}" class="flex items-center gap-4 py-4 first:pt-0 last:pb-0">
                        <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-sky-950 text-white dark:bg-black"><span class="text-[9px] font-bold uppercase text-sky-300 dark:text-red-400">{{ $stage->scheduled_at->translatedFormat('M') }}</span><span class="text-lg font-extrabold leading-5">{{ $stage->scheduled_at->format('d') }}</span></div>
                        <div class="min-w-0 flex-1"><p class="truncate text-sm font-extrabold text-slate-900 dark:text-white">{{ $stage->label() }} &middot; {{ $stage->fieldJob->event_name ?: $stage->fieldJob->client_name }}</p><p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $stage->fieldJob->location ?: 'Lokasi belum ditentukan' }} &middot; {{ $stage->scheduled_at->translatedFormat('H:i') }}</p></div>
                        <svg class="h-4 w-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m9 18 6-6-6-6" /></svg>
                    </a>
                @empty<p class="py-10 text-center text-sm font-medium text-slate-500">Belum ada jadwal pasang atau bongkar mendatang.</p>@endforelse
            </div>
        </section>
        @endcan

        @if($canFinance)
        <section class="ip-card ip-card-body"><div><p class="ip-kicker">Armada</p><h2 class="mt-1 ip-section-title">Dokumen Samsat</h2></div><div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-1"><a href="{{ route('armada.index',['document_status'=>'overdue']) }}" class="rounded-xl border border-red-100 bg-red-50 p-4"><div class="flex items-center justify-between"><span class="text-sm font-bold text-red-700">Terlambat</span><strong class="text-2xl font-extrabold text-red-700">{{ $documentStats['overdue'] }}</strong></div><p class="mt-1 text-xs text-red-500">Perlu diperpanjang</p></a><a href="{{ route('armada.index',['document_status'=>'due_soon']) }}" class="rounded-xl border border-amber-100 bg-amber-50 p-4"><div class="flex items-center justify-between"><span class="text-sm font-bold text-amber-700">Mendekati jatuh tempo</span><strong class="text-2xl font-extrabold text-amber-700">{{ $documentStats['dueSoon'] }}</strong></div><p class="mt-1 text-xs text-amber-600">Dalam 30 hari</p></a></div></section>
        @endif
    </div>

    <section class="ip-card ip-card-body"><div class="flex items-center justify-between gap-4"><div><p class="ip-kicker">Penggajian</p><h2 class="mt-1 ip-section-title">Periode {{ now()->locale('id')->translatedFormat('F Y') }}</h2></div>@can('payrollmenu')<a href="{{ route('payroll.index') }}" class="text-xs font-extrabold text-red-600">Buka penggajian</a>@endcan</div>
        @if(auth()->user()->canViewAllPayrolls())
            <div class="mt-5 grid gap-3 sm:grid-cols-3"><div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-bold text-slate-500">Total slip</p><p class="mt-2 text-2xl font-extrabold">{{ $payrollStats['total'] }}</p></div><div class="rounded-xl bg-emerald-50 p-4"><p class="text-xs font-bold text-emerald-600">Sudah dibayar</p><p class="mt-2 text-2xl font-extrabold text-emerald-700">{{ $payrollStats['paid'] }}</p></div><div class="rounded-xl bg-slate-950 p-4 text-white"><p class="text-xs font-bold text-slate-400">Total bersih</p><p class="mt-2 text-xl font-extrabold">Rp {{ number_format($payrollStats['net'],0,',','.') }}</p></div></div>
        @else
            <div class="mt-5 divide-y divide-slate-100">@forelse($ownPayrolls as $payroll)<a href="{{ route('payroll.show',$payroll) }}" class="flex items-center justify-between py-3"><span class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::create()->month($payroll->period->month)->locale('id')->translatedFormat('F') }} {{ $payroll->period->year }}</span><span class="text-sm font-extrabold text-slate-950">Rp {{ number_format($payroll->net_pay,0,',','.') }}</span></a>@empty<p class="py-8 text-center text-sm text-slate-500">Belum ada slip gaji.</p>@endforelse</div>
        @endif
    </section>
</div>
</x-dashboard.sidebar></x-app-layout>
