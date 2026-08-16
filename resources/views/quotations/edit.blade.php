<x-app-layout><x-dashboard.sidebar><x-alert-information />
    <div class="ip-page">
        <div><p class="ip-kicker">{{ $quotation->quotation_number }}</p><h1 class="ip-title">Edit quotation</h1></div>
        @include('documents._form', ['kind' => 'quotation', 'document' => $quotation, 'action' => route('quotations.update', $quotation), 'method' => 'PUT', 'submitLabel' => 'Simpan Perubahan', 'cancelUrl' => route('quotations.show', $quotation)])
    </div>
</x-dashboard.sidebar></x-app-layout>
