<x-app-layout><x-dashboard.sidebar><x-alert-information />
    <div class="ip-page">
        <div><p class="ip-kicker">{{ $invoice->invoice_number ?: 'DRAFT #' . $invoice->id }}</p><h1 class="ip-title">Edit invoice</h1><p class="ip-subtitle">Pembayaran yang sudah masuk tidak berubah; saldo akan dihitung ulang.</p></div>
        @include('documents._form', ['kind' => 'invoice', 'document' => $invoice, 'action' => route('invoices.update', $invoice), 'method' => 'PUT', 'submitLabel' => 'Simpan & Hitung Ulang', 'cancelUrl' => route('invoices.show', $invoice)])
    </div>
</x-dashboard.sidebar></x-app-layout>
