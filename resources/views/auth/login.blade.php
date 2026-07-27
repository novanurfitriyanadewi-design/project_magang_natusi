<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk | Portal Magang CV Natusi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7f9ff] font-['Inter'] text-slate-900 antialiased lg:h-dvh lg:overflow-hidden">
    <div class="relative flex min-h-screen flex-col overflow-hidden lg:h-dvh lg:min-h-0">
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -left-28 -top-28 h-[440px] w-[440px] rounded-full bg-blue-100/70 blur-3xl"></div>
            <div class="absolute -bottom-36 -right-20 h-[420px] w-[420px] rounded-full bg-rose-100/70 blur-3xl"></div>
        </div>

        <main class="flex flex-1 items-center justify-center px-0 py-0 sm:px-5 sm:py-5 lg:min-h-0 lg:px-7 lg:py-3">
            <section
                class="grid w-full max-w-[1080px] overflow-hidden bg-white shadow-[0_18px_42px_rgba(33,55,93,0.10)] sm:rounded-2xl lg:h-[calc(100dvh-76px)] lg:max-h-[570px] lg:min-h-0 lg:grid-cols-[0.96fr_1.04fr]"
                aria-label="Halaman masuk Portal Magang CV Natusi"
            >
                <aside class="relative isolate flex min-h-[250px] flex-col overflow-hidden bg-gradient-to-br from-[#08678f] to-[#075c80] px-6 py-7 text-white sm:min-h-[300px] sm:px-9 sm:py-9 lg:min-h-0 lg:px-10 lg:py-9">
                    <div class="pointer-events-none absolute -bottom-[128px] -right-[118px] -z-10 h-[300px] w-[300px] rounded-full border-[38px] border-white/[0.065]"></div>
                    <div class="pointer-events-none absolute -bottom-[84px] -right-[76px] -z-10 h-[214px] w-[214px] rounded-full border-[38px] border-white/[0.055]"></div>

                    <div class="grid h-[56px] w-[56px] place-items-center overflow-hidden rounded-md bg-white shadow-md sm:h-[62px] sm:w-[62px]">
                        <img
                            src="{{ asset('images/logo.jpeg') }}"
                            alt="Logo CV Natusi"
                            class="h-[48px] w-[48px] object-contain sm:h-[54px] sm:w-[54px]"
                        >
                    </div>

                    <div class="mt-5 max-w-[500px] sm:mt-6">
                        <h1 class="text-[16px] font-bold leading-relaxed tracking-[-0.02em] sm:text-lg lg:text-[20px]">
                            Gerbang Menuju Masa Depan Profesional Anda.
                        </h1>
                        <p class="mt-2 text-[13px] font-medium leading-relaxed text-white/75 sm:text-[15px] lg:text-base">
                            Terhubung dengan para pemimpin industri, dapatkan magang utama, dan percepat jalur karier Anda bersama CV Natusi.
                        </p>
                    </div>

                    <div class="mt-auto hidden flex-wrap gap-2 pt-6 sm:flex">
                        <div class="flex min-w-[164px] items-center gap-2 rounded-[9px] border border-white/25 bg-white/[0.05] px-3 py-2 backdrop-blur-sm">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3.2 18.2 6v5.2c0 4-2.4 7.1-6.2 8.8-3.8-1.7-6.2-4.8-6.2-8.8V6L12 3.2Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="m9.2 12 1.8 1.8 3.9-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                                <strong class="block text-xs leading-tight">KONEKSI AMAN</strong>
                                <small class="mt-0.5 block text-[10px] leading-tight text-white/80">Enkripsi SSL 256-bit</small>
                            </span>
                        </div>

                        <div class="flex min-w-[184px] items-center gap-2 rounded-[9px] border border-white/25 bg-white/[0.05] px-3 py-2 backdrop-blur-sm">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3.2 18.2 6v5.2c0 4-2.4 7.1-6.2 8.8-3.8-1.7-6.2-4.8-6.2-8.8V6L12 3.2Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="m12 7.2 1.1 2.2 2.4.4-1.7 1.7.4 2.4-2.2-1.1-2.2 1.1.4-2.4-1.7-1.7 2.4-.4L12 7.2Z" fill="currentColor"/>
                            </svg>
                            <span>
                                <strong class="block text-xs leading-tight">TERSERTIFIKASI ISO</strong>
                                <small class="mt-0.5 block text-[10px] leading-tight text-white/80">Standar 27001</small>
                            </span>
                        </div>
                    </div>
                </aside>

                <section class="flex min-h-0 items-center justify-center bg-white px-6 py-8 sm:px-9 sm:py-9 lg:px-9 lg:py-6">
                    <div class="w-full max-w-[348px]" x-data="{ showPassword: false }">
                        <h2 class="text-[16px] font-semibold leading-snug tracking-[-0.02em] text-slate-950">
                            Portal Magang CV Natusi
                        </h2>
                        <p class="mt-1 text-[12px] leading-5 text-slate-600 sm:text-sm">
                            Silakan masukkan kredensial Anda untuk mengakses portal.
                        </p>

                        @if (session('status'))
                            <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-5 text-emerald-700" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mt-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm leading-5 text-rose-700" role="alert">
                                @foreach ($errors->all() as $error)
                                    <p @class(['mt-1' => ! $loop->first])>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="mt-3.5">
                            @csrf

                            <div>
                                <label for="email" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700 sm:text-xs">
                                    Email atau Username
                                </label>

                                <div class="mt-1.5 flex h-[41px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#08678f] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#08678f]/10 sm:h-[43px]">
                                    <svg class="h-5 w-5 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M4 20c.6-4 3.4-6 8-6s7.4 2 8 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        id="email"
                                        type="text"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="nama@contoh.com"
                                        autocomplete="username"
                                        required
                                        autofocus
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                    >
                                </div>
                                @error('email')
                                    <p class="mt-1.5 text-xs leading-5 text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between gap-4">
                                    <label for="password" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700 sm:text-xs">
                                        Kata Sandi
                                    </label>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-[11px] font-medium text-[#08678f] transition hover:text-[#075c80] hover:underline sm:text-xs">
                                            Lupa Kata Sandi?
                                        </a>
                                    @endif
                                </div>

                                <div class="mt-1.5 flex h-[41px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#08678f] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#08678f]/10 sm:h-[43px]">
                                    <svg class="h-5 w-5 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M8.5 10V7.5a3.5 3.5 0 1 1 7 0V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M12 14.5v2.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>

                                    <input
                                        id="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        placeholder="••••••••"
                                        autocomplete="current-password"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-500 focus:border-0 focus:ring-0 sm:text-sm"
                                    >

                                    <button
                                        type="button"
                                        class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-slate-500 transition hover:bg-[#08678f]/10 hover:text-[#08678f] focus:outline-none focus:ring-2 focus:ring-[#08678f]/30"
                                        @click="showPassword = !showPassword"
                                        :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                        :aria-pressed="showPassword"
                                    >
                                        <svg x-show="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M2.8 12s3.2-5.5 9.2-5.5 9.2 5.5 9.2 5.5-3.2 5.5-9.2 5.5S2.8 12 2.8 12Z" stroke="currentColor" stroke-width="1.8"/>
                                            <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        <svg x-show="showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m4 4 16 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M10.2 6.7c.6-.1 1.2-.2 1.8-.2 6 0 9.2 5.5 9.2 5.5a15.6 15.6 0 0 1-2.4 3.1M6.2 8.1A15.6 15.6 0 0 0 2.8 12s3.2 5.5 9.2 5.5c1 0 2-.2 2.8-.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1.5 text-xs leading-5 text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-2.5 flex items-center">
                                <label for="remember_me" class="inline-flex cursor-pointer select-none items-center gap-2 text-xs text-slate-700 sm:text-[13px]">
                                    <input
                                        id="remember_me"
                                        type="checkbox"
                                        name="remember"
                                        class="h-4 w-4 rounded border-slate-300 text-[#08678f] focus:ring-[#08678f]"
                                    >
                                    <span>Ingat Saya</span>
                                </label>
                            </div>

                            <button
                                type="submit"
                                class="mt-3 inline-flex h-[42px] w-full items-center justify-center gap-2 rounded-[8px] bg-gradient-to-b from-[#08739f] to-[#076b95] px-5 text-[13px] font-semibold tracking-[0.04em] text-white shadow-[0_7px_15px_rgba(8,103,143,0.15)] transition hover:-translate-y-0.5 hover:brightness-95 hover:shadow-[0_10px_20px_rgba(8,103,143,0.22)] focus:outline-none focus:ring-4 focus:ring-[#08678f]/25 active:translate-y-0 sm:h-[44px] sm:text-sm"
                            >
                                <span>MASUK</span>
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12h13M13 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>

                        <div class="my-3.5 grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                            <span class="h-px bg-slate-300"></span>
                            <span class="h-2 w-2 rounded-full bg-[#cf0a1f]"></span>
                            <span class="h-px bg-slate-300"></span>
                        </div>

                        <!-- Opsi Pilihan Registrasi Baru (Magang & Karyawan) -->
                        <div class="space-y-2 text-center text-xs text-slate-700 sm:text-[13px]">
                            <p>Belum memiliki akun?</p>
                            <div class="flex items-center justify-center gap-2 text-[12px] sm:text-xs">
                                <a href="{{ route('register.pelamar') }}" class="font-medium text-[#08678f] transition hover:underline">
                                    Ajukan Magang
                                </a>
                                <span class="text-slate-300">•</span>
                                <a href="{{ route('register.karyawan') }}" class="font-medium text-[#cf0a1f] transition hover:underline">
                                    Ajukan Karyawan
                                </a>
                            </div>
                        </div>

                    </div>
                </section>
            </section>
        </main>

        <footer class="flex min-h-14 flex-col items-start justify-between gap-3 border-t border-slate-300/30 bg-[#cfe2ff] px-5 py-3 text-slate-700 sm:px-8 lg:h-14 lg:min-h-14 lg:flex-row lg:items-center lg:px-[max(24px,calc((100vw-1080px)/2))] lg:py-0">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] sm:text-xs">
                <strong class="text-xs text-slate-950 sm:text-sm">CV Natusi</strong>
                <span class="hidden h-6 w-px bg-slate-500/40 sm:block"></span>
                <span>© 2026 Portal Magang CV Natusi. Hak cipta dilindungi undang-undang.</span>
            </div>

            <nav class="flex flex-wrap gap-x-6 gap-y-2 text-xs" aria-label="Tautan footer">
                <a href="#" class="hover:text-[#075c80] hover:underline">Kebijakan Privasi</a>
                <a href="#" class="hover:text-[#075c80] hover:underline">Ketentuan Layanan</a>
                <a href="#" class="hover:text-[#075c80] hover:underline">Hubungi Dukungan</a>
            </nav>
        </footer>
    </div>
</body>
</html>