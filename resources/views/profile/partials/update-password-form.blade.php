<div x-data="{ current: false, password: false, confirmation: false }">
    <p class="ip-kicker">Keamanan</p>
    <h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white">Ubah password</h2>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gunakan minimal 8 karakter yang sulit ditebak.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4" onsubmit="showFullScreenLoader()">
        @csrf
        @method('PUT')

        @foreach([
            ['update_password_current_password', 'current_password', 'Password saat ini', 'current', 'current-password'],
            ['update_password_password', 'password', 'Password baru', 'password', 'new-password'],
            ['update_password_password_confirmation', 'password_confirmation', 'Ulangi password baru', 'confirmation', 'new-password'],
        ] as [$id, $name, $label, $state, $autocomplete])
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">{{ $label }}
                <div class="relative mt-1">
                    <input id="{{ $id }}" name="{{ $name }}" :type="{{ $state }} ? 'text' : 'password'" autocomplete="{{ $autocomplete }}" class="ip-input pr-11">
                    <button type="button" @click="{{ $state }} = !{{ $state }}" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-sky-700" aria-label="Lihat {{ strtolower($label) }}"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get($name)" class="mt-2" />
            </label>
        @endforeach

        <div class="flex items-center justify-end gap-3 pt-1">
            @if(session('status') === 'password-updated')<span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-bold text-emerald-600">Password tersimpan.</span>@endif
            <button type="submit" class="ip-btn-primary">Simpan password</button>
        </div>
    </form>
</div>
