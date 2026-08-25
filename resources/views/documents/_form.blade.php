@php
    $isInvoice = $kind === 'invoice';
    $sourceLocations = $document->relationLoaded('locations') ? $document->locations : collect();
    $initialLocations = old('locations');
    if (!$initialLocations) {
        $initialLocations = $sourceLocations->map(fn($location) => [
            'name' => $location->name,
            'loading_date' => optional($location->loading_date)->format('Y-m-d\TH:i'),
            'teardown_date' => optional($location->teardown_date)->format('Y-m-d\TH:i'),
            'work_flow' => $location->work_flow ?: 'install_teardown',
            'items' => $location->items->map(function($item, $index) use ($location) {
                $previous = $index > 0 ? $location->items->get($index - 1) : null;
                return [
                    'item_name' => $item->item_name, 'qty' => (float) $item->qty,
                    'length' => $item->length !== null ? (float) $item->length : '',
                    'pricing_mode' => $item->pricing_mode ?: 'unit',
                    'unit_price' => (int) $item->unit_price ?: '', 'line_total' => (int) $item->total ?: '',
                    'merge_price' => (bool) ($item->price_group && $previous?->price_group === $item->price_group),
                ];
            })->values()->all(),
        ])->values()->all();
    }
    if (!$initialLocations) {
        $loadedItems = $document->relationLoaded('items') ? $document->items : collect();
        $initialLocations = [[
            'name' => $document->location_event,
            'loading_date' => optional($document->loading_date)->format('Y-m-d\TH:i'),
            'teardown_date' => optional($document->bongkaran_date)->format('Y-m-d\TH:i'),
            'work_flow' => $document->work_flow ?: 'install_teardown',
            'items' => $loadedItems->map(fn($item) => [
                'item_name' => $item->item_name, 'qty' => (float) $item->qty,
                'length' => $item->length !== null ? (float) $item->length : '',
                'pricing_mode' => $item->pricing_mode ?: 'unit', 'unit_price' => (int) $item->unit_price ?: '',
                'line_total' => (int) $item->total ?: '', 'merge_price' => false,
            ])->values()->all(),
        ]];
    }
    if (empty($initialLocations[0]['items'])) $initialLocations[0]['items'] = [['item_name'=>'','qty'=>1,'length'=>'','pricing_mode'=>'unit','unit_price'=>'','line_total'=>'','merge_price'=>false]];
    $discountAmount = $isInvoice ? ($document->discount_value ?? 0) : ($document->discount ?? 0);
    $discountMode = old('discount_mode', $document->discount_percent !== null ? 'percent' : 'amount');
    $taxMode = old('tax_mode', $document->tax_percent !== null ? 'percent' : 'amount');
    $eventStart = old('event_date', optional($document->event_date ?: $sourceLocations->first()?->event_start_date)->format('Y-m-d'));
    $eventEnd = old('event_end_date', optional($document->event_end_date ?: $sourceLocations->first()?->event_end_date)->format('Y-m-d'));
@endphp

<form method="POST" action="{{ $action }}" id="documentForm" class="space-y-4" @submit="if (!validateItemNames()) $event.preventDefault()"
    x-data="documentEditor(@js($initialLocations), @js($discountMode), @js($taxMode))">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <x-responsive-disclosure kicker="Informasi utama" title="Client & acara" :mobile-open="true">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nama client
                <input name="client_name" list="clientSuggestions" required value="{{ old('client_name', $document->client?->name) }}" class="ip-input mt-1" placeholder="Ketik nama client">
                <datalist id="clientSuggestions">@foreach($clients as $client)<option value="{{ $client->name }}">@endforeach</datalist>
                <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
            </label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nama acara<input name="event_name" value="{{ old('event_name', $document->event_name) }}" class="ip-input mt-1" placeholder="Nama acara"></label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Tanggal acara<input type="date" name="event_date" value="{{ $eventStart }}" class="ip-input mt-1"><x-input-error :messages="$errors->get('event_date')" class="mt-2" /></label>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Sampai (opsional)<input type="date" name="event_end_date" value="{{ $eventEnd }}" min="{{ $eventStart }}" class="ip-input mt-1"><x-input-error :messages="$errors->get('event_end_date')" class="mt-2" /></label>
        </div>
    </x-responsive-disclosure>

    <section class="ip-card overflow-hidden">
        <header class="flex flex-col gap-3 border-b border-sky-100 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
            <div><p class="ip-kicker">Lokasi & item</p><h2 class="ip-section-title mt-1">Rincian pekerjaan</h2></div>
            <button type="button" class="ip-btn-secondary" @click="addLocation()">+ Tambah lokasi</button>
        </header>
        <div class="space-y-4 p-4 sm:p-5">
            <template x-for="(location, locationIndex) in locations" :key="location.key">
                <article class="rounded-2xl border border-sky-100 bg-sky-50/35 p-4 dark:border-white/10 dark:bg-white/[.025]">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-extrabold text-slate-900 dark:text-white" x-text="`Lokasi ${locationIndex + 1}`"></h3>
                        <button type="button" x-show="locations.length > 1" @click="removeLocation(locationIndex)" class="text-xs font-extrabold text-red-600">Hapus lokasi</button>
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <label class="text-sm font-semibold text-slate-600 dark:text-slate-300">Lokasi<input :name="`locations[${locationIndex}][name]`" x-model="location.name" class="ip-input mt-1" placeholder="Nama lokasi"></label>
                        <label class="text-sm font-semibold text-slate-600 dark:text-slate-300">Loading<input type="datetime-local" :name="`locations[${locationIndex}][loading_date]`" x-model="location.loading_date" class="ip-input mt-1"></label>
                        <label class="text-sm font-semibold text-slate-600 dark:text-slate-300" x-show="location.work_flow === 'install_teardown'">Bongkar<input type="datetime-local" :name="`locations[${locationIndex}][teardown_date]`" x-model="location.teardown_date" class="ip-input mt-1"></label>
                    </div>
                    <p class="mt-4 text-xs font-extrabold uppercase tracking-wide text-slate-500">Tipe pekerjaan</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-3">
                        <template x-for="option in flowOptions" :key="option.value"><label class="cursor-pointer"><input type="radio" :name="`locations[${locationIndex}][work_flow]`" :value="option.value" x-model="location.work_flow" class="peer sr-only"><span class="block rounded-xl border border-sky-100 bg-white px-3 py-2 text-xs font-bold text-slate-600 peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-800 dark:border-white/10 dark:bg-white/[.04] dark:text-slate-300" x-text="option.label"></span></label></template>
                    </div>

                    <div class="mt-5">
                        <div class="-mx-1 overflow-x-auto overscroll-x-contain px-1 pb-1 [scrollbar-width:thin]">
                            <div class="min-w-[500px] lg:min-w-0">
                                <div class="mb-2 grid grid-cols-[48px,minmax(110px,1fr),58px,95px,95px,34px] gap-1.5 px-2 text-[9px] font-extrabold uppercase tracking-wide text-slate-400 lg:grid-cols-[72px,minmax(130px,1fr),88px,minmax(120px,145px),minmax(120px,145px),40px] lg:gap-2 lg:px-3 lg:text-[10px]">
                                    <span>Qty</span><span>Nama item</span><span>Panjang</span><span class="text-right">Harga satuan</span><span class="text-right">Total</span><span></span>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(item, itemIndex) in location.items" :key="item.key">
                                        <div class="rounded-xl border border-sky-100 bg-white p-2 transition focus-within:border-sky-400 lg:p-3 dark:border-white/10 dark:bg-[#10141d]">
                                            <div class="grid grid-cols-[48px,minmax(110px,1fr),58px,95px,95px,34px] items-center gap-1.5 lg:grid-cols-[72px,minmax(130px,1fr),88px,minmax(120px,145px),minmax(120px,145px),40px] lg:gap-2">
                                                <input type="number" min="0.01" step="0.01" :name="`locations[${locationIndex}][items][${itemIndex}][qty]`" x-model.number="item.qty" class="ip-input !px-2 !py-2 text-xs">
                                                <span class="relative block min-w-0"><input :name="`locations[${locationIndex}][items][${itemIndex}][item_name]`" x-model="item.item_name" @click="editItemName(item)" @keydown.enter.prevent="editItemName(item)" readonly required class="ip-input cursor-pointer truncate !py-2 pl-2 pr-8 text-xs font-bold text-slate-800 hover:border-sky-400 dark:text-white" placeholder="Nama item"><span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-sky-600 dark:text-red-400" aria-hidden="true">✎</span></span>
                                                <input type="number" min="0" step="0.01" :name="`locations[${locationIndex}][items][${itemIndex}][length]`" x-model="item.length" class="ip-input !px-2 !py-2 text-xs" placeholder="-">
                                                <input type="hidden" :name="`locations[${locationIndex}][items][${itemIndex}][pricing_mode]`" :value="item.pricing_mode">
                                                <input :name="`locations[${locationIndex}][items][${itemIndex}][unit_price]`" :value="money(item.unit_price)" @input="setUnitPrice(item, $event.target)" inputmode="numeric" class="ip-input !px-2 !py-2 text-right text-xs" placeholder="Rp">
                                                <input :name="`locations[${locationIndex}][items][${itemIndex}][line_total]`" :value="displayLineTotal(item)" @input="setLineTotal(item, $event.target)" @focus="$event.target.select()" :disabled="item.merge_price" inputmode="numeric" class="ip-input !px-2 !py-2 text-right text-xs font-extrabold text-sky-700 disabled:bg-sky-50 disabled:text-slate-400 dark:text-red-400" :placeholder="item.merge_price ? 'Gabung' : 'Total'">
                                                <button type="button" @click="removeItem(locationIndex,itemIndex)" class="flex h-9 items-center justify-center rounded-lg bg-red-50 font-bold text-red-600 hover:bg-red-100" aria-label="Hapus item">&times;</button>
                                            </div>
                                            <label x-show="itemIndex > 0" class="mt-2 inline-flex cursor-pointer items-center gap-2 rounded-lg px-1 py-1 text-[11px] font-bold text-slate-500"><input type="checkbox" value="1" :name="`locations[${locationIndex}][items][${itemIndex}][merge_price]`" x-model="item.merge_price" class="rounded border-sky-200 text-sky-600"> Gabung harga dengan item sebelumnya</label>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="addItem(locationIndex)" class="ip-btn-secondary mt-3 w-full sm:w-auto">+ Tambah item</button>
                    </div>
                </article>
            </template>
            <x-input-error :messages="$errors->get('locations')" />
        </div>
    </section>
    <template x-teleport="body">
        <div x-cloak x-show="itemModalOpen" @keydown.escape.window="closeItemName()" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="itemNameTitle">
            <div x-show="itemModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" @click="closeItemName()"></div>
            <div x-show="itemModalOpen" x-transition class="relative max-h-[calc(100dvh-2rem)] w-full max-w-2xl overflow-y-auto rounded-2xl border border-white/20 bg-white shadow-2xl sm:rounded-3xl dark:bg-[#11151e]">
                <div class="border-b border-sky-100 px-5 py-4 dark:border-white/10 sm:px-6">
                    <p class="ip-kicker">Rincian item</p>
                    <h2 id="itemNameTitle" class="mt-1 text-xl font-extrabold text-slate-950 dark:text-white">Nama item</h2>
                    <p class="mt-1 text-sm text-slate-500">Tulis nama pekerjaan atau barang dengan lengkap.</p>
                </div>
                <div class="p-5 sm:p-6">
                    <textarea x-ref="itemNameEditor" x-model="draftItemName" @keydown.enter.prevent="saveItemName()" maxlength="255" rows="4" class="ip-input min-h-32 resize-y text-base leading-7" placeholder="Contoh: Dekorasi panggung utama"></textarea>
                    <p class="mt-2 text-right text-xs font-bold text-slate-400"><span x-text="draftItemName.length"></span>/255</p>
                </div>
                <div class="flex justify-end gap-2 border-t border-sky-100 bg-slate-50 px-5 py-4 dark:border-white/10 dark:bg-white/[.03] sm:px-6">
                    <button type="button" @click="closeItemName()" class="ip-btn-secondary">Batal</button>
                    <button type="button" @click="saveItemName()" :disabled="!draftItemName.trim()" class="ip-btn-primary disabled:cursor-not-allowed disabled:opacity-50">Simpan nama</button>
                </div>
            </div>
        </div>
    </template>

    <x-responsive-disclosure kicker="Pembayaran" title="Detail rekening" :mobile-open="$errors->has('bank_detail_id')">
        <label class="block max-w-2xl text-sm font-medium text-slate-700 dark:text-slate-200">Rekening tujuan<select name="bank_detail_id" class="ip-input mt-1"><option value="">Tanpa detail rekening</option>@foreach($bankDetails as $bankDetail)<option value="{{ $bankDetail->id }}" @selected((string) old('bank_detail_id', $document->bank_detail_id) === (string) $bankDetail->id)>{{ $bankDetail->label }}{{ $bankDetail->bank_name ? ' - '.$bankDetail->bank_name : '' }}</option>@endforeach</select></label>
    </x-responsive-disclosure>

    <section class="grid gap-4 lg:grid-cols-[1fr,400px]">
        <x-responsive-disclosure kicker="Keterangan" title="Catatan tambahan" :mobile-open="$errors->hasAny(['notes','description','operational_notes','change_reason'])">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Catatan<textarea name="{{ $isInvoice ? 'notes' : 'description' }}" rows="4" class="ip-input mt-1">{{ old($isInvoice ? 'notes' : 'description', $isInvoice ? $document->notes : $document->description) }}</textarea></label>
            @if($isInvoice)<label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Catatan tim lapangan<textarea name="operational_notes" rows="3" class="ip-input mt-1">{{ old('operational_notes',$document->operational_notes) }}</textarea></label>@endif
            @if($isInvoice && $document->exists && $document->status !== 'draft')<label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Alasan perubahan<input name="change_reason" value="{{ old('change_reason') }}" class="ip-input mt-1"></label>@endif
        </x-responsive-disclosure>
        <x-responsive-disclosure kicker="Perhitungan" title="Ringkasan nilai" :mobile-open="true">
            <div class="space-y-4">
                <div class="flex justify-between text-sm"><span>Subtotal</span><strong x-text="rupiah(subtotal())"></strong></div>
                @foreach([['discount','Diskon',$discountAmount,$document->discount_percent],['tax','Potongan pajak',$document->tax_value,$document->tax_percent]] as [$prefix,$label,$amount,$percent])
                    <div class="grid grid-cols-[1fr,80px,1fr] items-end gap-2"><label class="text-xs font-semibold text-slate-500">{{ $label }}<select name="{{ $prefix }}_mode" x-model="{{ $prefix }}Mode" class="ip-input mt-1 !py-2"><option value="percent">Persen</option><option value="amount">Nominal</option></select></label><input type="number" step="0.01" min="0" max="100" name="{{ $prefix }}_percent" value="{{ old($prefix.'_percent',$percent) }}" x-ref="{{ $prefix }}Percent" :disabled="{{ $prefix }}Mode !== 'percent'" class="ip-input !py-2 text-right" placeholder="%"><input name="{{ $prefix }}_value" value="{{ old($prefix.'_value',$amount ?: '') }}" x-ref="{{ $prefix }}Value" :disabled="{{ $prefix }}Mode !== 'amount'" @input="$event.target.value = money($event.target.value)" class="ip-input !py-2 text-right" placeholder="Rp"></div>
                @endforeach
                <div class="flex items-end justify-between border-t border-sky-100 pt-4"><span class="text-sm font-semibold text-slate-500">Total tagihan</span><strong class="text-2xl font-extrabold text-sky-700 dark:text-red-400" x-text="rupiah(grandTotal())"></strong></div>
            </div>
        </x-responsive-disclosure>
    </section>
    <div class="sticky bottom-3 z-20 flex flex-wrap justify-end gap-3 rounded-2xl border border-sky-100 bg-white/95 p-3 shadow-xl backdrop-blur md:static md:border-0 md:bg-transparent md:p-0 md:shadow-none dark:border-white/10 dark:bg-[#11151e]/95"><a href="{{ $cancelUrl }}" class="ip-btn-secondary">Batal</a><button type="submit" class="ip-btn-primary px-6">{{ $submitLabel }}</button></div>
</form>

<script>
function documentEditor(initialLocations, discountMode, taxMode) {
    const row = () => ({ key: crypto.randomUUID(), item_name: '', qty: 1, length: '', pricing_mode: 'unit', unit_price: '', line_total: '', merge_price: false });
    const site = () => ({ key: crypto.randomUUID(), name: '', loading_date: '', teardown_date: '', work_flow: 'install_teardown', items: [row()] });
    const normalized = (initialLocations || []).map(location => ({ ...site(), ...location, items: (location.items || [row()]).map(item => ({ ...row(), ...item })) }));
    return {
        locations: normalized.length ? normalized : [site()], discountMode, taxMode,
        itemModalOpen: false, editingItem: null, draftItemName: '',
        flowOptions: [{value:'install_teardown',label:'Pasang & Bongkar'},{value:'install_only',label:'Pasang saja'},{value:'one_way',label:'Sekali jalan'}],
        addLocation() { this.locations.push(site()) }, removeLocation(index) { if (this.locations.length > 1) this.locations.splice(index,1) },
        addItem(index) { this.locations[index].items.push(row()) }, removeItem(location,item) { if (this.locations[location].items.length > 1) this.locations[location].items.splice(item,1) },
        editItemName(item) { this.editingItem = item; this.draftItemName = item.item_name || ''; this.itemModalOpen = true; this.$nextTick(() => this.$refs.itemNameEditor?.focus()) },
        closeItemName() { this.itemModalOpen = false; this.editingItem = null; this.draftItemName = '' },
        saveItemName() { const value = this.draftItemName.replace(/\s+/g,' ').trim(); if (!value || !this.editingItem) return; this.editingItem.item_name = value; this.closeItemName() },
        validateItemNames() { const empty = this.locations.flatMap(location => location.items).find(item => !String(item.item_name || '').trim()); if (!empty) return true; this.editItemName(empty); return false },
        raw(value) { return Number(String(value ?? '').replace(/[^0-9]/g,'')) || 0 }, money(value) { return new Intl.NumberFormat('id-ID').format(this.raw(value)) }, rupiah(value) { return value > 0 ? `Rp ${this.money(value)}` : 'Rp 0' },
        setUnitPrice(item,input) { item.unit_price = this.money(input.value); input.value = item.unit_price },
        setLineTotal(item,input) { const value = String(input.value ?? '').replace(/[^0-9]/g,''); item.pricing_mode = value ? 'total' : 'unit'; item.line_total = value ? this.money(value) : ''; input.value = this.displayLineTotal(item) },
        calculatedLineTotal(item) { return (Number(item.qty)||0) * ((Number(item.length)||0) > 0 ? Number(item.length) : 1) * this.raw(item.unit_price) },
        displayLineTotal(item) { if (item.merge_price) return ''; const total = item.pricing_mode === 'total' ? this.raw(item.line_total) : this.calculatedLineTotal(item); return total > 0 ? this.money(total) : '' },
        lineTotal(item) { if (item.merge_price) return 0; return item.pricing_mode === 'total' ? this.raw(item.line_total) : this.calculatedLineTotal(item) },
        subtotal() { return this.locations.reduce((sum,location) => sum + location.items.reduce((value,item) => value + this.lineTotal(item),0),0) },
        deduction(mode, percentRef, valueRef, base) { return mode === 'percent' ? Math.round(base * ((Number(percentRef?.value)||0)/100)) : this.raw(valueRef?.value) },
        grandTotal() { const subtotal=this.subtotal(); const discount=Math.min(this.deduction(this.discountMode,this.$refs.discountPercent,this.$refs.discountValue,subtotal),subtotal); const after=Math.max(subtotal-discount,0); return Math.max(after-Math.min(this.deduction(this.taxMode,this.$refs.taxPercent,this.$refs.taxValue,after),after),0) }
    }
}
</script>
