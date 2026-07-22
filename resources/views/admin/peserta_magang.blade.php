@extends('layouts.portal')

@section('title', 'Data Peserta Magang')

@section('content')
<div
    class="space-y-5"
    x-data="{
        importOpen: @json($errors->has('file_excel')),
        importLoading: false,
        importResultOpen: @json(session('success') || session('error') || $errors->any()),
        importResultType: @json(session('success') ? 'success' : ((session('error') || $errors->any()) ? 'error' : null)),
        importResultMessage: @json(session('success') ?? session('error') ?? ($errors->first() ?: '')),
        detailOpen: false,
        detail: {},
        startImport() {
            this.importLoading = true;
            this.importOpen = false;
        },
        closeImportResult() {
            this.importResultOpen = false;
        },
        openDetail(payload) {
            this.detail = payload;
            this.detailOpen = true;
        },
        closeDetail() {
            this.detailOpen = false;
        }
    }"
    x-effect="document.body.classList.toggle('overflow-hidden', importOpen || importLoading || detailOpen)"
    @keydown.escape.window="if (!importLoading) { importOpen = false; closeImportResult(); closeDetail(); }"
>
    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                Data Peserta Magang
            </h1>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                Peserta baru otomatis masuk setelah pengajuan disetujui. Import Excel hanya digunakan untuk memasukkan data peserta lama.
            </p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row">
            <a
                href="{{ route('admin.peserta.template') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-bold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-sky-50"
            >
                <span class="material-symbols-outlined text-[19px]">download</span>
                Unduh Template
            </a>
            <button
                type="button"
                @click="importOpen = true"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-cyan-500 px-4 py-2.5 text-sm font-bold text-white shadow-[0_12px_28px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5 hover:from-sky-700 hover:to-cyan-600"
            >
                <span class="material-symbols-outlined text-[19px]">upload_file</span>
                Import Excel
            </button>
        </div>
    </section>

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            <span class="material-symbols-outlined mt-0.5 text-[20px] text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            <span class="material-symbols-outlined mt-0.5 text-[20px] text-rose-600">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 text-[20px] text-rose-600">error</span>
                <div>
                    <p>Data belum dapat diproses.</p>
                    <ul class="mt-1 list-disc space-y-1 pl-5 text-xs font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 to-cyan-500 p-5 text-white shadow-[0_16px_36px_rgba(2,132,199,0.18)]">
            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full border-[18px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-sky-100">Total Peserta</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['total'] ?? 0) }}</p>
                    <p class="mt-1 text-sm text-sky-100">Seluruh data peserta magang</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-[27px]">groups</span>
                </span>
            </div>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-500 p-5 text-white shadow-[0_16px_36px_rgba(13,148,136,0.18)]">
            <div class="absolute -right-6 -top-10 h-32 w-32 rounded-[36px] border border-white/15"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-emerald-100">Peserta Aktif</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['aktif'] ?? 0) }}</p>
                    <p class="mt-1 text-sm text-emerald-100">Sedang menjalani masa magang</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-[27px]">verified_user</span>
                </span>
            </div>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-500 p-5 text-white shadow-[0_16px_36px_rgba(79,70,229,0.18)] sm:col-span-2 xl:col-span-1">
            <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-indigo-100">Peserta Nonaktif</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['nonaktif'] ?? 0) }}</p>
                    <p class="mt-1 text-sm text-indigo-100">Masa magang telah selesai</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-[27px]">person_off</span>
                </span>
            </div>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-[0_14px_40px_rgba(15,23,42,0.06)]">
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50/80 via-white to-cyan-50/70 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Daftar Peserta</h2>
                <p class="mt-1 text-xs text-slate-500">Cari dan kelola status peserta magang.</p>
            </div>

            <form method="GET" action="{{ route('admin.peserta.index') }}" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto lg:items-center">
                <div class="relative w-full sm:w-[190px] lg:w-[200px] shrink-0">
                    <select
                        name="status"
                        class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-white pl-4 pr-11 text-sm font-bold text-slate-600 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                    >
                        <option value="semua" @selected(($status ?? 'semua') === 'semua')>Semua Status</option>
                        <option value="aktif" @selected(($status ?? '') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(($status ?? '') === 'nonaktif')>Nonaktif</option>
                    </select>
                    <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">expand_more</span>
                </div>
                <div class="relative min-w-0 sm:w-80">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama, email, instansi..."
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 outline-none transition placeholder:font-medium placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                    >
                </div>
                <button class="inline-flex h-11 items-center justify-center rounded-xl bg-sky-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-100">
                    Terapkan
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] font-bold uppercase tracking-[0.13em] text-slate-500">
                        <th class="px-5 py-4">Nama Peserta</th>
                        <th class="px-5 py-4">Instansi</th>
                        <th class="px-5 py-4">Pendidikan</th>
                        <th class="px-5 py-4">Periode</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($peserta as $item)
                        @php
                            $isActive = $item->status === 'aktif';
                            $detailPayload = [
                                'nama' => $item->user->nama ?? '-',
                                'email' => $item->user->email ?? '-',
                                'username' => $item->user->username ?? '-',
                                'alamat' => $item->alamat ?: '-',
                                'instansi' => $item->permintaan->nama_sekolah ?? $item->user->university ?? '-',
                                'no_induk' => $item->permintaan->no_induk ?? $item->user->student_id ?? '-',
                                'jurusan' => $item->permintaan->jurusan ?? $item->user->major ?? '-',
                                'no_hp' => $item->permintaan->no_hp ?? $item->user->phone ?? '-',
                                'tingkat_pendidikan' => $item->tingkat_pendidikan === 'Mahasiswa' ? 'Universitas' : ($item->tingkat_pendidikan ?: '-'),
                                'kelas' => $item->kelas ?: '-',
                                'tanggal_mulai' => $item->tgl_mulai?->translatedFormat('d M Y') ?? '-',
                                'tanggal_selesai' => $item->tgl_selesai?->translatedFormat('d M Y') ?? '-',
                                'durasi_magang' => $item->durasi_magang ?: '-',
                                'nama_pembimbing' => $item->nama_guru ?: '-',
                                'no_hp_pembimbing' => $item->no_hpguru ?: '-',
                                'status' => $isActive ? 'Aktif' : 'Nonaktif',
                            ];
                        @endphp
                        <tr class="transition hover:bg-sky-50/40">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-sky-100 to-cyan-100 text-sm font-extrabold text-sky-700 ring-1 ring-sky-200">
                                        {{ strtoupper(mb_substr($item->user->nama ?? 'P', 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-extrabold text-slate-900">{{ $item->user->nama ?? '-' }}</p>
                                        <p class="mt-0.5 truncate text-xs font-medium text-slate-500">{{ $item->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-[220px] truncate text-sm font-bold text-slate-700">{{ $item->permintaan->nama_sekolah ?? $item->user->university ?? '-' }}</p>
                                <p class="mt-0.5 max-w-[220px] truncate text-xs text-slate-500">{{ $item->permintaan->jurusan ?? $item->user->major ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-slate-700">{{ $item->tingkat_pendidikan === 'Mahasiswa' ? 'Universitas' : ($item->tingkat_pendidikan ?: '-') }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $item->kelas ?: 'Kelas/semester belum diisi' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="text-sm font-semibold text-slate-700">{{ $item->tgl_mulai?->translatedFormat('d M Y') ?? '-' }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">s.d. {{ $item->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($isActive)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        @click='openDetail(@json($detailPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT))'
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                        Detail
                                    </button>

                                    <form
                                        action="{{ route('admin.peserta.status', $item->id_peserta) }}"
                                        method="POST"
                                        onsubmit="return confirm('{{ $isActive ? 'Nonaktifkan peserta ini?' : 'Aktifkan kembali peserta ini?' }}')"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $isActive ? 'nonaktif' : 'aktif' }}">
                                        <button
                                            class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs font-bold transition {{ $isActive ? 'border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                        >
                                            <span class="material-symbols-outlined text-[17px]">{{ $isActive ? 'person_off' : 'person_check' }}</span>
                                            {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <span class="material-symbols-outlined text-[30px]">group_off</span>
                                </span>
                                <p class="mt-4 text-sm font-bold text-slate-700">Data peserta belum tersedia</p>
                                <p class="mt-1 text-xs text-slate-500">Setujui pengajuan baru atau import data lama melalui Excel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peserta->hasPages())
            @php
                $peserta->appends(request()->except('page'));
                $startPage = max(1, $peserta->currentPage() - 2);
                $endPage = min($peserta->lastPage(), $peserta->currentPage() + 2);
            @endphp

            <div class="flex flex-col gap-3 border-t border-sky-100 bg-sky-50/50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs font-semibold text-slate-500">
                    Menampilkan {{ $peserta->firstItem() ?? 0 }}–{{ $peserta->lastItem() ?? 0 }}
                    dari {{ number_format($peserta->total()) }} peserta
                </p>

                <nav class="inline-flex w-fit overflow-hidden rounded-xl border border-sky-200 bg-white shadow-sm" aria-label="Navigasi halaman peserta">
                    @if($peserta->onFirstPage())
                        <span class="inline-grid h-11 w-11 cursor-not-allowed place-items-center border-r border-sky-200 bg-sky-50 text-sky-300" aria-disabled="true">
                            <span class="material-symbols-outlined text-[21px]">chevron_left</span>
                        </span>
                    @else
                        <a href="{{ $peserta->previousPageUrl() }}" class="inline-grid h-11 w-11 place-items-center border-r border-sky-200 bg-sky-50 text-sky-700 transition hover:bg-sky-100" aria-label="Halaman sebelumnya">
                            <span class="material-symbols-outlined text-[21px]">chevron_left</span>
                        </a>
                    @endif

                    @if($startPage > 1)
                        <a href="{{ $peserta->url(1) }}" class="inline-grid h-11 min-w-11 place-items-center border-r border-sky-200 px-3 text-sm font-bold text-sky-700 transition hover:bg-sky-50">1</a>
                        @if($startPage > 2)
                            <span class="inline-grid h-11 min-w-10 place-items-center border-r border-sky-200 bg-white px-2 text-sm font-bold text-sky-400">…</span>
                        @endif
                    @endif

                    @foreach(range($startPage, $endPage) as $page)
                        @if($page === $peserta->currentPage())
                            <span class="inline-grid h-11 min-w-11 place-items-center border-r border-sky-200 bg-gradient-to-br from-sky-500 to-blue-600 px-3 text-sm font-extrabold text-white shadow-inner" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $peserta->url($page) }}" class="inline-grid h-11 min-w-11 place-items-center border-r border-sky-200 bg-white px-3 text-sm font-bold text-sky-700 transition hover:bg-sky-50">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if($endPage < $peserta->lastPage())
                        @if($endPage < $peserta->lastPage() - 1)
                            <span class="inline-grid h-11 min-w-10 place-items-center border-r border-sky-200 bg-white px-2 text-sm font-bold text-sky-400">…</span>
                        @endif
                        <a href="{{ $peserta->url($peserta->lastPage()) }}" class="inline-grid h-11 min-w-11 place-items-center border-r border-sky-200 bg-white px-3 text-sm font-bold text-sky-700 transition hover:bg-sky-50">
                            {{ $peserta->lastPage() }}
                        </a>
                    @endif

                    @if($peserta->hasMorePages())
                        <a href="{{ $peserta->nextPageUrl() }}" class="inline-grid h-11 w-11 place-items-center bg-sky-50 text-sky-700 transition hover:bg-sky-100" aria-label="Halaman berikutnya">
                            <span class="material-symbols-outlined text-[21px]">chevron_right</span>
                        </a>
                    @else
                        <span class="inline-grid h-11 w-11 cursor-not-allowed place-items-center bg-sky-50 text-sky-300" aria-disabled="true">
                            <span class="material-symbols-outlined text-[21px]">chevron_right</span>
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </section>

    {{-- Modal Import Excel --}}
    <template x-teleport="body">
        <div
            x-show="importOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[9999] flex h-screen w-screen items-center justify-center overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-md"
            @click.self="if (!importLoading) importOpen = false"
        >
            <section
                x-show="importOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="my-auto w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_rgba(15,23,42,0.30)]"
            >
            <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                <div>
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">
                        Data Lama
                    </span>
                    <h3 class="mt-3 text-xl font-extrabold text-slate-950">Import Peserta dari Excel</h3>
                    <p class="mt-1 text-sm text-slate-500">Gunakan template agar nama kolom terbaca dengan benar.</p>
                </div>
                <button
                    type="button"
                    @click="if (!importLoading) importOpen = false"
                    :disabled="importLoading"
                    class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </header>

            <form
                action="{{ route('admin.peserta.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-5 px-6 py-6"
                @submit="startImport()"
            >
                @csrf

                <div class="rounded-2xl border border-sky-100 bg-sky-50/60 p-4 text-xs leading-6 text-slate-600">
                    <p class="font-extrabold text-slate-800">Ketentuan import:</p>
                    <p class="mt-1">Template hanya memuat data yang dibutuhkan sistem tanpa kolom timestamp.</p>
                    <p>Pilih <strong>SMK</strong> atau <strong>Universitas</strong> pada Tingkat Pendidikan, lalu isi Nama Sekolah/Universitas dan Kelas/Semester.</p>
                    <p>Kolom <strong>Status</strong> hanya boleh diisi <strong>Aktif</strong> atau <strong>Nonaktif</strong>.</p>
                </div>

                <label class="block cursor-pointer rounded-2xl border-2 border-dashed border-sky-200 bg-slate-50 px-5 py-8 text-center transition hover:border-sky-400 hover:bg-sky-50">
                    <span class="material-symbols-outlined text-[38px] text-sky-600">table_view</span>
                    <span class="mt-2 block text-sm font-extrabold text-slate-800">Pilih file Excel</span>
                    <span class="mt-1 block text-xs text-slate-500">Format XLSX, XLS, atau CSV. Maksimal 10 MB.</span>
                    <input
                        type="file"
                        name="file_excel"
                        accept=".xlsx,.xls,.csv"
                        :disabled="importLoading"
                        class="mt-4 block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-100 file:px-3 file:py-2 file:font-bold file:text-sky-700 disabled:cursor-not-allowed disabled:opacity-60"
                        required
                    >
                </label>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        @click="if (!importLoading) importOpen = false"
                        :disabled="importLoading"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="importLoading"
                        class="inline-flex min-w-[145px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-cyan-500 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.22)] transition hover:-translate-y-0.5 disabled:cursor-wait disabled:opacity-80"
                    >
                        <template x-if="!importLoading">
                            <span class="inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-[19px]">upload</span>
                                Import Data
                            </span>
                        </template>
                        <template x-if="importLoading">
                            <span class="inline-flex items-center gap-2">
                                <span class="h-5 w-5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                Memproses...
                            </span>
                        </template>
                    </button>
                </div>
            </form>
            </section>
        </div>
    </template>

    {{-- Overlay Loading Import --}}
    <template x-teleport="body">
        <div
            x-show="importLoading"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[10000] flex h-screen w-screen items-center justify-center bg-slate-950/70 p-4 backdrop-blur-md"
        >
            <section
                x-show="importLoading"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="w-full max-w-sm rounded-3xl bg-white px-8 py-9 text-center shadow-[0_30px_90px_rgba(15,23,42,0.35)]"
                role="status"
                aria-live="polite"
            >
                <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-sky-50 ring-8 ring-sky-50/60">
                    <span class="h-12 w-12 animate-spin rounded-full border-[5px] border-sky-100 border-t-sky-600"></span>
                </div>
                <h3 class="mt-6 text-xl font-extrabold text-slate-950">Mengimpor data peserta</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Sistem sedang membaca file dan menyimpan data. Jangan menutup atau memuat ulang halaman.
                </p>
                <div class="mt-6 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full w-2/3 animate-pulse rounded-full bg-gradient-to-r from-sky-500 to-cyan-400"></div>
                </div>
            </section>
        </div>
    </template>

    {{-- Notifikasi Hasil Import --}}
    <template x-teleport="body">
        <div
            x-show="importResultOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-180"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            x-init="if (importResultOpen) setTimeout(() => importResultOpen = false, 7000)"
            class="fixed right-4 top-4 z-[10001] w-[calc(100%-2rem)] max-w-md"
            role="alert"
            aria-live="assertive"
        >
            <div
                :class="importResultType === 'success'
                    ? 'border-emerald-200 bg-white shadow-[0_20px_55px_rgba(5,150,105,0.20)]'
                    : 'border-rose-200 bg-white shadow-[0_20px_55px_rgba(225,29,72,0.20)]'"
                class="flex items-start gap-4 rounded-2xl border p-4"
            >
                <span
                    :class="importResultType === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'"
                    class="grid h-11 w-11 shrink-0 place-items-center rounded-xl"
                >
                    <span
                        class="material-symbols-outlined text-[24px]"
                        x-text="importResultType === 'success' ? 'check_circle' : 'error'"
                    ></span>
                </span>
                <div class="min-w-0 flex-1">
                    <p
                        :class="importResultType === 'success' ? 'text-emerald-800' : 'text-rose-800'"
                        class="text-sm font-extrabold"
                        x-text="importResultType === 'success' ? 'Import berhasil' : 'Import gagal'"
                    ></p>
                    <p class="mt-1 break-words text-sm leading-6 text-slate-600" x-text="importResultMessage"></p>
                </div>
                <button
                    type="button"
                    @click="closeImportResult()"
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Tutup notifikasi"
                >
                    <span class="material-symbols-outlined text-[19px]">close</span>
                </button>
            </div>
        </div>
    </template>

    {{-- Modal Detail Peserta --}}
    <template x-teleport="body">
        <div
            x-show="detailOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[9999] flex h-screen w-screen items-center justify-center overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-md"
            @click.self="closeDetail()"
        >
            <section
                x-show="detailOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="my-auto max-h-[92vh] w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_rgba(15,23,42,0.30)]"
            >
            <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                <div>
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">Detail Peserta</span>
                    <h3 class="mt-3 text-xl font-extrabold text-slate-950" x-text="detail.nama || '-'">-</h3>
                    <p class="mt-1 text-sm text-slate-500">Informasi lengkap peserta magang.</p>
                </div>
                <button type="button" @click="closeDetail()" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-rose-50 hover:text-rose-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </header>

            <div class="max-h-[calc(92vh-150px)] overflow-y-auto px-6 py-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        ['Nama Lengkap', 'nama'],
                        ['Email', 'email'],
                        ['Username', 'username'],
                        ['Instansi', 'instansi'],
                        ['Nomor Induk', 'no_induk'],
                        ['Jurusan', 'jurusan'],
                        ['Nomor HP', 'no_hp'],
                        ['Tingkat Pendidikan', 'tingkat_pendidikan'],
                        ['Kelas/Semester', 'kelas'],
                        ['Tanggal Mulai', 'tanggal_mulai'],
                        ['Tanggal Selesai', 'tanggal_selesai'],
                        ['Durasi Magang', 'durasi_magang'],
                        ['Nama Pembimbing', 'nama_pembimbing'],
                        ['No. HP Pembimbing', 'no_hp_pembimbing'],
                        ['Status', 'status'],
                    ] as [$label, $key])
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">{{ $label }}</p>
                            <p class="mt-2 break-words text-sm font-bold text-slate-800" x-text="detail['{{ $key }}'] || '-'">-</p>
                        </div>
                    @endforeach
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:col-span-2 lg:col-span-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Alamat</p>
                        <p class="mt-2 whitespace-pre-line break-words text-sm font-semibold leading-6 text-slate-700" x-text="detail.alamat || '-'">-</p>
                    </div>
                </div>
            </div>
            </section>
        </div>
    </template>
</div>
@endsection
