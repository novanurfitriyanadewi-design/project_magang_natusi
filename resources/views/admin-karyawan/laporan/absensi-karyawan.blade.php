@extends('layouts.portal')

@section('title', 'Laporan Absensi Karyawan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <header>
            <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-950">Laporan Absensi</h1>
            <p class="mt-1 text-sm text-slate-500">Rekapitulasi performa kehadiran karyawan CV Natusi.</p>
        </header>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('admin-karyawan.laporan.absensi') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ $dariTgl }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampaiTgl }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Jabatan</label>
                <select name="jabatan" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua Jabatan</option>
                    @foreach ($jabatanList as $j)
                        <option value="{{ $j }}" {{ $jabatan === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Cari Nama Karyawan</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800">
                <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                Terapkan
            </button>
            <a href="{{ route('admin-karyawan.laporan.absensi.export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-slate-800">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Export Excel
            </a>
        </form>
    </section>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm border-l-4 border-l-blue-700">
            <p class="text-xs text-slate-500">Total Kehadiran</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $stats['tingkat_kehadiran'] }}%</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm border-l-4 border-l-red-500">
            <p class="text-xs text-slate-500">Rata-rata Terlambat</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $stats['rata_terlambat_menit'] }} Menit</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs text-slate-500">Total Izin/Sakit</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $stats['total_izin_sakit'] }} Hari</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm border-l-4 border-l-red-500">
            <p class="text-xs text-slate-500">Total Alpha</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $stats['total_alpha'] }} Hari</p>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900">Tren Kehadiran Bulanan ({{ now()->year }})</h2>
        <div class="mt-6 flex items-end gap-2 h-48">
            @php
                $bulanLabel = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            @endphp
            @foreach ($monthlyRate as $bulan => $rate)
                <div class="flex-1 flex flex-col items-center justify-end gap-1">
                    @if ($rate > 0)
                        <span class="text-[10px] font-bold text-blue-700">{{ $rate }}%</span>
                    @endif
                    <div class="w-full rounded-t-md bg-blue-600" style="height: {{ max(2, $rate) }}%"></div>
                    <span class="text-[10px] text-slate-400">{{ $bulanLabel[$bulan - 1] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-slate-900">Daftar Ringkasan Absensi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[960px] w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-4 font-semibold">No</th>
                        <th class="px-6 py-4 font-semibold">Karyawan</th>
                        <th class="px-6 py-4 font-semibold">NIP</th>
                        <th class="px-6 py-4 font-semibold">Jabatan</th>
                        <th class="px-6 py-4 font-semibold text-center">Hadir</th>
                        <th class="px-6 py-4 font-semibold text-center">Terlambat</th>
                        <th class="px-6 py-4 font-semibold text-center">Izin/Sakit</th>
                        <th class="px-6 py-4 font-semibold text-center">Alpha</th>
                        <th class="px-6 py-4 font-semibold text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rekap as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 text-slate-500">{{ $rekap->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
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