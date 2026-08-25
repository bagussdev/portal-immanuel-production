<x-app-layout><x-dashboard.sidebar><x-alert-information />
    <div class="ip-page max-w-5xl"><header><p class="ip-kicker">Pengguna</p><h1 class="ip-title">Tambah user</h1></header>
        @include('users._form', ['action' => route('users.store'), 'user' => new \App\Models\User()])
    </div>
</x-dashboard.sidebar></x-app-layout>
