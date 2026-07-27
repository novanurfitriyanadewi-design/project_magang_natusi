@extends('layouts.portal')

@section('title', 'Data Pengumpulan Tugas')

@section('content')
@php
    $groupLabels = [
        'smk_tkj' => 'SMK TKJ',
        'smk_rpl' => 'SMK RPL',
        'universitas' => 'Universitas',
    ];

    $rawStats = is_array($stats ?? null) ? $stats : [];
    $stats = array_merge($rawStats, [
        'mengumpulkan' => (int) ($rawStats['mengumpulkan']
            ?? $rawStats['tugas_terkumpul']
            ?? $rawStats['tugas_selesai']
            ?? 0),
        'terlambat' => (int) ($rawStats['terlambat']
            ?? $rawStats['tugas_terlambat']
            ?? 0),
        'tidak_mengumpulkan' => (int) ($rawStats['tidak_mengumpulkan']
            ?? $rawStats['belum_mengumpulkan']
            ?? 0),
    ]);

    $jenjang = $jenjang ?? request('jenjang', 'semua');
    $daftarTugas = $daftarTugas ?? collect();

    if (!isset($submitted)) {
        $submitted = $submissions ?? new \Illuminate\Pagination\LengthAwarePaginator(
            [],
            0,
            10,
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    if (!isset($pending)) {
        $pending = new \Illuminate\Pagination\LengthAwarePaginator(
            [],
            0,
            10,
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    $participantName = static fn ($item) => $item->peserta?->user?->nama
        ?? $item->peserta?->nama_peserta
        ?? $item->peserta?->permintaan?->nama_pemohon
        ?? 'Peserta tidak ditemukan';

    $participantEmail = static fn ($item) => $item->peserta?->user?->email
        ?? $item->peserta?->permintaan?->email
        ?? '-';

    $groupLabel = static function ($item) use ($groupLabels): string {
        $target = $item->tugas?->target_peserta;
        if ($target && isset($groupLabels[$target])) {
            return $groupLabels[$target];
        }

        $education = mb_strtolower((string) ($item->peserta?->tingkat_pendidikan ?? ''));
        $major = mb_strtolower((string) ($item->peserta?->permintaan?->jurusan ?? ''));

        if (str_contains($major, 'tkj')) {
            return 'SMK TKJ';
        }
        if (str_contains($major, 'rpl')) {
            return 'SMK RPL';
        }
        if (str_contains($education, 'universitas') || str_contains($education, 'kuliah')) {
            return 'Universitas';
        }

        return $education !== '' ? mb_strtoupper($education) : '-';
    };

    $taskTitle = static fn ($item) => $item->tugas?->judul
        ?? $item->tugas?->judul_tugas
        ?? '-';

    $activeJenjangLabel = [
        'semua' => 'Semua Jenjang',
        'smk-tkj' => 'SMK TKJ',
        'smk-rpl' => 'SMK RPL',
        'universitas' => 'Universitas',
    ][$jenjang] ?? 'Semua Jenjang';
@endphp

<div class="space-y-6">
    <section>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
            Data Pengumpulan Tugas
        </h1>
        <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
            Pantau peserta yang mengumpulkan, terlambat, dan belum mengerjakan tugas magang.
        </p>
        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-[11px] font-medium text-slate-400">
            <span>Diperbarui {{ now()->translatedFormat('d M Y, H:i') }}</span>
            <span class="hidden h-1 w-1 rounded-full bg-slate-300 sm:block"></span>
            <span>Data mengikuti jadwal penugasan aktif peserta</span>
        </div>
    </section>

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Card statistik memakai bentuk yang sama dengan dashboard admin. --}}
    <section class="grid gap-4 md:grid-cols-3">
        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-600 to-red-500 p-5 text-white shadow-[0_16px_36px_rgba(225,29,72,0.18)] transition duration-200 hover:-translate-y-0.5">
            <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-rose-100">Terlambat</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['terlambat']) }}</p>
                    <p class="mt-1 text-sm text-rose-100">Dikumpulkan setelah deadline</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[26px]">running_with_errors</span>
                </span>
            </div>
        </article>

        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-500 p-5 text-white shadow-[0_16px_36px_rgba(5,150,105,0.18)] transition duration-200 hover:-translate-y-0.5">
            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full border-[18px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-emerald-100">Mengumpulkan</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['mengumpulkan']) }}</p>
                    <p class="mt-1 text-sm text-emerald-100">Terkumpul sesuai batas waktu</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[26px]">task_alt</span>
                </span>
            </div>
        </article>

        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-5 text-white shadow-[0_16px_36px_rgba(245,158,11,0.20)] transition duration-200 hover:-translate-y-0.5">
            <div class="absolute -bottom-20 left-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-amber-50">Tidak Mengumpulkan</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['tidak_mengumpulkan']) }}</p>
                    <p class="mt-1 text-sm text-amber-50">Tugas aktif belum dikerjakan</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[26px]">notification_important</span>
                </span>
            </div>
        </article>
    </section>

    {{-- DATA 1: peserta yang sudah mengumpulkan. Header, menu, filter, dan tabel berada dalam satu card. --}}
    <section class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
        <header class="flex flex-col gap-3 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-950">Data Peserta yang Mengumpulkan Tugas</h2>
                <p class="mt-1 text-sm text-slate-500">Gunakan Show Detail untuk melihat seluruh data dan bukti file peserta.</p>
            </div>
            <span class="w-fit rounded-xl bg-white px-4 py-2 text-xs font-extrabold text-sky-700 shadow-sm ring-1 ring-sky-100">
                {{ number_format($submitted->total()) }} data
            </span>
        </header>

        <div class="border-b border-sky-100 bg-white px-5 py-5 sm:px-6">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Filter jenjang peserta">
                @foreach([
                    'semua' => 'Semua',
                    'smk-tkj' => 'SMK TKJ',
                    'smk-rpl' => 'SMK RPL',
                    'universitas' => 'Universitas',
                ] as $value => $label)
                    <a
                        href="{{ route('admin.pengumpulan-tugas.index', array_filter([
                            'jenjang' => $value,
                            'search' => request('search'),
                            'tugas_id' => request('tugas_id'),
                        ])) }}"
                        @class([
                            'inline-flex min-w-24 items-center justify-center rounded-xl px-4 py-2.5 text-sm font-extrabold transition duration-200',
                            'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)]' => $jenjang === $value,
                            'border border-slate-200 bg-white text-slate-600 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' => $jenjang !== $value,
                        ])
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
            <form action="{{ route('admin.pengumpulan-tugas.index') }}" method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <input type="hidden" name="jenjang" value="{{ $jenjang }}">

                <div class="relative w-full lg:max-w-md">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama peserta atau tugas..."
                        class="w-full rounded-xl border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200"
                    >
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <select name="tugas_id" class="min-w-56 rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                        <option value="">Semua tugas</option>
                        @foreach($daftarTugas as $tugas)
                            <option value="{{ $tugas->id_tugas }}" @selected((string) request('tugas_id') === (string) $tugas->id_tugas)>
                                Minggu {{ $tugas->minggu_ke ?? '-' }} — {{ $tugas->judul }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[19px]">filter_alt</span>
                        Terapkan
                    </button>

                    @if(request()->filled('search') || request()->filled('tugas_id'))
                        <a href="{{ route('admin.pengumpulan-tugas.index', ['jenjang' => $jenjang]) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1260px] w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-sky-50/70">
                        <th class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Nama</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Jenjang</th>
                        <th class="px-5 py-4 text-center text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Minggu Ke</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Nama Tugas</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Waktu Pengumpulan</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Bukti Pengumpulan</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submitted as $item)
                        @php
                            $name = $participantName($item);
                            $initials = collect(preg_split('/\s+/', trim($name)) ?: [])
                                ->filter()->take(2)
                                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                            $isLate = in_array((string) $item->status, ['telat', 'Terlambat'], true);
                        @endphp
                        <tr class="group transition hover:bg-sky-50/45">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-black text-sky-700 ring-1 ring-sky-200">
                                        {{ $initials ?: 'P' }}
                                    </div>
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
                                    <p class="mt-1 text-xs text-slate-500">{{ $item->dikumpulkan_pada->format('H:i') }} WIB</p>
                                @else
                                    <span class="text-sm italic text-slate-400">Waktu tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($item->file_jawaban)
                                    <a href="{{ route('admin.pengumpulan-tugas.file', $item) }}" target="_blank" class="inline-flex max-w-52 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 shadow-sm transition hover:border-sky-200 hover:bg-sky-50">
                                        <span class="material-symbols-outlined text-[18px]">attach_file</span>
                                        <span class="truncate">{{ basename($item->file_jawaban) }}</span>
                                    </a>
                                @else
                                    <span class="text-sm italic text-slate-400">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold',
                                    'border-rose-200 bg-rose-50 text-rose-700' => $isLate,
                                    'border-emerald-200 bg-emerald-50 text-emerald-700' => !$isLate,
                                ])>
                                    <span @class([
                                        'h-1.5 w-1.5 rounded-full',
                                        'bg-rose-500' => $isLate,
                                        'bg-emerald-500' => !$isLate,
                                    ])></span>
                                    {{ $isLate ? 'Terlambat' : 'Mengumpulkan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.pengumpulan-tugas.show', $item) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-3.5 py-2 text-xs font-extrabold text-white shadow-[0_8px_18px_rgba(14,165,233,0.22)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-sky-200">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    Show Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <span class="material-symbols-outlined text-[30px]">inventory_2</span>
                                </div>
                                <p class="mt-4 font-extrabold text-slate-700">Belum ada data pengumpulan.</p>
                                <p class="mt-1 text-sm text-slate-500">Data akan muncul setelah peserta mengunggah bukti tugas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submitted->hasPages())
            <footer class="border-t border-slate-100 bg-white px-6 py-4">
                {{ $submitted->links() }}
            </footer>
        @endif
    </section>

    {{-- DATA 2: peserta yang belum mengumpulkan, dikembalikan sebagai card terpisah. --}}
    <section class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
        <header class="flex flex-col gap-3 border-b border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-amber-600 shadow-sm ring-1 ring-amber-100">
                    <span class="material-symbols-outlined text-[23px]">pending_actions</span>
                </span>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-950">Data Peserta yang Belum Mengumpulkan Tugas</h2>
                    <p class="mt-1 text-sm text-slate-500">Peserta dapat diperingatkan agar segera menyelesaikan tugas yang masih tertunda.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm ring-1 ring-slate-200">{{ $activeJenjangLabel }}</span>
                <span class="rounded-xl bg-white px-4 py-2 text-xs font-extrabold text-amber-700 shadow-sm ring-1 ring-amber-100">{{ number_format($pending->total()) }} data</span>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-amber-50/70">
                        <th class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Nama</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Jenjang</th>
                        <th class="px-5 py-4 text-center text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Minggu Ke</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Tugas yang Belum Dikerjakan</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Deadline</th>
                        <th class="px-6 py-4 text-right text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pending as $item)
                        @php
                            $name = $participantName($item);
                            $initials = collect(preg_split('/\s+/', trim($name)) ?: [])
                                ->filter()->take(2)
                                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                            $overdue = $item->deadline && now()->greaterThan($item->deadline);
                        @endphp
                        <tr class="transition hover:bg-amber-50/45">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-amber-100 to-orange-100 text-xs font-black text-amber-700 ring-1 ring-amber-200">{{ $initials ?: 'P' }}</div>
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
                                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold',
                                        'bg-rose-50 text-rose-700 ring-1 ring-rose-100' => $overdue,
                                        'bg-slate-100 text-slate-600 ring-1 ring-slate-200' => !$overdue,
                                    ])>
                                        <span class="material-symbols-outlined text-[16px]">schedule</span>
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
                                <form method="POST" action="{{ route('admin.pengumpulan-tugas.remind', $item) }}" class="inline-block" onsubmit="return confirm('Kirim notifikasi peringatan kepada peserta ini?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-3.5 py-2 text-xs font-extrabold text-white shadow-[0_8px_18px_rgba(245,158,11,0.24)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-amber-200">
                                        <span class="material-symbols-outlined text-[18px]">notifications_active</span>
                                        Peringati Peserta
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 text-emerald-500">
                                    <span class="material-symbols-outlined text-[30px]">done_all</span>
                                </div>
                                <p class="mt-4 font-extrabold text-slate-700">Tidak ada tugas yang tertunda.</p>
                                <p class="mt-1 text-sm text-slate-500">Seluruh tugas aktif pada filter ini sudah dikumpulkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pending->hasPages())
            <footer class="border-t border-slate-100 bg-white px-6 py-4">
                {{ $pending->links() }}
            </footer>
        @endif
    </section>
</div>
@endsection
