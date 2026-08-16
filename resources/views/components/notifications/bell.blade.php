@props([
    'limit' => 10,
    'syncUrl' => route('notifications.sync.changes'),
    'rowsUrl' => route('notifications.rows'),
    'readAllUrl' => route('notifications.readAll'),
    'readUrlBase' => url('/notifications'),
    'class' => '',
])

@php
    use Illuminate\Support\Str;
    $uid = 'nb-' . Str::uuid();
    $limit = (int) ($limit ?: 10);
@endphp

<div id="{{ $uid }}-wrap" class="relative">
    <button id="{{ $uid }}-toggle" type="button" class="relative p-0 focus:outline-none {{ $class }}"
        aria-label="Notifications" aria-expanded="false" aria-haspopup="true">
        <span class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 w-9 h-9">
            <svg class="w-[22px] h-[22px] text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.7">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 10-12 0v.75a8.967 8.967 0 01-2.311 6.022c1.766.68 3.55 1.1 5.454 1.31m5.714 0a24.24 24.24 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
        </span>
        <span id="{{ $uid }}-dot"
            class="absolute -top-1 -right-1 inline-flex items-center justify-center
                 min-w-[16px] h-4 px-1 rounded-full text-[10px] font-semibold
                 bg-red-500 text-white hidden"></span>
    </button>

    <div id="{{ $uid }}-overlay" class="fixed inset-0 bg-black/30 z-[200] hidden sm:hidden"></div>

    <div id="{{ $uid }}-menu"
        class="hidden z-[201]
              fixed inset-x-2 top-14 sm:inset-auto sm:absolute sm:right-0 sm:mt-2
              w-[calc(100vw-1rem)] sm:w-80 md:w-96
              max-h-[70vh] sm:max-h-[80vh]
              bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
              rounded-xl shadow-xl overflow-hidden">
        <div class="px-3 py-2 flex items-center justify-between bg-gray-50 dark:bg-gray-700">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">Notifikasi</div>
            <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-300">
                <button id="{{ $uid }}-markall" class="hover:underline">Tandai semua</button>
                <span class="inline-flex items-center gap-1">
                    <span id="{{ $uid }}-statusdot"
                        class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    <span class="hidden sm:inline">Polling aktif</span>
                </span>
            </div>
        </div>

        <ul id="{{ $uid }}-list"
            class="max-h-[60vh] sm:max-h-[70vh] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700"></ul>

        <div id="{{ $uid }}-empty" class="p-4 text-sm text-gray-500 dark:text-gray-300 hidden">
            Tidak ada notifikasi.
        </div>

        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 flex items-center justify-between text-xs">
            <a href="{{ route('notifications.index', [], false) ?? '#' }}"
                class="font-bold text-red-600 hover:underline">Lihat semua</a>
            <span id="{{ $uid }}-last" class="text-gray-400">—</span>
        </div>
    </div>
</div>

<script>
    (() => {
        const uid = @json($uid);
        const LIMIT = @json($limit);
        const SYNC_URL = @json($syncUrl);
        const ROWS_URL = @json($rowsUrl);
        const READALL_URL = @json($readAllUrl);
        const READ_BASE = @json($readUrlBase);

        const elToggle = document.getElementById(`${uid}-toggle`);
        const elMenu = document.getElementById(`${uid}-menu`);
        const elOverlay = document.getElementById(`${uid}-overlay`);
        const elDot = document.getElementById(`${uid}-dot`);
        const elList = document.getElementById(`${uid}-list`);
        const elEmpty = document.getElementById(`${uid}-empty`);
        const elLast = document.getElementById(`${uid}-last`);
        const elMarkAll = document.getElementById(`${uid}-markall`);
        const statusDot = document.getElementById(`${uid}-statusdot`);
        if (!elToggle || !elMenu) return;

        const isMobile = () => window.matchMedia('(max-width: 639px)').matches;

        function openMenu() {
            elMenu.classList.remove('hidden');
            elToggle.setAttribute('aria-expanded', 'true');
            if (isMobile()) elOverlay.classList.remove('hidden');
            poll();
        }

        function closeMenu() {
            elMenu.classList.add('hidden');
            elToggle.setAttribute('aria-expanded', 'false');
            elOverlay.classList.add('hidden');
        }

        elToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            elMenu.classList.contains('hidden') ? openMenu() : closeMenu();
        });
        elOverlay.addEventListener('click', closeMenu);
        document.addEventListener('click', (e) => {
            if (!elMenu.contains(e.target) && !elToggle.contains(e.target)) closeMenu();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeMenu();
        });

        let latestTs = new Date(0).toISOString();

        function visibleIds() {
            return Array.from(elList.querySelectorAll('li[data-id]')).map(li => parseInt(li.dataset.id, 10));
        }

        function setUnread(n) {
            if (!n) {
                elDot.classList.add('hidden');
                elDot.textContent = '';
            } else {
                elDot.classList.remove('hidden');
                elDot.textContent = n > 9 ? '9+' : String(n);
            }
        }

        function setEmpty() {
            elEmpty.classList.toggle('hidden', !!elList.querySelector('li[data-id]'));
        }

        function tickStamp() {
            elLast.textContent = new Date().toLocaleTimeString('id-ID', {
                hour12: false
            });
        }

        let backoff = 5000,
            MIN = 5000,
            MAX = 30000,
            timer = null;
        async function poll() {
            if (document.hidden) {
                statusDot.classList.replace('bg-green-500', 'bg-yellow-500');
                return schedule();
            }
            statusDot.classList.replace('bg-yellow-500', 'bg-green-500');

            try {
                const params = new URLSearchParams({
                    since: latestTs,
                    limit: String(LIMIT)
                });
                for (const id of visibleIds()) params.append('visible[]', id);

                const res = await fetch(`${SYNC_URL}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('sync failed');
                const data = await res.json();

                latestTs = data.latest_ts || latestTs;
                setUnread(data.unread_count || 0);

                (data.deleted || []).forEach(id => elList.querySelector(`li[data-id="${id}"]`)?.remove());

                const domIds = new Set(Array.from(elList.querySelectorAll('li[data-id]')).map(li => parseInt(li
                    .dataset.id, 10)));
                const missingTop = Array.isArray(data.top) ? data.top.filter(id => !domIds.has(id)) : [];
                const need = Array.from(new Set([...(data.created || []), ...(data.updated || []), ...
                    missingTop
                ]));

                if (need.length) {
                    const url = new URL(ROWS_URL, window.location.origin);
                    need.forEach(id => url.searchParams.append('ids[]', id));
                    const html = await (await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })).text();
                    const tmp = document.createElement('div');
                    tmp.innerHTML = html.trim();
                    tmp.querySelectorAll('li[data-id]').forEach(newLi => {
                        const id = newLi.getAttribute('data-id');
                        const old = elList.querySelector(`li[data-id="${id}"]`);
                        if (old) old.replaceWith(newLi);
                        else elList.prepend(newLi);
                    });
                }

                if (!elList.querySelector('li[data-id]') && (data.unread_count || 0) > 0) {
                    const url = new URL(ROWS_URL, window.location.origin);
                    url.searchParams.set('limit', String(LIMIT));
                    const html = await (await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })).text();
                    const tmp = document.createElement('div');
                    tmp.innerHTML = html.trim();
                    tmp.querySelectorAll('li[data-id]').forEach(li => elList.appendChild(li));
                }

                if (Array.isArray(data.top)) {
                    const map = new Map(Array.from(elList.querySelectorAll('li[data-id]')).map(li => [parseInt(
                        li.dataset.id, 10), li]));
                    const frag = document.createDocumentFragment();
                    let c = 0;
                    data.top.forEach(id => {
                        const li = map.get(id);
                        if (li && c < LIMIT) {
                            frag.appendChild(li);
                            c++;
                        }
                    });
                    Array.from(elList.querySelectorAll('li[data-id]')).forEach(li => {
                        if (!data.top.includes(parseInt(li.dataset.id, 10))) li.remove();
                    });
                    elList.prepend(frag);
                }

                setEmpty();
                tickStamp();
                backoff = Math.max(MIN, Math.floor(backoff * 0.8));
            } catch {
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

        elMarkAll?.addEventListener('click', async () => {
            try {
                await fetch(READALL_URL, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });
                elList.querySelectorAll('[data-action="mark-read"]').forEach(btn => btn.remove());
                setUnread(0);
            } catch {}
        });

        elList.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-action="mark-read"]');
            if (!btn) return;
            const id = btn.getAttribute('data-id');
            try {
                await fetch(`${READ_BASE}/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });
                btn.remove();
            } catch {}
        });
    })();
</script>
