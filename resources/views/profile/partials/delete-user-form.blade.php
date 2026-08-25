<section class="space-y-5">
    <div>
        <p class="ip-kicker !text-red-600">Akun</p>
        <h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white">Hapus akun</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Akun dan data terkait tidak dapat dipulihkan setelah dihapus.</p>
    </div>

    <button type="button" x-data x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="ip-btn-danger">Hapus akun saya</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}" class="p-6" onsubmit="showFullScreenLoader()">
            @csrf
            @method('DELETE')
            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Yakin ingin menghapus akun?</h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Masukkan password untuk mengonfirmasi penghapusan permanen.</p>
            <input id="delete_account_password" name="password" type="password" class="ip-input mt-5" placeholder="Password" autocomplete="current-password">
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="ip-btn-secondary">Batal</button>
                <button type="submit" class="ip-btn-danger">Hapus permanen</button>
            </div>
        </form>
    </x-modal>
</section>
