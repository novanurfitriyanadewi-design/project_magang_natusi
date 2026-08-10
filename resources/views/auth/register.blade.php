<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $isEmployee ?? false ? 'Daftar Karyawan' : 'Ajukan Magang' }} | CV Natusi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7f9ff] font-['Inter'] text-slate-900 antialiased lg:h-dvh lg:overflow-hidden">
    @php
        $registerRole = $registerRole ?? session('register_role', 'pelamar');
        $isEmployee = $registerRole === 'karyawan';
        $accent = $isEmployee ? '#cf0a1f' : '#08678f';
        $accentDark = $isEmployee ? '#b30a1c' : '#075c80';
    @endphp

    <div class="relative flex min-h-screen flex-col overflow-hidden lg:h-dvh lg:min-h-0">
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -left-28 -top-28 h-[440px] w-[440px] rounded-full bg-blue-100/70 blur-3xl"></div>
            <div class="absolute -bottom-36 -right-20 h-[420px] w-[420px] rounded-full bg-rose-100/70 blur-3xl"></div>
        </div>

        <main class="flex flex-1 items-center justify-center overflow-y-auto px-0 py-5 sm:px-5 lg:min-h-0 lg:overflow-hidden lg:px-7 lg:py-3">
            <section
                class="grid w-full max-w-[1100px] overflow-hidden bg-white shadow-[0_18px_42px_rgba(33,55,93,0.10)] sm:rounded-2xl lg:h-[calc(100dvh-76px)] lg:max-h-[570px] lg:min-h-0 lg:grid-cols-[0.82fr_1.18fr]"
                aria-label="Halaman {{ $isEmployee ? 'pendaftaran karyawan' : 'pengajuan magang' }} CV Natusi"
            >
                {{-- Panel Kiri --}}
                <aside class="relative isolate hidden flex-col overflow-hidden bg-gradient-to-br px-6 py-7 text-white lg:flex lg:min-h-0 lg:px-9 lg:py-9"
                    style="background-image: linear-gradient(to bottom right, {{ $accent }}, {{ $accentDark }});"
                >
                    <div class="pointer-events-none absolute -bottom-[128px] -right-[118px] -z-10 h-[300px] w-[300px] rounded-full border-[38px] border-white/[0.065]"></div>
                    <div class="pointer-events-none absolute -bottom-[84px] -right-[76px] -z-10 h-[214px] w-[214px] rounded-full border-[38px] border-white/[0.055]"></div>

                    <a href="{{ route('login') }}" class="grid h-[56px] w-[56px] place-items-center overflow-hidden rounded-md bg-white shadow-md transition hover:opacity-90 sm:h-[62px] sm:w-[62px]">
                        <img
                            src="{{ asset('images/logo.jpeg') }}"
                            alt="Logo CV Natusi"
                            class="h-[48px] w-[48px] object-contain sm:h-[54px] sm:w-[54px]"
                        >
                    </a>

                    <div class="mt-6 max-w-[420px]">
                        <h1 class="text-[18px] font-bold leading-relaxed tracking-[-0.02em] lg:text-[20px]">
                            @if ($isEmployee)
                                Bangun Karier Profesional Anda Bersama Kami.
                            @else
                                Gerbang Menuju Pengalaman Magang Terbaik.
                            @endif
                        </h1>
                        <p class="mt-2 text-[13px] font-medium leading-relaxed text-white/75 lg:text-[15px]">
                            @if ($isEmployee)
                                Bergabunglah bersama tim profesional CV Natusi. Kembangkan bakat, kepemimpinan, dan dorong inovasi bersama kami.
                            @else
                                Bergabunglah dengan portal eksklusif kami untuk terhubung dengan pemimpin industri dan membangun jalur karier yang lebih terarah.
                            @endif
                        </p>
                    </div>

                    <blockquote class="mt-auto max-w-[420px] border-l-[3px] border-white/50 pl-4 pt-6 text-[13px] italic leading-6 text-white/85">
                        &ldquo;CV Natusi terus berkomitmen menciptakan ruang tumbuh bagi profesional berbakat untuk mencetak karya berdampak.&rdquo;
                        <footer class="mt-2 text-[11px] font-semibold not-italic text-white">
                            &mdash; Tim Pengembang Natusi 
                        </footer>
                    </blockquote>
                </aside>

                {{-- Panel Kanan (Form) --}}
                <section class="flex min-h-0 flex-col bg-white px-6 py-6 sm:px-9 sm:py-7 lg:px-10 lg:py-7">
                    <div class="mb-4 flex shrink-0 items-center gap-3 lg:hidden">
                        <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo CV Natusi" class="h-9 w-9 object-contain">
                        </span>
                        <span class="text-base font-bold" style="color: {{ $accentDark }};">CV Natusi</span>
                    </div>

                    <header class="shrink-0">
                        <h2 class="text-[17px] font-semibold leading-snug tracking-[-0.02em] text-slate-950 sm:text-[18px]">
                            {{ $isEmployee ? 'Daftar Karyawan' : 'Ajukan Magang' }}
                        </h2>
                        <p class="mt-1 text-[12px] leading-5 text-slate-600 sm:text-sm">
                            {{ $isEmployee
                                ? 'Lengkapi data berikut untuk mengajukan pendaftaran sebagai calon karyawan CV Natusi.'
                                : 'Ajukan permohonan magang atau PKL Anda di CV Natusi.' }}
                        </p>
                    </header>

                    @if (session('success'))
                        <div class="mt-3 max-h-20 shrink-0 overflow-y-auto rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-5 text-emerald-700" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-3 max-h-24 shrink-0 overflow-y-auto rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm leading-5 text-rose-700" role="alert">
                            <p class="font-semibold">Periksa kembali data berikut:</p>
                            <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs leading-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('register.store') }}"
                        enctype="multipart/form-data"
                        x-data="{
                            jenjang: '{{ old('jenjang', '') }}',
                            major: '{{ old('major', '') }}',
                            jurusanAll: @js($jurusanList->map(fn ($j) => ['tingkat' => $j->tingkat, 'nama' => $j->nama_jurusan])),
                            get jurusanTersaring() {
                                return this.jurusanAll.filter(j => j.tingkat === this.jenjang);
                            }
                        }"
                        class="mt-3.5 min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain pr-2 [scrollbar-color:#cbd5e1_transparent] [scrollbar-width:thin] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-slate-300 hover:[&::-webkit-scrollbar-thumb]:bg-slate-400"
                    >
                        @csrf
                        <input type="hidden" name="role" value="{{ $registerRole }}">

                        {{-- Nama Lengkap & Email --}}
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label for="full_name" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                    Nama Lengkap
                                </label>
                                <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
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
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                    >
                                </div>
                                @error('full_name')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                    Alamat Email
                                </label>
                                <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
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
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                    >
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Jenjang Pendidikan (khusus alur magang) --}}
                        @unless ($isEmployee)
                        <div>
                            <label for="jenjang" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                Jenjang Pendidikan
                            </label>
                            <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
                                <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m3 9 9-5 9 5-9 5-9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <path d="M6.5 11v5.5c3.7 2.2 7.3 2.2 11 0V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                                <select
                                    id="jenjang"
                                    name="jenjang"
                                    x-model="jenjang"
                                    @change="major = ''"
                                    required
                                    class="min-w-0 flex-1 cursor-pointer border-0 bg-transparent p-0 text-[13px] text-slate-700 focus:border-0 focus:ring-0 sm:text-sm"
                                >
                                    <option value="" disabled>Pilih jenjang Anda</option>
                                    <option value="kuliah">Universitas / Kuliah</option>
                                    <option value="smk">SMK</option>
                                </select>
                            </div>
                            @error('jenjang')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endunless

                        {{-- Institusi / Pendidikan Terakhir --}}
                        <div>
                            <label for="university" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                {{ $isEmployee ? 'Pendidikan Terakhir / Lulusan' : 'Asal Sekolah / Universitas' }}
                            </label>
                            <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
                                <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m3 9 9-5 9 5-9 5-9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <path d="M6.5 11v5.5c3.7 2.2 7.3 2.2 11 0V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                                <input
                                    id="university"
                                    name="university"
                                    type="text"
                                    value="{{ old('university') }}"
                                    placeholder="{{ $isEmployee ? 'Contoh: S1 Teknik Informatika - Universitas X' : 'Nama Institusi' }}"
                                    autocomplete="organization"
                                    required
                                    class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                >
                            </div>
                            @error('university')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIS/NIM & Jurusan / Posisi & Keahlian --}}
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label for="student_id" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                    {{ $isEmployee ? 'Posisi Yang Dilamar' : 'NIS / NIM' }}
                                </label>
                                <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="4" y="5" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M9 5V3.5M15 5V3.5M8 10h8M8 14h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        id="student_id"
                                        name="student_id"
                                        type="text"
                                        value="{{ old('student_id') }}"
                                        placeholder="{{ $isEmployee ? 'Contoh: Web Developer / Staff HRD' : 'Masukkan NIS atau NIM Anda' }}"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                    >
                                </div>
                                @error('student_id')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jurusan / Keahlian Utama --}}
                            <div>
                                <label for="major" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                    {{ $isEmployee ? 'Bidang / Keahlian Utama' : 'Jurusan' }}
                                </label>
                                @if ($isEmployee)
                                    <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
                                        <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <rect x="4" y="7" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        <input
                                            id="major"
                                            name="major"
                                            type="text"
                                            value="{{ old('major') }}"
                                            placeholder="Contoh: Laravel, UI/UX Design, Akuntansi"
                                            required
                                            class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                        >
                                    </div>
                                @else
                                    <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white" :class="{ 'opacity-60': !jenjang }">
                                        <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <rect x="4" y="7" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        <select
                                            id="major"
                                            name="major"
                                            x-model="major"
                                            :disabled="!jenjang"
                                            required
                                            class="min-w-0 flex-1 cursor-pointer border-0 bg-transparent p-0 text-[13px] text-slate-700 focus:border-0 focus:ring-0 disabled:cursor-not-allowed sm:text-sm"
                                        >
                                            <option value="" disabled selected x-text="jenjang ? 'Pilih jurusan Anda' : 'Pilih jenjang terlebih dahulu'"></option>
                                            <template x-for="j in jurusanTersaring" :key="j.nama">
                                                <option :value="j.nama" x-text="j.nama"></option>
                                            </template>
                                            <option value="Lainnya">Lainnya / tidak ada di daftar</option>
                                        </select>
                                    </div>
                                @endif
                                @error('major')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Telepon --}}
                        <div>
                            <label for="phone" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                Nomor Telepon / WhatsApp
                            </label>
                            <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
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
                                    class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                >
                            </div>
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Upload Berkas Lamaran (khusus Karyawan) --}}
                        @if ($isEmployee)
                            <div class="rounded-lg border border-slate-200 bg-slate-50/70 p-3.5">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                    Berkas Lamaran (PDF/JPG/PNG, maks. 2MB per file)
                                </p>

                                <div class="mt-2.5 grid gap-2.5 sm:grid-cols-2">
                                    @php
                                        $berkasWajib = [
                                            'surat_lamaran' => 'Surat Lamaran Kerja',
                                            'cv'            => 'CV (Curriculum Vitae)',
                                            'ijazah'        => 'Ijazah & Transkrip Nilai',
                                            'ktp'           => 'Fotokopi KTP',
                                            'pas_foto'      => 'Pas Foto Terbaru',
                                            'skck'          => 'SKCK',
                                        ];
                                        $berkasOpsional = [
                                            'portfolio'        => 'Portofolio',
                                            'pengalaman_kerja' => 'Surat Pengalaman Kerja',
                                        ];
                                    @endphp

                                    @foreach ($berkasWajib as $field => $label)
                                        <div>
                                            <label for="{{ $field }}" class="block text-xs font-semibold text-slate-700">
                                                {{ $label }} <span class="text-rose-500">*</span>
                                            </label>
                                            <input
                                                id="{{ $field }}"
                                                name="{{ $field }}"
                                                type="file"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                required
                                                class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-[#cf0a1f] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-[#b30a1c]"
                                            >
                                            @error($field)
                                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach

                                    @foreach ($berkasOpsional as $field => $label)
                                        <div>
                                            <label for="{{ $field }}" class="block text-xs font-semibold text-slate-700">
                                                {{ $label }} <span class="text-slate-400">(opsional)</span>
                                            </label>
                                            <input
                                                id="{{ $field }}"
                                                name="{{ $field }}"
                                                type="file"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-slate-600"
                                            >
                                            @error($field)
                                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Kata Sandi --}}
                        <div class="grid gap-3 sm:grid-cols-2" x-data="{ showPassword: false, showConfirm: false }">
                            <div>
                                <label for="password" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                    Kata Sandi
                                </label>
                                <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M8.5 10V7.5a3.5 3.5 0 1 1 7 0V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        id="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        placeholder="Min. 8 karakter"
                                        autocomplete="new-password"
                                        required
                                        minlength="8"
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                    >
                                    <button type="button" class="grid h-7 w-7 shrink-0 place-items-center rounded-full text-slate-500 transition hover:bg-slate-100" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                        <svg x-show="!showPassword" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M2.8 12s3.2-5.5 9.2-5.5 9.2 5.5 9.2 5.5-3.2 5.5-9.2 5.5S2.8 12 2.8 12Z" stroke="currentColor" stroke-width="1.8"/>
                                            <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        <svg x-show="showPassword" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m4 4 16 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M10.2 6.7c.6-.1 1.2-.2 1.8-.2 6 0 9.2 5.5 9.2 5.5a15.6 15.6 0 0 1-2.4 3.1M6.2 8.1A15.6 15.6 0 0 0 2.8 12s3.2 5.5 9.2 5.5c1 0 2-.2 2.8-.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                    Konfirmasi Kata Sandi
                                </label>
                                <div class="mt-1.5 flex h-[42px] items-center gap-2 rounded-lg border border-slate-300 bg-[#f8faff] px-3 transition focus-within:bg-white">
                                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m7 12 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    <input
                                        id="password_confirmation"
                                        :type="showConfirm ? 'text' : 'password'"
                                        name="password_confirmation"
                                        placeholder="Ulangi kata sandi"
                                        autocomplete="new-password"
                                        required
                                        minlength="8"
                                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-0 focus:ring-0 sm:text-sm"
                                    >
                                    <button type="button" class="grid h-7 w-7 shrink-0 place-items-center rounded-full text-slate-500 transition hover:bg-slate-100" @click="showConfirm = !showConfirm" :aria-label="showConfirm ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                        <svg x-show="!showConfirm" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M2.8 12s3.2-5.5 9.2-5.5 9.2 5.5 9.2 5.5-3.2 5.5-9.2 5.5S2.8 12 2.8 12Z" stroke="currentColor" stroke-width="1.8"/>
                                            <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        <svg x-show="showConfirm" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m4 4 16 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M10.2 6.7c.6-.1 1.2-.2 1.8-.2 6 0 9.2 5.5 9.2 5.5a15.6 15.6 0 0 1-2.4 3.1M6.2 8.1A15.6 15.6 0 0 0 2.8 12s3.2 5.5 9.2 5.5c1 0 2-.2 2.8-.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <p class="rounded-lg border border-sky-100 bg-sky-50 px-3 py-2 text-[11px] leading-5 text-sky-800">
                            @if ($isEmployee)
                                Email dan kata sandi ini digunakan untuk masuk ke portal pendaftaran karyawan dan memantau pembaruan status seleksi berkas Anda.
                            @else
                                Email dan kata sandi ini digunakan untuk masuk kembali dan memeriksa status pengajuan. Setelah diterima, akun peserta baru akan diberikan melalui notifikasi lonceng.
                            @endif
                        </p>

                        {{-- Deskripsi / Ringkasan --}}
                        <div>
                            <label for="description" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                {{ $isEmployee ? 'Ringkasan Diri / Pengalaman Kerja' : 'Deskripsi / Pertanyaan' }}
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="{{ $isEmployee ? 'Ceritakan secara singkat pengalaman kerja atau keahlian utama Anda...' : 'Tanyakan ketersediaan magang atau jelaskan minat Anda...' }}"
                                class="mt-1.5 block w-full resize-none rounded-lg border border-slate-300 bg-[#f8faff] px-3 py-2.5 text-[13px] leading-5 text-slate-700 placeholder:text-slate-400 transition focus:bg-white sm:text-sm"
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
                                class="mt-0.5 h-4 w-4 rounded border-slate-300"
                            >
                            <span>
                                Saya setuju dengan
                                <a href="#" class="font-semibold hover:underline" style="color: {{ $accentDark }};">Syarat &amp; Ketentuan</a>
                                dan
                                <a href="#" class="font-semibold hover:underline" style="color: {{ $accentDark }};">Kebijakan Privasi</a>.
                            </span>
                        </label>

                        <button
                            type="submit"
                            class="inline-flex h-[44px] w-full items-center justify-center gap-2 rounded-[8px] px-5 text-[13px] font-semibold tracking-[0.04em] text-white shadow-[0_7px_15px_rgba(8,103,143,0.15)] transition hover:-translate-y-0.5 hover:brightness-95 active:translate-y-0 sm:text-sm"
                            style="background-image: linear-gradient(to bottom, {{ $accent }}, {{ $accentDark }});"
                        >
                            <span>{{ $isEmployee ? 'KIRIM LAMARAN KARYAWAN' : 'AJUKAN MAGANG' }}</span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h13M13 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>

                    <p class="mt-3 shrink-0 text-center text-xs text-slate-600 sm:text-[13px]">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-medium transition hover:underline" style="color: {{ $accentDark }};">
                            Masuk
                        </a>
                    </p>
                </section>
            </section>
        </main>

        <footer class="flex min-h-14 shrink-0 flex-col items-start justify-between gap-3 border-t border-slate-300/30 bg-[#cfe2ff] px-5 py-3 text-slate-700 sm:px-8 lg:h-14 lg:min-h-14 lg:flex-row lg:items-center lg:px-[max(24px,calc((100vw-1100px)/2))] lg:py-0">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] sm:text-xs">
                <strong class="text-xs text-slate-950 sm:text-sm">CV Natusi</strong>
                <span class="hidden h-6 w-px bg-slate-500/40 sm:block"></span>
                <span>&copy; 2026 Portal Magang CV Natusi. Hak cipta dilindungi undang-undang.</span>
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
