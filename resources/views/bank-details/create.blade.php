<x-app-layout><x-dashboard.sidebar><x-alert-information />
    <div class="ip-page max-w-4xl"><div><p class="ip-kicker">Detail rekening</p><h1 class="ip-title">Tambah profil rekening</h1><p class="ip-subtitle">Isi hanya informasi yang ingin ditampilkan pada quotation dan invoice.</p></div>
        @include('bank-details._form', ['action' => route('bank-details.store'), 'method' => 'POST', 'submitLabel' => 'Simpan rekening'])
    </div>
</x-dashboard.sidebar></x-app-layout>
