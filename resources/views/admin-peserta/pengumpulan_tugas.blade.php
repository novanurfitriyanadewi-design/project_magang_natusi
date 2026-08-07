@extends('layouts.portal')

@section('title', 'Data Pengumpulan Tugas')

@section('content')
@php
    $groupLabels = [
        'smk_tkj' => 'SMK TKJ',
        'smk_rpl' => 'SMK RPL',
        'smk_sija' => 'SMK SIJA',
        'kuliah_ti' => 'Teknik Informatika',
        'kuliah_si' => 'Sistem Informasi',
        'kuliah_ptik' => 'Pend Teknik Informatika',
    ];

    $rawStats = is_array($stats ?? null) ? $stats : [];
    $stats = array_merge([
        'mengumpulkan' => 0,
        'terlambat' => 0,
        'tidak_mengumpulkan' => 0,
    ], $rawStats);

    $jenjang = $jenjang ?? request('jenjang', 'semua');
    $daftarTugas = $daftarTugas ?? collect();

    if (!isset($submitted)) {
        $submitted = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => request()->url(), 'query' => request()->query()]);
    }
    if (!isset($pending)) {
        $pending = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => request()->url(), 'query' => request()->query()]);
    }

    $participantName = fn($item) => $item->peserta?->user?->nama ?? $item->peserta?->nama_peserta ?? $item->peserta?->permintaan?->nama_pemohon ?? 'Peserta tidak ditemukan';
    $participantEmail = fn($item) => $item->peserta?->user?->email ?? $item->peserta?->permintaan?->email ?? '-';
    $taskTitle = fn($item) => $item->tugas?->judul ?? $item->tugas?->judul_tugas ?? '-';

    $groupLabel = function($item) use ($groupLabels) {
        $target = $item->tugas?->target_peserta;
        if ($target && isset($groupLabels[$target])) return $groupLabels[$target];
        $edu = mb_strtolower((string)($item->peserta?->tingkat_pendidikan ?? ''));
        $major = mb_strtolower((string)($item->peserta?->permintaan?->jurusan ?? ''));
        if (str_contains($major, 'tkj')) return 'SMK TKJ';
        if (str_contains($major, 'rpl')) return 'SMK RPL';
        if (str_contains($major, 'sija')) return 'SMK SIJA';
        if (str_contains($major, 'teknik informatika') && !str_contains($major, 'pendidikan')) return 'Teknik Informatika';
        if (str_contains($major, 'sistem informasi')) return 'Sistem Informasi';
        if (str_contains($major, 'pendidikan teknik informatika') || str_contains($major, 'pend teknik informatika')) return 'Pend Teknik Informatika';
        if (str_contains($edu, 'universitas') || str_contains($edu, 'kuliah')) return 'Universitas';
        return $edu !== '' ? mb_strtoupper($edu) : '-';
    };

    $activeJenjangLabel = ['semua' => 'Semua Jenjang', 'smk-tkj' => 'SMK TKJ', 'smk-rpl' => 'SMK RPL', 'smk-sija' => 'SMK SIJA', 'kuliah-ti' => 'Teknik Informatika', 'kuliah-si' => 'Sistem Informasi', 'kuliah-ptik' => 'Pend Teknik Informatika'][$jenjang] ?? 'Semua Jenjang';
@endphp

<div class="space-y-6">

    {{-- Judul --}}
    <section>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Data Pengumpulan Tugas</h1>
        <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">Pantau peserta yang mengumpulkan, terlambat, dan belum mengerjakan tugas magang.</p>
    </section>

    {{-- Alert --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- SECTION 1: Sudah Mengumpulkan --}}
    <section class="overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <header class="flex flex-col gap-3 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Peserta yang Mengumpulkan Tugas</h2>
                <p class="mt-0.5 text-sm text-slate-500">Gunakan tombol Detail untuk melihat seluruh data dan bukti file peserta.</p>
            </div>
            <span class="w-fit rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200">{{ number_format($submitted->total()) }} data</span>
        </header>

        {{-- Filter jenjang --}}
        <div class="border-b border-sky-100 bg-white px-6 py-4">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Filter jenjang peserta">
                @foreach(['semua' => 'Semua', 'smk-tkj' => 'SMK TKJ', 'smk-rpl' => 'SMK RPL', 'smk-sija' => 'SMK SIJA', 'kuliah-ti' => 'Teknik Informatika', 'kuliah-si' => 'Sistem Informasi', 'kuliah-ptik' => 'Pend Teknik Informatika'] as $value => $label)
                    <a href="{{ route('admin-peserta.pengumpulan-tugas.index', array_filter(['jenjang' => $value, 'search' => request('search'), 'tugas_id' => request('tugas_id')])) }}" @class([
                        'inline-flex min-w-24 items-center justify-center rounded-xl px-4 py-2.5 text-sm font-bold transition',
                        'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)]' => $jenjang === $value,
                        'border border-slate-200 bg-white text-slate-600 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' => $jenjang !== $value,
                    ])>{{ $label }}</a>
                @endforeach
            </nav>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1260px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-sky-50/70 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-5 py-4">Jenjang</th>
                        <th class="px-5 py-4 text-center">Minggu Ke</th>
                        <th class="px-5 py-4">Nama Tugas</th>
                        <th class="px-5 py-4">Waktu Pengumpulan</th>
                        <th class="px-5 py-4">Bukti Pengumpulan</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submitted as $item)
                        @php
                            $name = $participantName($item);
                            $initials = collect(preg_split('/\s+/', trim($name)) ?: [])->filter()->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                            $isLate = in_array((string) $item->status, ['telat', 'Terlambat'], true);
                        @endphp
                        <tr class="group transition hover:bg-sky-50/40">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200">{{ $initials ?: 'P' }}</span>
                                    <div class="min-w-0">
                                        <p class="max-w-52 truncate text-sm font-extrabold text-slate-900" title="{{ $name }}">{{ $name }}</p>
                                        <p class="mt-0.5 max-w-52 truncate text-xs text-slate-500">{{ $participantEmail($item) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-sky-100 bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">{{ $groupLabel($item) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-grid h-9 min-w-9 place-items-center rounded-xl bg-slate-100 px-2 text-sm font-extrabold text-slate-700">{{ $item->tugas?->minggu_ke ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-64 text-sm font-bold leading-5 text-slate-800">{{ $taskTitle($item) }}</p>
                                @if($item->tugas?->kode_tugas)
                                    <p class="mt-1 text-xs text-slate-400">{{ $item->tugas->kode_tugas }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($item->dikumpulkan_pada)
                                    <p class="text-sm font-semibold text-slate-700">{{ $item->dikumpulkan_pada->translatedFormat('d M Y') }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $item->dikumpulkan_pada->format('H:i') }} WIB</p>
                                @else
                                    <span class="text-sm italic text-slate-400">Waktu tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($item->file_jawaban)
                                    <a href="{{ route('admin-peserta.pengumpulan-tugas.file', $item) }}" target="_blank" class="inline-flex max-w-52 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 shadow-sm transition hover:border-sky-200 hover:bg-sky-50">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span class="truncate">{{ basename($item->file_jawaban) }}</span>
                                    </a>
                                @else
                                    <span class="text-sm italic text-slate-400">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold',
                                    'border-rose-200 bg-rose-50 text-rose-700' => $isLate,
                                    'border-emerald-200 bg-emerald-50 text-emerald-700' => !$isLate,
                                ])>
                                    <span @class(['h-1.5 w-1.5 rounded-full', 'bg-rose-500' => $isLate, 'bg-emerald-500' => !$isLate])></span>
                                    {{ $isLate ? 'Terlambat' : 'Mengumpulkan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin-peserta.pengumpulan-tugas.show', $item) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-3.5 py-2 text-xs font-extrabold text-white shadow-[0_8px_18px_rgba(14,165,233,0.22)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Show Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </span>
                                <p class="mt-4 font-extrabold text-slate-700">Belum ada data pengumpulan.</p>
                                <p class="mt-1 text-sm text-slate-500">Data akan muncul setelah peserta mengunggah bukti tugas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submitted->hasPages())
            <footer class="border-t border-sky-100 bg-sky-50/50 px-6 py-4">
                {{ $submitted->links() }}
            </footer>
        @endif
    </section>

    {{-- SECTION 2: Belum Mengumpulkan --}}
    <section class="overflow-hidden rounded-3xl border border-amber-100/80 bg-white/95 shadow-[0_20px_50px_rgba(180,83,9,0.08)] backdrop-blur">
        <header class="flex flex-col gap-3 border-b border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50/80 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-amber-600 shadow-sm ring-1 ring-amber-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Data Peserta yang Belum Mengumpulkan Tugas</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Peserta dapat diperingatkan agar segera menyelesaikan tugas yang masih tertunda.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm ring-1 ring-slate-200">{{ $activeJenjangLabel }}</span>
                <span class="rounded-xl bg-white px-4 py-2 text-xs font-extrabold text-amber-700 shadow-sm ring-1 ring-amber-100">{{ number_format($pending->total()) }} data</span>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-amber-50/70 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-5 py-4">Jenjang</th>
                        <th class="px-5 py-4 text-center">Minggu Ke</th>
                        <th class="px-5 py-4">Tugas yang Belum Dikerjakan</th>
                        <th class="px-5 py-4">Deadline</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pending as $item)
                        @php
                            $name = $participantName($item);
                            $initials = collect(preg_split('/\s+/', trim($name)) ?: [])->filter()->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                            $overdue = $item->deadline && now()->greaterThan($item->deadline);
                        @endphp
                        <tr class="transition hover:bg-amber-50/40">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-amber-100 to-orange-100 text-xs font-extrabold text-amber-700 ring-1 ring-amber-200">{{ $initials ?: 'P' }}</span>
                                    <div class="min-w-0">
                                        <p class="max-w-56 truncate text-sm font-extrabold text-slate-900" title="{{ $name }}">{{ $name }}</p>
                                        <p class="mt-0.5 max-w-56 truncate text-xs text-slate-500">{{ $participantEmail($item) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-sky-100 bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">{{ $groupLabel($item) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-grid h-9 min-w-9 place-items-center rounded-xl bg-slate-100 px-2 text-sm font-extrabold text-slate-700">{{ $item->tugas?->minggu_ke ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-80 text-sm font-bold leading-5 text-slate-800">{{ $taskTitle($item) }}</p>
                                @if($item->tugas?->kode_tugas)
                                    <p class="mt-1 text-xs text-slate-400">{{ $item->tugas->kode_tugas }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($item->deadline)
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold ring-1',
                                        'bg-rose-50 text-rose-700 ring-rose-200' => $overdue,
                                        'bg-slate-50 text-slate-600 ring-slate-200' => !$overdue,
                                    ])>
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $item->deadline->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                    @if($overdue)
                                        <p class="mt-1 text-xs font-extrabold text-rose-600">Melewati deadline</p>
                                    @endif
                                @else
                                    <span class="text-sm italic text-slate-400">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin-peserta.pengumpulan-tugas.remind', $item) }}" class="inline-block" onsubmit="return confirm('Kirim notifikasi peringatan kepada peserta ini?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-3.5 py-2 text-xs font-extrabold text-white shadow-[0_8px_18px_rgba(245,158,11,0.24)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-amber-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        Peringati Peserta
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 text-emerald-500">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <p class="mt-4 font-extrabold text-slate-700">Tidak ada tugas yang tertunda.</p>
                                <p class="mt-1 text-sm text-slate-500">Seluruh tugas aktif pada filter ini sudah dikumpulkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pending->hasPages())
            <footer class="border-t border-amber-100 bg-amber-50/50 px-6 py-4">
                {{ $pending->links() }}
            </footer>
        @endif
    </section>

    {{-- Daftar Penugasan dari Template Excel --}}
    <section id="daftar-penugasan" class="overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="h-1.5 bg-gradient-to-r from-cyan-600 via-sky-500 to-blue-700"></div>

        <div class="border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-cyan-700 shadow-sm ring-1 ring-sky-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Penugasan dari Template</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Susunan tabel mengikuti template Excel: Minggu Ke, Materi &amp; Laporan, Tugas, Hari Tampil, Hari Deadline, dan Jam Deadline. Untuk mengunggah template baru, buka menu <a href="{{ route('admin-peserta.tugas.index') }}" class="font-bold text-sky-700 underline">Tugas Mingguan</a>.</p>
                </div>
            </div>
        </div>

        @php
            $filterKelompokTemplate = [
                ['value' => '', 'label' => 'Semua', 'icon' => 'groups'],
                ['value' => 'smk_tkj', 'label' => 'SMK TKJ', 'icon' => 'lan'],
                ['value' => 'smk_rpl', 'label' => 'SMK RPL', 'icon' => 'code'],
                ['value' => 'smk_sija', 'label' => 'SMK SIJA', 'icon' => 'hub'],
                ['value' => 'kuliah_ti', 'label' => 'Teknik Informatika', 'icon' => 'school'],
                ['value' => 'kuliah_si', 'label' => 'Sistem Informasi', 'icon' => 'school'],
                ['value' => 'kuliah_ptik', 'label' => 'Pend Teknik Informatika', 'icon' => 'school'],
            ];

            $kelompokMetaTemplate = [
                'smk_tkj' => ['judul' => 'SMK TKJ', 'deskripsi' => 'Tugas peserta SMK Teknik Komputer dan Jaringan · minimal magang 5 bulan', 'icon' => 'lan'],
                'smk_rpl' => ['judul' => 'SMK RPL', 'deskripsi' => 'Tugas peserta SMK Rekayasa Perangkat Lunak · minimal magang 5 bulan', 'icon' => 'code'],
                'smk_sija' => ['judul' => 'SMK SIJA', 'deskripsi' => 'Tugas peserta SMK Sistem Informasi Jaringan dan Aplikasi · minimal magang 5 bulan', 'icon' => 'hub'],
                'kuliah_ti' => ['judul' => 'Teknik Informatika', 'deskripsi' => 'Tugas peserta jurusan Teknik Informatika · magang 1–4 bulan', 'icon' => 'school'],
                'kuliah_si' => ['judul' => 'Sistem Informasi', 'deskripsi' => 'Tugas peserta jurusan Sistem Informasi · magang 1–4 bulan', 'icon' => 'school'],
                'kuliah_ptik' => ['judul' => 'Pend Teknik Informatika', 'deskripsi' => 'Tugas peserta jurusan Pend Teknik Informatika · magang 1–4 bulan', 'icon' => 'school'],
            ];

            $templateTargetAktif = in_array($templateTarget, array_keys($kelompokMetaTemplate), true) ? $templateTarget : '';
            $kelompokDitampilkanTemplate = $templateTargetAktif !== '' ? [$templateTargetAktif] : array_keys($kelompokMetaTemplate);
        @endphp

        {{-- Filter kelompok --}}
        <div class="border-b border-slate-200 bg-slate-50/70 px-6 py-4">
            <div class="flex flex-wrap items-center gap-2" aria-label="Filter kelompok peserta">
                @foreach ($filterKelompokTemplate as $filter)
                    @php
                        $aktif = $templateTargetAktif === $filter['value'];
                        $filterUrl = $filter['value'] === ''
                            ? route('admin-peserta.pengumpulan-tugas.index')
                            : route('admin-peserta.pengumpulan-tugas.index', ['template_target' => $filter['value']]);
                    @endphp
                    <a href="{{ $filterUrl }}#daftar-penugasan"
                        @class([
                            'inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-bold transition',
                            'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)]' => $aktif,
                            'border border-slate-200 bg-white text-slate-600 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' => !$aktif,
                        ])
                        @if ($aktif) aria-current="page" @endif>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Daftar penugasan --}}
        <div class="space-y-6 p-6">
            @if ($tugasList->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 px-6 py-14 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <p class="mt-3 text-sm font-bold text-slate-500">Belum ada template tugas yang diunggah.</p>
                    <p class="mt-1 text-xs text-slate-400">Unggah file template Excel di menu Tugas Mingguan agar daftar penugasan tampil mengikuti isi setiap sheet.</p>
                </div>
            @else
                @foreach ($kelompokDitampilkanTemplate as $target)
                    @php
                        $meta = $kelompokMetaTemplate[$target];
                        $groupTasks = $tugasList->where('target_peserta', $target)->sortBy([['minggu_ke', 'asc'], ['rilis_hari_ke', 'asc'], ['id_tugas', 'asc']])->values();
                    @endphp
                    @continue($groupTasks->isEmpty())

                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/70 ring-1 ring-slate-100">
                        <div class="flex items-center justify-between gap-4 bg-slate-800 px-5 py-3 text-white">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </span>
                                <h3 class="text-base font-bold tracking-wide">{{ $meta['judul'] }}</h3>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">{{ $groupTasks->count() }} penugasan</span>
                        </div>

                        <div class="border-b border-sky-100 bg-sky-50 px-5 py-3 text-sm font-semibold text-slate-700">{{ $meta['deskripsi'] }}</div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[980px] border-collapse text-left text-sm">
                                <thead>
                                    <tr class="bg-sky-600 text-xs font-bold uppercase tracking-wide text-white">
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
                                            @php $isLaporan = $tugas->kategori_tugas === 'laporan'; @endphp
                                            <tr @class(['transition hover:bg-sky-50/70', 'bg-purple-50/40' => $isLaporan, 'bg-white' => !$isLaporan])>
                                                @if ($baris === 0)
                                                    <td rowspan="{{ $tugasMinggu->count() }}" class="border-r border-slate-200 bg-amber-50 px-4 py-4 text-center align-middle">
                                                        <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl bg-amber-100 px-3 font-extrabold text-amber-800 ring-1 ring-amber-200">{{ $minggu ?: '-' }}</span>
                                                    </td>
                                                @endif
                                                <td class="border-r border-slate-200 px-4 py-4 align-top">
                                                    <span @class(['inline-flex rounded-full px-3 py-1 text-xs font-bold', 'bg-purple-100 text-purple-700' => $isLaporan, 'bg-amber-100 text-amber-700' => !$isLaporan])>{{ $tugas->materi ?: ($isLaporan ? 'Laporan' : 'Materi') }}</span>
                                                </td>
                                                <td class="border-r border-slate-200 px-4 py-4 align-top">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div>
                                                            <p class="font-semibold leading-6 text-slate-900">{{ $tugas->judul }}</p>
                                                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-400">
                                                                <span>{{ $tugas->kode_tugas ?: 'Tanpa kode' }}</span>
                                                                <span class="inline-flex items-center gap-1">
                                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                                    {{ $tugas->penugasan_peserta_count }} peserta
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <form action="{{ route('admin-peserta.tugas.destroy', $tugas) }}" method="POST" class="shrink-0" onsubmit="return confirm('Hapus tugas dan seluruh jadwal pesertanya?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="rounded-lg p-2 text-rose-500 transition hover:bg-rose-50" title="Hapus penugasan">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"/></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td class="border-r border-slate-200 px-4 py-4 text-center font-medium text-slate-700">{{ ucfirst($tugas->hari_tampil ?: '-') }}</td>
                                                <td class="border-r border-slate-200 px-4 py-4 text-center font-medium text-slate-700">{{ ucfirst($tugas->hari_deadline ?: '-') }}</td>
                                                <td class="px-4 py-4 text-center">
                                                    <span class="inline-flex rounded-xl bg-slate-100 px-3 py-1.5 font-bold tabular-nums text-slate-700">{{ $tugas->jam_deadline ? substr((string) $tugas->jam_deadline, 0, 5) : '-' }}</span>
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