<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        {{-- Back Button --}}
        <div class="mt-4 px-4">
            <a href="{{ route('gudang.index') }}" onclick="showFullScreenLoader();"
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
                    Create New Gudang
                </h1>

                <form method="POST" action="{{ route('gudang.store') }}" class="space-y-6"
                    onsubmit="return confirmAndLoad('Are you sure you want to create this Gudang?')">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <x-input-label for="name" :value="'Name'" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name')" required autofocus />
                    </div>

                    {{-- Site Code --}}
                    <div>
                        <x-input-label for="site_code" :value="'Site Code'" />
                        <x-text-input id="site_code" name="site_code" type="text" class="mt-1 block w-full"
                            :value="old('site_code')" required />
                    </div>

                    {{-- Location --}}
                    <div>
                        <x-input-label for="location" :value="'Location'" />
                        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full"
                            :value="old('location')" required />
                    </div>

                    {{-- Since --}}
                    <div>
                        <x-input-label for="since" :value="'Since'" />
                        <x-text-input id="since" name="since" type="date" class="mt-1 block w-full"
                            :value="old('since')" required />
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end">
                        <x-action-button text="Save" color="blue" />
                    </div>
                </form>
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
