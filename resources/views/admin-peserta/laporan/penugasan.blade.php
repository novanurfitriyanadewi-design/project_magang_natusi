@extends('layouts.portal')
@section('title', 'Laporan Penugasan')
@section('content')

    <section class="mb-6">
        <h1 class="headline text-2xl md:text-3xl font-bold text-slate-900 mb-1">Laporan Penugasan</h1>
        <p class="text-sm text-slate-500">Pantau progres pengumpulan tugas seluruh peserta magang.</p>
    </section>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-4 p-4">
        <form method="GET" action="{{ route('admin-peserta.laporan.penugasan') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari judul tugas..."
                class="rounded-lg border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600 md:col-span-2"
            >
            <select name="jenis_tugas" class="rounded-lg border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600">
                <option value="">Semua Jenis Tugas</option>
                @foreach ($jenisTugasList as $jenis)
                    <option value="{{ $jenis }}" @selected($jenisTugas == $jenis)>{{ ucfirst($jenis) }}</option>
                @endforeach
            </select>
            <select name="status_filter" class="rounded-lg border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600">
                <option value="">Semua Status</option>
                <option value="aktif" @selected($statusFilter == 'aktif')>Aktif</option>
                <option value="nonaktif" @selected($statusFilter == 'nonaktif')>Nonaktif</option>
                <option value="selesai" @selected($statusFilter == 'selesai')>Selesai</option>
            </select>
            <div class="md:col-span-4 flex justify-end gap-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Terapkan Filter</button>
                @if ($search || $jenisTugas || $statusFilter)
                    <a href="{{ route('admin-peserta.laporan.penugasan') }}" class="px-5 py-2 text-slate-500 text-sm font-semibold rounded-lg hover:bg-slate-100 transition-colors">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="headline text-lg font-semibold text-slate-900">Data Penugasan Detail</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nama Tugas</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Pengumpulan</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Jumlah Terlambat</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tugasList as $tugas)
                        @php
                            $statusLabel = match ($tugas->status) {
                                'selesai' => 'Selesai',
                                'nonaktif' => 'Nonaktif',
                                default => 'Aktif',
                            };
                            $statusClass = match ($tugas->status) {
                                'selesai' => 'bg-green-100 text-green-700',
                                'nonaktif' => 'bg-slate-100 text-slate-500',
                                default => 'bg-blue-100 text-blue-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $tugas->judul }}</p>
                                <p class="text-xs text-slate-400">{{ ucfirst($tugas->jenis_tugas) }} &middot; {{ $tugas->instansi }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $tugas->total_submitted }}/{{ $tugas->total_ditugaskan }}</td>
                            <td class="px-6 py-4 text-sm {{ $tugas->total_terlambat > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">{{ $tugas->total_terlambat }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 {{ $statusClass }} rounded text-[10px] font-bold uppercase">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data tugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tugasList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $tugasList->links() }}
            </div>
        @endif
    </div>
@endsection
