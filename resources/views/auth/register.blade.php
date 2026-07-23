<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ajukan Magang | CV Natusi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7f9ff] font-['Inter'] text-slate-900 antialiased lg:h-dvh lg:overflow-hidden">
    @php
        $registerRole = $registerRole ?? session('register_role', 'pelamar');
        $isEmployee = $registerRole === 'karyawan';
    @endphp

    <main class="relative min-h-screen overflow-hidden lg:h-dvh lg:min-h-0">
        <section class="grid min-h-screen lg:h-dvh lg:min-h-0 lg:grid-cols-[1.04fr_0.96fr]">
            {{-- Panel kiri --}}
            <aside class="relative isolate hidden overflow-hidden bg-[#e7f0ff] px-8 py-8 lg:flex lg:h-dvh lg:min-h-0 lg:flex-col xl:px-12 xl:py-10">
                <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
                    <div class="absolute right-[-110px] top-[15%] h-[380px] w-[380px] rounded-full border border-[#8dbde8]/35"></div>
                    <div class="absolute right-[-45px] top-[22%] h-[300px] w-[300px] rounded-full border border-[#8dbde8]/30"></div>
                    <div class="absolute left-[70px] top-[48%] h-[240px] w-[180px] rotate-[-14deg] rounded-[30px] border border-[#8dbde8]/35"></div>
                    <div class="absolute left-[-120px] top-[-150px] h-[340px] w-[340px] rounded-full bg-white/35 blur-3xl"></div>
                </div>

                <a href="{{ route('login') }}" class="inline-flex w-fit items-center gap-3 text-[#075f8c] transition hover:opacity-80">
                    <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200/70">
                        <img
                            src="{{ asset('images/logo.jpeg') }}"
                            alt="Logo CV Natusi"
                            class="h-9 w-9 object-contain"
                        >
                    </span>
                    <span class="font-['Manrope'] text-base font-bold">CV Natusi</span>
                </a>

                <div class="my-auto max-w-[560px] py-16">
                    <h1 class="font-['Manrope'] text-3xl font-extrabold leading-[1.08] tracking-[-0.035em] text-slate-950 xl:text-[38px]">
                        Ignite Your Career
                        <span class="block text-[#006b9d]">With Strategic Internships.</span>
                    </h1>

                    <p class="mt-5 max-w-[510px] text-[15px] leading-7 text-slate-700 xl:text-base">
                        Bergabunglah dengan portal eksklusif kami untuk terhubung dengan pemimpin industri,
                        memperoleh pengalaman profesional, dan membangun jalur karier yang lebih terarah.
                    </p>

                    <div class="mt-7 space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#0879ad] text-white shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3.2 18.2 6v5.2c0 4-2.4 7.1-6.2 8.8-3.8-1.7-6.2-4.8-6.2-8.8V6L12 3.2Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="m9.2 12 1.8 1.8 3.9-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Institutional Reliability</h2>
                                <p class="mt-0.5 text-xs leading-5 text-slate-600">
                                    Kemitraan terpercaya dengan institusi pendidikan dan perusahaan profesional.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#0879ad] text-white shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 16.5 9.2 12l3 2.8L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15.5 7.5H19V11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Career Progression</h2>
                                <p class="mt-0.5 text-xs leading-5 text-slate-600">
                                    Jalur terstruktur untuk membantu peserta berkembang dari pelajar menjadi profesional.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <blockquote class="max-w-[570px] border-l-[3px] border-[#0879ad] pl-5 text-sm italic leading-6 text-slate-700">
                    “CV Natusi menjadi jembatan antara dunia akademik dan dunia profesional. Sistem magangnya sangat membantu peserta berkembang.”
                    <footer class="mt-2 text-[11px] font-semibold not-italic text-slate-900">
                        — Tim Pengembangan SDM CV Natusi
                    </footer>
                </blockquote>
            </aside>

            {{-- Panel kanan --}}
            <section class="relative flex min-h-screen items-center justify-center bg-[#f8f9ff] px-5 py-8 sm:px-8 lg:h-dvh lg:min-h-0 lg:overflow-hidden lg:px-10 lg:py-4 xl:px-14">
                <div class="pointer-events-none absolute right-[-120px] top-[-100px] h-72 w-72 rounded-full bg-blue-100/50 blur-3xl"></div>

                <div class="relative w-full max-w-[430px] lg:flex lg:h-full lg:min-h-0 lg:flex-col">
                    <div class="mb-5 flex items-center gap-3 lg:hidden">
                        <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo CV Natusi" class="h-9 w-9 object-contain">
                        </span>
                        <span class="font-['Manrope'] text-base font-bold text-[#075f8c]">CV Natusi</span>
                    </div>

                    <header class="mb-4 shrink-0 lg:mb-3">
                        <h1 class="font-['Manrope'] text-[28px] font-extrabold leading-tight tracking-[-0.035em] text-slate-950 sm:text-[32px]">
                            {{ $isEmployee ? 'Daftar Karyawan' : 'Ajukan Magang' }}
                        </h1>
                        <p class="mt-1.5 text-[13px] leading-5 text-slate-600">
                            {{ $isEmployee
                                ? 'Lengkapi data berikut untuk mengajukan pendaftaran sebagai karyawan CV Natusi.'
                                : 'Ajukan permohonan magang atau PKL Anda di CV Natusi.' }}
                        </p>
                    </header>

                    @if (session('success'))
                        <div class="mb-4 max-h-24 shrink-0 overflow-y-auto rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 lg:mb-3" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 max-h-28 shrink-0 overflow-y-auto rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 lg:mb-3" role="alert">
                            <p class="font-semibold">Periksa kembali data berikut:</p>
                            <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs leading-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="rounded-xl border border-slate-300/90 bg-white p-5 shadow-[0_12px_32px_rgba(43,67,104,0.07)] sm:p-6 lg:flex lg:min-h-0 lg:flex-1 lg:flex-col lg:overflow-hidden">
                        <form
                            method="POST"
                            action="{{ route('register.store') }}"
                            class="space-y-3.5 lg:min-h-0 lg:flex-1 lg:overflow-y-auto lg:overscroll-contain lg:pr-2 [scrollbar-color:#cbd5e1_transparent] [scrollbar-width:thin] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-slate-300 hover:[&::-webkit-scrollbar-thumb]:bg-slate-400"
                        >
                            @csrf
                            <input type="hidden" name="role" value="{{ $registerRole }}">

                            {{-- Nama lengkap --}}
                            <div>
                                <label for="full_name" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                    Nama Lengkap
                                </label>
                                <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M4 20c.6-4 3.4-6 8-6s7.4 2 8 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        id="full_name"
                                        name="full_name"
                                        type="text"
                                        value="{{ old('full_name') }}"
                                        placeholder="Nama Lengkap Anda"
                                        autocomplete="name"
                                        required
                                        autofocus
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                    >
                                </div>
                                @error('full_name')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                    Alamat Email
                                </label>
                                <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3.5" y="5" width="17" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="m5 7 7 5 7-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email') }}"
                                        placeholder="email@contoh.com"
                                        autocomplete="email"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                    >
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Sekolah / Universitas --}}
                            <div>
                                <label for="university" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                    Asal Sekolah / Universitas
                                </label>
                                <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m3 9 9-5 9 5-9 5-9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M6.5 11v5.5c3.7 2.2 7.3 2.2 11 0V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        id="university"
                                        name="university"
                                        type="text"
                                        value="{{ old('university') }}"
                                        placeholder="Nama Institusi"
                                        autocomplete="organization"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                    >
                                </div>
                                @error('university')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- NIS / NIM --}}
                            <div>
                                <label for="student_id" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                    NIS / NIM
                                </label>
                                <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="4" y="5" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M9 5V3.5M15 5V3.5M8 10h8M8 14h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        id="student_id"
                                        name="student_id"
                                        type="text"
                                        value="{{ old('student_id') }}"
                                        placeholder="Masukkan NIS atau NIM Anda"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                    >
                                </div>
                                @error('student_id')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jurusan --}}
                            <div>
                                <label for="major" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                    Jurusan
                                </label>
                                <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="4" y="7" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    <input
                                        id="major"
                                        name="major"
                                        type="text"
                                        value="{{ old('major') }}"
                                        placeholder="Masukkan Jurusan Anda"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                    >
                                </div>
                                @error('major')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Telepon --}}
                            <div>
                                <label for="phone" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                    Nomor Telepon
                                </label>
                                <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7.2 4.5 9.4 8l-1.7 2a14.8 14.8 0 0 0 6.3 6.3l2-1.7 3.5 2.2-.8 3.2c-.2.7-.8 1.1-1.5 1.1C9.3 20.4 3.6 14.7 2.9 6.8c-.1-.7.4-1.3 1.1-1.5l3.2-.8Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <input
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        value="{{ old('phone') }}"
                                        placeholder="0812..."
                                        autocomplete="tel"
                                        inputmode="tel"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                    >
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kata sandi akun untuk memeriksa status --}}
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                        Kata Sandi
                                    </label>
                                    <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                        <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M8.5 10V7.5a3.5 3.5 0 1 1 7 0V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            placeholder="Minimal 8 karakter"
                                            autocomplete="new-password"
                                            required
                                            minlength="8"
                                            class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                        >
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                        Konfirmasi Kata Sandi
                                    </label>
                                    <div class="mt-1.5 flex h-11 items-center gap-2.5 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:border-[#0879ad] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#0879ad]/10">
                                        <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m7 12 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        <input
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            placeholder="Ulangi kata sandi"
                                            autocomplete="new-password"
                                            required
                                            minlength="8"
                                            class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:border-0 focus:ring-0"
                                        >
                                    </div>
                                </div>
                            </div>

                            @if (! $isEmployee)
                                <p class="rounded-lg border border-sky-100 bg-sky-50 px-3 py-2 text-[11px] leading-5 text-sky-800">
                                    Email dan kata sandi ini digunakan untuk masuk kembali dan memeriksa status pengajuan. Setelah diterima, akun peserta baru akan diberikan melalui notifikasi lonceng.
                                </p>
                            @endif

                            {{-- Deskripsi --}}
                            <div>
                                <label for="description" class="block text-[10px] font-bold uppercase tracking-[0.065em] text-slate-800">
                                    Deskripsi / Pertanyaan
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    placeholder="Tanyakan ketersediaan magang atau jelaskan minat Anda..."
                                    class="mt-1.5 block w-full resize-none rounded-lg border border-slate-300 bg-[#f8faff] px-3 py-2.5 text-sm leading-5 text-slate-800 placeholder:text-slate-400 transition focus:border-[#0879ad] focus:bg-white focus:ring-4 focus:ring-[#0879ad]/10"
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Persetujuan --}}
                            <label for="terms" class="flex cursor-pointer items-start gap-2.5 pt-0.5 text-[11px] leading-[1.55] text-slate-600">
                                <input
                                    id="terms"
                                    name="terms"
                                    type="checkbox"
                                    value="1"
                                    required
                                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0879ad] focus:ring-[#0879ad]"
                                >
                                <span>
                                    Saya setuju dengan
                                    <a href="#" class="font-semibold text-[#006b9d] hover:underline">Syarat & Ketentuan</a>
                                    dan
                                    <a href="#" class="font-semibold text-[#006b9d] hover:underline">Kebijakan Privasi</a>.
                                </span>
                            </label>

                            <button
                                type="submit"
                                class="inline-flex h-12 w-full items-center justify-center gap-3 rounded-lg bg-gradient-to-b from-[#0879ad] to-[#066c9b] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(8,121,173,0.18)] transition hover:-translate-y-0.5 hover:brightness-95 hover:shadow-[0_11px_24px_rgba(8,121,173,0.24)] focus:outline-none focus:ring-4 focus:ring-[#0879ad]/25 active:translate-y-0"
                            >
                                <span>{{ $isEmployee ? 'Daftar Karyawan' : 'Ajukan Magang' }}</span>
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12h13M13 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <p class="mt-5 shrink-0 text-center text-xs text-slate-600 sm:text-sm lg:mt-3">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-semibold text-[#006b9d] transition hover:underline">
                            Masuk
                            <span aria-hidden="true">↪</span>
                        </a>
                    </p>

                    <p class="mt-7 shrink-0 text-center text-[9px] font-medium uppercase tracking-[0.12em] text-slate-500 lg:mt-3">
                        © 2024 CV Natusi Corporation • Professional Excellence
                    </p>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
