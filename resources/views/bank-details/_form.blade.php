<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <section class="ip-card ip-card-body">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nama pilihan <span class="text-red-500">*</span>
                <input name="label" required maxlength="100" value="{{ old('label', $bankDetail->label) }}" class="ip-input mt-2" placeholder="Contoh: Sugito">
                <x-input-error :messages="$errors->get('label')" class="mt-2" />
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Email
                <input type="email" name="email" value="{{ old('email', $bankDetail->email) }}" class="ip-input mt-2" placeholder="nama@email.com">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nama bank
                <input name="bank_name" value="{{ old('bank_name', $bankDetail->bank_name) }}" class="ip-input mt-2" placeholder="Contoh: BCA">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Atas nama rekening
                <input name="account_name" value="{{ old('account_name', $bankDetail->account_name) }}" class="ip-input mt-2" placeholder="Nama pemilik rekening">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nomor rekening
                <input name="account_number" inputmode="numeric" value="{{ old('account_number', $bankDetail->account_number) }}" class="ip-input mt-2" placeholder="Nomor rekening">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Nomor HP
                <input name="phone" value="{{ old('phone', $bankDetail->phone) }}" class="ip-input mt-2" placeholder="08...">
            </label>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">NPWP <span class="font-normal text-slate-400">(boleh kosong)</span>
                <input name="npwp" value="{{ old('npwp', $bankDetail->npwp) }}" class="ip-input mt-2" placeholder="Kosongkan bila tidak digunakan">
            </label>
            <label class="flex items-center gap-3 self-end rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm font-bold text-slate-700 dark:border-white/10 dark:bg-white/[.04] dark:text-slate-200">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" @checked(old('active', $bankDetail->active ?? true)) class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                Aktif dan dapat dipilih di dokumen
            </label>
        </div>
        <label class="mt-5 block text-sm font-bold text-slate-700 dark:text-slate-200">Catatan internal
            <textarea name="notes" rows="3" class="ip-input mt-2" placeholder="Opsional">{{ old('notes', $bankDetail->notes) }}</textarea>
        </label>
    </section>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('bank-details.index') }}" class="ip-btn-secondary">Batal</a>
        <button class="ip-btn-primary px-6">{{ $submitLabel }}</button>
    </div>
</form>
