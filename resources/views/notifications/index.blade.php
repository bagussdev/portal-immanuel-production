{{-- resources/views/notifications/index.blade.php --}}
<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        @php
            // supaya komponen per-page dapet nilai konsisten
            $search = request('search');
            $perPage = (int) ($perPage ?? ($items->perPage() ?? 10));
        @endphp

        <div
            class="mb-4 sm:mt-5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-xl font-bold text-gray-800 dark:text-white">
            <div>Notifications</div>

            {{-- Controls (match gaya Quotation) --}}
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto text-sm">
                <form method="GET" action="{{ route('notifications.index') }}" id="filterForm"
                    class="flex items-center gap-2 flex-1 min-w-0">
                    <div class="shrink-0">
                        <x-date-filter-dropdown :action="route('notifications.index')" :startDate="request('start_date')" :endDate="request('end_date')"
                            formId="filterForm" />
                    </div>

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="flex-1 min-w-0 w-full sm:w-48 text-xs sm:text-sm px-3 py-2 rounded-md
                           border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                           focus:ring-purple-500" />

                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <x-action-button type="submit" class="px-3 py-2 text-xs sm:text-sm rounded-md" text="Search"
                        color="blue" />
                </form>

                {{-- Optional: mark all read --}}
                <form method="POST" action="{{ route('notifications.readAll') }}"
                    onsubmit="return confirmAndLoad('Tandai semua notifikasi sebagai terbaca?')">
                    @csrf
                    <x-action-button text="Mark all read" color="green"
                        class="basis-full sm:basis-auto w-full sm:w-auto justify-center text-xs sm:text-sm px-3 py-2"
                        type="submit" />
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full text-sm whitespace-nowrap min-w-[900px]">
                    <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-2 text-left w-28">Type</th>
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2 text-left hidden sm:table-cell">Message</th>
                            <th class="px-4 py-2 text-left w-40">Created</th>
                            <th class="px-4 py-2 text-left w-24">Status</th>
                            <th class="px-4 py-2 text-left w-24">Action</th>
                        </tr>
                    </thead>
                    <tbody id="notifIndexBody">
                        @forelse ($items as $n)
                            <tr data-id="{{ $n->id }}"
                                class="border-t dark:border-gray-700 {{ $n->read_at ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }}">
                                <td class="px-4 py-2">{{ $n->type }}</td>
                                <td class="px-4 py-2 max-w-[260px] truncate">
                                    {{ $n->title ?? (data_get($n->data, 'title') ?? ucfirst(str_replace('_', ' ', $n->type))) }}
                                </td>
                                <td class="px-4 py-2 max-w-[380px] truncate hidden sm:table-cell">
                                    {{ $n->message ?? data_get($n->data, 'message') }}
                                </td>
                                <td class="px-4 py-2">{{ $n->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2">
                                    @if (is_null($n->read_at))
                                        <span
                                            class="inline-flex items-center gap-1 text-xs text-blue-700 dark:text-blue-300">
                                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span> Unread
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">Read</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if (is_null($n->read_at))
                                        <form method="POST" action="{{ route('notifications.read', $n) }}"
                                            onsubmit="return confirmAndLoad('Mark this as read?')">
                                            @csrf
                                            <x-action-button text="Mark" color="green" type="submit"
                                                dense="true" />
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada notifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Polling status bar --}}
            <div class="px-3 py-2 text-xs text-gray-500 border-t dark:border-gray-700 flex items-center gap-2">
                <span id="idxStatusDot" class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span>Polling aktif</span>
                <span class="mx-1">•</span>
                <span>Last update: <span id="idxLastUpdate">—</span></span>
            </div>
        </div>

        {{-- Per-page selector + pagination --}}
        <x-per-page-selector :route="'notifications.index'" :perPage="$perPage" :search="$search" :items="$items" />
    </x-dashboard.sidebar>
</x-app-layout>

<script>
    (() => {
        const body = document.getElementById('notifIndexBody');
        const idxDot = document.getElementById('idxStatusDot');
        const idxLast = document.getElementById('idxLastUpdate');

        function visibleIds() {
            return Array.from(body.querySelectorAll('tr[data-id]')).map(tr => parseInt(tr.dataset.id, 10));
        }

        function updateStamp() {
            idxLast.textContent = new Date().toLocaleTimeString('id-ID', {
                hour12: false
            });
        }

        let latestTs = @json($latestTs);
        let backoff = 5000,
            MIN = 5000,
            MAX = 30000,
            timer = null;

        async function poll() {
            if (document.hidden) {
                idxDot.classList.replace('bg-green-500', 'bg-yellow-500');
                return schedule();
            }
            idxDot.classList.replace('bg-yellow-500', 'bg-green-500');

            try {
                const url = new URL(@json(route('notifications.index.sync.changes')), window.location.origin);
                url.searchParams.set('since', latestTs);

                // kirim filter yang sama
                const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
                for (const [k, v] of params) url.searchParams.set(k, v);

                for (const id of visibleIds()) url.searchParams.append('visible[]', id);

                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('sync failed');
                const data = await res.json();

                latestTs = data.latest_ts || latestTs;

                // delete first
                (data.deleted || []).forEach(id => {
                    const tr = body.querySelector(`tr[data-id="${id}"]`);
                    if (tr) tr.remove();
                });

                const need = Array.from(new Set([...(data.created || []), ...(data.updated || [])]));
                if (need.length) {
                    const rowsUrl = new URL(@json(route('notifications.index.rows')), window.location.origin);
                    // kirim filter sama juga
                    const params2 = new URLSearchParams(new FormData(document.getElementById('filterForm')));
                    for (const [k, v] of params2) rowsUrl.searchParams.set(k, v);
                    need.forEach(id => rowsUrl.searchParams.append('ids[]', id));

                    const html = await (await fetch(rowsUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })).text();
                    const tmp = document.createElement('tbody');
                    tmp.innerHTML = html.trim();

                    // replace/insert
                    tmp.querySelectorAll('tr[data-id]').forEach(newTr => {
                        const id = newTr.getAttribute('data-id');
                        const old = body.querySelector(`tr[data-id="${id}"]`);
                        if (old) old.replaceWith(newTr);
                        else body.prepend(newTr);
                    });
                }

                updateStamp();
                backoff = Math.max(MIN, Math.floor(backoff * 0.8));
            } catch (e) {
                backoff = Math.min(MAX, Math.floor(backoff * 1.6));
            } finally {
                schedule();
            }
        }

        function schedule() {
            if (timer) clearTimeout(timer);
            timer = setTimeout(poll, backoff);
        }
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                backoff = MIN;
                poll();
            }
        });
        schedule();
    })();
</script>
