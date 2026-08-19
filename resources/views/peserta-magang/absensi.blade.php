@extends('layouts.portal')

@section('title', 'Presensi Harian')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl">Presensi Harian</h1>
        <p class="text-sm text-slate-500">Silakan lakukan pencatatan kehadiran Anda hari ini.</p>
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
                    <form method="POST" action="{{ route('peserta-magang.absensi.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ status: 'hadir' }" id="form-absensi-peserta">
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

                        <div x-show="status === 'hadir'" x-cloak class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-blue-600 text-xl">location_on</span>
                                    <div>
                                        <span class="font-semibold text-sm text-slate-900">Lokasi Saat Ini</span>
                                        <p class="text-xs text-slate-500">Aktifkan izin lokasi/GPS lalu tekan tombol deteksi.</p>
                                    </div>
                                </div>
                                <button type="button" id="btn-detect-location"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                    <span class="material-symbols-outlined text-sm">my_location</span>
                                    <span id="location-button-text">Deteksi Lokasi</span>
                                </button>
                            </div>

                            <div id="location-status-box" class="min-h-[150px] w-full rounded-lg border border-slate-200 bg-white flex flex-col items-center justify-center px-5 text-center">
                                <span id="location-status-icon" class="material-symbols-outlined text-4xl text-blue-600 mb-2">location_searching</span>
                                <p id="location-status-title" class="text-sm font-semibold text-slate-700">Lokasi belum dideteksi</p>
                                <p id="location-status-message" class="mt-1 max-w-xl text-xs leading-5 text-slate-500">
                                    Tekan “Deteksi Lokasi” dan izinkan browser mengakses lokasi Anda.
                                </p>
                                <p id="location-coordinate" class="mt-2 hidden text-xs font-medium text-blue-700"></p>
                            </div>

                            <input type="hidden" name="latitude" id="attendance-latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="attendance-longitude" value="{{ old('longitude') }}">
                            <input type="hidden" name="alamat" id="attendance-address" value="{{ old('alamat') }}">

                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">Foto Kehadiran</label>
                                <input type="file" name="foto" accept="image/*" capture="user" class="block w-full text-sm">
                                @error('foto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            @error('latitude')
                                <p class="text-xs text-red-600 mt-2">Lokasi wajib dideteksi sebelum submit.</p>
                            @enderror
                            @error('longitude')
                                <p class="text-xs text-red-600 mt-1">Koordinat lokasi belum lengkap. Silakan deteksi ulang lokasi.</p>
                            @enderror

                            <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-800">
                                <span class="font-semibold">Catatan:</span> deteksi lokasi browser membutuhkan HTTPS atau localhost. Jika halaman dibuka melalui alamat IP HTTP seperti <span class="font-mono">http://192.168.x.x:8000</span>, browser dapat memblokir akses lokasi.
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-blue-600/20">
                            Submit Kehadiran
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
                                        {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') . ' WIB' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($item->status === 'hadir' && $item->latitude && $item->longitude)
                                            <a href="https://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="text-blue-600 hover:underline">Lihat Map</a>
                                        @elseif ($item->surat_sakit)
                                            <a href="{{ Storage::url($item->surat_sakit) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                                        @elseif ($item->surat_izin)
                                            <a href="{{ Storage::url($item->surat_izin) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
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
                        Absensi 'Hadir' wajib dilakukan dari lokasi kantor yang terdaftar.
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-absensi-peserta');
    const detectButton = document.getElementById('btn-detect-location');
    if (!form || !detectButton) return;

    const latitudeInput = document.getElementById('attendance-latitude');
    const longitudeInput = document.getElementById('attendance-longitude');
    const addressInput = document.getElementById('attendance-address');
    const title = document.getElementById('location-status-title');
    const message = document.getElementById('location-status-message');
    const icon = document.getElementById('location-status-icon');
    const coordinate = document.getElementById('location-coordinate');
    const buttonText = document.getElementById('location-button-text');

    const setLocationState = (type, heading, description, coords = null) => {
        title.textContent = heading;
        message.textContent = description;
        coordinate.classList.add('hidden');
        coordinate.textContent = '';

        if (type === 'success') {
            icon.textContent = 'location_on';
            icon.className = 'material-symbols-outlined text-4xl text-emerald-600 mb-2';
            if (coords) {
                coordinate.textContent = `Lat: ${coords.lat.toFixed(6)}, Lng: ${coords.lng.toFixed(6)} · Akurasi ±${Math.round(coords.accuracy)} m`;
                coordinate.classList.remove('hidden');
            }
        } else if (type === 'error') {
            icon.textContent = 'location_off';
            icon.className = 'material-symbols-outlined text-4xl text-red-500 mb-2';
        } else {
            icon.textContent = 'location_searching';
            icon.className = 'material-symbols-outlined text-4xl text-blue-600 mb-2';
        }
    };

    const detectLocation = () => {
        if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            setLocationState(
                'error',
                'Lokasi diblokir karena koneksi belum aman',
                `Halaman ini dibuka melalui ${location.origin}. Browser hanya mengizinkan lokasi pada HTTPS atau localhost. Gunakan HTTPS saat di-hosting, atau localhost saat tes di komputer ini.`
            );
            return;
        }

        if (!('geolocation' in navigator)) {
            setLocationState('error', 'Perangkat tidak mendukung lokasi', 'Browser/perangkat ini tidak menyediakan fitur geolocation. Coba gunakan browser lain atau perangkat yang memiliki GPS/lokasi.');
            return;
        }

        buttonText.textContent = 'Mendeteksi...';
        detectButton.disabled = true;
        detectButton.classList.add('opacity-60', 'cursor-not-allowed');
        setLocationState('loading', 'Sedang mendeteksi lokasi', 'Tunggu beberapa saat dan pastikan GPS/lokasi perangkat aktif.');

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy || 0;

                latitudeInput.value = lat;
                longitudeInput.value = lng;
                addressInput.value = `Koordinat ${lat.toFixed(6)}, ${lng.toFixed(6)}`;

                setLocationState('success', 'Lokasi berhasil dideteksi', 'Koordinat sudah tersimpan dan siap digunakan untuk absensi.', { lat, lng, accuracy });
                buttonText.textContent = 'Perbarui Lokasi';
                detectButton.disabled = false;
                detectButton.classList.remove('opacity-60', 'cursor-not-allowed');
            },
            function (error) {
                latitudeInput.value = '';
                longitudeInput.value = '';
                addressInput.value = '';

                let heading = 'Lokasi gagal dideteksi';
                let description = 'Silakan coba lagi.';

                if (error.code === error.PERMISSION_DENIED) {
                    heading = 'Izin lokasi ditolak';
                    description = 'Izinkan akses Location/Lokasi untuk situs ini melalui ikon di samping alamat browser, lalu tekan Deteksi Lokasi lagi.';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    heading = 'Lokasi tidak tersedia';
                    description = 'Aktifkan GPS/lokasi perangkat dan Wi-Fi/data lokasi, lalu coba kembali.';
                } else if (error.code === error.TIMEOUT) {
                    heading = 'Deteksi lokasi terlalu lama';
                    description = 'Sinyal lokasi belum didapat. Pindah ke area dengan sinyal GPS/Wi-Fi yang lebih baik lalu coba lagi.';
                }

                setLocationState('error', heading, description);
                buttonText.textContent = 'Coba Lagi';
                detectButton.disabled = false;
                detectButton.classList.remove('opacity-60', 'cursor-not-allowed');
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    };

    detectButton.addEventListener('click', detectLocation);

    form.addEventListener('submit', function (event) {
        const status = form.querySelector('input[name="status"]:checked')?.value;
        if (status === 'hadir' && (!latitudeInput.value || !longitudeInput.value)) {
            event.preventDefault();
            setLocationState('error', 'Lokasi belum tersedia', 'Untuk status Hadir, tekan Deteksi Lokasi terlebih dahulu sampai koordinat berhasil ditemukan.');
            document.getElementById('location-status-box')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>

@endsection
