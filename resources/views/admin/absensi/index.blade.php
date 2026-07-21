@extends('layouts.portal')

@section('title', 'Data Absensi - Natusi Admin')
@section('page-title', 'Data Absensi')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h3 class="text-3xl font-bold tracking-tight text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Data Absensi</h3>
    <p class="text-sm text-slate-500 mt-1">Monitoring kehadiran harian peserta magang secara real-time.</p>
</div>

{{-- Statistik --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- Hadir --}}
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#006191]"></div>
        <div class="flex justify-between items-start">
            <span class="text-[11px] font-semibold tracking-wider text-slate-500">TOTAL HADIR</span>
            <span class="material-symbols-outlined text-[#006191]">check_circle</span>
        </div>
        <div>
            <h3 class="text-2xl font-extrabold headline" style="font-family: 'Manrope', sans-serif;">{{ $totalHadir }}</h3>
            <p class="text-xs text-slate-500">{{ $persenHadir }}% dari total peserta</p>
        </div>
    </div>

    {{-- Terlambat --}}
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#e41f25]"></div>
        <div class="flex justify-between items-start">
            <span class="text-[11px] font-semibold tracking-wider text-slate-500">TERLAMBAT</span>
            <span class="material-symbols-outlined text-[#e41f25]">schedule</span>
        </div>
        <div>
            <h3 class="text-2xl font-extrabold text-red-600 headline" style="font-family: 'Manrope', sans-serif;">{{ $totalTerlambat }}</h3>
            <p class="text-xs text-red-500">Perlu evaluasi</p>
        </div>
    </div>

    {{-- Izin & Sakit --}}
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#6f7881]"></div>
        <div class="flex justify-between items-start">
            <span class="text-[11px] font-semibold tracking-wider text-slate-500">IZIN & SAKIT</span>
            <span class="material-symbols-outlined text-slate-600">medical_services</span>
        </div>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-600 headline" style="font-family: 'Manrope', sans-serif;">{{ $totalIzinSakit }}</h3>
            <p class="text-xs text-slate-500">Total pengajuan hari ini</p>
        </div>
    </div>

    {{-- Belum Absen --}}
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#bec7d2]"></div>
        <div class="flex justify-between items-start">
            <span class="text-[11px] font-semibold tracking-wider text-slate-500">BELUM ABSEN</span>
            <span class="material-symbols-outlined text-slate-600">pending</span>
        </div>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-600 headline" style="font-family: 'Manrope', sans-serif;">{{ $totalBelumAbsen }}</h3>
            <p class="text-xs text-slate-500">Menunggu konfirmasi</p>
        </div>
    </div>
</section>

{{-- Filter & Tabel --}}
<section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <form method="GET" action="{{ route('admin.absensi.index') }}" class="p-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50">
        <div class="flex items-center gap-4 flex-grow max-w-2xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau instansi..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#006191] focus:border-[#006191] text-sm">
            
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm">
            
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm min-w-[150px]">
                <option value="">Semua Status</option>
                <option value="hadir" @selected(request('status') === 'hadir')>Hadir</option>
                <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
                <option value="izin" @selected(request('status') === 'izin')>Izin</option>
                <option value="sakit" @selected(request('status') === 'sakit')>Sakit</option>
                <option value="alfa" @selected(request('status') === 'alfa')>Alfa</option>
                <option value="belum_absen" @selected(request('status') === 'belum_absen')>Belum Absen</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">filter_list</span> Terapkan
        </button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase">Peserta</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase">Instansi</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase text-center">Jam</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase">Lokasi</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($pesertas as $peserta)
                    @php
                        $absen = $peserta->absensi->first();
                        $nama = $peserta->user->nama ?? '-';
                        $instansi = $peserta->permintaan->nama_sekolah ?? ($peserta->user->university ?? '-');
                        $jurusan = $peserta->permintaan->jurusan ?? null;
                        $initial = $nama !== '-' ? strtoupper(substr($nama, 0, 1)) : '?';
                        $status = $absen->status ?? 'belum_absen';
                        
                        $statusClass = match ($status) {
                            'hadir' => 'bg-green-100 text-green-700',
                            'terlambat' => 'bg-red-100 text-red-700',
                            'izin', 'sakit' => 'bg-yellow-100 text-yellow-700',
                            default => 'bg-slate-200 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 {{ !$absen ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#006191]/10 text-[#006191] flex items-center justify-center font-bold text-xs">{{ $initial }}</div>
                                <div>
                                    <div class="text-sm font-semibold">{{ $nama }}</div>
                                    @if($jurusan)<div class="text-[11px] text-slate-500">{{ $jurusan }}</div>@endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $instansi }}</td>
                        <td class="px-6 py-4 text-center text-sm font-mono">{{ $absen && $absen->jam ? \Carbon\Carbon::parse($absen->jam)->format('H:i') : '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-[10px] font-bold rounded uppercase {{ $statusClass }}">
                                {{ str_replace('_', ' ', $status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $absen && $absen->jarak_meter ? $absen->jarak_meter.'m' : '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($absen && $absen->latitude)
                                <button type="button" onclick="toggleMap('map-{{ $peserta->id_peserta }}')" class="text-[#006191] hover:underline text-xs font-semibold">Lihat</button>
                            @else
                                <span class="text-slate-300 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                    @if($absen && $absen->latitude)
                        <tr class="hidden bg-slate-50" id="map-{{ $peserta->id_peserta }}">
                            <td colspan="6" class="px-6 py-4 text-sm text-slate-600">
                                <strong>Koordinat:</strong> {{ $absen->latitude }}, {{ $absen->longitude }} | 
                                @if($absen->keterangan) <strong>Info:</strong> {{ $absen->keterangan }} @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-200 bg-slate-50">
        {{ $pesertas->links() }}
    </div>
</section>

<script>
    function toggleMap(id) {
        document.querySelectorAll('[id^="map-"]').forEach(r => r.classList.add('hidden'));
        document.getElementById(id).classList.toggle('hidden');
    }
</script>
@endsection