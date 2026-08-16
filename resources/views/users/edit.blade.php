<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />
        {{-- Back Button --}}
        <div class="mt-4 px-4">
            <a href="{{ route('users.index') }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        {{-- Form Card --}}
        <div class="flex justify-center mt-6 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-xl bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md">
                <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 dark:text-white mb-8">
                    Edit User
                </h1>

                <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-6"
                    onsubmit="return confirmAndLoad('Are you sure you want to create this user?')">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div>
                        <x-input-label for="name" :value="'Name'" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $user->name)" required />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="'Email'" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email', $user->email)" required />
                    </div>

                    {{-- Phone --}}
                    <div>
                        <x-input-label for="no_telf" :value="'Phone Number'" />
                        <x-text-input id="no_telf" name="no_telf" type="number" class="mt-1 block w-full"
                            :value="old('no_telf', $user->no_telf)" required maxlength="12"
                            oninput="if (this.value.length > 12) this.value = this.value.slice(0, 12)" />
                    </div>

                    {{-- Role --}}
                    <div>
                        <x-input-label for="role_id" :value="'Role'" />
                        <select id="role_id" name="role_id" required
                            class="w-full mt-1 border-gray-300 dark:border-gray-700 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">Choose role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Optional New Password --}}
                    <div>
                        <x-input-label for="password" :value="'New Password (Optional)'" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="'Confirm Password'" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-center">
                        <x-action-button text="Save" color="blue" />
                    </div>
                </form>
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
