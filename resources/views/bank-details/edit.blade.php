<x-app-layout><x-dashboard.sidebar><x-alert-information />
    <div class="ip-page max-w-4xl"><div><p class="ip-kicker">{{ $bankDetail->label }}</p><h1 class="ip-title">Edit profil rekening</h1><p class="ip-subtitle">Perubahan berlaku pada PDF dokumen yang menggunakan profil ini.</p></div>
        @include('bank-details._form', ['action' => route('bank-details.update', $bankDetail), 'method' => 'PUT', 'submitLabel' => 'Simpan perubahan'])
    </div>
</x-dashboard.sidebar></x-app-layout>
