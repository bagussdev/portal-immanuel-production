<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="mt-4">
            <a href="{{ route('equipment.index') }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="flex justify-between items-center mt-4 w-full max-w-full">
            <h2 class="font-bold text-xl sm:text-2xl">
                Equipment Detail
            </h2>
        </div>

        <hr class="h-[3px] my-4 bg-gray-200 border-0 dark:bg-gray-700 w-full">

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-sm text-gray-700 dark:text-gray-300">
            {{-- MOBILE VERSION --}}
            <div class="block md:hidden space-y-4">
                @php
                    $fields = [
                        'Name' => $equipment->name,
                        'Brand' => $equipment->brand ?? '-',
                        'Model' => $equipment->model ?? '-',
                        'Serial Number' => $equipment->serial_number ?? '-',
                        'Qty' => $equipment->qty,
                        'Status' => ucfirst($equipment->status),
                        'Location' => $equipment->gudang->name ?? '-',
                        'Created By' => $equipment->createdBy->name ?? '-',
                    ];
                @endphp
                <div class="grid grid-cols-1 gap-3">
                    @foreach ($fields as $label => $value)
                        <div class="flex">
                            <div class="w-40 font-medium">{{ $label }}</div>
                            <div class="flex-1">: {!! nl2br(e($value)) !!}</div>
                        </div>
                    @endforeach
                    <div class="flex">
                        <div class="w-40 font-medium">Photo</div>
                        <div class="flex-1">
                            @if ($equipment->photo)
                                <img src="{{ asset('storage/equipments/' . $equipment->photo) }}"
                                    class="w-full max-w-xs rounded-md shadow mt-2" alt="Photo">
                            @else
                                <p class="italic text-gray-400">No Photo Found</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- DESKTOP VERSION --}}
            <div class="hidden md:grid grid-cols-1 md:grid-cols-4 gap-6">
                <div><span class="font-medium">Name :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $equipment->name }}</div>
                </div>
                <div><span class="font-medium">Brand :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $equipment->brand ?? '-' }}</div>
                </div>
                <div><span class="font-medium">Model :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $equipment->model ?? '-' }}</div>
                </div>
                <div><span class="font-medium">Serial Number :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">
                        {{ $equipment->serial_number ?? '-' }}</div>
                </div>
                <div><span class="font-medium">Qty :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $equipment->qty }}</div>
                </div>
                <div><span class="font-medium">Status :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ ucfirst($equipment->status) }}
                    </div>
                </div>
                <div><span class="font-medium">Location :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">
                        {{ $equipment->gudang->name ?? '-' }}</div>
                </div>
                <div><span class="font-medium">Created By :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">
                        {{ $equipment->createdBy->name ?? '-' }}</div>
                </div>
                <div><span class="font-medium">Photo :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1 text-center"
                        id="equipment-photo-viewer">
                        @if ($equipment->photo)
                            <img src="{{ asset('storage/' . $equipment->photo) }}"
                                class="w-full h-auto max-h-48 object-contain mx-auto rounded shadow cursor-pointer"
                                alt="Photo">
                        @else
                            <p class="italic text-gray-400">No Photo Found</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- NOTES --}}
            <div class="mt-6">
                <p class="font-semibold">Notes :</p>
                <div class="bg-blue-50 dark:bg-blue-900 rounded px-4 py-3 mt-1">
                    {{ $equipment->notes ?? '-' }}
                </div>
            </div>
        </div>

        {{-- Viewer.js --}}
        @push('scripts')
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const container = document.getElementById('equipment-photo-viewer');
                    if (container) {
                        new Viewer(container, {
                            inline: false,
                            toolbar: true,
                            movable: true,
                            zoomable: true,
                            scalable: true,
                            transition: true,
                        });
                    }
                });
            </script>
        @endpush
    </x-dashboard.sidebar>
</x-app-layout>
