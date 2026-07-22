@extends('layouts.portal')

@section('title', 'Detail Pengumpulan Tugas')

@section('content')
@php
    $name = $pengumpulan->peserta?->user?->nama
        ?? $pengumpulan->peserta?->permintaan?->nama_pemohon
        ?? 'Peserta tidak ditemukan';

    $email = $pengumpulan->peserta?->user?->email
        ?? $pengumpulan->peserta?->permintaan?->email
        ?? '-';

    $group = match ($pengumpulan->tugas?->target_peserta) {
        'smk_tkj' => 'SMK TKJ',
        'smk_rpl' => 'SMK RPL',
        'universitas' => 'Universitas',
        default => ucfirst(str_replace('_', ' ', (string) ($pengumpulan->peserta?->tingkat_pendidikan ?? '-'))),
    };

    $isLate = $pengumpulan->status === 'telat';
    $previewableImage = in_array($file['extension'] ?? '', ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    $previewablePdf = ($file['extension'] ?? '') === 'pdf';
@endphp

<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <a href="{{ route('admin.pengumpulan-tugas.index') }}" class="mb-3 inline-flex items-center gap-2 text-sm font-bold text-sky-700 transition hover:text-sky-800">
                <span class="material-symbols-outlined text-[19px]">arrow_back</span>
                Kembali ke Data Pengumpulan
            </a>
            <h1 class="headline text-3xl font-extrabold tracking-tight text-slate-950">Detail Pengumpulan Tugas</h1>
            <p class="mt-2 text-sm text-slate-500">Periksa data peserta, jadwal, status, dan bukti file yang dikumpulkan.</p>
        </div>

        <span @class([
            'inline-flex w-fit items-center gap-2 rounded-full border px-4 py-2 text-sm font-extrabold',
            'border-rose-200 bg-rose-50 text-rose-700' => $isLate,
            'border-emerald-200 bg-emerald-50 text-emerald-700' => !$isLate,
        ])>
            <span class="h-2 w-2 rounded-full {{ $isLate ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
            {{ $isLate ? 'Terlambat' : 'Mengumpulkan' }}
        </span>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <article class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_18px_46px_rgba(15,23,42,0.09)]">
            <header class="border-b border-slate-100 bg-gradient-to-r from-sky-50 via-white to-white px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="grid h-11 w-11 place-items-center rounded-2xl bg-sky-100 text-sky-700">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-950">Data Peserta dan Tugas</h2>
                        <p class="mt-1 text-sm text-slate-500">Informasi lengkap dari data pengumpulan.</p>
                    </div>
                </div>
            </header>

            <dl class="divide-y divide-slate-100 px-6">
                @foreach([
                    'Nama Peserta' => $name,
                    'Email' => $email,
                    'Jenjang' => $group,
                    'Sekolah / Kampus' => $pengumpulan->peserta?->permintaan?->nama_sekolah ?? $pengumpulan->peserta?->user?->university ?? '-',
                    'Jurusan' => $pengumpulan->peserta?->permintaan?->jurusan ?? $pengumpulan->peserta?->user?->major ?? '-',
                    'Minggu Ke' => $pengumpulan->tugas?->minggu_ke ?? '-',
                    'Kode Tugas' => $pengumpulan->tugas?->kode_tugas ?? '-',
                    'Nama Tugas' => $pengumpulan->tugas?->judul ?? '-',
                    'Materi / Laporan' => $pengumpulan->tugas?->materi ?? '-',
                    'Waktu Pengumpulan' => $pengumpulan->dikumpulkan_pada?->translatedFormat('d F Y, H:i') . ' WIB',
                    'Deadline' => $penugasan?->deadline?->translatedFormat('d F Y, H:i') . ' WIB',
                ] as $label => $value)
                    <div class="grid gap-1 py-4 sm:grid-cols-[150px_1fr] sm:gap-4">
                        <dt class="text-xs font-extrabold uppercase tracking-[0.1em] text-slate-400">{{ $label }}</dt>
                        <dd class="text-sm font-semibold leading-6 text-slate-800">{{ filled($value) ? $value : '-' }}</dd>
                    </div>
                @endforeach
            </dl>
        </article>

        <article class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_18px_46px_rgba(15,23,42,0.09)]">
            <header class="flex flex-col gap-3 border-b border-slate-100 bg-gradient-to-r from-indigo-50 via-white to-white px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="grid h-11 w-11 place-items-center rounded-2xl bg-indigo-100 text-indigo-700">
                        <span class="material-symbols-outlined">description</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-950">Bukti Pengumpulan</h2>
                        <p class="mt-1 text-sm text-slate-500">Tinjau file yang diunggah oleh peserta.</p>
                    </div>
                </div>

                @if($fileExists)
                    <a href="{{ route('admin.pengumpulan-tugas.file', $pengumpulan) }}" target="_blank" class="inline-flex w-fit items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        <span class="material-symbols-outlined text-[19px]">open_in_new</span>
                        Buka File
                    </a>
                @endif
            </header>

            <div class="p-6">
                @if($fileExists)
                    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Nama File</p>
                            <p class="mt-2 break-all text-sm font-bold text-slate-800">{{ $file['name'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Jenis File</p>
                            <p class="mt-2 text-sm font-bold uppercase text-slate-800">{{ $file['extension'] ?: '-' }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Ukuran</p>
                            <p class="mt-2 text-sm font-bold text-slate-800">{{ number_format(($file['size'] ?? 0) / 1024, 1) }} KB</p>
                        </div>
                    </div>

                    @if($previewableImage)
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                            <img src="{{ route('admin.pengumpulan-tugas.file', $pengumpulan) }}" alt="Bukti pengumpulan {{ $name }}" class="max-h-[650px] w-full object-contain">
                        </div>
                    @elseif($previewablePdf)
                        <iframe
                            src="{{ route('admin.pengumpulan-tugas.file', $pengumpulan) }}"
                            title="Bukti pengumpulan {{ $name }}"
                            class="h-[650px] w-full rounded-2xl border border-slate-200 bg-slate-100"
                        ></iframe>
                    @else
                        <div class="flex min-h-80 flex-col items-center justify-center rounded-2xl border border-dashed border-indigo-200 bg-indigo-50/50 px-6 text-center">
                            <div class="grid h-16 w-16 place-items-center rounded-2xl bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100">
                                <span class="material-symbols-outlined text-[34px]">draft</span>
                            </div>
                            <h3 class="mt-5 text-lg font-extrabold text-slate-900">Pratinjau browser tidak tersedia</h3>
                            <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">
                                Format {{ strtoupper($file['extension'] ?: 'file') }} perlu dibuka dengan aplikasi yang sesuai. Gunakan tombol “Buka File” untuk melihat atau mengunduh bukti.
                            </p>
                        </div>
                    @endif
                @else
                    <div class="flex min-h-80 flex-col items-center justify-center rounded-2xl border border-dashed border-rose-200 bg-rose-50/40 px-6 text-center">
                        <span class="material-symbols-outlined text-[48px] text-rose-400">file_off</span>
                        <h3 class="mt-4 text-lg font-extrabold text-slate-900">File tidak ditemukan</h3>
                        <p class="mt-2 text-sm text-slate-500">Path bukti tersimpan, tetapi file tidak tersedia pada storage publik.</p>
                    </div>
                @endif
            </div>
        </article>
    </section>
</div>
@endsection
