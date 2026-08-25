<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="mb-6">
            <a href="{{ route('payroll.index', ['month' => $month, 'year' => $year]) }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Create Payroll</h2>
                <div class="text-sm text-gray-500">
                    Period: {{ \Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F') }}
                    {{ $year }}
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.store') }}"
                onsubmit="return confirmAndLoad('Apakah Anda Yakin Membuat Gaji?')">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Employee</label>
                    <select name="user_id" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">— Select —</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>{{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Base</label>
                        <button type="button" id="btnAddBase"
                            class="text-xs sm:text-sm px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Add Row
                        </button>
                    </div>

                    <div id="baseList" class="space-y-2 overflow-x-auto w-full">
                    </div>
                    @error('bases')
                        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deduction</label>
                        <button type="button" id="btnAddDed"
                            class="text-xs sm:text-sm px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Add Row
                        </button>
                    </div>

                    <div id="dedList" class="space-y-2 overflow-x-auto w-full">
                    </div>
                    @error('deductions')
                        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3 rounded border dark:border-gray-700">
                        <div class="text-gray-500 text-xs">Base</div>
                        <div id="sumBase" class="font-semibold text-blue-600">0</div>
                    </div>
                    <div class="p-3 rounded border dark:border-gray-700">
                        <div class="text-gray-500 text-xs">Deduction</div>
                        <div id="sumDed" class="font-semibold text-red-600">0</div>
                    </div>
                    <div class="p-3 rounded border dark:border-gray-700">
                        <div class="text-gray-500 text-xs">Net Total</div>
                        <div id="sumNet" class="font-bold">0</div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-2">
                    <x-action-button type="submit" text="Save" color="blue" />
                </div>
            </form>
        </div>

        <template id="tplBaseRow">
            <div
                class="base-row flex flex-nowrap items-center gap-2 border rounded-md p-2 dark:border-gray-700 w-full overflow-x-auto">
                <input type="text" name="bases[name][]" placeholder="Nama komponen gaji (mis. Gaji Pokok/Tunjangan)"
                    class="flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />

                <input type="text" name="bases[amount][]" placeholder="0"
                    class="rp flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-right" />

                <button type="button" aria-label="Remove"
                    class="btnDelBase shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 dark:border-gray-600
                        hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600">
                    ×
                </button>
            </div>
        </template>

        <template id="tplDedRow">
            <div
                class="ded-row flex flex-nowrap items-center gap-2 border rounded-md p-2 dark:border-gray-700 w-full overflow-x-auto">
                <input type="text" name="deductions[name][]" placeholder="Nama potongan"
                    class="flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                <input type="text" name="deductions[amount][]" placeholder="0"
                    class="rp flex-1 min-w-[240px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-right" />
                <button type="button" aria-label="Remove"
                    class="btnDelRow shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 dark:border-gray-600
                        hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600">
                    ×
                </button>
            </div>
        </template>

        <script>
            (function() {
                const q = sel => document.querySelector(sel);
                const qa = sel => Array.from(document.querySelectorAll(sel));

                const formatRp = (num) => {
                    num = Number.isFinite(num) ? num : 0;
                    return num.toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    });
                };
                const unformatRp = (s) => {
                    if (!s) return 0;
                    s = String(s).replace(/[^\d]/g, '');
                    return parseInt(s || '0', 10);
                };

                function bindRpInput(el) {
                    if (el.dataset.bound === '1') return;
                    el.dataset.bound = '1';
                    el.addEventListener('input', () => {
                        el.value = formatRp(unformatRp(el.value));
                        computeSummary();
                    });
                    el.addEventListener('blur', () => {
                        el.value = formatRp(unformatRp(el.value));
                        computeSummary();
                    });
                }

                function computeSummary() {
                    const bases = qa('input[name="bases[amount][]"]').reduce((sum, e) => sum + unformatRp(e.value), 0);
                    const deds = qa('input[name="deductions[amount][]"]').reduce((sum, e) => sum + unformatRp(e.value), 0);
                    q('#sumBase').textContent = formatRp(bases);
                    q('#sumDed').textContent = formatRp(deds);
                    q('#sumNet').textContent = formatRp(bases - deds);
                }

                function addBaseRow(name = '', amount = '') {
                    const tpl = q('#tplBaseRow');
                    const list = q('#baseList');
                    const node = tpl.content.cloneNode(true);
                    list.appendChild(node);

                    const row = list.lastElementChild;
                    const nameEl = row.querySelector('input[name="bases[name][]"]');
                    const amtEl = row.querySelector('input[name="bases[amount][]"]');
                    const btn = row.querySelector('.btnDelBase');

                    nameEl.value = name || '';
                    if (amount) {
                        const raw = String(amount).replace(/[^\d]/g, '');
                        amtEl.value = formatRp(parseInt(raw || '0', 10));
                    }

                    bindRpInput(amtEl);
                    btn.addEventListener('click', () => {
                        row.remove();
                        computeSummary();
                    });
                    computeSummary();
                }

                function addDedRow(name = '', amount = '') {
                    const tpl = q('#tplDedRow');
                    const list = q('#dedList');
                    const node = tpl.content.cloneNode(true);
                    list.appendChild(node);

                    const row = list.lastElementChild;
                    const nameEl = row.querySelector('input[name="deductions[name][]"]');
                    const amtEl = row.querySelector('input[name="deductions[amount][]"]');
                    const btn = row.querySelector('.btnDelRow');

                    nameEl.value = name || '';
                    if (amount) {
                        const raw = String(amount).replace(/[^\d]/g, '');
                        amtEl.value = formatRp(parseInt(raw || '0', 10));
                    }

                    bindRpInput(amtEl);
                    btn.addEventListener('click', () => {
                        row.remove();
                        computeSummary();
                    });
                    computeSummary();
                }

                const btnAddBase = q('#btnAddBase');
                if (btnAddBase) btnAddBase.addEventListener('click', () => addBaseRow());

                const btnAddDed = q('#btnAddDed');
                if (btnAddDed) btnAddDed.addEventListener('click', () => addDedRow());

                @php
                    $oldBaseNames = old('bases.name', []);
                    $oldBaseAmts = old('bases.amount', []);
                    $oldDedNames = old('deductions.name', []);
                    $oldDedAmts = old('deductions.amount', []);
                @endphp

                if (@json(!empty($oldBaseNames))) {
                    @foreach ($oldBaseNames as $i => $nm)
                        addBaseRow(@json($nm), @json($oldBaseAmts[$i] ?? ''));
                    @endforeach
                } else {
                    addBaseRow('Gaji Pokok', '');
                }

                if (@json(!empty($oldDedNames))) {
                    @foreach ($oldDedNames as $i => $nm)
                        addDedRow(@json($nm), @json($oldDedAmts[$i] ?? ''));
                    @endforeach
                }

                qa('.rp').forEach(bindRpInput);
                computeSummary();
            })();
        </script>
    </x-dashboard.sidebar>
</x-app-layout>
