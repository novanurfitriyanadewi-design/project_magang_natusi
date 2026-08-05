@extends('layouts.portal')

@section('title', 'Detail Absensi Karyawan')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('admin-karyawan.absensi-karyawan.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-sky-600 hover:text-sky-800 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m0 0 6 6m-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Daftar
            </a>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                Detail Absensi Karyawan
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Informasi lengkap kehadiran karyawan.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin-karyawan.absensi-karyawan.edit', $absensiKaryawan) }}" class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-amber-600 active:scale-95">
                <span class="material-symbols-outlined text-[20px]">edit</span>
                Edit
            </a>
        </div>
    </section>

    @php
        $statusMap = [
            'hadir' => ['label' => 'Hadir', 'bg' => '#dcfce7', 'text' => '#166534', 'icon' => 'task_alt'],
            'terlambat' => ['label' => 'Terlambat', 'bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'warning'],
            'izin' => ['label' => 'Izin', 'bg' => '#fef9c3', 'text' => '#854d0e', 'icon' => 'event_busy'],
            'sakit' => ['label' => 'Sakit', 'bg' => '#e0f2fe', 'text' => '#075985', 'icon' => 'medical_services'],
            'alpha' => ['label' => 'Alpha', 'bg' => '#f1f5f9', 'text' => '#475569', 'icon' => 'person_off'],
        ];
        $badge = $statusMap[$absensiKaryawan->status] ?? $statusMap['alpha'];
    @endphp

    <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-sky-600 to-blue-600 px-6 py-5 text-white">
            <div class="flex items-center gap-4">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-[32px]">badge</span>
                </span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-sky-100">Data Absensi</p>
                    <h2 class="mt-1 text-xl font-extrabold">{{ $absensiKaryawan->karyawan->nama_karyawan ?? '-' }}</h2>
                    <p class="mt-0.5 text-sm text-white/80">
                        {{ \Illuminate\Support\Carbon::parse($absensiKaryawan->tanggal)->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Karyawan</p>
                    <p class="mt-1.5 text-sm font-bold text-slate-800">{{ $absensiKaryawan->karyawan->nama_karyawan ?? '-' }}</p>
                    @if ($absensiKaryawan->karyawan->nip)
                        <p class="mt-0.5 text-xs text-slate-500">NIP: {{ $absensiKaryawan->karyawan->nip }}</p>
                    @endif
                    @if ($absensiKaryawan->karyawan->jabatan)
                        <p class="mt-0.5 text-xs text-slate-500">{{ $absensiKaryawan->karyawan->jabatan }}</p>
                    @endif
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Tanggal</p>
                    <p class="mt-1.5 text-sm font-bold text-slate-800">
                        {{ \Illuminate\Support\Carbon::parse($absensiKaryawan->tanggal)->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">
                        Dicatat: {{ $absensiKaryawan->created_at->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Jam Masuk</p>
                    <p class="mt-1.5 text-sm font-bold text-slate-800">{{ $absensiKaryawan->jam_masuk ?? '--:--' }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Jam Pulang</p>
                    <p class="mt-1.5 text-sm font-bold text-slate-800">{{ $absensiKaryawan->jam_pulang ?? '--:--' }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Status</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-extrabold" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['text'] }};">
                            <span class="material-symbols-outlined text-[16px]">{{ $badge['icon'] }}</span>
                            {{ $badge['label'] }}
                        </span>
                    </div>
                </div>

                @if ($absensiKaryawan->keterangan)
                <div class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Keterangan</p>
                    <p class="mt-1.5 text-sm text-slate-700">{{ $absensiKaryawan->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
            <a href="{{ route('admin-karyawan.absensi-karyawan.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                Kembali
            </a>
            <a href="{{ route('admin-karyawan.absensi-karyawan.edit', $absensiKaryawan) }}" class="rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5">
                Edit Data
            </a>
        </div>
    </section>
</div>
@endsection

