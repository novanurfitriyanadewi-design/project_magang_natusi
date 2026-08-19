@extends('layouts.portal')

@section('title', 'Detail Pengumpulan Tugas')

@section('content')
<style>
    /* Tombol revisi dibuat dengan CSS lokal agar tetap terlihat tanpa rebuild Vite/Tailwind. */
    .btn-revisi-admin {
        background-color: #f97316 !important;
        color: #ffffff !important;
        border: 1px solid #f97316 !important;
    }
    .btn-revisi-admin:hover {
        background-color: #ea580c !important;
        border-color: #ea580c !important;
    }
    .btn-revisi-admin .material-symbols-outlined {
        color: #ffffff !important;
    }
</style>

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
        'smk_sija' => 'SMK SIJA',
        'kuliah_ti' => 'Teknik Informatika',
        'kuliah_si' => 'Sistem Informasi',
        'kuliah_ptik' => 'Pend Teknik Informatika',
        default => ucfirst(str_replace('_', ' ', (string) ($pengumpulan->peserta?->tingkat_pendidikan ?? '-'))),
    };

    $isLate = $pengumpulan->status === 'telat';
    $reviewStatus = (string) ($pengumpulan->status_review ?: 'disetujui');
    $reviewPending = $reviewStatus === 'menunggu_review';
    $needsRevision = $reviewStatus === 'perlu_revisi';
    $approved = $reviewStatus === 'disetujui';
    $previewableImage = in_array($file['extension'] ?? '', ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    $previewablePdf = ($file['extension'] ?? '') === 'pdf';
@endphp

<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <a href="{{ route('admin-peserta.pengumpulan-tugas.index') }}" class="mb-3 inline-flex items-center gap-2 text-sm font-bold text-sky-700 transition hover:text-sky-800">
                <span class="material-symbols-outlined text-[19px]">arrow_back</span>
                Kembali ke Data Pengumpulan
            </a>
            <h1 class="headline text-3xl font-extrabold tracking-tight text-slate-950">Detail Pengumpulan Tugas</h1>
            <p class="mt-2 text-sm text-slate-500">Periksa data peserta, jadwal, status, dan bukti file yang dikumpulkan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span @class([
                'inline-flex w-fit items-center gap-2 rounded-full border px-4 py-2 text-sm font-bold',
                'border-rose-200 bg-rose-50 text-rose-700' => $isLate,
                'border-sky-200 bg-sky-50 text-sky-700' => !$isLate,
            ])>
                <span class="h-2 w-2 rounded-full {{ $isLate ? 'bg-rose-500' : 'bg-sky-500' }}"></span>
                {{ $isLate ? 'Terlambat' : 'Sudah Mengumpulkan' }}
            </span>

            <span @class([
                'inline-flex w-fit items-center gap-2 rounded-full border px-4 py-2 text-sm font-bold',
                'border-amber-200 bg-amber-50 text-amber-700' => $reviewPending,
                'border-orange-200 bg-orange-50 text-orange-700' => $needsRevision,
                'border-emerald-200 bg-emerald-50 text-emerald-700' => $approved,
            ])>
                <span class="material-symbols-outlined text-[18px]">{{ $reviewPending ? 'hourglass_top' : ($needsRevision ? 'edit_note' : 'verified') }}</span>
                {{ $reviewPending ? 'Menunggu Koreksi' : ($needsRevision ? 'Perlu Revisi' : 'Disetujui') }}
            </span>
        </div>
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
                    <a href="{{ route('admin-peserta.pengumpulan-tugas.file', $pengumpulan) }}" target="_blank" class="inline-flex w-fit items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
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
                            <img src="{{ route('admin-peserta.pengumpulan-tugas.file', $pengumpulan) }}" alt="Bukti pengumpulan {{ $name }}" class="max-h-[650px] w-full object-contain">
                        </div>
                    @elseif($previewablePdf)
                        <iframe
                            src="{{ route('admin-peserta.pengumpulan-tugas.file', $pengumpulan) }}"
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
                                Format {{ strtoupper($file['extension'] ?: 'file') }} perlu dibuka dengan aplikasi yang sesuai. Gunakan tombol â€œBuka Fileâ€ untuk melihat atau mengunduh bukti.
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

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_18px_46px_rgba(15,23,42,0.08)]">
        <header class="border-b border-slate-100 bg-gradient-to-r from-amber-50 via-white to-white px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-amber-100 text-amber-700">
                    <span class="material-symbols-outlined">fact_check</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Koreksi Admin</h2>
                    <p class="mt-1 text-sm text-slate-500">Setujui hasil tugas atau kirim catatan revisi kepada peserta.</p>
                </div>
            </div>
        </header>

        <div class="p-6">
            @if($reviewPending)
                <form method="POST" action="{{ route('admin-peserta.pengumpulan-tugas.review', $pengumpulan) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="catatan_revisi" class="block text-sm font-semibold text-slate-700">Catatan Koreksi / Revisi</label>
                        <p class="mt-1 text-xs leading-5 text-slate-500">Wajib diisi jika tugas perlu diperbaiki. Catatan ini akan diterima peserta melalui portal dan email.</p>
                        <textarea id="catatan_revisi" name="catatan_revisi" rows="5" maxlength="2000" class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-700 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-100" placeholder="Contoh: Bagian flowchart belum sesuai. Perbaiki percabangan pada langkah ke-3 dan kirim ulang file.">{{ old('catatan_revisi', $pengumpulan->catatan_revisi) }}</textarea>
                        @error('catatan_revisi')
                            <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" name="aksi" value="revisi" onclick="return document.getElementById('catatan_revisi').value.trim() !== '' || (alert('Isi catatan revisi terlebih dahulu.'), false)" class="btn-revisi-admin inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-sm font-bold shadow-sm transition">
                            <span class="material-symbols-outlined text-[19px]">edit_note</span>
                            Minta Revisi
                        </button>
                        <button type="submit" name="aksi" value="disetujui" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
                            <span class="material-symbols-outlined text-[19px]">check_circle</span>
                            Setujui Tugas
                        </button>
                    </div>
                </form>
            @elseif($needsRevision)
                <div class="rounded-2xl border border-orange-200 bg-orange-50 p-5">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-orange-600">pending_actions</span>
                        <div>
                            <p class="font-bold text-orange-900">Menunggu peserta mengirim file revisi</p>
                            <p class="mt-2 text-sm leading-6 text-orange-800">{{ $pengumpulan->catatan_revisi ?: 'Admin meminta perbaikan tugas.' }}</p>
                            <p class="mt-3 text-xs text-orange-700">Setelah peserta mengirim ulang, status akan kembali menjadi <strong>Menunggu Koreksi</strong> dan tombol koreksi akan aktif lagi.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-emerald-600">verified</span>
                        <div>
                            <p class="font-bold text-emerald-900">Tugas sudah disetujui</p>
                            <p class="mt-1 text-sm text-emerald-700">Dikoreksi {{ $pengumpulan->reviewed_at?->translatedFormat('d F Y, H:i') ?? '-' }}. Tugas ini sudah dihitung selesai untuk progres mingguan peserta.</p>
                            @if((int) $pengumpulan->revisi_ke > 0)
                                <p class="mt-2 text-xs font-semibold text-emerald-700">Jumlah revisi: {{ $pengumpulan->revisi_ke }} kali.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
