<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="mt-4 px-4">
            <a href="{{ route('client.index') }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="flex justify-center mt-6 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-xl bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md">
                <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 dark:text-white mb-10">
                    Create Client
                </h1>

                <form method="POST" action="{{ route('client.store') }}"
                    onsubmit="return confirmAndLoad('Are you sure to create this client?')">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" :value="'Client Name'" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company" :value="'Company'" />
                            <x-text-input id="company" name="company" type="text" class="mt-1 block w-full"
                                :value="old('company')" />
                            <x-input-error :messages="$errors->get('company')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="'Email'" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="'Phone'" />
                            <x-text-input id="phone" name="phone" type="text" maxlength="13"
                                class="mt-1 block w-full" :value="old('phone')" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-center">
                        <x-action-button text="Create Client" color="blue" />
                    </div>
                </form>
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
