<x-app-layout><x-dashboard.sidebar><x-alert-information />
        <div class="ip-page">
            <div>
                <p class="ip-kicker">Invoice</p>
                <h1 class="ip-title">Buat invoice draft</h1>
                <p class="ip-subtitle">Nomor resmi dibuat saat invoice diterbitkan.</p>
            </div>
            @include('documents._form', [
                'kind' => 'invoice',
                'document' => $invoice,
                'action' => route('invoices.store'),
                'method' => 'POST',
                'submitLabel' => 'Simpan Draft',
                'cancelUrl' => route('invoices.index'),
            ])
        </div>
    </x-dashboard.sidebar></x-app-layout>
