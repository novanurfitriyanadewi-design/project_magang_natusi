@extends('layouts.portal')

@section('title', 'Data Karyawan')

@section('content')

<div
    x-data="{
        detailOpen: false,
        editOpen: false,
        deleteOpen: false,

        detailKaryawan: {},
        editKaryawan: {},
        deleteKaryawan: {},

        // 💡 Masukkan data daftar divisi dari backend ke Alpine (opsional jika ingin auto lookup nama divisi)
        divisiList: {{ json_encode($divisiList ?? []) }},

        openDetail(k) {
            // Memastikan data detail membaca nama field dengan fallback yang konsisten
            this.detailKaryawan = {
                ...k,
                nama: k.nama_karyawan || k.nama || '-',
                divisi: k.nama_divisi || k.divisi || '-',
                tanggal: k.tanggal_bergabung || k.tanggal || '-'
            };
            this.detailOpen = true;
            this.editOpen = false;
            this.deleteOpen = false;
        },

        openEdit(k) {
            // 💡 1. Salin objek k ke editKaryawan & petakan field yang beda nama
            this.editKaryawan = {
                action: k.action || '',
                nama_karyawan: k.nama_karyawan || k.nama || '',
                nik: k.nik || '',
                nip: k.nip || '',
                email: k.email || '',
                no_hp: k.no_hp || '',
                alamat: k.alamat || '',
                jabatan: k.jabatan || '',
                status: (k.status || 'aktif').toLowerCase(),
                tanggal_bergabung: k.tanggal_bergabung || k.tanggal || '',
                divisi_id: k.divisi_id || k.id_divisi || '',
                
                // Simpan referensi objek asli tabel untuk di-update saat simpan
                _original: k 
            };
            this.editOpen = true;
            this.detailOpen = false;
            this.deleteOpen = false;
        },

        openDelete(k) {
            this.deleteKaryawan = {
                action: k.action || '',
                nama: k.nama_karyawan || k.nama || ''
            };
            this.deleteOpen = true;
            this.detailOpen = false;
            this.editOpen = false;
        },

        // 💡 2. Fungsi untuk menyinkronkan data edit ke data detail jika menggunakan AJAX submit
        syncEditToDetail() {
            let edit = this.editKaryawan;
            
            // Cari nama divisi berdasarkan id divisi yang dipilih
            let selectedDiv = Array.isArray(this.divisiList) 
                ? this.divisiList.find(d => (d.id_divisi || d.id) == edit.divisi_id)
                : null;
            let namaDivisi = selectedDiv ? selectedDiv.nama_divisi : edit.divisi;

            // Update data detail secara langsung
            this.detailKaryawan = {
                ...this.detailKaryawan,
                nama: edit.nama_karyawan,
                nama_karyawan: edit.nama_karyawan,
                nik: edit.nik,
                nip: edit.nip,
                email: edit.email,
                no_hp: edit.no_hp,
                alamat: edit.alamat,
                status: edit.status,
                divisi: namaDivisi,
                nama_divisi: namaDivisi,
                tanggal: edit.tanggal_bergabung,
                tanggal_bergabung: edit.tanggal_bergabung
            };

            // Update juga ke referensi objek tabelnya
            if (edit._original) {
                Object.assign(edit._original, this.detailKaryawan);
            }
        },

        closeModals() {
            this.detailOpen = false;
            this.editOpen = false;
            this.deleteOpen = false;
        }
    }"
    @keydown.escape.window="closeModals()"
    x-effect="document.body.classList.toggle('overflow-hidden', detailOpen || editOpen || deleteOpen)"
>

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-700">
                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                Manajemen Personil
            </span>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                Data Karyawan
            </h1>

            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                Kelola informasi seluruh personil CV Natusi secara efisien,
                mulai dari data pribadi, jabatan, divisi, hingga status kepegawaian.
            </p>
        </div>
    </div>


    {{-- =========================================================
        STATISTIK
    ========================================================== --}}
    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total --}}
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-5 text-white shadow-lg shadow-indigo-500/10">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-indigo-100">
                        Total Karyawan
                    </p>

                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8" />
                            <path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </span>
                </div>

                <p class="mt-4 text-3xl font-extrabold">
                    {{ $totalKaryawan ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-indigo-100">
                    Seluruh personil terdaftar
                </p>
            </div>

            <span class="pointer-events-none absolute -bottom-8 -right-8 h-28 w-28 rounded-full bg-white/10"></span>
        </article>


        {{-- Aktif --}}
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-lg shadow-emerald-500/10">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-100">
                        Karyawan Aktif
                    </p>

                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>

                <p class="mt-4 text-3xl font-extrabold">
                    {{ $karyawanAktif ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-emerald-100">
                    Sedang bekerja aktif
                </p>
            </div>

            <span class="pointer-events-none absolute -bottom-8 -right-8 h-28 w-28 rounded-full bg-white/10"></span>
        </article>


        {{-- Non Aktif --}}
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white shadow-lg shadow-amber-500/10">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-amber-100">
                        Karyawan Non-Aktif
                    </p>

                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </span>
                </div>

                <p class="mt-4 text-3xl font-extrabold">
                    {{ $karyawanNonAktif ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-amber-100">
                    Tidak lagi bertugas
                </p>
            </div>

            <span class="pointer-events-none absolute -bottom-8 -right-8 h-28 w-28 rounded-full bg-white/10"></span>
        </article>


        {{-- Baru --}}
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 to-purple-700 p-5 text-white shadow-lg shadow-violet-500/10">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-violet-100">
                        Baru Bulan Ini
                    </p>

                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" />
                        </svg>
                    </span>
                </div>

                <p class="mt-4 text-3xl font-extrabold">
                    {{ $karyawanBaruBulanIni ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-violet-100">
                    Bergabung bulan ini
                </p>
            </div>

            <span class="pointer-events-none absolute -bottom-8 -right-8 h-28 w-28 rounded-full bg-white/10"></span>
        </article>

    </section>


    {{-- =========================================================
        FILTER
    ========================================================== --}}
    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">
            <div class="flex items-center gap-3">

                <span class="grid h-9 w-9 place-items-center rounded-xl bg-sky-100 text-sky-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                </span>

                <div>
                    <h2 class="text-sm font-bold text-slate-800">
                        Filter Data Karyawan
                    </h2>

                    <p class="text-xs text-slate-400">
                        Gunakan filter untuk menemukan data karyawan dengan cepat.
                    </p>
                </div>

            </div>
        </div>


        <form method="GET" class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Cari Karyawan
                </label>

                <div class="relative">

                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7" />
                            <path d="m20 20-4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                        </svg>
                    </span>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, NIP, atau email..."
                        class="w-full rounded-xl border-slate-200 py-2.5 pl-10 pr-4 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                    >

                </div>
            </div>


            {{-- Divisi --}}
            <div>
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Divisi
                </label>

                <select
                    name="divisi_id"
                    class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                >

                    <option value="">
                        Semua Divisi
                    </option>

                    @foreach ($divisiList as $divisi)

                        <option
                            value="{{ $divisi->id_divisi }}"
                            @selected(
                                (string) request('divisi_id') ===
                                (string) $divisi->id_divisi
                            )
                        >
                            {{ $divisi->nama_divisi }}
                        </option>

                    @endforeach

                </select>
            </div>


            {{-- Status --}}
            <div>
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                >

                    <option value="">
                        Semua Status
                    </option>

                    <option value="aktif" @selected(request('status') === 'aktif')>
                        Aktif
                    </option>

                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>
                        Non-Aktif
                    </option>

                </select>
            </div>


            {{-- Urutan --}}
            <div>
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Urutan
                </label>

                <select
                    name="urutan"
                    class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                >

                    <option value="nama_asc" @selected(request('urutan', 'nama_asc') === 'nama_asc')>
                        Nama (A-Z)
                    </option>

                    <option value="nama_desc" @selected(request('urutan') === 'nama_desc')>
                        Nama (Z-A)
                    </option>

                    <option value="nip" @selected(request('urutan') === 'nip')>
                        NIP
                    </option>

                    <option value="tanggal" @selected(request('urutan') === 'tanggal')>
                        Tanggal Bergabung
                    </option>

                </select>
            </div>


            {{-- Tombol --}}
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-3">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700 hover:shadow-md"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <path d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                    </svg>
                    Terapkan Filter
                </button>

                <a
                    href="{{ route('admin-karyawan.karyawan.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Reset
                </a>

            </div>

        </form>

    </section>


    {{-- =========================================================
        TABLE
    ========================================================== --}}
    <section class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        {{-- Header --}}
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-base font-extrabold text-slate-900">
                    Daftar Karyawan
                </h2>

                <p class="mt-0.5 text-xs text-slate-400">
                    Informasi personil yang terdaftar pada sistem.
                </p>
            </div>

            <div class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">
                {{ $karyawans->total() }} Data
            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-[1100px] w-full divide-y divide-slate-100">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Nama Karyawan</th>
                        <th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-wider text-slate-500">NIP</th>
                        <th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Jabatan</th>
                        <th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Divisi</th>
                        <th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-wider text-slate-500">No. HP</th>
                        <th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Bergabung</th>
                        <th class="px-5 py-4 text-left text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-5 py-4 text-center text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($karyawans as $karyawan)

                        @php
                            $status = strtolower($karyawan->status ?? 'nonaktif');

                            $meta = match ($status) {
                                'aktif' => [
                                    'label' => 'Aktif',
                                    'text' => 'text-emerald-600',
                                    'dot' => 'bg-emerald-500',
                                ],
                                'nonaktif' => [
                                    'label' => 'Non-Aktif',
                                    'text' => 'text-rose-600',
                                    'dot' => 'bg-rose-500',
                                ],
                                default => [
                                    'label' => ucfirst($status),
                                    'text' => 'text-slate-500',
                                    'dot' => 'bg-slate-400',
                                ],
                            };

                            $nama = trim($karyawan->nama_karyawan ?? '-');
                            $namaParts = preg_split('/\s+/', $nama);
                            $initials = collect($namaParts)
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                ->implode('');

                            $tanggalBergabung = $karyawan->tanggal_bergabung
                                ? \Carbon\Carbon::parse($karyawan->tanggal_bergabung)
                                : null;
                        @endphp


                        <tr class="group transition hover:bg-sky-50/50">

                            {{-- Nama --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-100 to-blue-100 text-sm font-extrabold text-sky-700 ring-4 ring-sky-50">
                                        {{ $initials ?: '?' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-slate-800">
                                            {{ $karyawan->nama_karyawan ?? '-' }}
                                        </p>
                                        <p class="mt-0.5 truncate text-xs text-slate-400">
                                            {{ $karyawan->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- NIP --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-600">
                                {{ $karyawan->nip ?? '-' }}
                            </td>

                            {{-- Jabatan --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $karyawan->jabatan ?? '-' }}
                            </td>

                            {{-- Divisi --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($karyawan->divisi)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1.5 text-[11px] font-bold text-sky-700 ring-1 ring-inset ring-sky-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                        {{ $karyawan->divisi->nama_divisi }}
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-slate-400">
                                        Belum ada divisi
                                    </span>
                                @endif
                            </td>

                            {{-- HP --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $karyawan->no_hp ?? '-' }}
                            </td>

                            {{-- Tanggal --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none">
                                        <rect x="3" y="4" width="18" height="17" rx="2" stroke="currentColor" stroke-width="1.6" />
                                        <path d="M16 2v4M8 2v4M3 9h18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                                    </svg>
                                    {{ $tanggalBergabung ? $tanggalBergabung->translatedFormat('d M Y') : '-' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center gap-2 text-xs font-bold {{ $meta['text'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $meta['dot'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex justify-center gap-1.5">

                                    {{-- Detail --}}
                                    <button
                                        type="button"
                                        title="Lihat Detail"
                                        class="grid h-9 w-9 place-items-center rounded-xl text-sky-600 transition hover:bg-sky-50"
                                        @click="openDetail({
                                            nama: @js($karyawan->nama_karyawan ?? '-'),
                                            nik: @js(optional($karyawan->permintaanLamaran)->nik ?? '-'),
                                            nip: @js($karyawan->nip ?? '-'),
                                            email: @js($karyawan->email ?? '-'),
                                            no_hp: @js($karyawan->no_hp ?? '-'),
                                            alamat: @js($karyawan->alamat ?? '-'),
                                            jabatan: @js($karyawan->jabatan ?? '-'),
                                            divisi: @js($karyawan->divisi?->nama_divisi ?? 'Belum ada'),
                                            status: @js($meta['label']),
                                            tanggal: @js($tanggalBergabung ? $tanggalBergabung->translatedFormat('d M Y') : '-'),
                                            berkas: @js($karyawan->berkas ?? [])
                                        })"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" stroke="currentColor" stroke-width="1.7" />
                                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7" />
                                        </svg>
                                    </button>

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        title="Edit Karyawan"
                                        class="grid h-9 w-9 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                        @click="openEdit({
                                            action: @js(route('admin-karyawan.karyawan.update', $karyawan->id_karyawan)),
                                            nama_karyawan: @js($karyawan->nama_karyawan ?? ''),
                                            nip: @js($karyawan->nip ?? ''),
                                            email: @js($karyawan->email ?? ''),
                                            no_hp: @js($karyawan->no_hp ?? ''),
                                            alamat: @js($karyawan->alamat ?? ''),
                                            jabatan: @js($karyawan->jabatan ?? ''),
                                            status: @js($karyawan->status ?? 'aktif'),
                                            tanggal_bergabung: @js($tanggalBergabung ? $tanggalBergabung->format('Y-m-d') : ''),
                                            divisi_id: @js($karyawan->divisi_id ?? '')
                                        })"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M11 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>

                                    {{-- Hapus --}}
                                    <button
                                        type="button"
                                        title="Hapus Karyawan"
                                        class="grid h-9 w-9 place-items-center rounded-xl text-rose-600 transition hover:bg-rose-50"
                                        @click="openDelete({
                                            action: @js(route('admin-karyawan.karyawan.destroy', $karyawan->id_karyawan)),
                                            nama: @js($karyawan->nama_karyawan ?? '-')
                                        })"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <span class="grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <h3 class="mt-4 text-sm font-bold text-slate-700">
                                        Belum Ada Data Karyawan
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-400">
                                        Tidak ada data karyawan yang sesuai dengan filter pencarian.
                                    </p>
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <span>
                Menampilkan
                <strong class="text-slate-700">{{ $karyawans->firstItem() ?? 0 }}</strong>
                –
                <strong class="text-slate-700">{{ $karyawans->lastItem() ?? 0 }}</strong>
                dari
                <strong class="text-slate-700">{{ $karyawans->total() }}</strong>
                karyawan
            </span>

            <div>
                {{ $karyawans->links() }}
            </div>
        </div>

    </section>


{{-- =========================================================
    MODAL DETAIL
========================================================== --}}
<div
    x-cloak
    x-show="detailOpen"
    x-transition.opacity
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    {{-- Overlay / Backdrop --}}
    <div 
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" 
        @click="detailOpen = false"
    ></div>

    {{-- Modal Wrapper --}}
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
        
        {{-- Modal Container --}}
        <div
            x-show="detailOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            class="relative flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl"
        >
            {{-- Header --}}
            <div class="relative shrink-0 overflow-hidden bg-gradient-to-br from-sky-600 via-blue-600 to-indigo-600 px-6 py-7 text-white">
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-white/20 text-xl font-extrabold ring-1 ring-white/30">
                            <span
                                x-text="
                                    detailKaryawan.nama_karyawan
                                        ? detailKaryawan.nama_karyawan.split(' ').map(n => n.charAt(0)).slice(0,2).join('').toUpperCase()
                                        : '?'
                                "
                            ></span>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-sky-100">Profil Karyawan</p>
                            <h3 class="mt-1 text-xl font-extrabold" x-text="detailKaryawan.nama_karyawan"></h3>
                            <p class="mt-1 text-sm text-blue-100" x-text="detailKaryawan.nama_divisi || detailKaryawan.jabatan || '-'"></p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="detailOpen = false"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white/10 text-white transition hover:bg-white/20"
                    >
                        <span class="text-lg">✕</span>
                    </button>
                </div>

                <span class="pointer-events-none absolute -bottom-10 -right-10 h-32 w-32 rounded-full bg-white/10"></span>
                <span class="pointer-events-none absolute -top-16 right-28 h-28 w-28 rounded-full bg-white/5"></span>
            </div>

            {{-- Body (Scrollable Container) --}}
            <div class="flex-1 overflow-y-auto px-6 py-6 custom-scrollbar">

                {{-- Status --}}
                <div class="mb-6 flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-white shadow-sm">
                            <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3v18M3 12h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Kepegawaian</p>
                            <p class="mt-0.5 text-sm font-bold text-slate-800 capitalize" x-text="detailKaryawan.status"></p>
                        </div>
                    </div>

                    <span
                        class="h-2.5 w-2.5 rounded-full"
                        :class="detailKaryawan.status?.toLowerCase() === 'aktif' ? 'bg-emerald-500' : 'bg-slate-400'"
                    ></span>
                </div>

                {{-- Informasi Utama --}}
                <div>
                    <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-slate-400">
                        Informasi Utama
                    </h4>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Lengkap</p>
                            <p class="mt-1.5 text-sm font-bold text-slate-800" x-text="detailKaryawan.nama_karyawan"></p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIK</p>
                            <p class="mt-1.5 break-all text-sm font-bold text-slate-800" x-text="detailKaryawan.nik || '-'"></p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIP</p>
                            <p class="mt-1.5 break-all text-sm font-bold text-slate-800" x-text="detailKaryawan.nip || '-'"></p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</p>
                            <p class="mt-1.5 break-all text-sm font-bold text-slate-800" x-text="detailKaryawan.email || '-'"></p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Divisi</p>
                            <p class="mt-1.5 text-sm font-bold text-slate-800" x-text="detailKaryawan.nama_divisi || '-'"></p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor HP</p>
                            <p class="mt-1.5 text-sm font-bold text-slate-800" x-text="detailKaryawan.no_hp || '-'"></p>
                        </div>

                        <div class="sm:col-span-2 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Bergabung</p>
                            <p class="mt-1.5 text-sm font-bold text-slate-800" x-text="detailKaryawan.tanggal_bergabung || '-'"></p>
                        </div>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="mt-6">
                    <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-slate-400">Alamat</h4>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-slate-500 shadow-sm">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="1.7" />
                                    <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.7" />
                                </svg>
                            </span>
                            <p class="text-sm leading-6 text-slate-600" x-text="detailKaryawan.alamat || '-'"></p>
                        </div>
                    </div>
                </div>

                {{-- Berkas Lamaran --}}
                <div class="mt-6" x-show="detailKaryawan.berkas && detailKaryawan.berkas.length">
                    <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-slate-400">
                        Berkas Lamaran
                    </h4>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <template x-for="dok in detailKaryawan.berkas" :key="dok.key">
                            <div class="flex items-center justify-between gap-2 rounded-2xl border border-slate-100 bg-white p-3.5 shadow-sm">
                                <span class="text-xs font-bold text-slate-700" x-text="dok.label"></span>

                                <div class="shrink-0">
                                    <template x-if="dok.url">
                                        <div class="flex items-center gap-1.5">
                                            <a :href="dok.url" target="_blank" class="grid h-8 w-8 place-items-center rounded-lg bg-sky-500 text-white hover:bg-sky-600" title="Lihat">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a :href="dok.url" download class="grid h-8 w-8 place-items-center rounded-lg border border-sky-200 text-sky-500 hover:bg-sky-50" title="Unduh">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                        </div>
                                    </template>
                                    <template x-if="!dok.url">
                                        <span class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-400">Tidak ada</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex shrink-0 justify-end border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button
                    type="button"
                    @click="detailOpen = false"
                    class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-slate-900"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


{{-- =========================================================
    MODAL EDIT
========================================================== --}}
<div
    x-cloak
    x-show="editOpen"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="editOpen = false"></div>

    {{-- Modal --}}
    <form
        method="POST"
        :action="editKaryawan.action"
        x-show="editOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        class="relative max-h-[92vh] w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl"
    >
        @csrf
        @method('PUT')

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-sky-600">Manajemen Personil</p>
                <h3 class="mt-1 text-lg font-extrabold text-slate-950">Edit Karyawan</h3>
                <p class="mt-0.5 text-xs text-slate-400">Perbarui data karyawan di bawah ini.</p>
            </div>

            <button
                type="button"
                @click="editOpen = false"
                class="grid h-9 w-9 place-items-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
            >
                ✕
            </button>
        </div>

        {{-- Body --}}
        <div class="max-h-[calc(92vh-150px)] overflow-y-auto px-6 py-6 custom-scrollbar">
            <div class="grid gap-4 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Karyawan</label>
                    <input type="text" name="nama_karyawan" x-model="editKaryawan.nama_karyawan" required class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">NIK</label>
                    <input type="text" name="nik" x-model="editKaryawan.nik" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">NIP</label>
                    <input type="text" name="nip" x-model="editKaryawan.nip" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</label>
                    <input type="email" name="email" x-model="editKaryawan.email" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">No. HP</label>
                    <input type="text" name="no_hp" x-model="editKaryawan.no_hp" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Divisi</label>
                    <select name="divisi_id" x-model="editKaryawan.divisi_id" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        <option value="">Pilih Divisi</option>
                        @foreach ($divisiList as $divisi)
                            <option value="{{ $divisi->id_divisi }}">{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                    <select name="status" x-model="editKaryawan.status" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Bergabung</label>
                    <input type="date" name="tanggal_bergabung" x-model="editKaryawan.tanggal_bergabung" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Alamat</label>
                    <textarea name="alamat" x-model="editKaryawan.alamat" rows="3" class="w-full rounded-xl border-slate-200 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex shrink-0 justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
            <button
                type="button"
                @click="editOpen = false"
                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100"
            >
                Batal
            </button>
            <button
                type="submit"
                class="rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-sky-700"
            >
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>


    {{-- =========================================================
        MODAL DELETE
    ========================================================== --}}
    <div
        x-cloak
        x-show="deleteOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="deleteOpen = false"></div>

        <form
            method="POST"
            :action="deleteKaryawan.action"
            x-show="deleteOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl"
        >
            @csrf
            @method('DELETE')

            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-rose-100 text-rose-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                            <path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-lg font-extrabold text-slate-950">Hapus Data Karyawan?</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Apakah Anda yakin ingin menghapus data
                            <span class="font-bold text-slate-800" x-text="deleteKaryawan.nama"></span>?
                        </p>
                        <p class="mt-2 text-xs leading-5 text-rose-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="deleteOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700">
                        Hapus Karyawan
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection