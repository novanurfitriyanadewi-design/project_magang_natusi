@php
    
@endphp
@extends('layouts.portal')
@extends('layouts.portal')

@section('title', 'Data Absensi - CV Natusi Admin Portal')

@section('content')

{{-- Statistik Ringkasan Absensi Hari Ini --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 animate-fade-in">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-600"></div>
        <div>
            <p class="text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Total Hadir</p>
            <h3 class="text-3xl font-bold text-slate-900 mt-2">124</h3>
            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px] text-green-600">trending_up</span>
                85% dari total peserta
            </p>
        </div>
        <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-600">
            <span class="material-symbols-outlined">check_circle</span>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-600"></div>
        <div>
            <p class="text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Terlambat</p>
            <h3 class="text-3xl font-bold text-red-600 mt-2">12</h3>
            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px] text-red-500">warning</span>
                Perlu evaluasi
            </p>
        </div>
        <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center text-red-600">
            <span class="material-symbols-outlined">schedule</span>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-500"></div>
        <div>
            <p class="text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Izin & Sakit</p>
            <h3 class="text-3xl font-bold text-slate-700 mt-2">12</h3>
            <p class="text-xs text-slate-400 mt-1">Total pengajuan hari ini</p>
        </div>
        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-600">
            <span class="material-symbols-outlined">medical_services</span>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
        <div>
            <p class="text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Belum Absen</p>
            <h3 class="text-3xl font-bold text-slate-700 mt-2">8</h3>
            <p class="text-xs text-slate-400 mt-1">Menunggu konfirmasi</p>
        </div>
        <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center text-amber-500">
            <span class="material-symbols-outlined">pending</span>
        </div>
    </div>
</section>

{{-- Kotak Pembungkus Utama Data --}}
<section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/75 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-3.5">Nama Peserta</th>
                    <th class="px-6 py-3.5">Asal Instansi</th>
                    <th class="px-6 py-3.5">Jam Absen</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5">Keterangan Lokasi</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                @forelse($data_absensi as $index => $item)
                    <tr class="hover:bg-slate-50/75 transition duration-150">
                        <td class="px-6 py-4 font-medium text-slate-950">
                            <div class="font-semibold">{{ $item['nama'] }}</div>
                            <div class="text-xs text-slate-400 font-normal">{{ $item['role'] }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $item['instansi'] }}</td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $item['jam'] }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $item['status'] == 'Hadir' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10' : '' }}
                                {{ $item['status'] == 'Terlambat' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/10' : '' }}
                                {{ $item['status'] == 'Izin' ? 'bg-sky-50 text-sky-700 ring-1 ring-sky-600/10' : '' }}
                                {{ $item['status'] == 'Alpa' ? 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/10' : '' }}
                            ">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs">{{ $item['lokasi'] }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($item['status'] == 'Hadir' || $item['status'] == 'Terlambat')
                                <button onclick="toggleMap('map-row-{{ $index }}')" class="inline-flex items-center gap-1 text-xs font-bold text-[#006191] hover:text-[#004f77] transition duration-150">
                                    <span class="material-symbols-outlined text-base">pin_drop</span> Lihat Lokasi
                                </button>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- Row untuk Dropdown Peta Google Maps --}}
                    <tr id="map-row-{{ $index }}" class="hidden bg-slate-50/50">
                        <td colspan="6" class="px-6 py-4">
                            <div class="w-full h-48 rounded-xl border border-slate-200 overflow-hidden shadow-inner bg-slate-100 flex items-center justify-center">
                                <span class="text-xs text-slate-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">map</span> [Simulasi Peta Lokasi Absen {{ $item['nama'] }}]
                                </span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 font-medium">
                            Data nama tidak ditemukan atau tidak sesuai kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer Info --}}
    <div class="p-4 border-t border-slate-200 flex items-center justify-between bg-slate-50">
        <span class="text-xs font-medium text-slate-500">Menampilkan {{ count($data_absensi) }} peserta terfilter</span>
    </div>
</section>

{{-- Script Dropdown Map --}}
<script>
    function toggleMap(id) {
        const row = document.getElementById(id);
        if (row.classList.contains('hidden')) {
            document.querySelectorAll('[id^="map-"]').forEach(r => r.classList.add('hidden'));
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    }
</script>

@endsection