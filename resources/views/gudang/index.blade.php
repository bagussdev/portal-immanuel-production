<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div
            class="mb-4 sm:mt-5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 text-xl font-bold text-gray-800 dark:text-white">
            <div>Gudang Management</div>

            <div class="flex flex-row gap-2 sm:gap-3 items-start sm:items-center text-sm">
                <form method="GET" action="{{ route('gudang.index') }}" class="flex gap-2 items-center"
                    onsubmit="showFullScreenLoader();">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 w-36 sm:w-44" />
                    <input type="hidden" name="per_page" value="{{ $perPage ?? 5 }}">
                    <button type="submit"
                        class="text-xs sm:text-sm px-3 py-1.5 sm:px-3 sm:py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Search
                    </button>
                </form>

                @can('creategudang')
                    <a href="{{ route('gudang.create') }}" onclick="showFullScreenLoader();"
                        class="text-xs sm:text-sm px-3 py-1.5 sm:px-4 sm:py-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-md focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700 text-center">
                        Add Gudang
                    </a>
                @endcan
            </div>
        </div>

        <hr class="h-[3px] my-8 bg-gray-200 border-0 dark:bg-gray-700 w-full">

        <div class="w-full overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden shadow rounded-lg" id="gudangList">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="no">No <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="name">Name <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="site_code">Site Code <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="since">Since <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="location">Location <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody
                            class="list bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 whitespace-nowrap">
                            @forelse ($gudangs as $index => $gudang)
                                <tr>
                                    <td class="px-4 py-3 text-left no">
                                        {{ $loop->iteration + ($gudangs instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($gudangs->currentPage() - 1) * $gudangs->perPage() : 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-left name">{{ ucwords(strtolower($gudang->name)) }}</td>
                                    <td class="px-4 py-3 site_code">{{ $gudang->site_code }}</td>
                                    <td class="px-4 py-3 since">{{ $gudang->since }}</td>
                                    <td class="px-4 py-3 text-left location">{{ $gudang->location }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-row items-center justify-center gap-1">
                                            @can('editgudang')
                                                <a href="{{ route('gudang.edit', $gudang->id) }}"
                                                    onclick="showFullScreenLoader();">
                                                    <x-action-button text="Edit" color="green" />
                                                </a>
                                            @endcan

                                            @can('deletegudang')
                                                <form action="{{ route('gudang.destroy', $gudang->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure to delete this location?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-action-button text="Delete" color="red" />
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No
                                        locations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <x-per-page-selector :route="'gudang.index'" :perPage="$perPage" :search="$search" :items="$gudangs" />

        @push('scripts')
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const list = new List('gudangList', {
                        valueNames: ['no', 'name', 'site_code', 'since', 'location']
                    });

                    const headers = document.querySelectorAll("th.sort");
                    let currentSort = {
                        el: null,
                        key: '',
                        order: ''
                    };

                    // Simpan isi awal tbody
                    const originalTbody = document.querySelector("#gudangList tbody").innerHTML;

                    headers.forEach(header => {
                        header.addEventListener("click", () => {
                            const sortKey = header.dataset.sort;
                            const icon = header.querySelector(".sort-icon");

                            // Bersihkan semua ikon
                            document.querySelectorAll(".sort-icon").forEach(el => el.textContent = "");

                            if (currentSort.el === header) {
                                if (currentSort.order === 'asc') {
                                    list.sort(sortKey, {
                                        order: "desc"
                                    });
                                    icon.textContent = "↓";
                                    currentSort.order = "desc";
                                } else if (currentSort.order === 'desc') {
                                    // RESET: Kembalikan isi tabel ke semula
                                    document.querySelector("#gudangList tbody").innerHTML = originalTbody;
                                    currentSort = {
                                        el: null,
                                        key: '',
                                        order: ''
                                    };
                                    list.reIndex(); // refresh List.js
                                }
                            } else {
                                list.sort(sortKey, {
                                    order: "asc"
                                });
                                icon.textContent = "↑";
                                currentSort = {
                                    el: header,
                                    key: sortKey,
                                    order: "asc"
                                };
                            }
                        });
                    });
                });
            </script>
        @endpush

    </x-dashboard.sidebar>
</x-app-layout>
