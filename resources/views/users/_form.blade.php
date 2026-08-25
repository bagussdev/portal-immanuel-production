@php($editing = isset($user) && $user->exists)
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5"
    x-data="{ showPassword: false, showConfirmation: false }"
    onsubmit="return confirmAndLoad('{{ $editing ? 'Simpan perubahan user?' : 'Buat user ini?' }}')">
    @csrf
    @if($editing) @method('PUT') @endif
    <div class="grid gap-5 lg:grid-cols-[240px,1fr]">
        <section class="ip-card p-5">
            <p class="ip-kicker">Identitas</p>
            <div class="mt-4 space-y-5">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Foto profil
                    <div class="mt-2 flex items-center gap-3">
                        @if($editing && $user->profile_photo_path)
                            <img src="{{ route('users.photo', [$user, 'profile']) }}" class="h-20 w-20 rounded-2xl object-cover" alt="Foto {{ $user->name }}">
                        @else
                            <span class="flex h-20 w-20 items-center justify-center rounded-2xl bg-sky-100 text-2xl font-black text-sky-700">{{ strtoupper(substr(old('name', $user->name ?? 'U'), 0, 1)) }}</span>
                        @endif
                        <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" class="min-w-0 text-xs">
                    </div>
                </label>
                @if($editing && $user->profile_photo_path)<label class="flex items-center gap-2 text-xs font-semibold text-slate-500"><input type="checkbox" name="remove_profile_photo" value="1" class="rounded"> Hapus foto profil</label>@endif
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Foto KTP
                    <input type="file" name="ktp_photo" accept=".jpg,.jpeg,.png,.webp" class="mt-2 block w-full text-xs">
                    <span class="mt-1 block text-xs font-normal text-slate-400">JPG, PNG, atau WebP. Maksimal 8 MB.</span>
                </label>
                @if($editing && $user->ktp_photo_path)
                    <img src="{{ route('users.photo', [$user, 'ktp']) }}" class="w-full rounded-xl border border-slate-200 object-cover" alt="KTP {{ $user->name }}">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-500"><input type="checkbox" name="remove_ktp_photo" value="1" class="rounded"> Hapus foto KTP</label>
                @endif
            </div>
        </section>
        <section class="ip-card p-5 sm:p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nama lengkap<input name="name" value="{{ old('name', $user->name ?? '') }}" required autofocus class="ip-input mt-1"><x-input-error :messages="$errors->get('name')" class="mt-2" /></label>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Username<input name="username" value="{{ old('username', $user->username ?? '') }}" required autocomplete="username" class="ip-input mt-1" placeholder="contoh: bagus.p"><x-input-error :messages="$errors->get('username')" class="mt-2" /></label>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Email<input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="ip-input mt-1"><x-input-error :messages="$errors->get('email')" class="mt-2" /></label>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nomor telepon<input type="tel" inputmode="tel" autocomplete="tel" name="no_telf" value="{{ old('no_telf', $user->no_telf ?? '') }}" class="ip-input mt-1"><x-input-error :messages="$errors->get('no_telf')" class="mt-2" /></label>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 sm:col-span-2">Role<select name="role_id" required class="ip-input mt-1"><option value="">Pilih role</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id ?? '') === (string) $role->id)>{{ ucfirst($role->name) }}</option>@endforeach</select></label>
            </div>
            <div class="mt-5 grid gap-4 border-t border-sky-100 pt-5 sm:grid-cols-2 dark:border-white/10">
                @foreach([['password', $editing ? 'Password baru (opsional)' : 'Password', 'showPassword'], ['password_confirmation', 'Ulangi password', 'showConfirmation']] as [$name, $label, $state])
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">{{ $label }}
                        <div class="relative mt-1"><input name="{{ $name }}" :type="{{ $state }} ? 'text' : 'password'" @if(!$editing) required @endif autocomplete="new-password" class="ip-input pr-11"><button type="button" @click="{{ $state }} = !{{ $state }}" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-sky-700" aria-label="Lihat password"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div>
                        <x-input-error :messages="$errors->get($name)" class="mt-2" />
                    </label>
                @endforeach
            </div>
        </section>
    </div>
    <div class="flex flex-wrap justify-end gap-3"><a href="{{ route('users.index') }}" class="ip-btn-secondary">Batal</a><button class="ip-btn-primary">Simpan user</button></div>
</form>
