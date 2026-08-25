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

            <x-responsive-disclosure kicker="Informasi event" title="Jadwal & lokasi" description="Ringkasan waktu pelaksanaan dan catatan pekerjaan." :mobile-open="false">
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
                    $stageItems = $stage->field_job_site_id ? $fieldJob->items->where('field_job_site_id', $stage->field_job_site_id) : $fieldJob->items;
                    $isAssigned = $stage->assignees->contains('id', auth()->id());
                    $canAct = $canManage || $isAssigned;
                    $teardownStage = $stage->type === FieldJobStage::TYPE_INSTALL ? $fieldJob->stages->first(fn($candidate) => $candidate->type === FieldJobStage::TYPE_TEARDOWN && $candidate->field_job_site_id === $stage->field_job_site_id) : null;
                    $copyTeamToTeardown = $teardownStage && ($teardownStage->assignees->isEmpty() || $teardownStage->assignees->pluck('id')->sort()->values()->all() === $stage->assignees->pluck('id')->sort()->values()->all());
                @endphp

                <x-responsive-disclosure
                    id="stage-{{ $stage->id }}"
                    kicker="Tahap pekerjaan"
                    title="{{ $stage->label() }}{{ $stage->site?->name ? ' - '.$stage->site->name : '' }}"
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
                                    <div
                                        x-data="{
                                            open: false,
                                            current: 0,
                                            src: '',
                                            caption: '',
                                            meta: '',
                                            trigger: null,
                                            touchStart: 0,
                                            items() { return [...this.$refs.grid.querySelectorAll('[data-lightbox-photo]')] },
                                            showPhoto(index) {
                                                const photos = this.items();
                                                if (!photos.length) return;
                                                if (!this.open) this.trigger = document.activeElement;
                                                this.current = (index + photos.length) % photos.length;
                                                const photo = photos[this.current];
                                                this.src = photo.dataset.src;
                                                this.caption = photo.dataset.caption;
                                                this.meta = photo.dataset.meta;
                                                this.open = true;
                                                document.documentElement.classList.add('overflow-hidden');
                                                this.$nextTick(() => this.$refs.closePhoto.focus());
                                            },
                                            closePhoto() {
                                                this.open = false;
                                                document.documentElement.classList.remove('overflow-hidden');
                                                this.$nextTick(() => this.trigger?.focus());
                                            },
                                            nextPhoto() { this.showPhoto(this.current + 1) },
                                            previousPhoto() { this.showPhoto(this.current - 1) }
                                        }"
                                        @keydown.escape.window="if (open) closePhoto()"
                                        @keydown.arrow-right.window="if (open) nextPhoto()"
                                        @keydown.arrow-left.window="if (open) previousPhoto()"
                                    >
                                        <div x-ref="grid" class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                            @foreach($stage->photos as $photo)
                                                @php($photoUrl = route('field-jobs.stages.photos.show', [$fieldJob, $stage, $photo]))
                                                <figure class="group relative overflow-hidden rounded-xl border border-sky-100 bg-slate-100 dark:border-white/10 dark:bg-white/[.04]">
                                                    <button
                                                        type="button"
                                                        data-lightbox-photo
                                                        data-src="{{ $photoUrl }}"
                                                        data-caption="{{ $photo->caption }}"
                                                        data-meta="{{ ($photo->uploader?->name ?: 'Pengguna').' · '.$photo->created_at->translatedFormat('d M Y, H:i') }}"
                                                        @click="showPhoto({{ $loop->index }})"
                                                        class="block w-full cursor-zoom-in overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500"
                                                        aria-label="Buka foto {{ $loop->iteration }} secara penuh"
                                                    >
                                                        <img src="{{ $photoUrl }}" alt="Foto {{ $stage->label() }}" loading="lazy" class="aspect-square w-full object-cover transition duration-300 group-hover:scale-105">
                                                    </button>
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

                                        <template x-teleport="body">
                                            <div
                                                x-cloak
                                                x-show="open"
                                                x-transition.opacity
                                                class="fixed inset-0 z-[100] flex items-center justify-center p-3 backdrop-blur-sm sm:p-6"
                                                style="width: 100vw; min-height: 100vh; background-color: rgba(0, 0, 0, 0.82)"
                                                role="dialog"
                                                aria-modal="true"
                                                aria-label="Pratinjau foto"
                                                @click.self="closePhoto()"
                                                @touchstart.passive="touchStart = $event.changedTouches[0].screenX"
                                                @touchend.passive="Math.abs($event.changedTouches[0].screenX - touchStart) > 50 && ($event.changedTouches[0].screenX < touchStart ? nextPhoto() : previousPhoto())"
                                            >
                                                <div class="absolute inset-x-0 top-0 z-10 flex items-center justify-between gap-4 bg-gradient-to-b from-black/75 to-transparent px-4 py-4 text-white sm:px-6">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-xs font-bold text-white/75" x-text="meta"></p>
                                                    </div>
                                                    <button x-ref="closePhoto" type="button" @click="closePhoto()" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/20 bg-black/30 text-2xl text-white backdrop-blur transition hover:bg-white/15" aria-label="Tutup foto">&times;</button>
                                                </div>

                                                <div class="flex h-full w-full items-center justify-center px-1 pb-28 pt-20 sm:px-16 sm:pb-24" @click.self="closePhoto()">
                                                    <img :src="src" :alt="caption || 'Foto hasil pekerjaan'" class="max-h-full max-w-full select-none rounded-lg object-contain shadow-2xl" @click.stop>
                                                </div>

                                                <div class="absolute inset-x-0 bottom-0 z-10 flex flex-col items-center gap-3 bg-gradient-to-t from-black/80 via-black/45 to-transparent px-4 pb-20 pt-12 text-white sm:bottom-6 sm:pb-4">
                                                    <p x-show="caption" class="max-w-xl text-center text-sm font-semibold" x-text="caption"></p>
                                                    <div x-show="items().length > 1" class="flex items-center gap-3 rounded-full border border-white/15 bg-black/35 p-1.5 shadow-lg backdrop-blur-md">
                                                        <button type="button" @click.stop="previousPhoto()" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white" aria-label="Foto sebelumnya">
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                        <span class="min-w-12 text-center text-xs font-bold text-white/80"><span x-text="current + 1"></span> / <span x-text="items().length"></span></span>
                                                        <button type="button" @click.stop="nextPhoto()" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white" aria-label="Foto berikutnya">
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
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
                                    <div class="flex items-center justify-between gap-2"><h3 class="ip-section-title">Atur anggota</h3><div class="flex gap-2"><button type="button" class="text-xs font-extrabold text-sky-700" onclick="this.closest('form').querySelectorAll('input[name=\'assignee_ids[]\']').forEach(el => el.checked = true)">Pilih semua</button><button type="button" class="text-xs font-extrabold text-slate-400" onclick="this.closest('form').querySelectorAll('input[name=\'assignee_ids[]\']').forEach(el => el.checked = false)">Kosongkan</button></div></div>
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
                                        <p class="mt-1 text-xs leading-5 text-slate-400">
                                            {{ $stage->type === FieldJobStage::TYPE_TEARDOWN
                                                ? 'Foto Bongkar bersifat opsional.'
                                                : 'Minimal satu foto diperlukan sebelum pekerjaan dapat diselesaikan.' }}
                                        </p>
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
