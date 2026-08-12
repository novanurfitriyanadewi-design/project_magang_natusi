@extends('layouts.portal')

@section('title', 'Data Absensi - Natusi Admin')

@section('content')
@php
    $statusLabel = fn (string $status) => match ($status) {
        'hadir' => 'Hadir',
        'terlambat' => 'Terlambat',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpa' => 'Alpa',
        default => 'Belum Absensi',
    };

    $statusClass = fn (string $status) => match ($status) {
        'hadir' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'terlambat' => 'border-amber-200 bg-amber-50 text-amber-700',
        'izin' => 'border-violet-200 bg-violet-50 text-violet-700',
        'sakit' => 'border-blue-200 bg-blue-50 text-blue-700',
        'alpa' => 'border-rose-200 bg-rose-50 text-rose-700',
        default => 'border-slate-200 bg-slate-100 text-slate-600',
    };

    $statusDotClass = fn (string $status) => match ($status) {
        'hadir' => 'bg-emerald-500',
        'terlambat' => 'bg-amber-500',
        'izin' => 'bg-violet-500',
        'sakit' => 'bg-blue-500',
        'alpa' => 'bg-rose-500',
        default => 'bg-slate-400',
    };
@endphp

<div
    class="space-y-6"
    x-data="{
        detailOpen: false,
        mapOpen: false,
        detail: {},
        mapData: {},
        mapEmbedUrl: '',
        mapOpenUrl: '',
        openDetail(payload) {
            this.detail = payload;
            this.detailOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeDetail() {
            this.detailOpen = false;
            if (!this.mapOpen) document.body.classList.remove('overflow-hidden');
        },
        openMap(payload) {
            this.mapData = payload;
            this.mapEmbedUrl = `https://maps.google.com/maps?q=${payload.latitude},${payload.longitude}&z=16&output=embed`;
            this.mapOpenUrl = `https://www.google.com/maps?q=${payload.latitude},${payload.longitude}`;
            this.mapOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeMap() {
            this.mapOpen = false;
            this.mapEmbedUrl = '';
            if (!this.detailOpen) document.body.classList.remove('overflow-hidden');
        }
    }"
    @keydown.escape.window="closeDetail(); closeMap()"
>
    {{-- Header --}}
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Data Absensi</h1>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                Pantau absensi hari ini dan telusuri riwayat kehadiran peserta pada tanggal sebelumnya.
            </p>
        </div>
        <div>
            <p class="mt-1 text-sm font-extrabold text-slate-800">{{ $todayLabel }}</p>
        </div>
    </section>

    {{-- Data absensi hari ini --}}
    <section class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
        <header class="border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-5 py-5 sm:px-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Absensi Hari Ini</h2>
                    <p class="mt-1 text-sm text-slate-500">Data selalu menggunakan tanggal hari ini dan tidak tercampur dengan riwayat.</p>
                </div>
            </div>

            <nav class="mt-5 flex flex-wrap gap-2" aria-label="Kategori absensi hari ini">
                <a
                    href="{{ route('admin-peserta.absensi.index', array_merge(request()->except(['tab', 'today_page']), ['tab' => 'sudah_absen'])) }}"
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold transition {{ $todayTab === 'sudah_absen' ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)]' : 'border border-slate-200 bg-white text-slate-600 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' }}"
                >
                    Sudah Absen
                    <span class="rounded-full px-2 py-0.5 text-[10px] {{ $todayTab === 'sudah_absen' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $totalSudahAbsen }}</span>
                </a>
                <a
                    href="{{ route('admin-peserta.absensi.index', array_merge(request()->except(['tab', 'today_page']), ['tab' => 'belum_absen'])) }}"
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold transition {{ $todayTab === 'belum_absen' ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)]' : 'border border-slate-200 bg-white text-slate-600 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' }}"
                >
                    Belum Absen
                    <span class="rounded-full px-2 py-0.5 text-[10px] {{ $todayTab === 'belum_absen' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $totalBelumAbsen }}</span>
                </a>
            </nav>
        </header>

        <div class="overflow-x-auto">
            @if($todayTab === 'sudah_absen')
                <table class="w-full min-w-[980px] border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] font-bold uppercase tracking-[0.13em] text-slate-500">
                            <th class="px-5 py-4">Nama Peserta</th>
                            <th class="px-5 py-4">No. Telepon</th>
                            <th class="px-5 py-4">Jam Kehadiran</th>
                            <th class="px-5 py-4">Lokasi</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($todayAttendances as $attendance)
                            @php
                                $participant = $attendance->peserta;
                                $name = $participant?->user?->nama ?? '-';
                                $phone = $participant?->permintaan?->no_hp ?? $participant?->user?->phone ?? '-';
                                $institution = $participant?->permintaan?->nama_sekolah ?? $participant?->user?->university ?? '-';
                                $status = strtolower((string) $attendance->status);
                                $hasLocation = filled($attendance->latitude) && filled($attendance->longitude);
                                $locationText = $hasLocation
                                    ? (filled($attendance->jarak_meter) ? number_format((float) $attendance->jarak_meter, 0, ',', '.') . ' m dari titik kantor' : 'Koordinat tersedia')
                                    : ($attendance->keterangan ?: 'Lokasi tidak tersedia');
                                $detailPayload = [
                                    'nama' => $name,
                                    'instansi' => $institution,
                                    'telepon' => $phone,
                                    'tanggal' => $attendance->tanggal?->translatedFormat('d F Y') ?? '-',
                                    'jam' => $attendance->jam ? \Carbon\Carbon::parse($attendance->jam)->format('H:i') : '-',
                                    'status' => $statusLabel($status),
                                    'lokasi' => $locationText,
                                    'keterangan' => $attendance->keterangan ?: '-',
                                ];
                                $mapPayload = [
                                    'nama' => $name,
                                    'latitude' => (string) $attendance->latitude,
                                    'longitude' => (string) $attendance->longitude,
                                    'lokasi' => $locationText,
                                ];
                            @endphp
                            <tr class="transition hover:bg-sky-50/40">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-sky-100 to-cyan-100 text-sm font-extrabold text-sky-700 ring-1 ring-sky-200">
                                            {{ strtoupper(mb_substr($name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-extrabold text-slate-900">{{ $name }}</p>
                                            <p class="mt-0.5 max-w-[220px] truncate text-xs text-slate-500">{{ $institution }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-600">{{ $phone }}</td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-extrabold text-slate-800">{{ $attendance->jam ? \Carbon\Carbon::parse($attendance->jam)->format('H:i') : '-' }}</p>
                                    <p class="mt-0.5 text-[11px] text-slate-400">WIB</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="max-w-[240px] text-sm font-semibold text-slate-600">{{ $locationText }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-bold {{ $statusClass($status) }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusDotClass($status) }}"></span>
                                        {{ $statusLabel($status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-center gap-2">
                                        <button
                                            type="button"
                                            @click='openDetail(@json($detailPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT))'
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100"
                                        >
                                            <span class="material-symbols-outlined text-[17px]">visibility</span>
                                            View
                                        </button>
                                        @if($hasLocation)
                                            <button
                                                type="button"
                                                @click='openMap(@json($mapPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT))'
                                                class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 transition hover:bg-indigo-100"
                                            >
                                                <span class="material-symbols-outlined text-[17px]">location_on</span>
                                                Lokasi
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                        <span class="material-symbols-outlined text-[30px]">event_busy</span>
                                    </span>
                                    <p class="mt-4 text-sm font-bold text-slate-700">Belum ada absensi hari ini</p>
                                    <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah peserta melakukan absensi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="w-full min-w-[850px] border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] font-bold uppercase tracking-[0.13em] text-slate-500">
                            <th class="px-5 py-4">Nama Peserta</th>
                            <th class="px-5 py-4">Sekolah/Universitas</th>
                            <th class="px-5 py-4">No. Telepon</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($todayMissingParticipants as $participant)
                            @php
                                $name = $participant->user?->nama ?? '-';
                                $phone = $participant->permintaan?->no_hp ?? $participant->user?->phone ?? '-';
                                $institution = $participant->permintaan?->nama_sekolah ?? $participant->user?->university ?? '-';
                                $detailPayload = [
                                    'nama' => $name,
                                    'instansi' => $institution,
                                    'telepon' => $phone,
                                    'tanggal' => now()->translatedFormat('d F Y'),
                                    'jam' => '-',
                                    'status' => 'Belum Absensi',
                                    'lokasi' => '-',
                                    'keterangan' => 'Peserta belum mengirimkan absensi hari ini.',
                                ];
                            @endphp
                            <tr class="transition hover:bg-sky-50/40">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 text-sm font-extrabold text-slate-600 ring-1 ring-slate-200">
                                            {{ strtoupper(mb_substr($name, 0, 1)) }}
                                        </span>
                                        <p class="text-sm font-extrabold text-slate-900">{{ $name }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-600">{{ $institution }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-600">{{ $phone }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Belum Absensi
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button
                                        type="button"
                                        @click='openDetail(@json($detailPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT))'
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 text-emerald-500">
                                        <span class="material-symbols-outlined text-[30px]">done_all</span>
                                    </span>
                                    <p class="mt-4 text-sm font-bold text-slate-700">Semua peserta sudah memiliki data absensi</p>
                                    <p class="mt-1 text-xs text-slate-500">Tidak ada peserta aktif yang belum absensi hari ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <div class="border-t border-sky-100 bg-slate-50/60 px-5 py-4">
            @if($todayTab === 'sudah_absen')
                {{ $todayAttendances->links() }}
            @else
                {{ $todayMissingParticipants->links() }}
            @endif
        </div>
    </section>

    {{-- Modal detail --}}
    <template x-teleport="body">
        <div x-show="detailOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex h-screen w-screen items-center justify-center overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-md" @click.self="closeDetail()">
            <section x-show="detailOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="my-auto w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_rgba(15,23,42,0.30)]">
                <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">Detail Absensi</span>
                        <h3 class="mt-3 text-xl font-extrabold text-slate-950" x-text="detail.nama || '-' "></h3>
                        <p class="mt-1 text-sm text-slate-500" x-text="detail.instansi || '-' "></p>
                    </div>
                    <button type="button" @click="closeDetail()" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-rose-50 hover:text-rose-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </header>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-slate-400">No. Telepon</p>
                        <p class="mt-2 text-sm font-extrabold text-slate-800" x-text="detail.telepon || '-' "></p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-slate-400">Status</p>
                        <p class="mt-2 text-sm font-extrabold text-slate-800" x-text="detail.status || '-' "></p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-slate-400">Tanggal</p>
                        <p class="mt-2 text-sm font-extrabold text-slate-800" x-text="detail.tanggal || '-' "></p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-slate-400">Jam Kehadiran</p>
                        <p class="mt-2 text-sm font-extrabold text-slate-800" x-text="detail.jam || '-' "></p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 sm:col-span-2">
                        <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-slate-400">Lokasi</p>
                        <p class="mt-2 text-sm font-extrabold text-slate-800" x-text="detail.lokasi || '-' "></p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 sm:col-span-2">
                        <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-slate-400">Keterangan</p>
                        <p class="mt-2 text-sm leading-6 text-slate-700" x-text="detail.keterangan || '-' "></p>
                    </div>
                </div>

                <footer class="flex justify-end border-t border-slate-100 bg-slate-50 px-6 py-4">
                    <button type="button" @click="closeDetail()" class="rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-sky-700">Tutup</button>
                </footer>
            </section>
        </div>
    </template>

    {{-- Modal map --}}
    <template x-teleport="body">
        <div x-show="mapOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex h-screen w-screen items-center justify-center overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-md" @click.self="closeMap()">
            <section x-show="mapOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="my-auto w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_rgba(15,23,42,0.30)]">
                <header class="flex items-start justify-between gap-4 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 via-blue-50 to-cyan-50 px-6 py-5">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-indigo-700 ring-1 ring-indigo-200">Lokasi Absensi</span>
                        <h3 class="mt-3 text-xl font-extrabold text-slate-950" x-text="mapData.nama || '-' "></h3>
                        <p class="mt-1 text-sm text-slate-500" x-text="mapData.lokasi || '-' "></p>
                    </div>
                    <button type="button" @click="closeMap()" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-rose-50 hover:text-rose-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </header>

                <div class="p-4 sm:p-6">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-inner">
                        <iframe x-show="mapEmbedUrl" :src="mapEmbedUrl" class="h-[430px] w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi absensi"></iframe>
                    </div>
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-semibold text-slate-500">
                            Koordinat: <span class="text-slate-700" x-text="`${mapData.latitude || '-'}, ${mapData.longitude || '-'}`"></span>
                        </p>
                        <a :href="mapOpenUrl" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[19px]">open_in_new</span>
                            Buka di Google Maps
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </template>
</div>
@endsection
