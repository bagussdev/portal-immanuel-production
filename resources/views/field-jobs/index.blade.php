<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />
        <div class="ip-page">
            <header class="ip-page-header">
                <div>
                    <p class="ip-kicker">Operasional lapangan</p>
                    <h1 class="ip-title">Jadwal Event</h1>
                    <p class="ip-subtitle">Jadwal operasional, penugasan, progres, dan dokumentasi pasang, bongkar, serta pekerjaan sekali jalan.</p>
                </div>
            </header>

            <form class="ip-card flex flex-col gap-3 p-4 sm:flex-row">
                <input name="search" value="{{ $search }}" placeholder="Cari nomor pekerjaan, client, acara, atau lokasi" class="ip-input flex-1">
                <select name="status" class="ip-input sm:w-56">
                    <option value="">Semua status</option>
                    @foreach(['pending' => 'Belum mulai', 'in_progress' => 'Dikerjakan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="ip-btn-dark">Terapkan filter</button>
            </form>

            <div class="grid gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                @forelse($jobs as $job)
                    <a href="{{ route('field-jobs.show', $job) }}" class="ip-card group block p-5 hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-lg dark:hover:border-red-500/40">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-[11px] font-extrabold uppercase tracking-[.18em] text-sky-600 dark:text-red-400">{{ $job->job_number }}</p>
                                <h2 class="mt-2 truncate text-lg font-extrabold text-slate-950 group-hover:text-sky-700 dark:text-white dark:group-hover:text-red-300">{{ $job->event_name ?: $job->client_name }}</h2>
                                <p class="mt-1 truncate text-sm font-semibold text-slate-500">{{ $job->client_name }}</p>
                            </div>
                            <x-status-badge :status="$job->status" />
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3 rounded-xl bg-sky-50/70 p-3 dark:bg-white/[.035]">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Jadwal terdekat</p>
                                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">{{ optional($job->activeStages->sortBy('scheduled_at')->first()?->scheduled_at)->translatedFormat('d M Y, H:i') ?: 'Belum diatur' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Lokasi</p>
                                <p class="mt-1 truncate text-sm font-bold text-slate-800 dark:text-slate-200">{{ $job->location ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($job->activeStages as $stage)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-100 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600 dark:border-white/10 dark:bg-white/[.04] dark:text-slate-300">
                                    {{ $stage->label() }}
                                    <span class="text-slate-400">&middot; {{ $stage->assignees->count() }} orang</span>
                                </span>
                            @endforeach
                        </div>
                    </a>
                @empty
                    <div class="ip-card p-12 text-center lg:col-span-2 2xl:col-span-3">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-100 text-sky-700 dark:bg-red-500/10 dark:text-red-300">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16v12H4zM8 7V5h8v2M8 12h8m-4-2v4"/></svg>
                        </div>
                        <h2 class="mt-4 font-extrabold text-slate-900 dark:text-white">Belum ada pekerjaan</h2>
                        <p class="mt-1 text-sm text-slate-500">Pekerjaan otomatis dibuat ketika invoice diterbitkan dan akan muncul setelah anggota ditugaskan.</p>
                    </div>
                @endforelse
            </div>

            @if($jobs->hasPages())
                <div class="ip-card p-4">{{ $jobs->links() }}</div>
            @endif
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
