@extends('layouts.portal')

@section('title', 'Presensi Harian')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl">Presensi Harian</h1>
        <p class="text-sm text-slate-500">Presensi "Hadir" hanya dapat dilakukan dari area kantor CV Natusi.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">edit_calendar</span>
                    Input Kehadiran
                </h2>

                @if ($sudahAbsenHariIni)
                    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-700 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined">check_circle</span>
                        Anda sudah melakukan presensi untuk hari ini. Sampai jumpa besok!
                    </div>
                @else
                    <form
                        method="POST"
                        action="{{ route('karyawan.absensi.store') }}"
                        enctype="multipart/form-data"
                        class="space-y-6"
                        x-data="{
                            status: 'hadir',
                            lat: null,
                            lng: null,
                            locating: false,
                            locationError: false,
                            officeLat: {{ $officeLat }},
                            officeLng: {{ $officeLng }},
                            radius: {{ $radiusMeters }},
                            distance: null,

                            haversine(lat1, lng1, lat2, lng2) {
                                const R = 6371000; // meter
                                const toRad = (v) => v * Math.PI / 180;
                                const dLat = toRad(lat2 - lat1);
                                const dLng = toRad(lng2 - lng1);
                                const a = Math.sin(dLat/2) ** 2 +
                                          Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                                          Math.sin(dLng/2) ** 2;
                                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                                return R * c;
                            },

                            detectLocation() {
                                this.locating = true;
                                this.locationError = false;
                                navigator.geolocation.getCurrentPosition(
                                    (pos) => {
                                        this.lat = pos.coords.latitude;
                                        this.lng = pos.coords.longitude;
                                        this.distance = this.haversine(this.lat, this.lng, this.officeLat, this.officeLng);
                                        this.locating = false;
                                    },
                                    () => {
                                        this.locating = false;
                                        this.locationError = true;
                                    },
                                    { enableHighAccuracy: true, timeout: 10000 }
                                );
                            },

                            get inRange() {
                                return this.distance !== null && this.distance <= this.radius;
                            },

                            get canSubmit() {
                                if (this.status !== 'hadir') return true;
                                return this.inRange;
                            }
                        }"
                        x-init="detectLocation()"
                    >
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative group cursor-pointer">
                                <input type="radio" name="status" value="hadir" x-model="status" class="peer sr-only" checked>
                                <div class="p-4 rounded-xl border border-slate-200 flex flex-col items-center gap-2 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 group-hover:bg-slate-50">
                                    <span class="material-symbols-outlined text-3xl text-blue-600">check_circle</span>
                                    <span class="font-semibold text-sm">Hadir</span>
                                </div>
                            </label>
                            <label class="relative group cursor-pointer">
                                <input type="radio" name="status" value="sakit" x-model="status" class="peer sr-only">
                                <div class="p-4 rounded-xl border border-slate-200 flex flex-col items-center gap-2 transition-all peer-checked:border-rose-600 peer-checked:bg-rose-50 group-hover:bg-slate-50">
                                    <span class="material-symbols-outlined text-3xl text-rose-600">medical_services</span>
                                    <span class="font-semibold text-sm">Sakit</span>
                                </div>
                            </label>
                            <label class="relative group cursor-pointer">
                                <input type="radio" name="status" value="izin" x-model="status" class="peer sr-only">
                                <div class="p-4 rounded-xl border border-slate-200 flex flex-col items-center gap-2 transition-all peer-checked:border-slate-600 peer-checked:bg-slate-100 group-hover:bg-slate-50">
                                    <span class="material-symbols-outlined text-3xl text-slate-600">assignment_late</span>
                                    <span class="font-semibold text-sm">Izin</span>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div x-show="status === 'sakit' || status === 'izin'" x-cloak>
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Lampiran Bukti (Dokumen/Foto)</label>
                            <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:bg-slate-50 transition-colors cursor-pointer">
                                <input type="file" name="bukti" id="file-input" class="hidden">
                                <label for="file-input" class="cursor-pointer">
                                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">cloud_upload</span>
                                    <p class="text-sm text-slate-500">Klik untuk unggah atau seret file ke sini</p>
                                    <p class="text-xs text-slate-400 mt-1">PDF, JPG, atau PNG (Maks. 5MB)</p>
                                </label>
                            </div>
                            @error('bukti')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="status === 'sakit' || status === 'izin'" x-cloak>
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="w-full rounded-xl border border-slate-200 p-3 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none" placeholder="Contoh: Demam, izin acara keluarga, dsb."></textarea>
                        </div>

                        <div x-show="status === 'hadir'" x-cloak class="rounded-xl p-4 border" :class="inRange ? 'bg-green-50 border-green-200' : 'bg-rose-50 border-rose-200'">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-xl" :class="inRange ? 'text-green-600' : 'text-rose-600'">location_on</span>
                                    <span class="font-semibold text-sm">Lokasi Saat Ini</span>
                                </div>
                                <button type="button" @click="detectLocation()" class="text-blue-600 text-xs font-semibold hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">refresh</span>
                                    <span x-text="locating ? 'Mendeteksi...' : 'Perbarui'"></span>
                                </button>
                            </div>

                            <template x-if="locationError">
                                <p class="text-sm text-rose-600">Gagal mendeteksi lokasi. Pastikan izin lokasi browser diaktifkan.</p>
                            </template>

                            <template x-if="!locationError && lat && lng">
                                <div>
                                    <p class="text-sm font-medium" :class="inRange ? 'text-green-700' : 'text-rose-700'" x-text="inRange ? 'Anda berada di dalam area kantor.' : 'Anda berada di luar area kantor (' + Math.round(distance) + ' m dari kantor).'"></p>
                                    <p class="text-xs text-slate-500 mt-1" x-text="'Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6)"></p>
                                </div>
                            </template>

                            <template x-if="!locationError && (!lat || !lng)">
                                <p class="text-sm text-slate-500">Mendeteksi lokasi Anda...</p>
                            </template>

                            <input type="hidden" name="latitude" :value="lat">
                            <input type="hidden" name="longitude" :value="lng">

                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">Foto Kehadiran</label>
                                <input type="file" name="foto" accept="image/*" capture="user" class="block w-full text-sm">
                                @error('foto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            @error('latitude')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            :disabled="!canSubmit"
                            :class="canSubmit ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-slate-300 cursor-not-allowed'"
                            class="w-full text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-blue-600/20"
                        >
                            <span x-show="canSubmit">Submit Kehadiran</span>
                            <span x-show="!canSubmit">Di Luar Area Kantor — Tidak Dapat Submit</span>
                        </button>
                    </form>
                @endif
            </section>

            <section class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">history</span>
                        Riwayat Kehadiran
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 font-semibold text-sm text-slate-700">Tanggal</th>
                                <th class="px-6 py-4 font-semibold text-sm text-slate-700">Status</th>
                                <th class="px-6 py-4 font-semibold text-sm text-slate-700">Waktu</th>
                                <th class="px-6 py-4 font-semibold text-sm text-slate-700">Lokasi/Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($riwayat as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badge = match ($item->status) {
                                                'hadir' => 'bg-green-100 text-green-800',
                                                'sakit' => 'bg-yellow-100 text-yellow-800',
                                                'izin'  => 'bg-slate-100 text-slate-700',
                                                default => 'bg-slate-100 text-slate-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') . ' WIB' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($item->status === 'hadir' && $item->latitude && $item->longitude)
                                            <a href="https://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="text-blue-600 hover:underline">Lihat Map</a>
                                        @elseif ($item->bukti)
                                            <a href="{{ Storage::url($item->bukti) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                        Belum ada riwayat presensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($riwayat->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </section>
        </div>

        <div class="space-y-6">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 text-white shadow-lg shadow-blue-200/40">
                <h3 class="text-lg font-bold mb-4">Statistik Bulan Ini</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="opacity-80 text-sm">Kehadiran</span>
                        <span class="font-bold">{{ $stats['total_hadir'] }}/{{ $stats['total_hari_kerja'] }} Hari</span>
                    </div>
                    @php
                        $persen = $stats['total_hari_kerja'] > 0
                            ? min(100, round(($stats['total_hadir'] / $stats['total_hari_kerja']) * 100))
                            : 0;
                    @endphp
                    <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                        <div class="bg-white h-full" style="width: {{ $persen }}%"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-white/10">
                        <div>
                            <p class="text-xs opacity-70">Sakit</p>
                            <p class="text-lg font-bold">{{ $stats['total_sakit'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs opacity-70">Izin</p>
                            <p class="text-lg font-bold">{{ $stats['total_izin'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600 text-xl">info</span>
                    Peraturan Absensi
                </h3>
                <ul class="text-sm text-slate-500 space-y-3">
                    <li class="flex gap-2">
                        <span class="text-blue-600 font-bold">•</span>
                        Batas waktu absensi pagi adalah pukul 08:30 WIB.
                    </li>
                    <li class="flex gap-2">
                        <span class="text-blue-600 font-bold">•</span>
                        Absensi 'Hadir' hanya dapat dilakukan dalam radius {{ $radiusMeters }} meter dari kantor CV Natusi.
                    </li>
                    <li class="flex gap-2">
                        <span class="text-blue-600 font-bold">•</span>
                        Status 'Sakit' wajib melampirkan surat dokter asli.
                    </li>
                    <li class="flex gap-2">
                        <span class="text-blue-600 font-bold">•</span>
                        Keterlambatan lebih dari 3 kali akan mempengaruhi evaluasi performa.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection