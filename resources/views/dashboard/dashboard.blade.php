<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="text-xl font-bold text-gray-800 dark:text-white">
            Welcome, {{ auth()->user()->name }}
        </div>


    </x-dashboard.sidebar>
</x-app-layout>
