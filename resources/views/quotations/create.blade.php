<x-app-layout><x-dashboard.sidebar><x-alert-information />
    <div class="ip-page">
        <div><p class="ip-kicker">Quotation</p><h1 class="ip-title">Buat penawaran baru</h1><p class="ip-subtitle">Simpan sebagai draft, lalu setujui ketika kesepakatan sudah tercapai.</p></div>
        @include('documents._form', ['kind' => 'quotation', 'document' => new \App\Models\Quotation(), 'action' => route('quotations.store'), 'method' => 'POST', 'submitLabel' => 'Simpan Draft', 'cancelUrl' => route('quotations.index')])
    </div>
</x-dashboard.sidebar></x-app-layout>
