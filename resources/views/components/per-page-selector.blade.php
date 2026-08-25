@props(['route', 'perPage' => 5, 'search' => '', 'items', 'showPagination' => true])

@php
    use Illuminate\Pagination\LengthAwarePaginator;
@endphp

@if ($showPagination)
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        @if ($items instanceof LengthAwarePaginator)
            <div>
                {{ $items->appends(['per_page' => $perPage, 'search' => $search])->links() }}
            </div>
        @else
            <div class="text-sm text-gray-500 dark:text-gray-400 italic">
                Menampilkan semua {{ $items->count() }} data.
            </div>
        @endif

        <div class="flex items-center gap-4 flex-wrap justify-end">
            <form method="GET" action="{{ route($route) }}" onsubmit="showFullScreenLoader();">
                <input type="hidden" name="search" value="{{ $search }}">
                <div class="flex items-center gap-1">
                    <label for="per_page" class="text-sm text-gray-600 dark:text-gray-300">Tampilkan</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()"
                        class="ip-input !w-20 !py-2 text-sm">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                        <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>All</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">per halaman</span>
                </div>
            </form>
        </div>
    </div>
@endif
