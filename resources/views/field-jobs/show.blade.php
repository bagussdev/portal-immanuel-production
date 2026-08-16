@php
    use App\Models\FieldJobStage;

    $canManage = auth()->user()->canManageAllFieldJobs();
    $hasTeardown = $fieldJob->stages->contains('type', FieldJobStage::TYPE_TEARDOWN);
    $formatQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
@endphp

<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />
        <div class="mx-auto max-w-6xl space-y-6">
            <header class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-sky-700 via-sky-800 to-slate-950 p-6 text-white shadow-xl dark:from-[#11151e] dark:via-[#0b0c0f] dark:to-black sm:p-8">
                <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-sky-300/20 blur-3xl dark:bg-red-600/25"></div>
                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div class="min-w-0">
                        <a href="{{ route('field-jobs.index') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-bold text-sky-100 hover:text-white">&larr; Kembali ke pekerjaan</a>
                        <p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-sky-200 dark:text-red-400">{{ $fieldJob->job_number }}</p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $fieldJob->event_name ?: $fieldJob->client_name }}</h1>
                        <p class="mt-2 text-sm text-sky-100/80">{{ $fieldJob->client_name }}{{ $fieldJob->location ? ' · '.$fieldJob->location : '' }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$fieldJob->status" />
                        @can('invoicemenu')
                            @if($fieldJob->relationLoaded('invoice') && $fieldJob->invoice)
                                <a href="{{ route('invoices.show', $fieldJob->invoice) }}" class="ip-btn border border-white/20 bg-white/10 text-white hover:bg-white/20">Buka invoice</a>
                            @endif
                        @endcan
                    </div>
                </div>
            </header>

            <x-responsive-disclosure kicker="Informasi event" title="Jadwal & lokasi" description="Ringkasan waktu pelaksanaan dan catatan pekerjaan." :mobile-open="true">
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Hari acara</p><p class="mt-1 font-bold text-slate-900 dark:text-white">{{ optional($fieldJob->event_date)->translatedFormat('d F Y') ?: '-' }}</p></div>
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Loading</p><p class="mt-1 font-bold text-slate-900 dark:text-white">{{ optional($fieldJob->loading_date)->translatedFormat('d M Y, H:i') ?: '-' }}</p></div>
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Bongkar</p><p class="mt-1 font-bold text-slate-900 dark:text-white">{{ optional($fieldJob->teardown_date)->translatedFormat('d M Y, H:i') ?: 'Tidak diperlukan' }}</p></div>
                    <div><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Lokasi</p><p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $fieldJob->location ?: '-' }}</p></div>
                </div>
                @if($fieldJob->notes)
                    <div class="mt-5 rounded-xl bg-sky-50 p-4 dark:bg-white/[.04]"><p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Catatan pekerjaan</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-300">{{ $fieldJob->notes }}</p></div>
                @endif
            </x-responsive-disclosure>

            @foreach($fieldJob->stages as $stage)
                @php
                    $stageItems = $fieldJob->items;
                    $isAssigned = $stage->assignees->contains('id', auth()->id());
                    $canAct = $canManage || $isAssigned;
                    $teardownStage = $stage->type === FieldJobStage::TYPE_INSTALL ? $fieldJob->stages->firstWhere('type', FieldJobStage::TYPE_TEARDOWN) : null;
                    $copyTeamToTeardown = $teardownStage && ($teardownStage->assignees->isEmpty() || $teardownStage->assignees->pluck('id')->sort()->values()->all() === $stage->assignees->pluck('id')->sort()->values()->all());
                @endphp

                <x-responsive-disclosure
                    id="stage-{{ $stage->id }}"
                    kicker="Tahap pekerjaan"
                    title="{{ $stage->label() }}"
                    description="{{ optional($stage->scheduled_at)->translatedFormat('l, d F Y · H:i') ?: 'Jadwal belum diatur' }}"
                    :mobile-open="$stage->status === FieldJobStage::STATUS_IN_PROGRESS"
                    content-class="p-0"
                >
                    <x-slot name="meta"><x-status-badge :status="$stage->status" /></x-slot>
                    <div class="grid gap-6 p-5 lg:grid-cols-[minmax(0,1fr),360px]">
                        <div class="space-y-6">
                            <div>
                                <h3 class="ip-section-title">Detail yang dikerjakan</h3>
                                <div class="mt-3 divide-y divide-sky-100 rounded-xl border border-sky-100 dark:divide-white/10 dark:border-white/10">
                                    @foreach($stageItems as $item)
                                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                                            <p class="font-bold text-slate-900 dark:text-white">{{ $item->item_name }}</p>
                                            <p class="shrink-0 text-sm font-semibold text-slate-500">{{ $formatQty($item->qty) }}{{ $item->length ? ' × '.$formatQty($item->length).' m' : '' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-3"><h3 class="ip-section-title">Foto hasil</h3><span class="text-xs font-bold text-slate-400">{{ $stage->photos->count() }} foto</span></div>
                                @if($stage->photos->isNotEmpty())
                                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                        @foreach($stage->photos as $photo)
                                            <figure class="group relative overflow-hidden rounded-xl border border-sky-100 bg-slate-100 dark:border-white/10 dark:bg-white/[.04]">
                                                <a href="{{ route('field-jobs.stages.photos.show', [$fieldJob, $stage, $photo]) }}" target="_blank">
                                                    <img src="{{ route('field-jobs.stages.photos.show', [$fieldJob, $stage, $photo]) }}" alt="Foto {{ $stage->label() }}" loading="lazy" class="aspect-square w-full object-cover transition duration-300 group-hover:scale-105">
                                                </a>
                                                <figcaption class="p-2.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                    <p class="truncate font-bold text-slate-700 dark:text-slate-200">{{ $photo->uploader?->name ?: 'Pengguna' }}</p>
                                                    <p>{{ $photo->created_at->translatedFormat('d M Y, H:i') }}</p>
                                                    @if($photo->caption)<p class="mt-1 line-clamp-2">{{ $photo->caption }}</p>@endif
                                                    @if($canManage || ((int) $photo->uploaded_by === (int) auth()->id() && $isAssigned))
                                                        <form method="POST" action="{{ route('field-jobs.stages.photos.destroy', [$fieldJob, $stage, $photo]) }}" class="mt-2" onsubmit="return confirmAndLoad('Hapus foto ini?')">
                                                            @csrf @method('DELETE')
                                                            <button class="font-extrabold text-red-600 hover:text-red-800">Hapus</button>
                                                        </form>
                                                    @endif
                                                </figcaption>
                                            </figure>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mt-3 rounded-xl border border-dashed border-sky-200 p-7 text-center text-sm font-semibold text-slate-400 dark:border-white/10">Belum ada foto hasil.</div>
                                @endif
                            </div>

                            @if($canAct && $stage->status !== FieldJobStage::STATUS_COMPLETED)
                                @can('uploadfieldjobphotos')
                                    <form method="POST" enctype="multipart/form-data" action="{{ route('field-jobs.stages.photos.store', [$fieldJob, $stage]) }}" class="rounded-xl border border-sky-100 bg-sky-50/60 p-4 dark:border-white/10 dark:bg-white/[.025]">
                                        @csrf
                                        <label class="ip-label">Pilih foto</label>
                                        <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple required class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-sky-600 file:px-4 file:py-2.5 file:font-bold file:text-white hover:file:bg-sky-700 dark:text-slate-300 dark:file:bg-red-600">
                                        <label class="mt-3 block"><span class="ip-label">Catatan foto (opsional)</span><input name="caption" maxlength="255" class="ip-input" placeholder="Contoh: Hasil panggung sisi depan"></label>
                                        <p class="mt-2 text-xs leading-5 text-slate-400">Bisa memilih beberapa foto. Maks. 8 MB per foto; akses tetap privat.</p>
                                        <button class="ip-btn-primary mt-3">Unggah foto</button>
                                    </form>
                                @endcan
                            @endif
                        </div>

                        <aside class="space-y-5">
                            <div class="rounded-xl border border-sky-100 p-4 dark:border-white/10">
                                <div class="flex items-center justify-between gap-3"><h3 class="ip-section-title">Tim bertugas</h3><span class="text-xs font-bold text-slate-400">{{ $stage->assignees->count() }} orang</span></div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @forelse($stage->assignees as $member)
                                        <span class="rounded-full bg-sky-100 px-3 py-1.5 text-xs font-bold text-sky-800 dark:bg-white/[.07] dark:text-slate-200">{{ $member->name }}</span>
                                    @empty
                                        <p class="text-sm font-semibold text-amber-600">Belum ada anggota yang ditugaskan.</p>
                                    @endforelse
                                </div>
                            </div>

                            @if($canManage)
                                <form method="POST" action="{{ route('field-jobs.stages.assignments', [$fieldJob, $stage]) }}" class="rounded-xl border border-sky-100 p-4 dark:border-white/10">
                                    @csrf @method('PUT')
                                    <h3 class="ip-section-title">Atur anggota</h3>
                                    <div class="mt-3 max-h-56 space-y-2 overflow-y-auto pr-1">
                                        @foreach($teamMembers as $member)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-lg p-2 hover:bg-sky-50 dark:hover:bg-white/[.04]">
                                                <input type="checkbox" name="assignee_ids[]" value="{{ $member->id }}" @checked($stage->assignees->contains('id', $member->id)) class="rounded border-sky-200 text-sky-600 focus:ring-sky-500">
                                                <span class="min-w-0"><strong class="block truncate text-sm text-slate-800 dark:text-slate-200">{{ $member->name }}</strong><small class="uppercase text-slate-400">{{ $member->role?->name }}</small></span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if($stage->type === FieldJobStage::TYPE_INSTALL && $hasTeardown)
                                        <label class="mt-3 flex items-start gap-2 rounded-lg bg-sky-50 p-3 text-xs font-semibold text-slate-600 dark:bg-white/[.04] dark:text-slate-300">
                                            <input type="hidden" name="copy_to_teardown" value="0"><input type="checkbox" name="copy_to_teardown" value="1" @checked($copyTeamToTeardown) class="mt-0.5 rounded border-sky-200 text-sky-600">
                                            <span>Gunakan tim yang sama untuk tahap Bongkar</span>
                                        </label>
                                    @endif
                                    <button class="ip-btn-secondary mt-3 w-full">Simpan penugasan</button>
                                </form>
                            @endif

                            @if($canAct)
                                @can('updatefieldjobstatus')
                                    <div class="rounded-xl bg-sky-950 p-4 text-white dark:bg-black">
                                        <h3 class="font-extrabold">Perbarui progres</h3>
                                        <p class="mt-1 text-xs leading-5 text-slate-400">Minimal satu foto diperlukan sebelum pekerjaan dapat diselesaikan.</p>
                                        @if($stage->status === FieldJobStage::STATUS_PENDING)
                                            <form method="POST" action="{{ route('field-jobs.stages.update', [$fieldJob, $stage]) }}" class="mt-3">@csrf @method('PATCH')<input type="hidden" name="status" value="in_progress"><button class="ip-btn w-full bg-white text-slate-950 hover:bg-slate-100">Mulai pekerjaan</button></form>
                                        @elseif($stage->status === FieldJobStage::STATUS_IN_PROGRESS)
                                            <form method="POST" action="{{ route('field-jobs.stages.update', [$fieldJob, $stage]) }}" class="mt-3">@csrf @method('PATCH')<input type="hidden" name="status" value="completed"><button class="ip-btn w-full bg-emerald-500 text-white hover:bg-emerald-600">Tandai selesai</button></form>
                                        @elseif($canManage)
                                            <form method="POST" action="{{ route('field-jobs.stages.update', [$fieldJob, $stage]) }}" class="mt-3">@csrf @method('PATCH')<input type="hidden" name="status" value="pending"><button class="ip-btn w-full bg-white/10 text-white hover:bg-white/20">Buka kembali</button></form>
                                        @endif
                                    </div>
                                @endcan
                            @endif
                        </aside>
                    </div>
                </x-responsive-disclosure>
            @endforeach
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
