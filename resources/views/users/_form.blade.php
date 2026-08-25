@php
    $editing = isset($user) && $user->exists;
    $profileUrl = $editing && $user->profile_photo_path ? route('users.photo', [$user, 'profile']) : null;
    $ktpUrl = $editing && $user->ktp_photo_path ? route('users.photo', [$user, 'ktp']) : null;
@endphp
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5"
    x-data="userImageEditor({ profileUrl: @js($profileUrl), ktpUrl: @js($ktpUrl) })"
    onsubmit="return confirmAndLoad('{{ $editing ? 'Simpan perubahan user?' : 'Buat user ini?' }}')">
    @csrf
    @if($editing) @method('PUT') @endif
    <div class="grid gap-5 xl:grid-cols-[380px,minmax(0,1fr)]">
        <section class="ip-card p-5 sm:p-6">
            <p class="ip-kicker">Identitas</p>
            <div class="mt-4 space-y-6">
                <div>
                    <div class="flex items-end justify-between gap-3"><div><h2 class="text-sm font-extrabold text-slate-800 dark:text-white">Foto profil</h2><p class="mt-0.5 text-xs text-slate-400">Bingkai persegi, maksimal 5 MB.</p></div><span class="rounded-lg bg-sky-50 px-2 py-1 text-[10px] font-extrabold text-sky-700 dark:bg-white/[.05] dark:text-red-300">1 : 1</span></div>
                    <div class="mx-auto mt-3 aspect-square w-full max-w-[210px] overflow-hidden rounded-3xl border-2 border-dashed border-sky-200 bg-sky-50 shadow-inner dark:border-white/15 dark:bg-white/[.03]">
                        <template x-if="profilePreview"><img :src="profilePreview" :style="profileStyle()" class="h-full w-full object-cover transition-transform duration-150" alt="Preview foto profil"></template>
                        <template x-if="!profilePreview"><div class="flex h-full w-full items-center justify-center text-5xl font-black text-sky-700 dark:text-red-400">{{ strtoupper(substr(old('name', $user->name ?? 'U'), 0, 1)) }}</div></template>
                    </div>
                    <label class="ip-btn-secondary mt-3 w-full cursor-pointer justify-center"><span>Pilih foto profil</span><input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" class="sr-only" @change="previewFile($event, 'profile')"></label>
                    <p x-show="profileFileName" x-text="profileFileName" class="mt-2 truncate text-center text-xs font-semibold text-slate-500"></p>
                    <x-image-adjust-controls kind="profile" :zoom-max="3" />
                    <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
                </div>
                @if($editing && $user->profile_photo_path)<label class="flex items-center gap-2 text-xs font-semibold text-slate-500"><input type="checkbox" name="remove_profile_photo" value="1" class="rounded"> Hapus foto profil</label>@endif
                <div class="border-t border-sky-100 pt-5 dark:border-white/10">
                    <div class="flex items-end justify-between gap-3"><div><h2 class="text-sm font-extrabold text-slate-800 dark:text-white">Foto KTP</h2><p class="mt-0.5 text-xs text-slate-400">Bingkai mengikuti ukuran kartu, maksimal 8 MB.</p></div><span class="rounded-lg bg-sky-50 px-2 py-1 text-[10px] font-extrabold text-sky-700 dark:bg-white/[.05] dark:text-red-300">85,6 × 54 mm</span></div>
                    <div class="relative mt-3 aspect-[856/540] w-full overflow-hidden rounded-2xl border-2 border-dashed border-sky-200 bg-slate-100 shadow-inner dark:border-white/15 dark:bg-black/30">
                        <template x-if="ktpPreview"><img :src="ktpPreview" :style="ktpStyle()" class="h-full w-full object-cover transition-transform duration-150" alt="Preview KTP"></template>
                        <template x-if="!ktpPreview"><div class="flex h-full w-full flex-col items-center justify-center text-slate-400"><svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8" cy="11" r="2"/><path d="M5.5 16c.6-1.6 1.5-2.4 2.5-2.4s1.9.8 2.5 2.4M13 10h5M13 14h5"/></svg><span class="mt-2 text-xs font-bold">Belum ada KTP</span></div></template>
                    </div>
                    <x-image-adjust-controls kind="ktp" :zoom-max="4" />
                    <input type="hidden" name="ktp_rotation" :value="ktpRotation">
                    <div class="mt-3 grid grid-cols-[1fr,44px,44px] gap-2">
                        <label class="ip-btn-secondary min-w-0 cursor-pointer justify-center"><span class="truncate">Pilih foto KTP</span><input type="file" name="ktp_photo" accept=".jpg,.jpeg,.png,.webp" class="sr-only" @change="previewFile($event, 'ktp')"></label>
                        <button type="button" @click="rotateKtp(-90)" :disabled="!ktpPreview" class="flex h-11 items-center justify-center rounded-xl border border-sky-200 bg-white text-lg font-bold text-sky-700 disabled:opacity-40 dark:border-white/10 dark:bg-white/[.04] dark:text-red-300" title="Putar ke kiri" aria-label="Putar KTP ke kiri">↶</button>
                        <button type="button" @click="rotateKtp(90)" :disabled="!ktpPreview" class="flex h-11 items-center justify-center rounded-xl border border-sky-200 bg-white text-lg font-bold text-sky-700 disabled:opacity-40 dark:border-white/10 dark:bg-white/[.04] dark:text-red-300" title="Putar ke kanan" aria-label="Putar KTP ke kanan">↷</button>
                    </div>
                    <p x-show="ktpFileName" x-text="ktpFileName" class="mt-2 truncate text-center text-xs font-semibold text-slate-500"></p>
                    <x-input-error :messages="$errors->get('ktp_photo')" class="mt-2" />
                    @if($editing && $user->ktp_photo_path)<label class="mt-3 flex items-center gap-2 text-xs font-semibold text-slate-500"><input type="checkbox" name="remove_ktp_photo" value="1" class="rounded"> Hapus foto KTP</label>@endif
                </div>
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
