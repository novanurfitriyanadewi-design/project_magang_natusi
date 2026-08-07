@extends('layouts.portal')

@section('title', 'Laporan Mingguan')

@section('content')
<div class="space-y-6">
    <header class="mt-5">
        <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-950 sm:mt-0">Laporan Mingguan</h1>
        <p class="mt-1 text-sm text-slate-500">Rekap laporan mingguan yang dikumpulkan peserta magang, terpisah dari tugas mingguan dan data penugasan.</p>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
                <span>Data belum dapat diproses</span>
            </div>
            <ul class="mt-2 list-disc space-y-1 pl-6 text-xs font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Template Laporan Peserta --}}
    <section class="overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="h-1.5 bg-gradient-to-r from-purple-600 via-fuchsia-500 to-purple-600"></div>

        <div class="border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-purple-700 shadow-sm ring-1 ring-sky-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Template Laporan Peserta</h2>
                    <p class="mt-0.5 text-sm text-slate-500">File Word dapat diunduh peserta. Ketentuan laporan disimpan terpisah dan tampil langsung pada setiap penugasan berkategori laporan.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 p-6 xl:grid-cols-[minmax(0,1fr)_minmax(360px,0.8fr)]">
            <form action="{{ route('admin-peserta.laporan-mingguan.template.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Judul Template</label>
                        <input type="text" name="judul_template" value="{{ old('judul_template', 'Template Laporan Magang') }}" required class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Instansi</label>
                        <select name="instansi_laporan" required class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                            <option value="universitas">Universitas</option>
                            <option value="sekolah">Sekolah</option>
                            <option value="semua">Semua Instansi</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">File Template Laporan Word</label>
                    <input type="file" name="file_word" accept=".doc,.docx" required class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-purple-100 file:px-3 file:py-2 file:text-xs file:font-bold file:text-purple-700">
                    <p class="mt-1.5 text-xs text-slate-400">Format .doc atau .docx, maksimal 10 MB.</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Ketentuan Pembuatan Laporan</label>
                    <textarea name="ketentuan_laporan" rows="8" required placeholder="Contoh:&#10;1. Gunakan font Times New Roman 12.&#10;2. Minimal 15 halaman.&#10;3. Lampirkan dokumentasi kegiatan." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">{{ old('ketentuan_laporan') }}</textarea>
                    <p class="mt-1.5 text-xs text-slate-400">Ketentuan ini tidak dimasukkan ke file Word, tetapi tampil di halaman tugas peserta.</p>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-fuchsia-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(168,85,247,0.24)] transition hover:-translate-y-0.5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Simpan Template Laporan
                </button>
            </form>

            {{-- Daftar template tersimpan --}}
            <div>
                <h3 class="text-sm font-bold text-slate-900">Template yang Tersimpan</h3>
                <div class="mt-3 space-y-3">
                    @forelse ($templateLaporan as $template)
                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
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
                                <form action="{{ route('admin-peserta.laporan-mingguan.template.destroy', $template) }}" method="POST" onsubmit="return confirm('Hapus template laporan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-rose-500 transition hover:bg-rose-50" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-10 text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="mt-2 text-sm font-medium text-slate-500">Belum ada template laporan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-4 sm:px-6">
            <h2 class="text-base font-extrabold text-slate-950">Riwayat Laporan Masuk</h2>

            <form method="GET" action="{{ route('admin-peserta.laporan-mingguan.index') }}" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama peserta..."
                        class="w-56 rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                </div>
                <select name="minggu_ke" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                    <option value="">Semua Minggu</option>
                    @foreach ($mingguList as $mgg)
                        <option value="{{ $mgg }}" {{ (string) $mingguKe === (string) $mgg ? 'selected' : '' }}>Minggu {{ $mgg }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-bold text-white hover:bg-sky-700">Cari</button>
                @if ($search !== '' || $mingguKe !== '')
                    <a href="{{ route('admin-peserta.laporan-mingguan.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-sky-50/70 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-5 py-4">Jurusan</th>
                        <th class="px-5 py-4 text-center">Minggu Ke</th>
                        <th class="px-5 py-4">Waktu Dikumpulkan</th>
                        <th class="px-5 py-4">File Laporan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $item)
                        @php
                            $nama = $item->peserta?->user?->nama ?? 'Peserta tidak ditemukan';
                            $initials = collect(preg_split('/\s+/', trim($nama)) ?: [])->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                        @endphp
                        <tr class="transition hover:bg-sky-50/40">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200">{{ $initials ?: 'P' }}</span>
                                    <div class="min-w-0">
                                        <p class="max-w-52 truncate text-sm font-extrabold text-slate-900" title="{{ $nama }}">{{ $nama }}</p>
                                        <p class="mt-0.5 max-w-52 truncate text-xs text-slate-500">{{ $item->peserta?->user?->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-sky-100 bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">{{ $item->peserta?->jurusan?->nama_jurusan ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-grid h-9 min-w-9 place-items-center rounded-xl bg-slate-100 px-2 text-sm font-extrabold text-slate-700">{{ $item->minggu_ke }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($item->dikumpulkan_pada)
                                    <p class="text-sm font-semibold text-slate-700">{{ $item->dikumpulkan_pada->translatedFormat('d M Y') }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $item->dikumpulkan_pada->format('H:i') }} WIB</p>
                                @else
                                    <span class="text-sm italic text-slate-400">Waktu tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($item->laporan)
                                    <a href="{{ route('admin-peserta.laporan-mingguan.download', $item) }}" class="inline-flex max-w-52 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 shadow-sm transition hover:border-sky-200 hover:bg-sky-50">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                                        <span class="truncate">Unduh Laporan</span>
                                    </a>
                                @else
                                    <span class="text-sm italic text-slate-400">Tidak ada file</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">
                                {{ $search !== '' || $mingguKe !== '' ? 'Tidak ada laporan yang cocok dengan pencarian.' : 'Belum ada laporan mingguan yang masuk.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($riwayat->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $riwayat->links() }}
            </div>
        @endif
    </section>

    @if ($belumLaporMingguIni->isNotEmpty())
        <section class="overflow-hidden rounded-3xl border border-rose-100 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
            <header class="border-b border-rose-100 bg-rose-50 px-5 py-4 sm:px-6">
                <h2 class="text-base font-extrabold text-rose-800">Belum Lapor Minggu Ini ({{ $belumLaporMingguIni->count() }})</h2>
                <p class="mt-0.5 text-xs text-rose-600">Peserta aktif yang belum mengumpulkan laporan untuk minggu berjalan.</p>
            </header>
            <ul class="divide-y divide-slate-100">
                @foreach ($belumLaporMingguIni->take(20) as $peserta)
                    <li class="flex items-center justify-between gap-3 px-6 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $peserta->user?->nama ?? '-' }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $peserta->permintaan?->nama_sekolah ?? '-' }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">Belum lapor</span>
                    </li>
                @endforeach
            </ul>
            @if ($belumLaporMingguIni->count() > 20)
                <p class="px-6 py-3 text-xs text-slate-400">dan {{ $belumLaporMingguIni->count() - 20 }} peserta lainnya...</p>
            @endif
        </section>
    @endif
</div>
@endsection
