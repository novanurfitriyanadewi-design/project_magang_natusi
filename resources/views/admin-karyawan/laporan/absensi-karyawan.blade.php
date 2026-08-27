@extends('layouts.portal')

@section('title', 'Laporan Absensi Karyawan')

@section('content')

<style>
    .lp-header{background:linear-gradient(to right,#006191,#0a7ab5,#12a3d4);}
    .lp-decor-10{background-color:rgba(255,255,255,.10);}
    .lp-badge{background-color:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);}

    .lp-card-emerald{background:linear-gradient(135deg,#10b981,#22c55e,#0d9488);}
    .lp-card-amber{background:linear-gradient(135deg,#f59e0b,#f97316,#eab308);}
    .lp-card-sky{background:linear-gradient(135deg,#0ea5e9,#3b82f6,#4f46e5);}
    .lp-card-rose{background:linear-gradient(135deg,#f43f5e,#ef4444,#ea580c);}

    .lp-track{background-color:rgba(255,255,255,.25);}
    .lp-fill{background-color:#ffffff;}

    .lp-btn{background:linear-gradient(to right,#006191,#0a7ab5);transition:background-color .2s;}
    .lp-btn:hover{background:#004b70;}

    .lp-btn-export{background-color:#059669;transition:background-color .2s;}
    .lp-btn-export:hover{background-color:#047857;}

    .lp-badge-soft{background-color:#eaf6fd;color:#006191;border:1px solid #d3ecfa;}

    .lp-thead{background:linear-gradient(to right,#006191,#0a7ab5);}
    .lp-avatar{background:linear-gradient(135deg,#006191,#12a3d4);}

    .lp-bar{background:linear-gradient(to top,#006191,#12a3d4);transition:filter .2s;}
    .lp-bar:hover{filter:brightness(1.12);}

    .lp-input{transition:border-color .2s,box-shadow .2s,background-color .2s;}
    .lp-input:focus{outline:none;border-color:#006191;box-shadow:0 0 0 2px rgba(0,97,145,.18);background-color:#ffffff;}
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
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight">Laporan Absensi</h1>
            <p class="mt-1 text-sm text-blue-100">Rekapitulasi performa kehadiran karyawan CV Natusi.</p>
        </div>
    </header>

    <section class="rounded-2xl border border-slate-100 bg-white p-5 sm:p-6 shadow-sm">
            <form method="GET" action="{{ route('admin-karyawan.laporan.absensi') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ $dariTgl }}" class="lp-input w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm">
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampaiTgl }}" class="lp-input w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm">
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Jabatan</label>
                <select name="jabatan" class="lp-input w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm">
                    <option value="">Semua Jabatan</option>
                    @foreach ($jabatanList as $j)
                        <option value="{{ $j }}" {{ $jabatan === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Cari Nama Karyawan</label>
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama..." class="lp-input w-full rounded-lg border-slate-300 pl-9 pr-4 py-2.5 text-sm shadow-sm">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="lp-btn flex-1 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold text-white shadow">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    Terapkan
                </button>
                <a href="{{ route('admin-karyawan.laporan.absensi.export', request()->query()) }}" title="Export Excel"
                    class="lp-btn-export grid place-items-center rounded-lg px-4 py-2.5 text-sm font-bold text-white shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                </a>
            </div>
        </form>
    </section>

    <div class="grid gap-4 md:grid-cols-4">
        {{-- Total Kehadiran --}}
        <div class="lp-card-emerald relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100">Total Kehadiran</p>
            <p class="mt-2 text-2xl font-black">{{ $stats['tingkat_kehadiran'] }}<span class="text-base font-bold">%</span></p>
            <div class="lp-track mt-3 h-1.5 rounded-full"><div class="lp-fill h-full rounded-full transition-all" style="width: {{ min(100, (float) str_replace(',', '.', $stats['tingkat_kehadiran'])) }}%"></div></div>
        </div>

        {{-- Rata-rata Terlambat --}}
        <div class="lp-card-amber relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-orange-100">Rata-rata Terlambat</p>
            <p class="mt-2 text-2xl font-black">{{ $stats['rata_terlambat_menit'] }} <span class="text-sm font-bold">Menit</span></p>
        </div>

        {{-- Izin/Sakit --}}
        <div class="lp-card-sky relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-blue-100">Total Izin/Sakit</p>
            <p class="mt-2 text-2xl font-black">{{ $stats['total_izin_sakit'] }} <span class="text-sm font-bold">Hari</span></p>
        </div>

        {{-- Alpha --}}
        <div class="lp-card-rose relative overflow-hidden rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="lp-decor-10 absolute -right-6 -bottom-8 h-24 w-24 rounded-full"></div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-rose-100">Total Alpha</p>
            <p class="mt-2 text-2xl font-black">{{ $stats['total_alpha'] }} <span class="text-sm font-bold">Hari</span></p>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-slate-900">Tren Kehadiran Bulanan ({{ now()->year }})</h2>
            <span class="lp-badge-soft rounded-full px-3 py-1 text-[11px] font-bold">12 bulan</span>
        </div>
        <div class="mt-6 flex items-end gap-2 h-48">
            @php
                $bulanLabel = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            @endphp
            @foreach ($monthlyRate as $bulan => $rate)
                <div class="group flex-1 flex flex-col items-center justify-end gap-1">
                    @if ($rate > 0)
                        <span class="text-[10px] font-extrabold opacity-80 group-hover:opacity-100" style="color:#006191;">{{ $rate }}%</span>
                    @endif
                    <div class="lp-bar w-full rounded-t-md" style="height: {{ max(2, $rate) }}%"></div>
                    <span class="text-[10px] font-semibold text-slate-400">{{ $bulanLabel[$bulan - 1] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-slate-900">Daftar Ringkasan Absensi</h2>
            <span class="lp-badge-soft rounded-full px-4 py-1.5 text-xs font-bold">{{ $rekap->total() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[960px] w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="lp-thead text-[11px] uppercase tracking-wider text-white">
                        <th class="px-6 py-3.5 font-extrabold">No</th>
                        <th class="px-6 py-3.5 font-extrabold">Karyawan</th>
                        <th class="px-6 py-3.5 font-extrabold">NIP</th>
                        <th class="px-6 py-3.5 font-extrabold">Jabatan</th>
                        <th class="px-6 py-3.5 font-extrabold text-center">Hadir</th>
                        <th class="px-6 py-3.5 font-extrabold text-center">Terlambat</th>
                        <th class="px-6 py-3.5 font-extrabold text-center">Izin/Sakit</th>
                        <th class="px-6 py-3.5 font-extrabold text-center">Alpha</th>
                        <th class="px-6 py-3.5 font-extrabold text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rekap as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 text-slate-500">{{ $rekap->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="lp-avatar grid h-9 w-9 shrink-0 place-items-center rounded-full text-xs font-extrabold text-white shadow">
                                        {{ strtoupper(substr($item->karyawan->nama_karyawan ?? '-', 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $item->karyawan->nama_karyawan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->karyawan->nip ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->karyawan->jabatan ?? '-' }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">{{ $item->total_hadir }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-amber-600">{{ $item->total_terlambat }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">{{ $item->total_izin + $item->total_sakit }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-red-600">{{ $item->total_alpha }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold {{ $item->persentase >= 90 ? 'text-emerald-600' : ($item->persentase >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $item->persentase }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-sm text-slate-400">Tidak ada data untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rekap->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $rekap->links() }}
            </div>
        @endif
    </section>
</div>
@endsection