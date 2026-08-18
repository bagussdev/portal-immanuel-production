<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="mt-4 px-4">
            <a href="{{ route('armada.index') }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="flex justify-center mt-6 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-5xl bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md">
                <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 dark:text-white mb-10">
                    Create New Armada
                </h1>

                <form action="{{ route('armada.store') }}" method="POST" enctype="multipart/form-data"
                    onsubmit="return confirmAndLoad('Are you sure to create?')">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="name" :value="'Nama Armada'" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nomor_polisi" :value="'Nomor Polisi'" />
                            <x-text-input id="nomor_polisi" name="nomor_polisi" type="text" class="mt-1 block w-full"
                                :value="old('nomor_polisi')" maxlength="" required />
                            <x-input-error :messages="$errors->get('nomor_polisi')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="type" :value="'Tipe Armada'" />
                            <select id="type" name="type"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih Tipe --</option>
                                @foreach (['Motor', 'Mobil', 'Engkel', 'Double', 'Fuso'] as $tipe)
                                    <option value="{{ $tipe }}" @selected(old('type') == $tipe)>
                                        {{ $tipe }} </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <x-input-label for="model" :value="'Model'" />
                            <x-text-input id="model" name="model" type="text" class="mt-1 block w-full"
                                :value="old('model')" required />
                            <x-input-error :messages="$errors->get('model')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="brand" :value="'Brand'" />
                            <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full"
                                :value="old('brand')" required />
                            <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="year" :value="'Tahun'" />
                            <x-text-input id="year" name="year" type="number" class="mt-1 block w-full"
                                :value="old('year')" required />
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <x-input-label for="nomor_rangka" :value="'No Rangka'" />
                            <x-text-input id="nomor_rangka" name="nomor_rangka" type="text" class="mt-1 block w-full"
                                :value="old('nomor_rangka')" required />
                            <x-input-error :messages="$errors->get('nomor_rangka')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nomor_mesin" :value="'No Mesin'" />
                            <x-text-input id="nomor_mesin" name="nomor_mesin" type="text" class="mt-1 block w-full"
                                :value="old('nomor_mesin')" required />
                            <x-input-error :messages="$errors->get('nomor_mesin')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <x-input-label for="stnk_expired" :value="'Masa Berlaku STNK'" />
                            <x-text-input id="stnk_expired" name="stnk_expired" type="date" class="mt-1 block w-full"
                                :value="old('stnk_expired')" />
                            <x-input-error :messages="$errors->get('stnk_expired')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="location_id" :value="'Lokasi Armada (Gudang)'" />
                            <select id="location_id" name="location_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="user_id" :value="'Penanggung Jawab Armada'" />
                            <select id="user_id" name="user_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>
                    </div>

                    <div id="equipment-photo-viewer" class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                        @foreach ([
        'qr_pertamina' => 'QR MyPertamina',
        'foto_depan' => 'Foto Depan',
        'foto_belakang' => 'Foto Belakang',
        'foto_samping' => 'Foto Samping',
    ] as $field => $label)
                            <div>
                                <x-input-label :for="$field" :value="$label" />
                                <input type="file" name="{{ $field }}" id="{{ $field }}"
                                    accept="image/*" onchange="previewImage(event, '{{ $field }}_preview')"
                                    class="block w-full text-sm border rounded-md cursor-pointer file:bg-blue-500 file:text-white file:rounded-md file:border-0 file:py-2 file:px-4 dark:bg-gray-700 dark:text-white" />
                                <x-input-error :messages="$errors->get($field)" class="mt-2" />
                                <div id="{{ $field }}_preview_container" class="mt-2 hidden">
                                    <img id="{{ $field }}_preview"
                                        class="w-full h-24 object-contain rounded border cursor-zoom-in" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Notes --}}
                    <div class="mt-6">
                        <x-input-label for="notes" :value="'Catatan (Opsional)'" />
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white focus:ring focus:ring-blue-500">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="mt-8 flex justify-center">
                        <x-action-button text="Simpan" color="blue" />
                    </div>
                </form>
            </div>
        </div>
        @push('scripts')
            <script>
                function previewImage(event, previewId) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        const preview = document.getElementById(previewId);
                        const container = document.getElementById(previewId + '_container');
                        preview.src = reader.result;
                        container.classList.remove('hidden');

                        const viewerContainer = document.getElementById('equipment-photo-viewer');
                        if (viewerContainer._viewerInstance) {
                            viewerContainer._viewerInstance.destroy();
                        }
                        viewerContainer._viewerInstance = new Viewer(viewerContainer, {
                            inline: false,
                            toolbar: true,
                            movable: true,
                            zoomable: true,
                            scalable: true,
                            transition: true,
                        });
                    };
                    reader.readAsDataURL(event.target.files[0]);
                }
            </script>
        @endpush
    </x-dashboard.sidebar>
</x-app-layout>
