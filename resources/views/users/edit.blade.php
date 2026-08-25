<x-app-layout><x-dashboard.sidebar><x-alert-information />
    <div class="ip-page max-w-5xl"><header><p class="ip-kicker">Pengguna</p><h1 class="ip-title">Edit user</h1><p class="ip-subtitle">{{ $user->name }}</p></header>
        @include('users._form', ['action' => route('users.update', $user)])
    </div>
</x-dashboard.sidebar></x-app-layout>
