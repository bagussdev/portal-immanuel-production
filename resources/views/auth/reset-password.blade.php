<x-guest-layout>
    <section class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-sky-100 bg-white shadow-[0_32px_90px_-35px_rgba(15,23,42,.35)] lg:grid-cols-[.82fr_1.18fr]">
        <aside class="relative hidden min-h-[690px] overflow-hidden bg-gradient-to-br from-slate-950 via-sky-950 to-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-500 via-red-500 to-sky-500"></div>
            <div class="absolute -right-24 -top-20 h-72 w-72 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-red-600/15 blur-3xl"></div>

            <div class="relative inline-flex w-fit items-center gap-3 rounded-2xl border border-white/10 bg-black/40 px-4 py-3">
                <img src="{{ asset('assets/brand/immanuel-production-white-logo.png') }}" alt="Immanuel Production" class="h-16 w-24 object-contain">
                <span><strong class="block text-xs tracking-wide">PORTAL IMMANUEL</strong><span class="text-[9px] font-bold tracking-[.2em] text-slate-400">PRODUCTION</span></span>
            </div>

            <div class="relative">
                <span class="inline-flex rounded-full border border-sky-300/20 bg-sky-400/10 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[.2em] text-sky-200">Langkah terakhir</span>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight">Buat password baru yang lebih kuat.</h1>
                <p class="mt-4 text-sm leading-7 text-slate-300">Gunakan kombinasi yang mudah kamu ingat, tetapi sulit ditebak orang lain.</p>

                <div class="mt-8 rounded-2xl border border-white/10 bg-white/[.06] p-5">
                    <p class="text-[10px] font-extrabold uppercase tracking-[.18em] text-slate-400">Saran keamanan</p>
                    <ul class="mt-3 space-y-2.5 text-xs font-semibold text-slate-200">
                        <li class="flex gap-2"><span class="text-sky-300">&bull;</span> Minimal 8 karakter</li>
                        <li class="flex gap-2"><span class="text-sky-300">&bull;</span> Hindari nama dan tanggal lahir</li>
                        <li class="flex gap-2"><span class="text-sky-300">&bull;</span> Jangan gunakan password akun lain</li>
                    </ul>
                </div>
            </div>

            <p class="relative text-[11px] font-semibold text-slate-500">Tautan reset berlaku selama 60 menit</p>
        </aside>

        <div class="flex min-h-[660px] items-center px-6 py-10 sm:px-12 lg:px-16" x-data="{ showPassword: false, showConfirmation: false }">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-8 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-3 py-2 lg:hidden">
                        <img src="{{ asset('assets/brand/immanuel-production-white-logo.png') }}" alt="Immanuel Production" class="h-11 w-16 object-contain">
                        <span class="text-[10px] font-extrabold tracking-wide text-white">PORTAL IMMANUEL</span>
                    </div>
                    <span class="ml-auto rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">Tautan terverifikasi</span>
                </div>

                <p class="ip-kicker">Atur ulang password</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Masukkan password baru</h2>
                <p class="mt-3 text-sm leading-6 text-slate-500">Setelah tersimpan, gunakan password baru untuk masuk ke Portal Immanuel.</p>

                @if ($errors->any())
                    <div class="ip-alert-error mt-6" role="alert">
                        <p class="font-extrabold">Password belum dapat diperbarui</p>
                        <p class="mt-1 text-xs leading-5">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="mt-7 space-y-4" onsubmit="showFullScreenLoader();">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="ip-label">Email akun</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18v12H3zM3 7l9 7 9-7" /></svg>
                            </span>
                            <input id="email" class="ip-input min-h-12 bg-slate-50 pl-12 text-slate-500" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly autocomplete="username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="ip-label">Password baru</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 10V8a6 6 0 0 1 12 0v2M5 10h14v11H5z" /></svg>
                            </span>
                            <input id="password" class="ip-input min-h-12 px-12" :type="showPassword ? 'text' : 'password'" name="password" required autofocus autocomplete="new-password" placeholder="Minimal 8 karakter">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 hover:text-sky-700" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" /><circle cx="12" cy="12" r="2.5" /></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="ip-label">Ulangi password baru</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m7 12 3 3 7-7M5 3h14v18H5z" /></svg>
                            </span>
                            <input id="password_confirmation" class="ip-input min-h-12 px-12" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang password">
                            <button type="button" @click="showConfirmation = !showConfirmation" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 hover:text-sky-700" :aria-label="showConfirmation ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" /><circle cx="12" cy="12" r="2.5" /></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="ip-btn-primary min-h-12 w-full text-[15px]">
                        Simpan password baru
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </button>
                </form>

                <p class="mt-6 text-center text-xs leading-5 text-slate-400">Tidak meminta perubahan password?<br><a href="{{ route('login') }}" class="font-extrabold text-sky-700 hover:text-sky-900">Kembali ke halaman login</a></p>
            </div>
        </div>
    </section>
</x-guest-layout>
