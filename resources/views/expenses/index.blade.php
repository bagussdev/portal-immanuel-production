    <x-app-layout>
        <x-dashboard.sidebar>
            <x-alert-information />

            {{-- HEADER BAR --}}
            <div class="mb-4 sm:mt-5 text-xl font-bold text-gray-800 dark:text-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <div>Pengeluaran</div>

                    {{-- Kanan: FORM + ACTIONS --}}
                    <div class="w-full sm:w-auto flex flex-col sm:flex-row sm:items-center gap-2">

                        {{-- FILTERS --}}
                        <form method="GET" action="{{ route('expenses.index') }}" id="filterForm"
                            onsubmit="showFullScreenLoader();" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">

                            @php $curMonth = (int) ($month ?? now()->month); @endphp

                            <select name="month"
                                class="text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected((int) $m === (int) $curMonth)>
                                        {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>

                            <input type="number" name="year" value="{{ $year ?? now()->year }}"
                                class="w-24 text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search..."
                                class="text-xs sm:text-sm px-2 py-1.5 sm:px-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white w-36 sm:w-44">

                            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">

                            <x-action-button color="blue" type="submit" text="Terapkan" :dense="true" />
                        </form>

                        {{-- ACTIONS --}}
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto sm:justify-end">
                            @can('createexpenses')
                                <x-action-button :href="route('expenses.create', ['month' => $month, 'year' => $year])" onclick="showFullScreenLoader();" class="{{ $locks['can_add'] ? '' : 'pointer-events-none opacity-50' }}" color="blue" text="Tambah" :dense="true" />
                            @endcan

                            @can('manageexpenses')
                                {{-- Close (jika period ada & belum CLOSED) --}}
                                @if (($locks['period_exists'] ?? false) && ($locks['can_close'] ?? false) && ($period ?? null))
                                    <form
                                        action="{{ route('expenses.period.close', $period) }}?month={{ $month }}&year={{ $year }}"
                                        method="POST" onsubmit="return confirmAndLoad('Tutup periode ini (CLOSED)?');">
                                        @csrf @method('PATCH')
                                        <x-action-button color="red" text="Tutup" :dense="true" />
                                    </form>
                                @endif

                                {{-- Reopen (hanya jika CLOSED) --}}
                                @if (($locks['period_exists'] ?? false) && ($locks['can_reopen'] ?? false) && ($period ?? null))
                                    <form
                                        action="{{ route('expenses.period.reopen', $period) }}?month={{ $month }}&year={{ $year }}"
                                        method="POST"
                                        onsubmit="return confirmAndLoad('Reopen periode ini? Editing akan dibuka kembali.');">
                                        @csrf @method('PATCH')
                                        <x-action-button color="yellow" text="Buka lagi" :dense="true" />
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATUS PERIODE --}}
            <div class="flex flex-col gap-3 mb-4">
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        Period:
                    </span>
                    <span class="font-semibold">
                        {{ \Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F') }}
                        {{ $year }}
                    </span>

                    @if ($locks['period_exists'] ?? false)
                        @php
                            $pStatus = strtolower($period->status ?? '');
                            $pLabel = \Illuminate\Support\Str::of($pStatus)->title();
                            $badgeClasses = match ($pStatus) {
                                'open' => 'bg-green-100 text-green-800',
                                'reopen' => 'bg-yellow-100 text-yellow-800',
                                'closed' => 'bg-red-200 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-2 py-1 rounded text-xs {{ $badgeClasses }}">
                            {{ $pLabel }}
                        </span>
                    @else
                        <span class="px-2 py-1 rounded text-xs bg-rose-100 text-rose-800">
                            Not Opened
                        </span>
                    @endif
                </div>

                @unless ($locks['period_exists'] ?? false)
                    <div class="text-xs text-gray-600 dark:text-gray-300">
                        @if ($locks['is_current'] ?? false)
                            Periode bulan berjalan akan otomatis OPEN saat ada transaksi pertama.
                        @elseif($locks['is_past'] ?? false)
                            Periode lampau tidak dapat di-Open. Gunakan <strong>Reopen</strong> jika period-nya sudah pernah
                            dibuat dan ditutup.
                        @else
                            Periode mendatang belum dapat dibuka sebelum waktunya.
                        @endif
                    </div>
                @endunless
            </div>

            {{-- SUMMARY CARDS --}}
            @php
                $count = (int) ($stats['count'] ?? 0);
                $total = (int) ($stats['total'] ?? 0);
                $avg = (int) ($stats['avg'] ?? 0);
            @endphp
            <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-gray-500 text-xs">Transactions</div>
                    <div class="font-semibold text-blue-600">{{ number_format($count, 0, ',', '.') }}</div>
                </div>
                <div class="rounded border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-gray-500 text-xs">Total</div>
                    <div class="font-bold">{{ 'Rp ' . number_format($total, 0, ',', '.') }}</div>
                </div>
                <div class="rounded border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-gray-500 text-xs">Avg / Txn</div>
                    <div class="font-semibold">{{ 'Rp ' . number_format($avg, 0, ',', '.') }}</div>
                </div>
            </div>

            <hr class="h-[3px] mb-6 bg-gray-200 border-0 dark:bg-gray-700 w-full">

            {{-- TABLE + POLLING --}}
            <div class="w-full overflow-x-auto">
                <div class="min-w-full inline-block align-middle">
                    <div class="overflow-hidden shadow rounded-lg bg-white dark:bg-gray-800" id="expenseList"
                        data-month="{{ $month }}" data-year="{{ $year }}"
                        data-search="{{ $search }}" data-changes-url="{{ route('expenses.sync.changes') }}"
                        data-rows-url="{{ route('expenses.rows') }}" data-latest-ts="{{ $latestTs ?? '' }}">

                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3 text-left">Expense No</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3 text-left">Name</th>
                                    <th class="px-4 py-3">Qty</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3 text-left">Created By</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>

                            <tbody id="expenses_tbody"
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($rows as $row)
                                    @php
                                        $canAct = $allowActions ?? false;
                                    @endphp
                                    <tr data-id="{{ $row->id }}">
                                        <td class="px-4 py-3">
                                            {{ ($baseOffset ?? 0) + $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3 text-left font-medium">{{ $row->expense_number }}</td>
                                        <td class="px-4 py-3">{{ optional($row->expense_date)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-left">{{ $row->name }}</td>
                                        <td class="px-4 py-3">{{ number_format((int) $row->qty, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 font-semibold">
                                            {{ 'Rp ' . number_format((int) $row->total, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-left">{{ $row->creator->name ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center items-center gap-1">
                                                @php $canAct = ($allowActions ?? ($locks['can_add'] ?? false)); @endphp
                                                @can('editexpenses')
                                                    <x-action-button :href="route('expenses.edit', $row)" onclick="showFullScreenLoader();" class="{{ $canAct ? '' : 'pointer-events-none opacity-50' }}" color="green" text="Edit" :dense="true" />
                                                @endcan
                                                @can('deleteexpenses')
                                                    <form action="{{ route('expenses.destroy', $row) }}" method="POST"
                                                        onsubmit="return confirmAndLoad('Hapus expense ini?');"
                                                        class="{{ $canAct ? '' : 'pointer-events-none opacity-50' }}">
                                                        @csrf @method('DELETE')
                                                        <x-action-button color="red" text="Hapus" :dense="true" />
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                            @if (!($locks['period_exists'] ?? false))
                                                @if ($locks['is_current'] ?? false)
                                                    Period akan otomatis OPEN saat ada transaksi pertama.
                                                @elseif($locks['is_past'] ?? false)
                                                    Periode lampau tidak bisa di-Open. Reopen hanya tersedia bila period
                                                    sebelumnya sudah ada dan ditutup.
                                                @else
                                                    Belum ada data untuk periode ini.
                                                @endif
                                            @else
                                                Tidak ada data expense untuk periode ini.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Footer status polling --}}
            <div id="pollFooter"
                class="mt-2 mb-4 flex items-center justify-between text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 px-4">
                <span class="inline-flex items-center">
                    <span id="pollDot" class="inline-block w-2 h-2 rounded-full mr-2"></span>
                    <span id="pollLabel">Polling off</span>
                </span>
                <span>Last update: <span id="lastUpdatedAt">—</span></span>
            </div>

            {{-- Pagination --}}
            <x-per-page-selector :route="'expenses.index'" :perPage="$perPage ?? 10" :search="$search ?? ''" :items="$rows" />

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const box = document.getElementById('expenseList');
                        if (!box) return;

                        const tbody = document.getElementById('expenses_tbody');
                        const changesUrl = box.dataset.changesUrl;
                        const rowsUrl = box.dataset.rowsUrl;
                        const month = box.dataset.month;
                        const year = box.dataset.year;
                        const search = box.dataset.search || '';
                        let latestTs = box.dataset.latestTs || '';

                        const pollDot = document.getElementById('pollDot');
                        const pollLbl = document.getElementById('pollLabel');
                        const lastLbl = document.getElementById('lastUpdatedAt');

                        function setFooter(mode, ts = '') {
                            if (pollDot && pollLbl) {
                                if (mode === 'active') {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-green-500';
                                    pollLbl.textContent = 'Polling active';
                                } else if (mode === 'error') {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-rose-500';
                                    pollLbl.textContent = 'Polling error, retrying…';
                                } else if (mode === 'paused') {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-gray-400';
                                    pollLbl.textContent = 'Polling paused';
                                } else {
                                    pollDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-gray-400';
                                    pollLbl.textContent = 'Polling off';
                                }
                            }
                            if (lastLbl) {
                                if (!ts) lastLbl.textContent = '—';
                                else {
                                    try {
                                        lastLbl.textContent = new Date(ts).toLocaleString('id-ID');
                                    } catch {
                                        lastLbl.textContent = '—';
                                    }
                                }
                            }
                        }

                        // period belum ada (lampau) → polling off
                        if (!changesUrl || !rowsUrl || !latestTs) {
                            setFooter('off', '');
                            return;
                        }
                        setFooter('active', latestTs);

                        function visibleIds() {
                            return Array.from(tbody.querySelectorAll('tr[data-id]'))
                                .map(tr => tr.getAttribute('data-id'))
                                .filter(Boolean);
                        }

                        function renumber() {
                            let i = 0;
                            tbody.querySelectorAll('tr[data-id]').forEach(tr => {
                                const cell = tr.querySelector('td:first-child');
                                if (cell) cell.textContent = (++i).toString();
                            });
                        }

                        async function tick() {
                            try {
                                if (document.hidden) {
                                    setFooter('paused', latestTs);
                                    return;
                                }

                                const params = new URLSearchParams({
                                    since: latestTs,
                                    month,
                                    year,
                                    search
                                });
                                visibleIds().forEach(id => params.append('visible[]', id));

                                const res = await fetch(`${changesUrl}?${params.toString()}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (!res.ok) {
                                    setFooter('error', latestTs);
                                    return;
                                }

                                const data = await res.json();

                                if (data.latest_ts) {
                                    latestTs = data.latest_ts;
                                    setFooter('active', latestTs);
                                }

                                const need = [...new Set([...(data.created || []), ...(data.updated || [])])];
                                const del = data.deleted || [];

                                del.forEach(id => {
                                    const tr = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (tr) tr.remove();
                                });

                                if (need.length === 0) {
                                    return;
                                }

                                const p = new URLSearchParams({
                                    month,
                                    year
                                });
                                need.forEach(id => p.append('ids[]', id));

                                const htmlRes = await fetch(`${rowsUrl}?${p.toString()}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (!htmlRes.ok) return;

                                const html = await htmlRes.text();

                                const temp = document.createElement('tbody');
                                temp.innerHTML = html.trim();

                                Array.from(temp.children).forEach(newTr => {
                                    const id = newTr.getAttribute('data-id');
                                    const old = tbody.querySelector(`tr[data-id="${id}"]`);
                                    if (old) old.replaceWith(newTr);
                                    else tbody.prepend(newTr);
                                });

                                renumber();
                            } catch (_) {
                                setFooter('error', latestTs);
                            }
                        }

                        setInterval(tick, 6000);
                        document.addEventListener('visibilitychange', () => {
                            if (!document.hidden) setFooter('active', latestTs);
                        });
                    });
                </script>
            @endpush
        </x-dashboard.sidebar>
    </x-app-layout>
