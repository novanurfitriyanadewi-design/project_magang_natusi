@extends('layouts.portal')
@section('title', 'Laporan Absensi - CV Natusi')
@section('content')

<div class="p-6 space-y-6">
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#006191]">Laporan Absensi</h1>
            <p class="text-slate-500 text-sm">Ringkasan performa kehadiran seluruh peserta magang.</p>
        </div>
    </section>

    <section class="bg-white p-4 rounded-xl border border-slate-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="text-xs font-semibold text-slate-500">Kategori</label>
                <select name="kategori" class="w-full border border-slate-200 rounded-lg text-sm mt-1 p-2">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriList as $k)
                        <option value="{{ $k }}" @selected(($kategori ?? '') === $k)>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Status</label>
                <select name="status" class="w-full border border-slate-200 rounded-lg text-sm mt-1 p-2">
                    <option value="">Semua Status</option>
                    <option value="hadir" @selected(($status ?? '') === 'hadir')>Hadir</option>
                    <option value="terlambat" @selected(($status ?? '') === 'terlambat')>Terlambat</option>
                    <option value="izin" @selected(($status ?? '') === 'izin')>Izin</option>
                    <option value="sakit" @selected(($status ?? '') === 'sakit')>Sakit</option>
                    <option value="alfa" @selected(($status ?? '') === 'alfa')>Alfa</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Dari tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ $dariTgl ?? '' }}" class="w-full border border-slate-200 rounded-lg text-sm mt-1 p-2">
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Sampai tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampaiTgl ?? '' }}" class="w-full border border-slate-200 rounded-lg text-sm mt-1 p-2">
            </div>

            <button type="submit" class="bg-[#006191] text-white rounded-lg py-2.5 text-sm font-semibold hover:bg-[#004b70] transition-colors">Terapkan Filter</button>
        </form>
    </section>

    <section class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-lg font-semibold">Data Detail Peserta</h3>
            <form method="GET">
                <input type="hidden" name="kategori" value="{{ $kategori ?? '' }}">
                <input type="hidden" name="status" value="{{ $status ?? '' }}">
                <input type="hidden" name="dari_tanggal" value="{{ $dariTgl ?? '' }}">
                <input type="hidden" name="sampai_tanggal" value="{{ $sampaiTgl ?? '' }}">

                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari peserta..."
                       class="border border-slate-200 rounded-lg text-sm px-3 py-2 w-full sm:w-64">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Nama Peserta</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3 text-center">Total Hadir</th>
                        <th class="px-6 py-3 text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($rekap as $r)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">
                                <div class="font-semibold text-slate-800">{{ $r->peserta->user->nama ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $r->peserta->tingkat_pendidikan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="bg-slate-100 px-3 py-1 rounded-full text-xs text-[#006191] font-medium">{{ $r->peserta->tingkat_pendidikan ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-3 text-center font-medium">{{ $r->total_hadir + $r->total_terlambat }}/{{ $r->total_absen }}</td>
                            <td class="px-6 py-3">
                                <div class="flex flex-col items-center gap-1">
                                    @php
                                        $color = $r->persentase >= 90 ? 'text-green-600' : ($r->persentase >= 75 ? 'text-amber-600' : 'text-red-600');
                                        $bar   = $r->persentase >= 90 ? 'bg-green-500' : ($r->persentase >= 75 ? 'bg-amber-500' : 'bg-red-500');
                                    @endphp
                                    <span class="font-semibold text-sm {{ $color }}">{{ $r->persentase }}%</span>
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $bar }}" style="width: {{ $r->persentase }}%;"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200">
            {{ $rekap->links() }}
        </div>
    </section>

</div>
@endsection