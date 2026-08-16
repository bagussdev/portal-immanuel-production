<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />
        <div class="ip-page">
            <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="ip-kicker">Data operasional</p><h1 class="ip-title">Detail rekening</h1><p class="ip-subtitle">Pilih profil ini saat membuat quotation atau invoice. Kolom yang kosong tidak akan dicetak.</p></div>
                @can('createbankdetail')<a href="{{ route('bank-details.create') }}" class="ip-btn-primary">+ Tambah rekening</a>@endcan
            </header>

            <form method="GET" class="ip-card flex flex-col gap-3 p-4 sm:flex-row">
                <input name="search" value="{{ $search }}" class="ip-input flex-1" placeholder="Cari nama, bank, atau nomor rekening">
                <button class="ip-btn-dark">Cari</button>
                @if ($search)<a href="{{ route('bank-details.index') }}" class="ip-btn-secondary">Reset</a>@endif
            </form>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($bankDetails as $bankDetail)
                    <article class="ip-card ip-card-body flex flex-col">
                        <div class="flex items-start justify-between gap-3">
                            <div><p class="ip-kicker">{{ $bankDetail->bank_name ?: 'Rekening belum lengkap' }}</p><h2 class="mt-1 text-xl font-extrabold text-slate-950 dark:text-white">{{ $bankDetail->label }}</h2></div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide {{ $bankDetail->active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400' }}">{{ $bankDetail->active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <dl class="mt-5 flex-1 space-y-3 text-sm">
                            @foreach (['Atas nama' => $bankDetail->account_name, 'No. rekening' => $bankDetail->account_number, 'Email' => $bankDetail->email, 'No. HP' => $bankDetail->phone, 'NPWP' => $bankDetail->npwp] as $label => $value)
                                <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-0.5 break-words font-semibold text-slate-700 dark:text-slate-300">{{ $value ?: 'Belum diisi' }}</dd></div>
                            @endforeach
                        </dl>
                        <div class="mt-5 flex gap-2 border-t border-sky-100 pt-4 dark:border-white/10">
                            @can('editbankdetail')<a href="{{ route('bank-details.edit', $bankDetail) }}" class="ip-btn-secondary flex-1">Edit</a>@endcan
                            @can('deletebankdetail')
                                <form method="POST" action="{{ route('bank-details.destroy', $bankDetail) }}" onsubmit="return confirmAndLoad('Hapus detail rekening ini?')">@csrf @method('DELETE')<button class="ip-btn-danger">Hapus</button></form>
                            @endcan
                        </div>
                    </article>
                @empty
                    <div class="ip-card ip-card-body text-center text-slate-500 md:col-span-2 xl:col-span-3">Belum ada detail rekening.</div>
                @endforelse
            </div>
            {{ $bankDetails->links() }}
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
