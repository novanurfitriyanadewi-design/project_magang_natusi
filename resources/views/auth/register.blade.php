
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $registerRole = $registerRole ?? session('register_role', 'pelamar');
        $isEmployee = $registerRole === 'karyawan';

        $accent = '#08678f';
        $accentDark = '#075c80';
        $accentSoft = '#e8f4f9';
    @endphp

    <title>{{ $isEmployee ? 'Daftar Karyawan' : 'Ajukan Magang' }} | CV Natusi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        input[type="file"]::file-selector-button {
            margin-right: 0.75rem;
            border: 0;
            border-radius: 0.375rem;
            background-color: {{ $accent }};
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: {{ $accentDark }};
        }

        input[name="portfolio"]::file-selector-button,
        input[name="pengalaman_kerja"]::file-selector-button {
            background-color: #64748b;
        }

        input[name="portfolio"]::file-selector-button:hover,
        input[name="pengalaman_kerja"]::file-selector-button:hover {
            background-color: #475569;
        }
    </style>
</head>

<body class="min-h-screen bg-[#f7f9ff] font-['Inter'] text-slate-900 antialiased lg:h-dvh lg:overflow-hidden">

<div class="relative flex min-h-screen flex-col overflow-hidden lg:h-dvh lg:min-h-0">

    <div class="pointer-events-none absolute inset-0 -z-10">
        <div
            class="absolute -left-28 -top-28 h-[440px] w-[440px] rounded-full blur-3xl"
            style="background-color: {{ $accentSoft }};"
        ></div>

        <div class="absolute -bottom-36 -right-20 h-[420px] w-[420px] rounded-full bg-rose-50/60 blur-3xl"></div>
    </div>

    <main class="flex flex-1 items-center justify-center overflow-y-auto px-0 py-5 sm:px-5 lg:min-h-0 lg:overflow-hidden lg:px-7 lg:py-3">

        <section
            class="grid w-full max-w-[1100px] overflow-hidden bg-white shadow-[0_18px_42px_rgba(33,55,93,0.10)] sm:rounded-2xl lg:h-[calc(100dvh-76px)] lg:max-h-[570px] lg:min-h-0 lg:grid-cols-[0.82fr_1.18fr]"
            aria-label="Halaman {{ $isEmployee ? 'pendaftaran karyawan' : 'pengajuan magang' }} CV Natusi"
        >

            {{-- PANEL KIRI --}}
            <aside
                class="relative isolate hidden flex-col overflow-hidden bg-gradient-to-br px-6 py-7 text-white lg:flex lg:min-h-0 lg:px-9 lg:py-9"
                style="background-image: linear-gradient(135deg, {{ $accent }}, {{ $accentDark }});"
            >

                <div class="pointer-events-none absolute -bottom-[128px] -right-[118px] -z-10 h-[300px] w-[300px] rounded-full border-[38px] border-white/[0.065]"></div>

                <div class="pointer-events-none absolute -bottom-[84px] -right-[76px] -z-10 h-[214px] w-[214px] rounded-full border-[38px] border-white/[0.055]"></div>

                <a
                    href="{{ route('login') }}"
                    class="grid h-[56px] w-[56px] place-items-center overflow-hidden rounded-md bg-white shadow-md transition hover:opacity-90 sm:h-[62px] sm:w-[62px]"
                >
                    <img
                        src="{{ asset('images/logo.jpeg') }}"
                        alt="CV Natusi Logo"
                        class="h-[48px] w-[48px] object-contain sm:h-[54px] sm:w-[54px]"
                    >
                </a>

                <div class="mt-6 max-w-[420px]">
                    <h1 class="text-[18px] font-bold leading-relaxed tracking-[-0.02em] lg:text-[20px]">
                        @if ($isEmployee)
                            Build Your Professional Career With Us.
                        @else
                            Your Gateway to the Best Internship Experience.
                        @endif
                    </h1>

                    <p class="mt-2 text-[13px] font-medium leading-relaxed text-white/75 lg:text-[15px]">
                        @if ($isEmployee)
                            Join the professional team at CV Natusi. Grow your talent, leadership, and drive innovation together with us.
                        @else
                            Join our exclusive portal to connect with industry leaders and build a clearer career path.
                        @endif
                    </p>
                </div>

                <blockquote class="mt-auto max-w-[420px] border-l-[3px] border-white/50 pl-4 pt-6 text-[13px] italic leading-6 text-white/85">
                    &ldquo;CV Natusi remains committed to creating a space for talented professionals to grow and produce impactful work.&rdquo;

                    <footer class="mt-2 text-[11px] font-semibold not-italic text-white">
                        &mdash; Natusi Development Team
                    </footer>
                </blockquote>

            </aside>

            {{-- PANEL KANAN --}}
            <section class="flex min-h-0 flex-col bg-white px-6 py-6 sm:px-9 sm:py-7 lg:px-10 lg:py-7">

                {{-- Logo mobile --}}
                <div class="mb-4 flex shrink-0 items-center gap-3 lg:hidden">
                    <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                        <img
                            src="{{ asset('images/logo.jpeg') }}"
                            alt="CV Natusi Logo"
                            class="h-9 w-9 object-contain"
                        >
                    </span>

                    <span
                        class="text-base font-bold"
                        style="color: {{ $accentDark }};"
                    >
                        CV Natusi
                    </span>
                </div>

                <header class="shrink-0">
                    <h2 class="text-[17px] font-semibold leading-snug tracking-[-0.02em] text-slate-950 sm:text-[18px]">
                        {{ $isEmployee ? 'Daftar Karyawan' : 'Ajukan Magang' }}
                    </h2>

                    <p class="mt-1 text-[12px] leading-5 text-slate-600 sm:text-sm">
                        {{ $isEmployee
                            ? 'Lengkapi data berikut untuk mengajukan pendaftaran sebagai calon karyawan CV Natusi.'
                            : 'Ajukan permohonan magang atau PKL Anda di CV Natusi.'
                        }}
                    </p>
                </header>

                @if (session('success'))
                    <div class="mt-3 max-h-20 shrink-0 overflow-y-auto rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-5 text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-3 max-h-24 shrink-0 overflow-y-auto rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm leading-5 text-rose-700">
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

                        jurusanAll: @js(
                            $jurusanList->map(fn ($j) => [
                                'tingkat' => $j->tingkat,
                                'nama' => $j->nama_jurusan
                            ])
                        ),

                        get jurusanTersaring() {
                            return this.jurusanAll.filter(
                                j => j.tingkat === this.jenjang
                            );
                        }
                    }"

                    class="mt-3.5 min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain pr-2"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="role"
                        value="{{ $registerRole }}"
                    >

                    {{-- NAMA & EMAIL --}}
                    <div class="grid gap-3 sm:grid-cols-2">

                        <div>
                            <label
                                for="full_name"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Nama Lengkap
                            </label>

                            <input
                                id="full_name"
                                name="full_name"
                                type="text"
                                value="{{ old('full_name') }}"
                                placeholder="Nama Lengkap Anda"
                                autocomplete="name"
                                required
                                autofocus
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >

                            @error('full_name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="email"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Alamat Email
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="email@contoh.com"
                                autocomplete="email"
                                required
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >

                            @error('email')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>


                    {{-- ========================= --}}
                    {{-- BAGIAN KHUSUS MAGANG --}}
                    {{-- ========================= --}}

                    @unless ($isEmployee)

                        {{-- JENJANG --}}
                        <div>
                            <label
                                for="jenjang"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Jenjang Pendidikan
                            </label>

                            <select
                                id="jenjang"
                                name="jenjang"
                                x-model="jenjang"
                                @change="major = ''"
                                required
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >
                                <option value="" disabled>
                                    Pilih jenjang Anda
                                </option>

                                <option value="kuliah">
                                    Universitas / Kuliah
                                </option>

                                <option value="smk">
                                    SMK
                                </option>
                            </select>

                            @error('jenjang')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ASAL SEKOLAH --}}
                        <div>
                            <label
                                for="university"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Asal Sekolah / Universitas
                            </label>

                            <input
                                id="university"
                                name="university"
                                type="text"
                                value="{{ old('university') }}"
                                placeholder="Nama Sekolah / Universitas"
                                required
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >

                            @error('university')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIS/NIM & JURUSAN --}}
                        <div class="grid gap-3 sm:grid-cols-2">

                            <div>
                                <label
                                    for="student_id"
                                    class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                                >
                                    NIS / NIM
                                </label>

                                <input
                                    id="student_id"
                                    name="student_id"
                                    type="text"
                                    value="{{ old('student_id') }}"
                                    placeholder="Masukkan NIS atau NIM"
                                    required
                                    class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                                >

                                @error('student_id')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="major"
                                    class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                                >
                                    Jurusan
                                </label>

                                <select
                                    id="major"
                                    name="major"
                                    x-model="major"
                                    :disabled="!jenjang"
                                    required
                                    class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 focus:border-slate-400 focus:bg-white focus:ring-0 disabled:cursor-not-allowed sm:text-sm"
                                >
                                    <option value="" disabled>
                                        Pilih jurusan Anda
                                    </option>

                                    <template
                                        x-for="j in jurusanTersaring"
                                        :key="j.nama"
                                    >
                                        <option
                                            :value="j.nama"
                                            x-text="j.nama"
                                        ></option>
                                    </template>

                                    <option value="Lainnya">
                                        Lainnya / tidak ada di daftar
                                    </option>
                                </select>

                                @error('major')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                    @endunless


                    {{-- ========================= --}}
                    {{-- BAGIAN KHUSUS KARYAWAN --}}
                    {{-- ========================= --}}

                    @if ($isEmployee)

                        {{-- PENDIDIKAN TERAKHIR --}}
                        <div>
                            <label
                                for="university"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Pendidikan Terakhir / Lulusan
                            </label>

                            <input
                                id="university"
                                name="university"
                                type="text"
                                value="{{ old('university') }}"
                                placeholder="Contoh: S1 Teknik Informatika - Universitas X"
                                required
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >

                            @error('university')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIK & POSISI --}}
                        <div class="grid gap-3 sm:grid-cols-2">

                            <div>
                                <label
                                    for="nik"
                                    class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                                >
                                    NIK
                                </label>

                                <input
                                    id="nik"
                                    name="nik"
                                    type="text"
                                    value="{{ old('nik') }}"
                                    placeholder="Masukkan NIK"
                                    maxlength="16"
                                    inputmode="numeric"
                                    required
                                    class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                                >

                                @error('nik')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="position"
                                    class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                                >
                                    Posisi Yang Dilamar
                                </label>

                                <input
                                    id="position"
                                    name="position"
                                    type="text"
                                    value="{{ old('position') }}"
                                    placeholder="Contoh: Web Developer / Staff HRD"
                                    required
                                    class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                                >

                                @error('position')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- KEAHLIAN --}}
                        <div>
                            <label
                                for="major"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Keahlian Utama
                            </label>

                            <input
                                id="major"
                                name="major"
                                type="text"
                                value="{{ old('major') }}"
                                placeholder="Contoh: Laravel, UI/UX Design, Akuntansi"
                                required
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >

                            @error('major')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- BERKAS LAMARAN --}}
                        <div class="rounded-lg border border-slate-200 bg-slate-50/70 p-3.5">

                            <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700">
                                Berkas Lamaran (PDF/JPG/PNG, maks. 2MB per file)
                            </p>

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

                            <div class="mt-2.5 grid gap-2.5 sm:grid-cols-2">

                                @foreach ($berkasWajib as $field => $label)

                                    <div>
                                        <label
                                            for="{{ $field }}"
                                            class="block text-xs font-semibold text-slate-700"
                                        >
                                            {{ $label }}
                                            <span class="text-rose-500">*</span>
                                        </label>

                                        <input
                                            id="{{ $field }}"
                                            name="{{ $field }}"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            class="mt-1 block w-full text-xs text-slate-600"
                                        >

                                        @error($field)
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                @endforeach

                                @foreach ($berkasOpsional as $field => $label)

                                    <div>
                                        <label
                                            for="{{ $field }}"
                                            class="block text-xs font-semibold text-slate-700"
                                        >
                                            {{ $label }}
                                            <span class="text-slate-400">(opsional)</span>
                                        </label>

                                        <input
                                            id="{{ $field }}"
                                            name="{{ $field }}"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            class="mt-1 block w-full text-xs text-slate-600"
                                        >

                                        @error($field)
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                @endforeach

                            </div>
                        </div>

                    @endif


                    {{-- TELEPON --}}
                    <div>
                        <label
                            for="phone"
                            class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                        >
                            Nomor Telepon / WhatsApp
                        </label>

                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone') }}"
                            placeholder="0812..."
                            autocomplete="tel"
                            inputmode="tel"
                            required
                            class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                        >

                        @error('phone')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- PASSWORD --}}
                    <div
                        class="grid gap-3 sm:grid-cols-2"
                        x-data="{ showPassword: false, showConfirm: false }"
                    >

                        <div>
                            <label
                                for="password"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Kata Sandi
                            </label>

                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                placeholder="Min. 8 karakter"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >

                            @error('password')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="password_confirmation"
                                class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                            >
                                Konfirmasi Kata Sandi
                            </label>

                            <input
                                id="password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                name="password_confirmation"
                                placeholder="Ulangi kata sandi"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="mt-1.5 block h-[42px] w-full rounded-lg border border-slate-300 bg-[#f8faff] px-3 text-[13px] text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                            >

                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>


                    {{-- INFO --}}
                    <p
                        class="rounded-lg border px-3 py-2 text-[11px] leading-5"
                        style="border-color: {{ $accentSoft }}; background-color: {{ $accentSoft }}; color: {{ $accentDark }};"
                    >
                        @if ($isEmployee)
                            Email dan kata sandi digunakan untuk masuk ke portal pendaftaran karyawan dan memantau pembaruan status seleksi berkas Anda.
                        @else
                            Email dan kata sandi digunakan untuk masuk kembali dan memeriksa status pengajuan magang.
                        @endif
                    </p>


                    {{-- DESKRIPSI --}}
                    <div>
                        <label
                            for="description"
                            class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-700"
                        >
                            {{ $isEmployee ? 'Ringkasan Diri / Pengalaman Kerja' : 'Deskripsi / Pertanyaan' }}
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="{{ $isEmployee
                                ? 'Ceritakan secara singkat pengalaman kerja atau keahlian utama Anda...'
                                : 'Tanyakan ketersediaan magang atau jelaskan minat Anda...'
                            }}"
                            class="mt-1.5 block w-full resize-none rounded-lg border border-slate-300 bg-[#f8faff] px-3 py-2.5 text-[13px] leading-5 text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-0 sm:text-sm"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-xs text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- PERSETUJUAN --}}
                    <label
                        for="terms"
                        class="flex cursor-pointer items-start gap-2.5 pt-0.5 text-[11px] leading-[1.55] text-slate-600"
                    >
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
                            <a
                                href="#"
                                class="font-semibold hover:underline"
                                style="color: {{ $accentDark }};"
                            >
                                Syarat &amp; Ketentuan
                            </a>
                            dan
                            <a
                                href="#"
                                class="font-semibold hover:underline"
                                style="color: {{ $accentDark }};"
                            >
                                Kebijakan Privasi
                            </a>.
                        </span>
                    </label>


                    {{-- BUTTON --}}
                    <button
                        type="submit"
                        class="inline-flex h-[44px] w-full items-center justify-center gap-2 rounded-[8px] px-5 text-[13px] font-semibold tracking-[0.04em] text-white shadow-[0_7px_15px_rgba(30,58,95,0.18)] transition hover:-translate-y-0.5 hover:brightness-110 active:translate-y-0 sm:text-sm"
                        style="background-image: linear-gradient(to bottom, {{ $accent }}, {{ $accentDark }});"
                    >
                        <span>
                            {{ $isEmployee ? 'KIRIM LAMARAN KARYAWAN' : 'AJUKAN MAGANG' }}
                        </span>

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M5 12h13M13 7l5 5-5 5"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>

                </form>

                <p class="mt-3 shrink-0 text-center text-xs text-slate-600 sm:text-[13px]">
                    Sudah punya akun?

                    <a
                        href="{{ route('login') }}"
                        class="font-medium transition hover:underline"
                        style="color: {{ $accentDark }};"
                    >
                        Masuk
                    </a>
                </p>

            </section>
        </section>
    </main>

    <footer class="flex min-h-14 shrink-0 flex-col items-start justify-between gap-3 border-t border-slate-300/30 bg-[#eef2f7] px-5 py-3 text-slate-700 sm:px-8 lg:h-14 lg:min-h-14 lg:flex-row lg:items-center lg:px-[max(24px,calc((100vw-1100px)/2))] lg:py-0">

        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] sm:text-xs">
            <strong class="text-xs text-slate-950 sm:text-sm">
                CV Natusi
            </strong>

            <span class="hidden h-6 w-px bg-slate-500/40 sm:block"></span>

            <span>
                &copy; 2026
                {{ $isEmployee ? 'Employee Portal' : 'Internship Portal' }}
                CV Natusi. All rights reserved.
            </span>
        </div>

        <nav class="flex flex-wrap gap-x-6 gap-y-2 text-xs">
            <a href="#" class="hover:underline" style="color: {{ $accentDark }};">
                Kebijakan Privasi
            </a>

            <a href="#" class="hover:underline" style="color: {{ $accentDark }};">
                Ketentuan Layanan
            </a>

            <a href="#" class="hover:underline" style="color: {{ $accentDark }};">
                Hubungi Dukungan
            </a>
        </nav>

    </footer>

</div>

</body>
</html>
```
