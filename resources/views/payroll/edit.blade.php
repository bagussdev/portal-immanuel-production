<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="mb-6">
            <a href="{{ route('payroll.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}"
                onclick="showFullScreenLoader();"
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
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Edit Payroll</h2>
                <div class="text-sm text-gray-500">
                    Period:
                    {{ \Carbon\Carbon::create()->month($payroll->period->month)->locale('id')->translatedFormat('F') }}
                    {{ $payroll->period->year }}
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.update', $payroll->id) }}"
                onsubmit="return confirmAndLoad('Apakah anda yakin mengedit gaji ini?')">
                @csrf
                @method('PUT')

                <input type="hidden" name="month" value="{{ $payroll->month }}">
                <input type="hidden" name="year" value="{{ $payroll->year }}">
                <input type="hidden" name="user_id" value="{{ $payroll->user_id }}">

                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Employee</label>
                    <select disabled
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" @selected($u->id == $payroll->user_id)>{{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Base</label>
                        <button type="button" id="btnAddBase"
                            class="text-xs sm:text-sm px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Add Row
                        </button>
                    </div>

                    <div id="baseList" class="space-y-2 w-full"></div>
                    <div id="baseDeleteBin"></div>

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

                    <div id="dedList" class="space-y-2 w-full"></div>
                    <div id="dedDeleteBin"></div>

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
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">{{ old('notes', $payroll->notes) }}</textarea>
                </div>

                <div class="flex gap-2">
                    <x-action-button type="submit" text="Update" color="blue" />
                </div>
            </form>
        </div>

        <template id="tplBaseRow">
            <div class="base-row flex flex-nowrap items-center gap-2 border rounded-md p-2 dark:border-gray-700 w-full">
                <input type="hidden" name="bases[id][]">

                <input type="text" name="bases[name][]" placeholder="Nama komponen gaji"
                    class="flex-1 min-w-[220px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />

                <input type="text" name="bases[amount][]" placeholder="0"
                    class="rp flex-1 min-w-[220px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-right"
                    inputmode="numeric" autocomplete="off" />

                <button type="button" aria-label="Remove"
                    class="btnDelBase shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 dark:border-gray-600
                        hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600">×</button>
            </div>
        </template>

        <template id="tplDedRow">
            <div class="ded-row flex flex-nowrap items-center gap-2 border rounded-md p-2 dark:border-gray-700 w-full">
                <input type="hidden" name="deductions[id][]">

                <input type="text" name="deductions[name][]" placeholder="Nama potongan"
                    class="flex-1 min-w-[220px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />

                <input type="text" name="deductions[amount][]" placeholder="0"
                    class="rp flex-1 min-w-[220px] rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-right"
                    inputmode="numeric" autocomplete="off" />

                <button type="button" aria-label="Remove"
                    class="btnDelRow shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 dark:border-gray-600
                        hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600">×</button>
            </div>
        </template>

        <script>
            (function() {
                const q = s => document.querySelector(s);
                const qa = s => Array.from(document.querySelectorAll(s));

                const formatRp = (num) => {
                    num = Number.isFinite(num) ? num : 0;
                    return num.toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    });
                };

                const unformatRp = (s) => {
                    if (!s) return 0;
                    s = String(s).trim();
                    s = s.replace(/([.,]\d{1,2})\s*$/, '');
                    s = s.replace(/[^\d]/g, '');
                    return parseInt(s || '0', 10);
                };

                function bindRpInput(el) {
                    if (el.dataset.bound === '1') return;
                    el.dataset.bound = '1';

                    if (el.value) el.value = formatRp(unformatRp(el.value));

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

                function addBaseRow(name = '', amount = '', id = '') {
                    const tpl = q('#tplBaseRow');
                    const list = q('#baseList');
                    const node = tpl.content.cloneNode(true);
                    list.appendChild(node);

                    const row = list.lastElementChild;
                    const idEl = row.querySelector('input[name="bases[id][]"]');
                    const nameEl = row.querySelector('input[name="bases[name][]"]');
                    const amtEl = row.querySelector('input[name="bases[amount][]"]');
                    const btn = row.querySelector('.btnDelBase');

                    idEl.value = id || '';
                    nameEl.value = name || '';
                    if (amount !== '') amtEl.value = formatRp(unformatRp(String(amount)));

                    bindRpInput(amtEl);

                    btn.addEventListener('click', () => {
                        if (idEl.value) {
                            const bin = q('#baseDeleteBin');
                            const hid = document.createElement('input');
                            hid.type = 'hidden';
                            hid.name = 'bases_delete[]';
                            hid.value = idEl.value;
                            bin.appendChild(hid);
                        }
                        row.remove();
                        computeSummary();
                    });

                    computeSummary();
                }

                function addDedRow(name = '', amount = '', id = '') {
                    const tpl = q('#tplDedRow');
                    const list = q('#dedList');
                    const node = tpl.content.cloneNode(true);
                    list.appendChild(node);

                    const row = list.lastElementChild;
                    const idEl = row.querySelector('input[name="deductions[id][]"]');
                    const nEl = row.querySelector('input[name="deductions[name][]"]');
                    const aEl = row.querySelector('input[name="deductions[amount][]"]');
                    const btn = row.querySelector('.btnDelRow');

                    idEl.value = id || '';
                    nEl.value = name || '';
                    if (amount !== '') aEl.value = formatRp(unformatRp(String(amount)));

                    bindRpInput(aEl);

                    btn.addEventListener('click', () => {
                        if (idEl.value) {
                            const bin = q('#dedDeleteBin');
                            const hid = document.createElement('input');
                            hid.type = 'hidden';
                            hid.name = 'deductions_delete[]';
                            hid.value = idEl.value;
                            bin.appendChild(hid);
                        }
                        row.remove();
                        computeSummary();
                    });

                    computeSummary();
                }

                qa('.rp').forEach(bindRpInput);

                q('#btnAddBase')?.addEventListener('click', () => addBaseRow());
                q('#btnAddDed')?.addEventListener('click', () => addDedRow());

                @php
                    $oldBaseIds = old('bases.id', []);
                    $oldBaseNames = old('bases.name', []);
                    $oldBaseAmts = old('bases.amount', []);
                    $oldDedIds = old('deductions.id', []);
                    $oldDedNames = old('deductions.name', []);
                    $oldDedAmts = old('deductions.amount', []);
                @endphp
                @if (!empty($oldBaseNames))
                    @foreach ($oldBaseNames as $i => $nm)
                        addBaseRow(@json($nm), @json($oldBaseAmts[$i] ?? ''),
                            @json($oldBaseIds[$i] ?? ''));
                    @endforeach
                @else
                    @foreach ($baseItems as $it)
                        addBaseRow(@json($it->name), @json($it->amount),
                            @json($it->id));
                    @endforeach
                @endif

                @if (!empty($oldDedNames))
                    @foreach ($oldDedNames as $i => $nm)
                        addDedRow(@json($nm), @json($oldDedAmts[$i] ?? ''), @json($oldDedIds[$i] ?? ''));
                    @endforeach
                @else
                    @foreach ($deductionItems as $it)
                        addDedRow(@json($it->name), @json($it->amount),
                            @json($it->id));
                    @endforeach
                @endif

                computeSummary();
            })();
        </script>
    </x-dashboard.sidebar>
</x-app-layout>
