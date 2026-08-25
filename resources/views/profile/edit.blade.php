<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />
        <div class="ip-page max-w-6xl">
            <header class="ip-page-header">
                <div>
                    <p class="ip-kicker">Akun saya</p>
                    <h1 class="ip-title">Edit profil</h1>
                    <p class="ip-subtitle">Perbarui identitas, foto, KTP, dan keamanan akun.</p>
                </div>
            </header>

            @include('profile.partials.update-profile-information-form')

            <div class="grid gap-5 lg:grid-cols-2">
                <section class="ip-card p-5 sm:p-6">
                    @include('profile.partials.update-password-form')
                </section>
                <section class="ip-card border-red-100 p-5 sm:p-6 dark:border-red-500/20">
                    @include('profile.partials.delete-user-form')
                </section>
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>
