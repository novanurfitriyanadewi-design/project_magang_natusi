@extends('layouts.portal')

@section('title', 'Kelola Tugas Magang')

@section('content')
<div x-data="taskTemplatePage()" class="space-y-6">
    <header>
        <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-950">Kelola Tugas Magang</h1>
        <p class="mt-1 text-sm text-slate-500">
            Unggah template penugasan dan sistem akan membentuk jadwal serta deadline berbeda untuk setiap peserta berdasarkan tanggal mulai magangnya.
        </p>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="flex items-center gap-2 font-semibold">
                <span class="material-symbols-outlined text-[20px]">error</span>
                Data belum dapat diproses
            </div>
            <ul class="mt-2 list-disc space-y-1 pl-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-blue-200 bg-white shadow-xl shadow-slate-200/70 ring-1 ring-blue-100">
        <div class="h-1.5 bg-gradient-to-r from-blue-700 via-cyan-500 to-blue-700"></div>
        <div class="border-b border-blue-100 bg-gradient-to-r from-blue-50/70 via-white to-white px-6 py-5 lg:flex lg:items-center lg:justify-between lg:gap-6">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-100 text-blue-700 shadow-sm ring-1 ring-blue-200">
                    <span class="material-symbols-outlined text-[22px]">upload_file</span>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Unggah Template Tugas</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Gunakan template tugas mingguan resmi. Sistem membaca ketiga sheet dan menentukan jadwal serta deadline peserta secara otomatis.
                    </p>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3 lg:mt-0">
                <a href="{{ route('admin.tugas.template.download') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                    <span class="material-symbols-outlined text-[19px]">table_view</span>
                    Unduh Template Excel
                </a>
            </div>
        </div>

        <div class="grid gap-6 p-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <form action="{{ route('admin.tugas.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Sheet yang Dibaca Sistem</p>
                            <p class="mt-1 text-xs text-slate-500">Satu file Excel memuat seluruh kelompok peserta.</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold text-blue-700">TUGAS MINGGUAN</span>
                    </div>

                    <div class="grid gap-3 md:grid-cols-3">
                        @foreach ([
                            ['title' => 'SMK RPL', 'subtitle' => 'RPL / PPLG', 'icon' => 'code', 'tone' => 'blue'],
                            ['title' => 'SMK TKJ', 'subtitle' => 'TKJ / TJKT', 'icon' => 'lan', 'tone' => 'amber'],
                            ['title' => 'Universitas', 'subtitle' => 'Mahasiswa / Politeknik', 'icon' => 'school', 'tone' => 'purple'],
                        ] as $sheet)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-center gap-3">
                                    <span @class([
                                        'grid h-11 w-11 place-items-center rounded-full',
                                        'bg-blue-100 text-blue-700' => $sheet['tone'] === 'blue',
                                        'bg-amber-100 text-amber-700' => $sheet['tone'] === 'amber',
                                        'bg-purple-100 text-purple-700' => $sheet['tone'] === 'purple',
                                    ])>
                                        <span class="material-symbols-outlined text-[21px]">{{ $sheet['icon'] }}</span>
                                    </span>
                                    <span>
                                        <strong class="block text-sm text-slate-900">{{ $sheet['title'] }}</strong>
                                        <span class="mt-0.5 block text-xs text-slate-500">{{ $sheet['subtitle'] }}</span>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-2xl border border-sky-100 bg-sky-50 px-4 py-3 text-xs leading-5 text-sky-800">
                        Sistem membaca kolom <strong>Minggu Ke</strong>, <strong>Materi &amp; Laporan</strong>, <strong>Tugas</strong>,
                        <strong>Hari Tampil</strong>, <strong>Hari Deadline</strong>, dan <strong>Jam Deadline</strong> pada ketiga sheet tersebut.
                    </div>
                </div>

                <div>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-800">Pilih File Tugas Excel</p>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-500">Maks. 10 MB</span>
                    </div>

                    <label class="flex min-h-[190px] cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center transition"
                        :class="dragOver ? 'border-blue-500 bg-blue-50' : 'border-slate-300 bg-slate-50/50 hover:border-blue-400 hover:bg-blue-50/40'"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleDrop($event)">
                        <input x-ref="excelInput" type="file" name="file_template" accept=".xlsx" required class="sr-only"
                            @change="selectFile($event.target.files[0])">

                        <span class="grid h-14 w-14 place-items-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                            <span class="material-symbols-outlined text-[27px]">upload_file</span>
                        </span>
                        <template x-if="!fileName">
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-blue-700">Klik untuk mengunggah atau drag &amp; drop</p>
                                <p class="mt-1 text-xs text-slate-400">Pastikan file menggunakan template resmi berformat .xlsx</p>
                            </div>
                        </template>
                        <template x-if="fileName">
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-slate-800" x-text="fileName"></p>
                                <p class="mt-1 text-xs text-emerald-600">File siap diproses</p>
                            </div>
                        </template>
                    </label>
                </div>

                <button type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-900 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800">
                    <span class="material-symbols-outlined text-[20px]">publish</span>
                    Unggah dan Publikasikan Jadwal
                </button>
            </form>

            <aside class="rounded-2xl border border-blue-100 bg-gradient-to-b from-blue-50 to-white p-5">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-100 text-blue-700">
                        <span class="material-symbols-outlined text-[21px]">route</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Alur Penugasan</h3>
                        <p class="text-xs text-slate-500">Jadwal dihitung per akun peserta.</p>
                    </div>
                </div>

                <ol class="mt-5 space-y-4">
                    @foreach ([
                        ['title' => 'Unduh template', 'text' => 'Gunakan file resmi dengan sheet SMK RPL, SMK TKJ, dan Universitas.'],
                        ['title' => 'Isi jadwal mingguan', 'text' => 'Isi minggu, materi/laporan, tugas, hari tampil, hari deadline, dan jam deadline.'],
                        ['title' => 'Unggah satu kali', 'text' => 'Sistem membaca semua sheet sekaligus dan mencocokkannya dengan jurusan peserta.'],
                        ['title' => 'Deadline otomatis', 'text' => 'Tanggal dan jam deadline dihitung berdasarkan tanggal mulai setiap peserta.'],
                    ] as $index => $step)
                        <li class="flex gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-blue-900 text-xs font-bold text-white">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $step['title'] }}</p>
                                <p class="mt-0.5 text-xs leading-5 text-slate-500">{{ $step['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                    <strong>Contoh:</strong> peserta mulai Rabu akan melewati tugas yang deadline-nya sudah berakhir pada Selasa, sedangkan tugas yang masih aktif langsung tampil pada hari mulai.
                </div>
            </aside>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-purple-200 bg-white shadow-xl shadow-slate-200/70 ring-1 ring-purple-100">
        <div class="h-1.5 bg-gradient-to-r from-purple-700 via-fuchsia-500 to-purple-700"></div>
        <div class="border-b border-purple-100 bg-gradient-to-r from-purple-50/70 via-white to-white px-6 py-5">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-purple-100 text-purple-700">
                    <span class="material-symbols-outlined text-[22px]">docs</span>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Template Laporan Peserta</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        File Word dapat diunduh peserta. Ketentuan laporan disimpan terpisah dan tampil langsung pada setiap penugasan berkategori laporan.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 p-6 xl:grid-cols-[minmax(0,1fr)_minmax(360px,0.8fr)]">
            <form action="{{ route('admin.tugas.template-laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Judul Template</label>
                        <input type="text" name="judul_template" value="{{ old('judul_template', 'Template Laporan Magang') }}" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Instansi</label>
                        <select name="instansi_laporan" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="universitas">Universitas</option>
                            <option value="sekolah">Sekolah</option>
                            <option value="semua">Semua Instansi</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">File Template Laporan Word</label>
                    <input type="file" name="file_word" accept=".doc,.docx" required
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-purple-100 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-purple-700">
                    <p class="mt-1.5 text-xs text-slate-400">Format .doc atau .docx, maksimal 10 MB.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Ketentuan Pembuatan Laporan</label>
                    <textarea name="ketentuan_laporan" rows="8" required
                        placeholder="Contoh:&#10;1. Gunakan font Times New Roman 12.&#10;2. Minimal 15 halaman.&#10;3. Lampirkan dokumentasi kegiatan."
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm leading-6 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('ketentuan_laporan') }}</textarea>
                    <p class="mt-1.5 text-xs text-slate-400">Ketentuan ini tidak dimasukkan ke file Word, tetapi tampil di halaman tugas peserta.</p>
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-purple-600">
                    <span class="material-symbols-outlined text-[19px]">save</span>
                    Simpan Template Laporan
                </button>
            </form>

            <div>
                <h3 class="text-sm font-bold text-slate-900">Template yang Tersimpan</h3>
                <div class="mt-3 space-y-3">
                    @forelse ($templateLaporan as $template)
                        <article class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="truncate text-sm font-semibold text-slate-900">{{ $template->judul }}</h4>
                                        @if ($template->is_active)
                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">AKTIF</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">ARSIP</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">{{ ucfirst($template->instansi) }} · {{ basename($template->file_word) }}</p>
                                    <p class="mt-2 line-clamp-3 whitespace-pre-line text-xs leading-5 text-slate-600">{{ $template->ketentuan }}</p>
                                </div>
                                <form action="{{ route('admin.tugas.template-laporan.destroy', $template) }}" method="POST"
                                    onsubmit="return confirm('Hapus template laporan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-10 text-center">
                            <span class="material-symbols-outlined text-3xl text-slate-300">draft</span>
                            <p class="mt-2 text-sm font-medium text-slate-500">Belum ada template laporan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section id="daftar-penugasan" class="overflow-hidden rounded-3xl border border-cyan-200 bg-white shadow-xl shadow-slate-200/70 ring-1 ring-cyan-100">
        <div class="h-1.5 bg-gradient-to-r from-cyan-600 via-sky-500 to-blue-700"></div>

        <div class="border-b border-cyan-100 bg-gradient-to-r from-cyan-50/70 via-white to-white px-6 py-5">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-cyan-100 text-cyan-700 shadow-sm ring-1 ring-cyan-200">
                    <span class="material-symbols-outlined text-[22px]">table_rows</span>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Penugasan dari Template</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Susunan tabel mengikuti template Excel: Minggu Ke, Materi &amp; Laporan, Tugas, Hari Tampil, Hari Deadline, dan Jam Deadline.
                    </p>
                </div>
            </div>
        </div>

        @php
            $filterKelompok = [
                ['value' => '', 'label' => 'Semua', 'icon' => 'groups'],
                ['value' => 'smk_tkj', 'label' => 'SMK TKJ', 'icon' => 'lan'],
                ['value' => 'smk_rpl', 'label' => 'SMK RPL', 'icon' => 'code'],
                ['value' => 'universitas', 'label' => 'Universitas', 'icon' => 'school'],
            ];

            $kelompokMeta = [
                'smk_tkj' => [
                    'judul' => 'SMK TKJ',
                    'deskripsi' => 'Tugas peserta SMK Teknik Komputer dan Jaringan',
                    'ikon' => 'lan',
                ],
                'smk_rpl' => [
                    'judul' => 'SMK RPL',
                    'deskripsi' => 'Tugas peserta SMK Rekayasa Perangkat Lunak',
                    'ikon' => 'code',
                ],
                'universitas' => [
                    'judul' => 'Universitas',
                    'deskripsi' => 'Tugas peserta Universitas/Politeknik',
                    'ikon' => 'school',
                ],
            ];

            $kelompokAktif = request('target_peserta', '');
            if (!array_key_exists($kelompokAktif, $kelompokMeta)) {
                $kelompokAktif = '';
            }

            $kelompokDitampilkan = $kelompokAktif !== ''
                ? [$kelompokAktif]
                : array_keys($kelompokMeta);
        @endphp

        <div class="border-b border-slate-100 bg-slate-50/70 px-6 py-5">
            <div class="flex flex-wrap items-center gap-2.5" aria-label="Filter kelompok peserta">
                @foreach ($filterKelompok as $filter)
                    @php
                        $aktif = $kelompokAktif === $filter['value'];
                        $filterUrl = $filter['value'] === ''
                            ? route('admin.tugas.index')
                            : route('admin.tugas.index', ['target_peserta' => $filter['value']]);
                    @endphp

                    <a href="{{ $filterUrl }}#daftar-penugasan"
                        @class([
                            'inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-bold transition-all duration-200',
                            'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-md shadow-blue-200 ring-1 ring-blue-500' => $aktif,
                            'border border-slate-200 bg-white text-slate-600 shadow-sm hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 hover:shadow-md' => !$aktif,
                        ])
                        @if ($aktif) aria-current="page" @endif>
                        <span class="material-symbols-outlined text-[18px]">{{ $filter['icon'] }}</span>
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="space-y-6 p-6">
            @if ($tugasList->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 px-6 py-14 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300">table_view</span>
                    <p class="mt-3 text-sm font-semibold text-slate-500">Belum ada template tugas yang diunggah.</p>
                    <p class="mt-1 text-xs text-slate-400">Unggah file template Excel agar daftar penugasan tampil mengikuti isi setiap sheet.</p>
                </div>
            @else
                @foreach ($kelompokDitampilkan as $target)
                    @php
                        $meta = $kelompokMeta[$target];
                        $groupTasks = $tugasList
                            ->where('target_peserta', $target)
                            ->sortBy([
                                ['minggu_ke', 'asc'],
                                ['rilis_hari_ke', 'asc'],
                                ['id_tugas', 'asc'],
                            ])
                            ->values();
                    @endphp

                    @continue($groupTasks->isEmpty())

                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/70 ring-1 ring-slate-100">
                        <div class="flex items-center justify-between gap-4 bg-[#102f50] px-5 py-3 text-white">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10">
                                    <span class="material-symbols-outlined text-[20px]">{{ $meta['ikon'] }}</span>
                                </span>
                                <h3 class="text-base font-bold tracking-wide">{{ $meta['judul'] }}</h3>
                            </div>

                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ $groupTasks->count() }} penugasan
                            </span>
                        </div>

                        <div class="border-b border-sky-100 bg-sky-50 px-5 py-3 text-sm font-semibold text-slate-700">
                            {{ $meta['deskripsi'] }}
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-[980px] w-full border-collapse text-left text-sm">
                                <thead>
                                    <tr class="bg-sky-600 text-xs uppercase tracking-wide text-white">
                                        <th class="w-[110px] border-r border-sky-500 px-4 py-3 text-center">Minggu Ke</th>
                                        <th class="w-[190px] border-r border-sky-500 px-4 py-3">Materi &amp; Laporan</th>
                                        <th class="min-w-[320px] border-r border-sky-500 px-4 py-3">Tugas</th>
                                        <th class="w-[140px] border-r border-sky-500 px-4 py-3 text-center">Hari Tampil</th>
                                        <th class="w-[150px] border-r border-sky-500 px-4 py-3 text-center">Hari Deadline</th>
                                        <th class="w-[140px] px-4 py-3 text-center">Jam Deadline</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200">
                                    @foreach ($groupTasks->groupBy('minggu_ke') as $minggu => $tugasMinggu)
                                        @foreach ($tugasMinggu as $baris => $tugas)
                                            @php
                                                $isLaporan = $tugas->kategori_tugas === 'laporan';
                                            @endphp

                                            <tr @class([
                                                'transition hover:bg-blue-50/70',
                                                'bg-purple-50/40' => $isLaporan,
                                                'bg-white' => !$isLaporan,
                                            ])>
                                                @if ($baris === 0)
                                                    <td rowspan="{{ $tugasMinggu->count() }}"
                                                        class="border-r border-slate-200 bg-amber-50 px-4 py-4 text-center align-middle">
                                                        <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl bg-amber-100 px-3 font-extrabold text-amber-800 ring-1 ring-amber-200">
                                                            {{ $minggu ?: '-' }}
                                                        </span>
                                                    </td>
                                                @endif

                                                <td class="border-r border-slate-200 px-4 py-4 align-top">
                                                    <span @class([
                                                        'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                                        'bg-purple-100 text-purple-700' => $isLaporan,
                                                        'bg-amber-100 text-amber-700' => !$isLaporan,
                                                    ])>
                                                        {{ $tugas->materi ?: ($isLaporan ? 'Laporan' : 'Materi') }}
                                                    </span>
                                                </td>

                                                <td class="border-r border-slate-200 px-4 py-4 align-top">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div class="min-w-0">
                                                            <p class="font-semibold leading-6 text-slate-900">{{ $tugas->judul }}</p>
                                                            <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-400">
                                                                <span>{{ $tugas->kode_tugas ?: 'Tanpa kode' }}</span>
                                                                <span class="inline-flex items-center gap-1">
                                                                    <span class="material-symbols-outlined text-[14px]">group</span>
                                                                    {{ $tugas->penugasan_peserta_count }} peserta
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <form action="{{ route('admin.tugas.destroy', $tugas) }}" method="POST" class="shrink-0"
                                                            onsubmit="return confirm('Hapus tugas dan seluruh jadwal pesertanya?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="rounded-lg p-2 text-red-500 transition hover:bg-red-50 hover:text-red-600"
                                                                title="Hapus penugasan">
                                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>

                                                <td class="border-r border-slate-200 px-4 py-4 text-center font-medium text-slate-700">
                                                    {{ ucfirst($tugas->hari_tampil ?: '-') }}
                                                </td>

                                                <td class="border-r border-slate-200 px-4 py-4 text-center font-medium text-slate-700">
                                                    {{ ucfirst($tugas->hari_deadline ?: '-') }}
                                                </td>

                                                <td class="px-4 py-4 text-center">
                                                    <span class="inline-flex rounded-xl bg-slate-100 px-3 py-1.5 font-bold tabular-nums text-slate-700">
                                                        {{ $tugas->jam_deadline ? substr((string) $tugas->jam_deadline, 0, 5) : '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function taskTemplatePage() {
        return {
            dragOver: false,
            fileName: null,

            selectFile(file) {
                if (!file) {
                    this.fileName = null;
                    return;
                }

                if (!file.name.toLowerCase().endsWith('.xlsx')) {
                    alert('File harus berformat .xlsx');
                    this.$refs.excelInput.value = '';
                    this.fileName = null;
                    return;
                }

                this.fileName = file.name;
            },

            handleDrop(event) {
                this.dragOver = false;
                const file = event.dataTransfer.files[0];
                if (!file) return;

                const transfer = new DataTransfer();
                transfer.items.add(file);
                this.$refs.excelInput.files = transfer.files;
                this.selectFile(file);
            },
        };
    }
</script>
@endpush
