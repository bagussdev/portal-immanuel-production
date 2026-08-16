@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-900 shadow-sm dark:border-red-500/30 dark:bg-red-950/30 dark:text-red-100" role="alert" x-data="{ visible: true }" x-show="visible">
        <div class="flex items-start gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 font-extrabold text-red-700 dark:bg-red-500/20 dark:text-red-300">!</span>
            <div class="min-w-0 flex-1">
                <p class="font-extrabold">Data belum dapat disimpan</p>
                <p class="mt-0.5 text-sm text-red-700 dark:text-red-300">Periksa bagian berikut, lalu coba lagi:</p>
                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" @click="visible = false" class="rounded-lg p-2 text-red-500 hover:bg-red-100 dark:hover:bg-red-500/10" aria-label="Tutup pesan">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
            </button>
        </div>
    </div>
@endif
