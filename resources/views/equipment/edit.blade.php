<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="mt-4 px-4">
            <a href="{{ route('equipment.index') }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="flex justify-center mt-6 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-3xl bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md">
                <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 dark:text-white mb-10">
                    Edit Equipment
                </h1>

                <form method="POST" action="{{ route('equipment.update', $equipment->id) }}"
                    enctype="multipart/form-data"
                    onsubmit="return confirmAndLoad('Are you sure to update this equipment?')">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name" :value="'Name'" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    value="{{ old('name', $equipment->name) }}" required autofocus />
                            </div>

                            <div>
                                <x-input-label for="brand" :value="'Brand'" />
                                <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full"
                                    value="{{ old('brand', $equipment->brand) }}" />
                            </div>

                            <div>
                                <x-input-label for="model" :value="'Model'" />
                                <x-text-input id="model" name="model" type="text" class="mt-1 block w-full"
                                    value="{{ old('model', $equipment->model) }}" />
                            </div>

                            <div>
                                <x-input-label for="serial_number" :value="'Serial Number'" />
                                <x-text-input id="serial_number" name="serial_number" type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('serial_number', $equipment->serial_number) }}" />
                            </div>
                        </div>

                        {{-- Right Side: Image --}}
                        <div class="flex flex-col gap-3">
                            <div>
                                <x-input-label for="photo" :value="'Photo'" />
                                <input type="file" name="photo" id="photo" accept="image/*"
                                    class="block w-full text-sm border rounded-md cursor-pointer file:bg-blue-500 file:text-white file:rounded-md file:border-0 file:py-2 file:px-4 dark:bg-gray-700 dark:text-white"
                                    onchange="previewImage(event)" />
                            </div>
                            <div id="imagePreviewContainer" class="mt-2">
                                @if ($equipment->photo)
                                    <img id="imagePreview" src="{{ asset('storage/' . $equipment->photo) }}"
                                        alt="Preview"
                                        class="h-64 rounded-lg object-contain shadow border border-gray-300 cursor-zoom-in" />
                                @else
                                    <p class="text-gray-500 italic">No Photo Found</p>
                                @endif
                            </div>
                        </div>

                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="qty" :value="'Quantity'" />
                                <x-text-input id="qty" name="qty" type="number" min="1"
                                    class="mt-1 block w-full" value="{{ old('qty', $equipment->qty) }}" required />
                            </div>

                            <div>
                                <x-input-label for="status" :value="'Status'" />
                                <select id="status" name="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white focus:ring-blue-500">
                                    <option value="baik"
                                        {{ old('status', $equipment->status) == 'baik' ? 'selected' : '' }}>Baik
                                    </option>
                                    <option value="rusak"
                                        {{ old('status', $equipment->status) == 'rusak' ? 'selected' : '' }}>Rusak
                                    </option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="location" :value="'Location (Gudang)'" />
                                <select id="location" name="location" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white focus:ring-blue-500">
                                    <option value="">Choose Location</option>
                                    @foreach ($gudangs as $gudang)
                                        <option value="{{ $gudang->id }}"
                                            {{ old('location', $equipment->location) == $gudang->id ? 'selected' : '' }}>
                                            {{ $gudang->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="'Notes (optional)'" />
                            <textarea id="notes" name="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white focus:ring-blue-500">{{ old('notes', $equipment->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-center">
                        <x-action-button text="Save Changes" color="blue" />
                    </div>
                </form>
            </div>
        </div>

        {{-- Viewer.js & Script --}}
        @push('scripts')
            <script>
                let viewerInstance = null;

                function previewImage(event) {
                    const file = event.target.files[0];
                    if (file && file.type.startsWith("image/")) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.getElementById('imagePreview');
                            img.src = e.target.result;

                            const container = document.getElementById('imagePreviewContainer');
                            container.classList.remove('hidden');

                            if (viewerInstance) {
                                viewerInstance.destroy();
                            }
                            viewerInstance = new Viewer(img, {
                                toolbar: true,
                                navbar: false,
                                title: false,
                                fullscreen: true,
                                tooltip: true
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                }

                // Inisialisasi viewer jika foto lama ada
                document.addEventListener('DOMContentLoaded', function() {
                    const img = document.getElementById('imagePreview');
                    if (img && img.src && !img.src.includes('#')) {
                        viewerInstance = new Viewer(img, {
                            toolbar: true,
                            navbar: false,
                            title: false,
                            fullscreen: true,
                            tooltip: true
                        });
                    }
                });
            </script>
        @endpush
    </x-dashboard.sidebar>
</x-app-layout>
