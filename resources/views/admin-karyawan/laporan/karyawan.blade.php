@extends('layouts.portal')

@section('title', 'Laporan Karyawan')

@section('content')

<style>
    .lp-header{background:linear-gradient(to right,#006191,#0a7ab5,#12a3d4);}
    .lp-decor-10{background-color:rgba(255,255,255,.10);}
    .lp-badge{background-color:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);}

    .lp-card-blue{background:linear-gradient(135deg,#2563eb,#0284c7,#0891b2);}
    .lp-card-emerald{background:linear-gradient(135deg,#10b981,#22c55e,#0d9488);}
    .lp-card-rose{background:linear-gradient(135deg,#f43f5e,#ef4444,#ea580c);}
    .lp-card-violet{background:linear-gradient(135deg,#7c3aed,#9333ea,#c026d3);}

    .lp-accent-bar{background-color:#1d4ed8;}

    .lp-bar-divisi{background-color:#2563eb;transition:background-color .3s;}
    .lp-bar-divisi:hover{background-color:#1d4ed8;}

    .lp-legend-dot-a{background-color:#1d4ed8;}
    .lp-legend-dot-b{background-color:#bfdbfe;}

    .lp-thead{background:linear-gradient(to right,#006191,#0a7ab5);}

    .lp-avatar-blue{background-color:#dbeafe;color:#1d4ed8;}
    .lp-avatar-emerald{background-color:#d1fae5;color:#047857;}
    .lp-avatar-amber{background-color:#fef3c7;color:#b45309;}
    .lp-avatar-rose{background-color:#ffe4e6;color:#be123c;}
    .lp-avatar-violet{background-color:#ede9fe;color:#6d28d9;}

    .lp-btn-outline{border:1px solid #1d4ed8;color:#1d4ed8;transition:background-color .2s;}
    .lp-btn-outline:hover{background-color:#eff6ff;}

    .lp-link-blue{color:#1d4ed8;transition:background-color .2s;}
    .lp-link-blue:hover{background-color:#eff6ff;}

    .lp-input{transition:border-color .2s,box-shadow .2s,background-color .2s;}
    .lp-input:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 2px rgba(37,99,235,.15);background-color:#ffffff;}
</style>

<div class="p-4 sm:p-6 space-y-6">

    <header class="lp-header relative overflow-hidden rounded-2xl p-6 sm:p-8 text-white shadow-lg">
        <div class="lp-decor-10 absolute -right-10 -top-14 h-52 w-52 rounded-full"></div>
        <div class="lp-decor-10 absolute right-24 top-20 h-28 w-28 rounded-full"></div>
        <div class="relative">
            <span class="lp-badge inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider backdrop-blur-sm">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><path d="M9 17v-6m4 6V7m4 10v-3M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Laporan
            </span>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight">Laporan Karyawan</h1>
            <p class="mt-1 text-sm text-blue-100">Ringkasan data dan status seluruh karyawan CV Natusi.</p>
        </div>
    </header>

    {{-- Filter --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ url('/admin-karyawan/laporan/karyawan') }}" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Dari Tanggal Bergabung</label>
                    <input type="date" name="dari_tanggal" value="{{ $dariTgl }}"
                        class="lp-input rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ $sampaiTgl }}"
                        class="lp-input rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Divisi</label>
                    <select name="divisi_id" class="lp-input rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm">
                        <option value="">Semua Divisi</option>
                        @foreach ($divisiList as $divisi)
                            <option value="{{ $divisi->id_divisi }}" @selected((string) $divisiId === (string) $divisi->id_divisi)>{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[200px] flex-1">
                    <label class="mb-1 block text-xs font-bold text-slate-500">Cari Nama / NIP</label>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari karyawan..."
                            class="lp-input w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="lp-btn-outline inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    Terapkan
                </button>
            </div>
        </form>
    </section>

    {{-- Summary Cards (full color) --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lp-card-blue relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-blue-100">Total Karyawan</p>
            <h3 class="mt-2 text-3xl font-black">{{ $stats['total'] }}</h3>
            <p class="mt-1 text-[11px] font-medium text-blue-100">Seluruh data karyawan terdaftar</p>
        </div>

        <div class="lp-card-emerald relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100">Karyawan Aktif</p>
            <h3 class="mt-2 text-3xl font-black">{{ $stats['aktif'] }}</h3>
            <p class="mt-1 text-[11px] font-medium text-emerald-100">Update per {{ now()->translatedFormat('d M Y') }}</p>
        </div>

        <div class="lp-card-rose relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-rose-100">Non-Aktif</p>
            <h3 class="mt-2 text-3xl font-black">{{ $stats['nonaktif'] }}</h3>
            <p class="mt-1 text-[11px] font-medium text-rose-100">Sudah tidak aktif bekerja</p>
        </div>

        <div class="lp-card-violet relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-violet-100">Karyawan Baru</p>
            <h3 class="mt-2 text-3xl font-black">{{ $stats['baru_bulan_ini'] }}</h3>
            <p class="mt-1 text-[11px] font-medium text-violet-100">Bulan ini ({{ now()->translatedFormat('F') }})</p>
        </div>
    </div>

    {{-- Bento: Distribusi Divisi + Status --}}
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="mb-6 flex items-center gap-2">
                <span class="lp-accent-bar h-6 w-1 rounded-full"></span>
                <h2 class="text-base font-bold text-slate-900">Distribusi per Divisi</h2>
            </div>
            @if ($distribusiDivisi->isEmpty())
                <p class="py-10 text-center text-sm text-slate-400">Belum ada data divisi.</p>
            @else
                @php $maxDivisi = max(1, $distribusiDivisi->max()); @endphp
                <div class="flex h-56 items-end justify-between gap-3 border-b border-l border-slate-100 px-2 pt-4">
                    @foreach ($distribusiDivisi as $namaDivisi => $total)
                        <div class="group flex flex-1 flex-col items-center">
                            <div class="lp-bar-divisi relative w-full rounded-t-lg duration-500"
                                style="height: {{ max(6, ($total / $maxDivisi) * 180) }}px">
                                <div class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white opacity-0 transition-opacity group-hover:opacity-100">
                                    {{ $total }}
                                </div>
                            </div>
                            <span class="mt-2 max-w-[70px] truncate text-[10px] font-medium text-slate-400" title="{{ $namaDivisi }}">{{ $namaDivisi }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center gap-2">
                <span class="lp-accent-bar h-6 w-1 rounded-full"></span>
                <h2 class="text-base font-bold text-slate-900">Status Karyawan</h2>
            </div>
            <div class="flex flex-col items-center justify-center gap-6">
                <div class="relative flex h-40 w-40 items-center justify-center rounded-full"
                    style="border:16px solid #dbeafe; background: conic-gradient(#1d4ed8 0% {{ $persenAktif }}%, #bfdbfe {{ $persenAktif }}% 100%);">
                    <div class="absolute flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white shadow-inner">
                        <span class="text-2xl font-extrabold" style="color:#1d4ed8;">{{ $stats['total'] }}</span>
                        <span class="text-[10px] uppercase tracking-widest text-slate-400">Total</span>
                    </div>
                </div>
                <div class="w-full space-y-2">
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                        <div class="flex items-center gap-2">
                            <span class="lp-legend-dot-a h-3 w-3 rounded-full"></span>
                            <span class="text-sm text-slate-600">Aktif</span>
                        </div>
                        <span class="text-sm font-bold text-slate-900">{{ $stats['aktif'] }} ({{ $persenAktif }}%)</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                        <div class="flex items-center gap-2">
                            <span class="lp-legend-dot-b h-3 w-3 rounded-full"></span>
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
            <span class="lp-accent-bar h-6 w-1 rounded-full"></span>
            <h2 class="text-base font-bold text-slate-900">Daftar Ringkasan Karyawan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] border-collapse text-left text-sm">
                <thead>
                    <tr class="lp-thead text-[11px] font-extrabold uppercase tracking-wider text-white">
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">NIP</th>
                        <th class="px-6 py-4">Divisi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Bergabung</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $avatarColors = [
                            'lp-avatar-blue',
                            'lp-avatar-emerald',
                            'lp-avatar-amber',
                            'lp-avatar-rose',
                            'lp-avatar-violet',
                        ];
                    @endphp
                    @forelse ($karyawan as $index => $item)
                        <tr class="transition-colors hover:bg-slate-50/70">
                            <td class="px-6 py-4 text-slate-400">{{ $karyawan->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="{{ $avatarColors[$index % count($avatarColors)] }} grid h-9 w-9 shrink-0 place-items-center rounded-full text-xs font-bold">
                                        {{ $item->initials() }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $item->nama_karyawan }}</p>
                                        <p class="text-xs text-slate-400">{{ $item->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $item->nip }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->divisi?->nama_divisi ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php $meta = $item->statusMeta(); @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $meta['text'] }}"
                                    style="background-color: color-mix(in srgb, currentColor 12%, white);">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $meta['dot'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->tanggal_bergabung?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ url('/admin-karyawan/karyawan?search=' . $item->nip) }}"
                                    class="lp-link-blue inline-flex items-center gap-1 rounded-lg p-1.5">
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