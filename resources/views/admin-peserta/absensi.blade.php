@extends('layouts.portal')

@section('title', 'Data Absensi - CV Natusi Admin Portal')

@section('content')
<div x-data="{
    toggleMap(index) {
        const row = document.getElementById('map-row-' + index);
        if (row) row.classList.toggle('hidden');
    }
}">

    <section class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Data Absensi</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau kehadiran peserta magang dan karyawan secara real-time.</p>
    </section>

    @php
        $totalHadir = $absensi->filter(fn($a) => $a->status === 'Hadir')->count();
        $totalTerlambat = $absensi->filter(fn($a) => $a->status === 'Terlambat')->count();
        $totalIzinSakit = $absensi->filter(fn($a) => in_array($a->status, ['Izin', 'Sakit']))->count();
        $totalAlpa = $absensi->filter(fn($a) => $a->status === 'Alpa')->count();
        $totalPeserta = $absensi->count();
    @endphp

    <section class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute left-0 top-0 h-full w-1 bg-emerald-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">Total Hadir</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalHadir }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $totalPeserta > 0 ? round(($totalHadir / $totalPeserta) * 100) : 0 }}% dari total</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute left-0 top-0 h-full w-1 bg-amber-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">Terlambat</p>
                    <p class="mt-1 text-3xl font-bold text-amber-600">{{ $totalTerlambat }}</p>
                    <p class="mt-1 text-xs text-slate-500">Perlu evaluasi</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute left-0 top-0 h-full w-1 bg-sky-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">Izin & Sakit</p>
                    <p class="mt-1 text-3xl font-bold text-sky-700">{{ $totalIzinSakit }}</p>
                    <p class="mt-1 text-xs text-slate-500">Total pengajuan hari ini</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-sky-50 text-sky-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M12 3a4 4 0 100 8 4 4 0 000-8z"/></svg>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute left-0 top-0 h-full w-1 bg-rose-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">Alpa</p>
                    <p class="mt-1 text-3xl font-bold text-rose-600">{{ $totalAlpa }}</p>
                    <p class="mt-1 text-xs text-slate-500">Belum absen</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-rose-50 text-rose-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Absensi Hari Ini</h2>
                <p class="mt-0.5 text-sm text-slate-500">Filter berdasarkan nama, status, atau tanggal.</p>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto lg:items-center">
                <div class="relative w-full sm:w-44">
                    <select name="status" class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-white pl-4 pr-10 text-sm font-semibold text-slate-600 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        <option value="">Semua Status</option>
                        <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                        <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative min-w-0 sm:w-60">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau instansi..." class="h-11 w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 outline-none transition placeholder:font-medium placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                </div>
                <button class="inline-flex h-11 items-center justify-center rounded-xl bg-sky-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-100">Terapkan</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] font-bold uppercase tracking-[0.13em] text-slate-500">
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4">Asal Instansi</th>
                        <th class="px-6 py-4">Jam Absen</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Keterangan Lokasi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($absensi as $index => $item)
                        @php
                            $user = $item->peserta->user ?? null;
                            $nama = $user->nama ?? '-';
                            $instansi = $user->instansi ?? $item->peserta->instansi ?? '-';
                            $waktu = \Carbon\Carbon::parse($item->jam ?? $item->created_at)->format('H:i');
                            $status = $item->status ?? 'Alpa';
                            $lokasi = $item->alamat ?? '-';
                            $isHadir = in_array($status, ['Hadir', 'Terlambat']);
                        @endphp
                        <tr class="transition hover:bg-sky-50/40">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-sky-100 to-cyan-100 text-sm font-extrabold text-sky-700 ring-1 ring-sky-200">{{ strtoupper(mb_substr($nama, 0, 1)) }}</span>
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">{{ $nama }}</p>
                                        <p class="text-xs text-slate-500">{{ $item->peserta->tingkat_pendidikan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $instansi }}</td>
                            <td class="px-6 py-4 font-mono text-sm">{{ $waktu }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold ring-1
                                    {{ $status == 'Hadir' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : '' }}
                                    {{ $status == 'Terlambat' ? 'bg-amber-50 text-amber-700 ring-amber-200' : '' }}
                                    {{ $status == 'Izin' ? 'bg-sky-50 text-sky-700 ring-sky-200' : '' }}
                                    {{ $status == 'Sakit' ? 'bg-violet-50 text-violet-700 ring-violet-200' : '' }}
                                    {{ $status == 'Alpa' ? 'bg-rose-50 text-rose-700 ring-rose-200' : '' }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $lokasi }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($isHadir)
                                    <button onclick="toggleMap('map-row-{{ $index }}')" class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 transition hover:text-sky-800">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Lihat Lokasi
                                    </button>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>

                        <tr id="map-row-{{ $index }}" class="hidden bg-slate-50/50">
                            <td colspan="6" class="px-6 py-4">
                                <div class="h-48 w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-inner">
                                    <div class="flex h-full items-center justify-center text-sm text-slate-500">
                                        <svg class="mr-2 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        [Simulasi Peta Lokasi Absen: {{ $nama }}]
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </span>
                                <p class="mt-4 text-sm font-bold text-slate-700">Tidak ada data absensi hari ini</p>
                                <p class="mt-1 text-xs text-slate-500">Ubah filter atau coba lagi nanti.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($absensi->hasPages())
            <div class="border-t border-sky-100 bg-sky-50/50 px-6 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs font-semibold text-slate-500">
                        Menampilkan {{ $absensi->firstItem() ?? 0 }}–{{ $absensi->lastItem() ?? 0 }} dari {{ $absensi->total() }} data
                    </p>
                    <nav class="inline-flex w-fit overflow-hidden rounded-xl border border-sky-200 bg-white shadow-sm" aria-label="Navigasi halaman">
                        {{ $absensi->links() }}
                    </nav>
                </div>
            </div>
        @endif
    </section>
</div>

<script>
    function toggleMap(id) {
        const row = document.getElementById(id);
        if (row) row.classList.toggle('hidden');
    }
</script>

@endsection