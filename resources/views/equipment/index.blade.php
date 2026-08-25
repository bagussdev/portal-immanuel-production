<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div
            class="mb-4 sm:mt-5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 text-xl font-bold text-gray-800 dark:text-white">
            <div>Equipment Management</div>

            <div class="flex flex-row gap-2 sm:gap-3 items-start sm:items-center text-sm">
                <form method="GET" action="{{ route('equipment.index') }}" class="flex gap-2 items-center"
                    onsubmit="showFullScreenLoader();">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 w-36 sm:w-44" />
                    <input type="hidden" name="per_page" value="{{ $perPage ?? 5 }}">

                    <button type="submit"
                        class="text-xs sm:text-sm px-3 py-1.5 sm:px-3 sm:py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Search
                    </button>
                </form>

                @can('createequipment')
                    <a href="{{ route('equipment.create') }}" onclick="showFullScreenLoader();"
                        class="text-xs sm:text-sm px-3 py-1.5 sm:px-4 sm:py-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-md focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700 text-center">
                        Add Equipment
                    </a>
                @endcan
            </div>
        </div>

        <hr class="h-[3px] my-8 bg-gray-200 border-0 dark:bg-gray-700 w-full">

        <div class="w-full overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden shadow rounded-lg" id="equipmentList" data-list>
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm text-center text-gray-700 dark:text-gray-300">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-400 whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="no">No <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="name">Name <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="brand">Brand <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="model">Model <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="serial">S/N <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="qty">Qty <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="createdby">Created By <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="status">Status <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3 cursor-pointer sort" data-sort="location">Location <span
                                        class="sort-icon"></span></th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>

                        @php
                            $seedTs = \Carbon\Carbon::parse($latestTs ?? now())
                                ->subSeconds(2)
                                ->toIso8601String();
                        @endphp

                        <tbody id="equipBody"
                            class="list bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 whitespace-nowrap"
                            data-last-ts="{{ $seedTs }}" data-base-offset="{{ $baseOffset ?? 0 }}"
                            data-changes-url="{{ route('equipment.sync.changes', request()->only('search')) }}"
                            data-rows-url="{{ route('equipment.rows') }}">
                            @forelse ($equipment as $index => $item)
                                <tr data-id="{{ $item->id }}">
                                    <td class="px-4 py-3 text-left no">
                                        {{ $loop->iteration + ($equipment instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($equipment->currentPage() - 1) * $equipment->perPage() : 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-left name">{{ $item->name }}</td>
                                    <td class="px-4 py-3 brand">{{ $item->brand ?? '-' }}</td>
                                    <td class="px-4 py-3 model">{{ $item->model ?? '-' }}</td>
                                    <td class="px-4 py-3 serial">{{ $item->serial_number ?? '-' }}</td>
                                    <td class="px-4 py-3 qty">{{ $item->qty }}</td>
                                    <td class="px-4 py-3 createdby">{{ $item->createdBy->name ?? '-' }}</td>
                                    <td class="px-4 py-3 status">
                                        @if ($item->status === 'baik')
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800">Baik</span>
                                        @else
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-md bg-red-100 text-red-700">Rusak</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 location">{{ $item->gudang->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-row items-center justify-center gap-1">
                                            @can('editequipment')
                                                <a href="{{ route('equipment.edit', $item->id) }}"
                                                    onclick="showFullScreenLoader();">
                                                    <x-action-button text="Edit" color="green" />
                                                </a>
                                            @endcan

                                            @can('deleteequipment')
                                                <form action="{{ route('equipment.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirmAndLoad('Are you sure to delete equipment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-action-button text="Delete" color="red" />
                                                </form>
                                            @endcan
                                            <a href="{{ route('equipment.show', $item->id) }}"
                                                onclick="showFullScreenLoader();">
                                                <x-action-button text="Details" color="blue" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No equipment found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div id="pollFooter"
            class="mt-2 mb-4 flex items-center justify-between text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 px-4">
            <span class="inline-flex items-center">
                <span id="pollDot" class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                <span id="pollLabel">Polling active</span>
            </span>
            <span>Last update: <span id="lastUpdatedAt">—</span></span>
        </div>

        <x-per-page-selector :route="'equipment.index'" :perPage="$perPage" :search="$search" :items="$equipment" />

        @push('scripts')
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const list = new List('equipmentList', {
                        valueNames: ['no', 'name', 'brand', 'serial', 'qty', 'createdby', 'status', 'location',
                            'model'
                        ],
                    });
                    window.equipmentList = list;

                    const sortHeaders = document.querySelectorAll('.sort');
                    const sortStates = {};

                    function applySortIcon(header, state) {
                        const icon = header.querySelector('.sort-icon');
                        icon.textContent = state === 1 ? '↑' : (state === 2 ? '↓' : '');
                    }

                    function reapplyCurrentSort() {
                        for (const h of sortHeaders) {
                            const key = h.getAttribute('data-sort');
                            const st = sortStates[key] ?? 0;
                            if (st === 1) {
                                list.sort(key, {
                                    order: 'asc'
                                });
                                break;
                            }
                            if (st === 2) {
                                list.sort(key, {
                                    order: 'desc'
                                });
                                break;
                            }
                        }
                    }
                    sortHeaders.forEach(header => {
                        const key = header.getAttribute('data-sort');
                        sortStates[key] = 0;
                        header.addEventListener('click', function() {
                            sortStates[key] = (sortStates[key] + 1) % 3;
                            sortHeaders.forEach(h => {
                                if (h !== header) {
                                    const ok = h.getAttribute('data-sort');
                                    sortStates[ok] = 0;
                                    applySortIcon(h, 0);
                                }
                            });
                            applySortIcon(header, sortStates[key]);
                            if (sortStates[key] === 1) list.sort(key, {
                                order: 'asc'
                            });
                            else if (sortStates[key] === 2) list.sort(key, {
                                order: 'desc'
                            });
                            else list.sort('', {
                                order: 'asc'
                            });
                        });
                    });

                    const tbody = document.getElementById('equipBody');
                    if (!tbody) return;

                    const pollDot = document.getElementById('pollDot');
                    const pollLbl = document.getElementById('pollLabel');
                    const lastLbl = document.getElementById('lastUpdatedAt');

                    function setPollUI(mode) {
                        if (!pollDot || !pollLbl) return;
                        if (mode === 'active') {
                            pollDot.className = 'inline-block w-2 h-2 rounded-full bg-green-500 mr-2';
                            pollLbl.textContent = 'Polling active';
                        } else if (mode === 'paused') {
                            pollDot.className = 'inline-block w-2 h-2 rounded-full bg-gray-400 mr-2';
                            pollLbl.textContent = 'Paused (tab hidden)';
                        } else {
                            pollDot.className = 'inline-block w-2 h-2 rounded-full bg-amber-500 mr-2';
                            pollLbl.textContent = 'Idle';
                        }
                    }

                    function updateLastUpdated() {
                        if (lastLbl) lastLbl.textContent = new Date().toLocaleString('id-ID');
                    }

                    let lastTs = tbody.dataset.lastTs || new Date().toISOString();
                    let baseOffset = parseInt(tbody.dataset.baseOffset || '0', 10);
                    const changesUrl = tbody.dataset.changesUrl;
                    const rowsUrl = tbody.dataset.rowsUrl;

                    let timer = null,
                        idle = 0;
                    const baseInterval = 10000;
                    const maxInterval = 60000; // 60s

                    function nextInterval() {
                        return Math.min(baseInterval + idle * 10000, maxInterval);
                    }

                    function schedule(ms, reason = '') {
                        clearTimeout(timer);
                        console.debug(
                            `[Equipment] Polling ${reason ? '('+reason+') ' : ''}→ next in ${(ms/1000).toFixed(1)}s`);
                        timer = setTimeout(tick, ms);
                    }

                    function renumber() {
                        const rows = tbody.querySelectorAll('tr[data-id]');
                        let i = 0;
                        rows.forEach(tr => {
                            const cell = tr.querySelector('.no');
                            if (cell) cell.textContent = (baseOffset + (++i)).toString();
                        });
                        list.reIndex();
                        reapplyCurrentSort();
                    }

                    async function tick() {
                        try {
                            if (document.hidden) {
                                setPollUI('paused');
                                console.debug('[Equipment] Tab hidden → pause polling');
                                schedule(maxInterval, 'hidden');
                                return;
                            }
                            setPollUI('active');
                            console.debug('[Equipment] Tick start', {
                                since: lastTs
                            });

                            const u = new URL(changesUrl, window.location.origin);
                            u.searchParams.set('since', lastTs);

                            tbody.querySelectorAll('tr[data-id]').forEach(tr => {
                                u.searchParams.append('visible[]', tr.getAttribute('data-id'));
                            });

                            const res = await fetch(u.toString(), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            if (!res.ok) throw new Error('changes fetch failed');
                            const j = await res.json();

                            const {
                                latest_ts,
                                created = [],
                                updated = [],
                                deleted = []
                            } = j || {};
                            let changed = false;

                            if (deleted.length) {
                                deleted.forEach(id => {
                                    const row = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (row) row.remove();
                                });
                                changed = true;
                            }

                            const need = [...new Set([...updated, ...created])];
                            if (need.length) {
                                const ru = new URL(rowsUrl, window.location.origin);
                                need.forEach(id => ru.searchParams.append('ids[]', id));
                                const html = await (await fetch(ru.toString(), {
                                    headers: {
                                        'Accept': 'text/html'
                                    }
                                })).text();

                                const tpl = document.createElement('template');
                                tpl.innerHTML = html.trim();
                                const fresh = Array.from(tpl.content.querySelectorAll('tr[data-id]'));

                                fresh.forEach(newRow => {
                                    const id = newRow.getAttribute('data-id');
                                    const old = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (old) old.replaceWith(newRow);
                                    else tbody.insertBefore(newRow, tbody.firstChild);
                                });
                                changed = true;
                                console.debug('[Equipment] Applied changes', {
                                    created: created.length,
                                    updated: updated.length,
                                    deleted: deleted.length
                                });
                            } else {
                                console.debug('[Equipment] No changes');
                            }

                            if (latest_ts) lastTs = latest_ts;
                            updateLastUpdated();

                            if (changed) {
                                renumber();
                                idle = 0;
                                setPollUI('active');
                                schedule(nextInterval(), 'after-change');
                            } else {
                                idle++;
                                setPollUI('idle');
                                schedule(nextInterval(), 'no-change');
                            }
                        } catch (e) {
                            console.error('[Equipment] Polling error:', e);
                            setPollUI('idle');
                            schedule(maxInterval, 'error');
                        }
                    }

                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) {
                            console.debug('[Equipment] Tab visible → resume now');
                            idle = 0;
                            setPollUI('active');
                            schedule(200, 'resume-visible');
                        }
                    });

                    async function tryInstantHighlight() {
                        const params = new URLSearchParams(location.search);
                        const highlightId = params.get('highlight');
                        if (!highlightId) return;
                        try {
                            const ru = new URL(rowsUrl, window.location.origin);
                            ru.searchParams.append('ids[]', highlightId);
                            const html = await (await fetch(ru.toString(), {
                                headers: {
                                    'Accept': 'text/html'
                                }
                            })).text();
                            const tpl = document.createElement('template');
                            tpl.innerHTML = html.trim();
                            const newRow = tpl.content.querySelector('tr[data-id]');
                            if (newRow) {
                                const old = tbody.querySelector(`tr[data-id="${highlightId}"]`);
                                if (old) old.replaceWith(newRow);
                                else tbody.insertBefore(newRow, tbody.firstChild);
                                renumber();
                                newRow.classList.add('animate-pulse');
                                setTimeout(() => newRow.classList.remove('animate-pulse'), 1200);
                                console.debug('[Equipment] Instant highlight appended for ID', highlightId);
                            }
                        } catch (e) {
                            console.error('[Equipment] highlight fetch failed', e);
                        }
                    }

                    updateLastUpdated();
                    tryInstantHighlight();
                    schedule(baseInterval, 'init');
                });
            </script>
        @endpush
    </x-dashboard.sidebar>
</x-app-layout>
