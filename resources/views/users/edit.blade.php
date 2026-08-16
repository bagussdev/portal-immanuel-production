<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />
        <div class="mt-4 px-4">
            <a href="{{ route('users.index') }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="flex justify-center mt-6 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-xl bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md">
                <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 dark:text-white mb-8">
                    Edit user
                </h1>

                <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5"
                    x-data="{ showPassword: false, showConfirmation: false }"
                    onsubmit="return confirmAndLoad('Simpan perubahan user?')">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="'Nama'" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $user->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="'Email'" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="no_telf" :value="'Nomor telepon'" />
                        <x-text-input id="no_telf" name="no_telf" type="tel" inputmode="tel"
                            autocomplete="tel" class="mt-1 block w-full"
                            :value="old('no_telf', $user->no_telf)" required />
                        <x-input-error :messages="$errors->get('no_telf')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role_id" :value="'Role'" />
                        <select id="role_id" name="role_id" required
                            class="w-full mt-1 border-gray-300 dark:border-gray-700 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">Pilih role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="'Password baru (opsional)'" />
                        <div class="relative mt-1">
                            <x-text-input id="password" name="password" type="password" x-bind:type="showPassword ? 'text' : 'password'"
                                class="block w-full pr-11" autocomplete="new-password" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-sky-700 dark:hover:text-white" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'" :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg x-show="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-cloak x-show="showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="m3 3 18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.6 6.2A11.7 11.7 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-2.2 3M6.6 6.6C3.6 8.4 2 12 2 12s3.5 6 10 6c1.7 0 3.2-.4 4.4-1"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="'Ulangi password'" />
                        <div class="relative mt-1">
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                x-bind:type="showConfirmation ? 'text' : 'password'" class="block w-full pr-11"
                                autocomplete="new-password" />
                            <button type="button" @click="showConfirmation = !showConfirmation" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-sky-700 dark:hover:text-white" :aria-label="showConfirmation ? 'Sembunyikan password' : 'Tampilkan password'" :title="showConfirmation ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg x-show="!showConfirmation" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-cloak x-show="showConfirmation" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="m3 3 18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.6 6.2A11.7 11.7 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-2.2 3M6.6 6.6C3.6 8.4 2 12 2 12s3.5 6 10 6c1.7 0 3.2-.4 4.4-1"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex justify-center">
                        <x-action-button text="Simpan" color="blue" />
                    </div>
                </form>
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
