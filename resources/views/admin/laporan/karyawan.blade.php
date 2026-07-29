@extends('layouts.portal')

@section('title', 'Laporan Karyawan')

@section('content')
<div class="space-y-6">

    <header>
        <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-950">Laporan Karyawan</h1>
        <p class="mt-1 text-sm text-slate-500">Ringkasan data dan status seluruh karyawan CV Natusi.</p>
    </header>

    {{-- Filter --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.laporan-karyawan.index') }}" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Dari Tanggal Bergabung</label>
                    <input type="date" name="dari_tanggal" value="{{ $dariTgl }}"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ $sampaiTgl }}"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Jabatan</label>
                    <select name="jabatan" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Jabatan</option>
                        @foreach ($jabatanList as $j)
                            <option value="{{ $j }}" {{ $jabatan === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[200px] flex-1">
                    <label class="mb-1 block text-xs font-bold text-slate-500">Cari Nama / NIP</label>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari karyawan..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-blue-700 px-5 py-2.5 text-sm font-bold text-blue-700 shadow-sm transition-colors hover:bg-blue-50">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    Terapkan
                </button>
            </div>
        </form>
    </section>

    {{-- Summary Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border-l-4 border-l-blue-700 border border-slate-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <p class="text-xs font-bold text-slate-500">Total Karyawan</p>
                <span class="material-symbols-outlined rounded-lg bg-blue-50 p-2 text-[20px] text-blue-700">groups</span>
            </div>
            <h3 class="text-3xl font-extrabold tracking-tight text-slate-950">{{ $stats['total'] }}</h3>
            <p class="mt-2 text-xs text-slate-400">Seluruh data karyawan terdaftar</p>
        </div>

        <div class="rounded-2xl border-l-4 border-l-emerald-500 border border-slate-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <p class="text-xs font-bold text-slate-500">Karyawan Aktif</p>
                <span class="material-symbols-outlined rounded-lg bg-emerald-50 p-2 text-[20px] text-emerald-600">person_check</span>
            </div>
            <h3 class="text-3xl font-extrabold tracking-tight text-slate-950">{{ $stats['aktif'] }}</h3>
            <p class="mt-2 text-xs text-slate-400">Update per {{ now()->translatedFormat('d M Y') }}</p>
        </div>

        <div class="rounded-2xl border-l-4 border-l-red-600 border border-slate-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <p class="text-xs font-bold text-slate-500">Non-Aktif</p>
                <span class="material-symbols-outlined rounded-lg bg-red-50 p-2 text-[20px] text-red-600">event_busy</span>
            </div>
            <h3 class="text-3xl font-extrabold tracking-tight text-slate-950">{{ $stats['nonaktif'] }}</h3>
            <p class="mt-2 text-xs text-slate-400">Sudah tidak aktif bekerja</p>
        </div>

        <div class="rounded-2xl border-l-4 border-l-sky-500 border border-slate-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <p class="text-xs font-bold text-slate-500">Karyawan Baru</p>
                <span class="material-symbols-outlined rounded-lg bg-sky-50 p-2 text-[20px] text-sky-600">person_add</span>
            </div>
            <h3 class="text-3xl font-extrabold tracking-tight text-slate-950">{{ $stats['baru_bulan_ini'] }}</h3>
            <p class="mt-2 text-xs text-slate-400">Bulan ini ({{ now()->translatedFormat('F') }})</p>
        </div>
    </div>

    {{-- Bento: Distribusi Jabatan + Status --}}
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="mb-6 flex items-center gap-2">
                <span class="h-6 w-1 rounded-full bg-blue-700"></span>
                <h2 class="text-base font-bold text-slate-900">Distribusi per Jabatan</h2>
            </div>
            @if ($distribusiJabatan->isEmpty())
                <p class="py-10 text-center text-sm text-slate-400">Belum ada data jabatan.</p>
            @else
                @php $maxJabatan = max(1, $distribusiJabatan->max()); @endphp
                <div class="flex h-56 items-end justify-between gap-3 border-b border-l border-slate-100 px-2 pt-4">
                    @foreach ($distribusiJabatan as $namaJabatan => $total)
                        <div class="group flex flex-1 flex-col items-center">
                            <div class="relative w-full rounded-t-lg bg-blue-600 transition-all duration-500 hover:bg-blue-700"
                                style="height: {{ max(6, ($total / $maxJabatan) * 180) }}px">
                                <div class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white opacity-0 transition-opacity group-hover:opacity-100">
                                    {{ $total }}
                                </div>
                            </div>
                            <span class="mt-2 max-w-[70px] truncate text-[10px] font-medium text-slate-400" title="{{ $namaJabatan }}">{{ $namaJabatan }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center gap-2">
                <span class="h-6 w-1 rounded-full bg-blue-700"></span>
                <h2 class="text-base font-bold text-slate-900">Status Karyawan</h2>
            </div>
            <div class="flex flex-col items-center justify-center gap-6">
                <div class="relative flex h-40 w-40 items-center justify-center rounded-full border-[16px] border-blue-100"
                    style="background: conic-gradient(#1d4ed8 0% {{ $persenAktif }}%, #bfdbfe {{ $persenAktif }}% 100%);">
                    <div class="absolute flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white shadow-inner">
                        <span class="text-2xl font-extrabold text-blue-700">{{ $stats['total'] }}</span>
                        <span class="text-[10px] uppercase tracking-widest text-slate-400">Total</span>
                    </div>
                </div>
                <div class="w-full space-y-2">
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-blue-700"></span>
                            <span class="text-sm text-slate-600">Aktif</span>
                        </div>
                        <span class="text-sm font-bold text-slate-900">{{ $stats['aktif'] }} ({{ $persenAktif }}%)</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-blue-200"></span>
                            <span class="text-sm text-slate-600">Non-Aktif</span>
                        </div>
                        <span class="text-sm font-bold text-slate-900">{{ $stats['nonaktif'] }} ({{ $persenNonaktif }}%)</span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Tabel --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 px-6 py-4">
            <span class="h-6 w-1 rounded-full bg-blue-700"></span>
            <h2 class="text-base font-bold text-slate-900">Daftar Ringkasan Karyawan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">NIP</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Bergabung</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $avatarColors = [
                            'bg-blue-100 text-blue-700',
                            'bg-emerald-100 text-emerald-700',
                            'bg-amber-100 text-amber-700',
                            'bg-rose-100 text-rose-700',
                            'bg-violet-100 text-violet-700',
                        ];
                    @endphp
                    @forelse ($karyawan as $index => $item)
                        <tr class="transition-colors hover:bg-slate-50/70">
                            <td class="px-6 py-4 text-slate-400">{{ $karyawan->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-xs font-bold {{ $avatarColors[$index % count($avatarColors)] }}">
                                        {{ $item->initials() }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $item->nama_karyawan }}</p>
                                        <p class="text-xs text-slate-400">{{ $item->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $item->nip }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->jabatan ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php $meta = $item->statusMeta(); @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $meta['text'] }}"
                                    style="background-color: color-mix(in srgb, currentColor 12%, white);">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $meta['dot'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->created_at?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.karyawan.index', ['search' => $item->nip]) }}"
                                    class="inline-flex items-center gap-1 rounded-lg p-1.5 text-blue-700 transition-colors hover:bg-blue-50">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-400">Tidak ada data untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($karyawan->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $karyawan->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
