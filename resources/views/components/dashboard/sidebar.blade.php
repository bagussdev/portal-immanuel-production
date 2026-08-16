@php
    $masterActive = request()->routeIs('equipment.*', 'armada.*', 'gudang.*', 'bank-details.*');
    $transactionActive = request()->routeIs('quotations.*', 'invoices.*', 'payments.*', 'expenses.*');
    $notificationActive = request()->routeIs('notifications.*');
    $roleName = ucfirst(Auth::user()->role?->name ?? 'Tanpa role');
    $navBase = 'group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition';
    $navIdle = 'text-slate-600 hover:bg-sky-50 hover:text-sky-800 dark:text-slate-400 dark:hover:bg-white/[.07] dark:hover:text-white';
    $navActive = 'bg-sky-600 text-white shadow-lg shadow-sky-600/20 dark:bg-red-600 dark:shadow-red-950/30';
    $subIdle = 'text-slate-500 hover:bg-sky-50 hover:text-sky-800 dark:text-slate-400 dark:hover:bg-white/[.07] dark:hover:text-white';
    $subActive = 'bg-sky-100 text-sky-800 dark:bg-white/10 dark:text-white';
@endphp

<div x-data="appShell({ masterOpen: {{ $masterActive ? 'true' : 'false' }}, transactionOpen: {{ $transactionActive ? 'true' : 'false' }}, notificationOpen: {{ $notificationActive ? 'true' : 'false' }}, accountOpen: false })" @keydown.escape.window="closeSidebar()" class="min-h-screen bg-sky-50/70 dark:bg-[#080b12]">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

    <aside x-cloak class="fixed inset-y-0 left-0 z-50 flex w-[286px] flex-col overflow-hidden border-r border-sky-100 bg-white transition-transform duration-300 lg:!translate-x-0 dark:border-white/10 dark:bg-[#0b0c0f]"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" aria-label="Navigasi utama">
        <div class="absolute inset-x-0 top-0 h-56 bg-gradient-to-b from-sky-100/80 to-transparent dark:from-red-950/30"></div>

        <div class="relative flex h-24 items-center justify-between border-b border-sky-100 px-4 dark:border-white/10">
            <a href="{{ route('dashboard') }}" class="flex h-14 min-w-0 items-center gap-2.5 rounded-xl bg-[#0b0c0f] px-2.5 pr-3 shadow-sm" onclick="showFullScreenLoader();">
                <img src="{{ asset('assets/brand/immanuel-production-white-logo.png') }}" alt="Immanuel Production" class="h-11 w-16 shrink-0 object-contain">
                <span class="min-w-0 leading-tight text-white"><strong class="block truncate text-xs tracking-wide">PORTAL IMMANUEL</strong><span class="block text-[10px] font-semibold tracking-[.16em] text-slate-400">PRODUCTION</span></span>
            </a>
            <button type="button" class="rounded-lg p-2 text-slate-500 hover:bg-sky-100 hover:text-sky-800 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white lg:hidden" @click="closeSidebar()" aria-label="Tutup menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
            </button>
        </div>

        <nav class="relative flex-1 overflow-y-auto px-4 py-5 scrollbar-thin" @click="if ($event.target.closest('a')) closeSidebar()">
            <p class="mb-2 px-3 text-[10px] font-extrabold uppercase tracking-[.2em] text-slate-400 dark:text-slate-600">Workspace</p>
            <ul class="space-y-1.5">
                <li>
                    <a href="{{ route('dashboard') }}" onclick="showFullScreenLoader();" class="{{ $navBase }} {{ request()->routeIs('dashboard') ? $navActive : $navIdle }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.2V20a1 1 0 0 0 1 1h5v-7h6v7h5a1 1 0 0 0 1-1v-6.8a2 2 0 0 0-.7-1.5L13.3 5.5a2 2 0 0 0-2.6 0l-7 6.2a2 2 0 0 0-.7 1.5Z" /></svg>
                        <span>Dashboard</span>
                    </a>
                </li>

                @canany(['equipmentmenu', 'armadamenu', 'gudangmenu', 'bankdetailmenu'])
                    <li>
                        <button type="button" @click="masterOpen = !masterOpen" class="{{ $navBase }} {{ $masterActive ? $navActive : $navIdle }}">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10" /></svg>
                            <span class="flex-1 text-left">Data Operasional</span>
                            <svg class="h-4 w-4 transition-transform" :class="masterOpen && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 9 6 6 6-6" /></svg>
                        </button>
                        <ul x-cloak x-show="masterOpen" class="ml-5 mt-1 space-y-1 border-l border-sky-100 pl-3 dark:border-white/10">
                            @can('equipmentmenu')<li><a href="{{ route('equipment.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('equipment.*') ? $subActive : $subIdle }}">Equipment</a></li>@endcan
                            @can('armadamenu')<li><a href="{{ route('armada.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('armada.*') ? $subActive : $subIdle }}">Armada & Samsat</a></li>@endcan
                            @can('gudangmenu')<li><a href="{{ route('gudang.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('gudang.*') ? $subActive : $subIdle }}">Gudang</a></li>@endcan
                            @can('bankdetailmenu')<li><a href="{{ route('bank-details.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('bank-details.*') ? $subActive : $subIdle }}">Detail Rekening</a></li>@endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['quotationmenu', 'invoicemenu', 'paymentsmenu', 'expensesmenu'])
                    <li>
                        <button type="button" @click="transactionOpen = !transactionOpen" class="{{ $navBase }} {{ $transactionActive ? $navActive : $navIdle }}">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h10a2 2 0 0 1 2 2v16l-3-2-4 2-4-2-3 2V5a2 2 0 0 1 2-2Z M8 8h8M8 12h6" /></svg>
                            <span class="flex-1 text-left">Transaksi</span>
                            <svg class="h-4 w-4 transition-transform" :class="transactionOpen && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 9 6 6 6-6" /></svg>
                        </button>
                        <ul x-cloak x-show="transactionOpen" class="ml-5 mt-1 space-y-1 border-l border-sky-100 pl-3 dark:border-white/10">
                            @can('quotationmenu')<li><a href="{{ route('quotations.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('quotations.*') ? $subActive : $subIdle }}">Quotation</a></li>@endcan
                            @can('invoicemenu')<li><a href="{{ route('invoices.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('invoices.*') ? $subActive : $subIdle }}">Invoice</a></li>@endcan
                            @can('paymentsmenu')<li><a href="{{ route('payments.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('payments.*') ? $subActive : $subIdle }}">Pembayaran</a></li>@endcan
                            @can('expensesmenu')<li><a href="{{ route('expenses.index') }}" onclick="showFullScreenLoader();" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('expenses.*') ? $subActive : $subIdle }}">Pengeluaran</a></li>@endcan
                        </ul>
                    </li>
                @endcanany

                @can('fieldjobsmenu')
                    <li><a href="{{ route('field-jobs.index') }}" onclick="showFullScreenLoader();" class="{{ $navBase }} {{ request()->routeIs('field-jobs.*') ? $navActive : $navIdle }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16v12H4zM8 7V5h8v2M8 12h8m-4-2v4" /></svg>
                        <span>Jadwal Event</span>
                    </a></li>
                @endcan

                @can('payrollmenu')
                    <li><a href="{{ route('payroll.index') }}" onclick="showFullScreenLoader();" class="{{ $navBase }} {{ request()->routeIs('payroll.*') ? $navActive : $navIdle }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z M8 10h8M8 14h4" /></svg>
                        <span>Penggajian</span>
                    </a></li>
                @endcan
            </ul>

            @canany(['menuuser', 'permission', 'notification'])
                <p class="mb-2 mt-7 px-3 text-[10px] font-extrabold uppercase tracking-[.2em] text-slate-400 dark:text-slate-600">Administrasi</p>
                <ul class="space-y-1.5">
                    @can('menuuser')<li><a href="{{ route('users.index') }}" onclick="showFullScreenLoader();" class="{{ $navBase }} {{ request()->routeIs('users.*') ? $navActive : $navIdle }}"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM17 11l2 2 4-4" /></svg><span>Management User</span></a></li>@endcan
                    @can('permission')<li><a href="{{ route('role-permissions.index') }}" onclick="showFullScreenLoader();" class="{{ $navBase }} {{ request()->routeIs('role-permissions.*') ? $navActive : $navIdle }}"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z M9 12l2 2 4-4" /></svg><span>Hak Akses</span></a></li>@endcan
                    @can('notification')<li><a href="{{ route('notifications.index') }}" onclick="showFullScreenLoader();" class="{{ $navBase }} {{ $notificationActive ? $navActive : $navIdle }}"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" /></svg><span>Notifikasi</span></a></li>@endcan
                </ul>
            @endcanany
        </nav>

        <div class="relative border-t border-sky-100 p-4 dark:border-white/10">
            <div class="flex items-center gap-3 rounded-2xl bg-sky-50 p-3 dark:bg-white/[.06]">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-600 text-sm font-extrabold text-white dark:bg-red-600">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="min-w-0 flex-1"><p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p><p class="truncate text-xs font-semibold text-slate-500">{{ $roleName }}</p></div>
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirmAndLoad('Keluar dari aplikasi?')">@csrf<button type="submit" class="rounded-lg p-2 text-slate-500 hover:bg-red-500/10 hover:text-red-400" aria-label="Keluar"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5M15 12H3M14 3h7v18h-7" /></svg></button></form>
            </div>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-[286px]">
        <header class="sticky top-0 z-30 border-b border-sky-100 bg-white/90 backdrop-blur-xl dark:border-white/10 dark:bg-[#0b0e15]/90">
            <div class="flex h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="rounded-xl border border-sky-200 bg-white p-2.5 text-sky-800 shadow-sm hover:bg-sky-50 dark:border-white/10 dark:bg-white/[.06] dark:text-white dark:hover:bg-white/10 lg:hidden" @click.stop="sidebarOpen = true" aria-label="Buka menu">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h10" /></svg>
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-slate-900 dark:text-white sm:text-sm sm:font-extrabold">
                            <span class="sm:hidden">{{ now()->format('d/m/Y') }}</span>
                            <span class="hidden sm:inline">{{ now()->translatedFormat('l, d F Y') }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <button type="button" class="ip-theme-toggle" @click="toggleTheme()" :aria-label="theme === 'dark' ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'" :title="theme === 'dark' ? 'Mode terang' : 'Mode gelap'">
                        <svg x-show="theme === 'light'" class="h-5 w-5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                        <svg x-cloak x-show="theme === 'dark'" class="h-5 w-5 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>
                        <span class="hidden md:inline" x-text="theme === 'dark' ? 'Light' : 'Dark'"></span>
                    </button>
                    @can('notification')<x-notifications.bell :limit="10" />@endcan
                    <a href="{{ route('profile.edit') }}" onclick="showFullScreenLoader();" class="flex items-center gap-3 rounded-xl border border-sky-200 bg-white px-2 py-2 shadow-sm hover:border-sky-300 dark:border-white/10 dark:bg-white/[.06] sm:px-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-950 text-xs font-extrabold text-white dark:bg-red-600">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        <div class="hidden min-w-0 sm:block"><p class="max-w-32 truncate text-xs font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p><p class="text-[10px] font-semibold text-slate-500">{{ $roleName }}</p></div>
                    </a>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div class="ip-alert-success mb-6 flex items-start gap-3" role="status"><span class="font-black">&#10003;</span><span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="ip-alert-error mb-6 flex items-start gap-3 shadow-sm" role="alert">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 8v5m0 3h.01"/><circle cx="12" cy="12" r="9"/></svg></span>
                    <span><strong class="block font-extrabold">Tindakan belum berhasil</strong><span class="mt-0.5 block font-medium leading-5">{{ session('error') }}</span></span>
                </div>
            @endif
            @if (session('warning'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 dark:border-amber-500/30 dark:bg-amber-950/20 dark:text-amber-100" role="alert"><span class="font-black">!</span><span>{{ session('warning') }}</span></div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
