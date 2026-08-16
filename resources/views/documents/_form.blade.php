@php
    $isInvoice = $kind === 'invoice';
    $loadedItems = $document->relationLoaded('items') ? $document->items : collect();
    $initialItems = old(
        'items',
        $loadedItems
            ->map(function ($item, $index) use ($loadedItems) {
                $previous = $index > 0 ? $loadedItems->get($index - 1) : null;
                return [
                    'item_name' => $item->item_name,
                    'qty' => (float) $item->qty,
                    'length' => $item->length !== null ? (float) $item->length : '',
                    'unit_price' => (int) $item->unit_price,
                    'merge_price' => (bool) ($item->price_group && $previous?->price_group === $item->price_group),
                ];
            })
            ->values()
            ->all(),
    );
    if (!$initialItems) {
        $initialItems = [['item_name' => '', 'qty' => 1, 'length' => '', 'unit_price' => '', 'merge_price' => false]];
    }
    $discountAmount = $isInvoice ? $document->discount_value ?? 0 : $document->discount ?? 0;
    $discountMode = old('discount_mode', $document->discount_percent !== null ? 'percent' : 'amount');
    $taxMode = old('tax_mode', $document->tax_percent !== null ? 'percent' : 'amount');
    $workFlow = $isInvoice ? old('work_flow', $document->work_flow ?: 'install_teardown') : null;
@endphp

<form method="POST" action="{{ $action }}" id="documentForm" class="space-y-4">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <x-responsive-disclosure kicker="Informasi utama" title="Client & acara" :mobile-open="true">
        <div class="grid gap-4 md:grid-cols-2">
            <label class="block text-sm font-medium text-slate-700">Nama client
                <input name="client_name" list="clientSuggestions" required
                    value="{{ old('client_name', $document->client?->name) }}" class="ip-input mt-1"
                    placeholder="Ketik nama client">
                <datalist id="clientSuggestions">
                    @foreach ($clients as $client)
                        <option value="{{ $client->name }}">
                    @endforeach
                </datalist>
                <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
            </label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nama acara
                <input name="event_name" value="{{ old('event_name', $document->event_name) }}" class="ip-input mt-1"
                    placeholder="Contoh: Wedding Andi & Sinta">
            </label>
        </div>
    </x-responsive-disclosure>

    <x-responsive-disclosure kicker="Operasional" title="{{ $isInvoice ? 'Jadwal event' : 'Jadwal pekerjaan' }}"
        :mobile-open="$errors->hasAny(['location_event', 'event_date', 'loading_date', 'bongkaran_date', 'work_flow'])">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Lokasi
                <input name="location_event" value="{{ old('location_event', $document->location_event) }}"
                    class="ip-input mt-1" placeholder="Lokasi pekerjaan">
            </label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Tanggal acara
                <input type="date" name="event_date"
                    value="{{ old('event_date', optional($document->event_date)->format('Y-m-d')) }}"
                    class="ip-input mt-1">
            </label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Jadwal loading
                <input type="datetime-local" name="loading_date"
                    value="{{ old('loading_date', optional($document->loading_date)->format('Y-m-d\TH:i')) }}"
                    class="ip-input mt-1">
            </label>
            <label id="bongkaranField" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Jadwal
                bongkar
                <input type="datetime-local" name="bongkaran_date"
                    value="{{ old('bongkaran_date', optional($document->bongkaran_date)->format('Y-m-d\TH:i')) }}"
                    class="ip-input mt-1">
            </label>
            @if ($isInvoice)
                <div
                    class="md:col-span-2 xl:col-span-3 rounded-2xl border border-sky-100 bg-sky-50/60 p-4 dark:border-white/10 dark:bg-white/[.035]">
                    <p class="mb-3 text-sm font-extrabold text-slate-900 dark:text-white">Tipe pekerjaan</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach ([
        'install_teardown' => ['Pasang & Bongkar', 'Tahap pasang/loading dan tahap bongkar.'],
        'install_only' => ['Pasang saja', 'Satu tahap pasang tanpa bongkar.'],
        'one_way' => ['Sekali jalan', 'Satu tahap untuk transport atau pengiriman.'],
    ] as $value => [$label, $description])
                            <label class="cursor-pointer">
                                <input type="radio" name="work_flow" value="{{ $value }}" class="peer sr-only"
                                    @checked($workFlow === $value)>
                                <span
                                    class="block h-full rounded-xl border border-slate-200 bg-white p-3 transition peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:ring-2 peer-checked:ring-sky-500/15 dark:border-white/10 dark:bg-white/[.04] dark:peer-checked:border-sky-400 dark:peer-checked:bg-sky-500/10">
                                    <span
                                        class="block text-sm font-extrabold text-slate-900 dark:text-white">{{ $label }}</span>
                                    <span
                                        class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $description }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('work_flow')" class="mt-3" />
                </div>
            @endif
        </div>
    </x-responsive-disclosure>

    <x-responsive-disclosure kicker="Pembayaran" title="Detail rekening"
        :mobile-open="$errors->has('bank_detail_id')">
        <label class="block max-w-2xl text-sm font-medium text-slate-700 dark:text-slate-200">Rekening tujuan
            <select name="bank_detail_id" class="ip-input mt-1">
                <option value="">Tanpa detail rekening</option>
                @foreach ($bankDetails as $bankDetail)
                    <option value="{{ $bankDetail->id }}" @selected((string) old('bank_detail_id', $document->bank_detail_id) === (string) $bankDetail->id)>
                        {{ $bankDetail->label }}{{ $bankDetail->bank_name ? ' - ' . $bankDetail->bank_name : '' }}{{ $bankDetail->account_number ? ' (' . $bankDetail->account_number . ')' : ' - belum lengkap' }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('bank_detail_id')" class="mt-2" />
        </label>
    </x-responsive-disclosure>

    <x-responsive-disclosure kicker="Rincian" title="Item pekerjaan" :mobile-open="true">
        <div class="mb-3 flex justify-end">
            <button type="button" id="addItem"
                class="ip-btn border border-sky-200 bg-sky-50 text-sky-700 shadow-sm hover:bg-sky-100 dark:border-white/10 dark:bg-white/[.06] dark:text-white dark:hover:bg-white/10">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Tambah item
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="w-16 pb-2">Qty</th>
                        <th class="pb-2">Nama item</th>
                        <th class="w-24 pb-2">Panjang</th>
                        <th class="w-48 pb-2">Harga</th>
                        <th class="w-36 pb-2 text-right">Total</th>
                        <th class="w-12"></th>
                    </tr>
                </thead>
                <tbody id="itemRows" class="divide-y divide-slate-100 dark:divide-slate-700"></tbody>
            </table>
        </div>
        <x-input-error :messages="$errors->get('items')" class="mt-3" />
    </x-responsive-disclosure>

    <section class="grid gap-4 lg:grid-cols-[1fr,400px]">
        <x-responsive-disclosure kicker="Keterangan" title="Catatan tambahan"
            :mobile-open="$errors->hasAny(['notes', 'description', 'operational_notes', 'change_reason'])">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Catatan
                <textarea name="{{ $isInvoice ? 'notes' : 'description' }}" rows="4" class="ip-input mt-1"
                    placeholder="Catatan untuk client atau tim internal">{{ old($isInvoice ? 'notes' : 'description', $isInvoice ? $document->notes : $document->description) }}</textarea>
            </label>
            @if ($isInvoice)
                <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Catatan khusus tim
                    lapangan
                    <textarea name="operational_notes" rows="3" class="ip-input mt-1"
                        placeholder="Instruksi teknis tanpa harga, pembayaran, atau informasi keuangan">{{ old('operational_notes', $document->operational_notes) }}</textarea>
                    <span class="mt-1 block text-xs text-slate-400">Hanya catatan ini yang disalin ke halaman Mandor
                        dan User.</span>
                </label>
            @endif
            @if ($isInvoice && $document->exists && $document->status !== 'draft')
                <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Alasan perubahan
                    <input name="change_reason" value="{{ old('change_reason') }}" class="ip-input mt-1"
                        placeholder="Opsional, tetapi disarankan untuk audit">
                </label>
            @endif
        </x-responsive-disclosure>

        <x-responsive-disclosure kicker="Perhitungan" title="Ringkasan nilai" :mobile-open="true">
            <div class="space-y-4">
                <div class="flex min-h-6 justify-between gap-4 text-sm text-slate-500 dark:text-slate-400">
                    <span>Subtotal</span><strong id="summarySubtotal" class="text-slate-950 dark:text-white"></strong>
                </div>
                <div class="grid grid-cols-[minmax(0,1fr)_72px_minmax(0,1.15fr)] items-end gap-2">
                    <label class="min-w-0 text-xs font-semibold text-slate-500 dark:text-slate-400">Diskon
                        <select name="discount_mode" id="discountMode" class="ip-input mt-1 min-w-0 !py-2 text-xs">
                            <option value="percent" @selected($discountMode === 'percent')>Persen</option>
                            <option value="amount" @selected($discountMode === 'amount')>Nominal</option>
                        </select>
                    </label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percent"
                        id="discountPercent" value="{{ old('discount_percent', $document->discount_percent) }}"
                        class="ip-input min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]"
                        placeholder="%">
                    <input name="discount_value" id="discountValue"
                        value="{{ old('discount_value', $discountAmount ?: '') }}"
                        class="ip-input money min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]"
                        placeholder="Rp">
                </div>
                <div class="grid grid-cols-[minmax(0,1fr)_72px_minmax(0,1.15fr)] items-end gap-2">
                    <label class="min-w-0 text-xs font-semibold text-slate-500 dark:text-slate-400">Potongan pajak
                        <select name="tax_mode" id="taxMode" class="ip-input mt-1 min-w-0 !py-2 text-xs">
                            <option value="percent" @selected($taxMode === 'percent')>Persen</option>
                            <option value="amount" @selected($taxMode === 'amount')>Nominal</option>
                        </select>
                    </label>
                    <input type="number" step="0.01" min="0" max="100" name="tax_percent"
                        id="taxPercent" value="{{ old('tax_percent', $document->tax_percent) }}"
                        class="ip-input min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]"
                        placeholder="%">
                    <input name="tax_value" id="taxValue"
                        value="{{ old('tax_value', $document->tax_value ?: '') }}"
                        class="ip-input money min-w-0 !py-2 text-right text-sm disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-white/[.04]"
                        placeholder="Rp">
                </div>
                <div
                    class="flex min-h-12 items-end justify-between gap-4 border-t border-sky-100 pt-4 dark:border-white/10">
                    <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Total tagihan</span><strong
                        id="summaryGrand" class="text-2xl font-extrabold text-sky-700 dark:text-red-400"></strong>
                </div>
            </div>
        </x-responsive-disclosure>
    </section>

    <div
        class="sticky bottom-3 z-20 flex flex-wrap justify-end gap-3 rounded-2xl border border-sky-100 bg-white/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur md:static md:border-0 md:bg-transparent md:p-0 md:shadow-none dark:border-white/10 dark:bg-[#11151e]/95 md:dark:bg-transparent">
        <a href="{{ $cancelUrl }}" class="ip-btn-secondary">Batal</a>
        <button type="submit" class="ip-btn-primary px-6">{{ $submitLabel }}</button>
    </div>
</form>

<template id="itemTemplate">
    <tr class="item-row">
        <td class="py-2 pr-2"><input type="number" min="0.01" step="0.01" data-name="qty"
                class="qty ip-input" required></td>
        <td class="py-2 pr-2"><input data-name="item_name" class="ip-input" required></td>
        <td class="py-2 pr-2"><input type="number" min="0" step="0.01" data-name="length"
                class="length ip-input" placeholder="Opsional"></td>
        <td class="py-2 pr-2">
            <input data-name="unit_price" class="unit-price money ip-input text-right" required placeholder="Rp">
            <label title="Gabungkan harga dengan item sebelumnya"
                class="merge-price-control mt-2 hidden w-fit cursor-pointer items-center gap-2 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                <input type="checkbox" value="1" data-name="merge_price"
                    class="merge-price peer sr-only">
                <span class="relative h-5 w-9 rounded-full bg-slate-200 transition peer-focus-visible:ring-2 peer-focus-visible:ring-sky-500 peer-checked:bg-sky-600 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:after:translate-x-4 dark:bg-slate-700 dark:peer-checked:bg-red-600"></span>
                <span>Gabung harga</span>
            </label>
        </td>
        <td class="py-2 pr-2 text-right font-extrabold row-total"></td>
        <td class="py-2"><button type="button"
                class="remove-row flex h-9 w-9 items-center justify-center rounded-xl border border-red-100 bg-red-50 text-lg font-bold text-red-600 hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300"
                aria-label="Hapus item">&times;</button></td>
    </tr>
</template>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const initial = @json($initialItems);
        const rows = document.getElementById('itemRows');
        const template = document.getElementById('itemTemplate');
        const money = value => new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(Number(value) ||
            0)));
        const raw = value => Number(String(value ?? '').replace(/[^0-9]/g, '')) || 0;
        let index = 0;

        function addRow(data = {}) {
            const fragment = template.content.cloneNode(true);
            const row = fragment.querySelector('.item-row');
            row.querySelectorAll('[data-name]').forEach(input => {
                input.name = `items[${index}][${input.dataset.name}]`;
                if (input.type === 'checkbox') input.checked = Boolean(Number(data[input.dataset
                    .name] ?? 0) || data[input.dataset.name] === true);
                else input.value = data[input.dataset.name] ?? '';
            });
            const price = row.querySelector('.unit-price');
            price.value = raw(data.unit_price) > 0 ? money(raw(data.unit_price)) : '';
            row.querySelector('.remove-row').addEventListener('click', () => {
                if (rows.children.length > 1) row.remove();
                calculate();
            });
            row.querySelectorAll('input').forEach(input => input.addEventListener('input', calculate));
            row.querySelector('.merge-price').addEventListener('change', calculate);
            price.addEventListener('input', () => {
                price.value = money(raw(price.value));
                calculate();
            });
            rows.appendChild(row);
            index++;
            calculate();
        }

        function calculate() {
            let subtotal = 0;
            const allRows = [...rows.querySelectorAll('.item-row')];
            allRows.forEach((row, rowIndex) => {
                const merge = row.querySelector('.merge-price');
                const mergeControl = row.querySelector('.merge-price-control');
                if (rowIndex === 0) {
                    merge.checked = false;
                    merge.disabled = true;
                    mergeControl.classList.add('hidden');
                    mergeControl.classList.remove('flex');
                } else {
                    merge.disabled = false;
                    mergeControl.classList.remove('hidden');
                    mergeControl.classList.add('flex');
                }
                const isMerged = rowIndex > 0 && merge.checked;
                const priceInput = row.querySelector('.unit-price');
                priceInput.readOnly = isMerged;
                priceInput.classList.toggle('bg-sky-50', isMerged);
                if (isMerged) priceInput.value = '0';

                const qty = Number(row.querySelector('.qty').value) || 0;
                const length = Number(row.querySelector('.length').value) || 1;
                const price = raw(priceInput.value);
                const hasMergedFollower = rowIndex + 1 < allRows.length && allRows[rowIndex + 1]
                    .querySelector('.merge-price').checked;
                const total = isMerged ? 0 : (hasMergedFollower ? price : qty * length * price);
                subtotal += total;
                row.querySelector('.row-total').textContent = total > 0 ? `Rp ${money(total)}` : (
                    isMerged ? 'Digabung' : '');
            });
            const dMode = document.getElementById('discountMode').value;
            const tMode = document.getElementById('taxMode').value;
            const discount = dMode === 'percent' ? subtotal * ((Number(document.getElementById(
                'discountPercent').value) || 0) / 100) : raw(document.getElementById('discountValue').value);
            const afterDiscount = Math.max(subtotal - discount, 0);
            const tax = tMode === 'percent' ? afterDiscount * ((Number(document.getElementById('taxPercent')
                .value) || 0) / 100) : raw(document.getElementById('taxValue').value);
            document.getElementById('summarySubtotal').textContent = subtotal > 0 ? `Rp ${money(subtotal)}` :
                '';
            const grand = Math.max(afterDiscount - tax, 0);
            document.getElementById('summaryGrand').textContent = grand > 0 ? `Rp ${money(grand)}` : '';
            document.getElementById('discountPercent').disabled = dMode !== 'percent';
            document.getElementById('discountValue').disabled = dMode !== 'amount';
            document.getElementById('taxPercent').disabled = tMode !== 'percent';
            document.getElementById('taxValue').disabled = tMode !== 'amount';
        }

        initial.forEach(addRow);
        document.getElementById('addItem').addEventListener('click', () => addRow({
            qty: 1
        }));
        ['discountMode', 'taxMode', 'discountPercent', 'discountValue', 'taxPercent', 'taxValue'].forEach(id =>
            document.getElementById(id).addEventListener('input', calculate));
        document.querySelectorAll('.money').forEach(input => input.addEventListener('input', () => {
            input.value = money(raw(input.value));
            calculate();
        }));

        const workFlowInputs = [...document.querySelectorAll('input[name="work_flow"]')];
        const teardownField = document.getElementById('bongkaranField');

        function syncWorkFlowFields() {
            if (!teardownField || workFlowInputs.length === 0) return;
            const selected = workFlowInputs.find(input => input.checked)?.value;
            const teardownInput = teardownField.querySelector('input');
            const needsTeardown = selected === 'install_teardown';
            teardownField.classList.toggle('hidden', !needsTeardown);
            teardownInput.disabled = !needsTeardown;
        }
        workFlowInputs.forEach(input => input.addEventListener('change', syncWorkFlowFields));
        syncWorkFlowFields();
        calculate();
    });
</script>
