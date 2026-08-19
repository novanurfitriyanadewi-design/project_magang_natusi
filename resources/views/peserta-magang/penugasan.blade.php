@extends('layouts.portal')

@section('title', 'Penugasan - CV Natusi')

@section('content')
<style>
    .penugasan-two-column {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1.5rem;
        align-items: start;
    }

    .penugasan-list-panel,
    .penugasan-detail-panel {
        min-width: 0;
    }


    /* Tombol revisi dibuat dengan CSS lokal agar tetap terlihat meskipun
       utility Tailwind warna oranye belum masuk ke build Vite. */
    .revision-file-input {
        width: 100%;
        margin-top: 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.75rem;
        background: #fff;
        padding: 0.45rem;
        font-size: 0.875rem;
        color: #334155;
    }

    .revision-file-input::file-selector-button {
        border: 0;
        border-radius: 0.6rem;
        background: #f97316;
        color: #fff;
        font-weight: 700;
        padding: 0.65rem 1rem;
        margin-right: 1rem;
        cursor: pointer;
    }

    .revision-file-input::file-selector-button:hover {
        background: #ea580c;
    }

    .revision-submit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 42px;
        margin-top: 1rem;
        border: 0;
        border-radius: 0.75rem;
        background: #f97316 !important;
        color: #fff !important;
        padding: 0.65rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 700;
        line-height: 1.2;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12);
        cursor: pointer;
    }

    .revision-submit-btn:hover {
        background: #ea580c !important;
    }

    @media (min-width: 1024px) {
        .penugasan-two-column {
            grid-template-columns: minmax(360px, 0.9fr) minmax(520px, 1.35fr);
        }

        /*
         * Panel kanan tetap mengikuti viewport, tetapi isi detail memiliki
         * scroll sendiri. Sebelumnya sticky tanpa max-height/overflow membuat
         * bagian bawah detail tidak dapat dijangkau ketika kontennya panjang.
         */
        .penugasan-detail-panel {
            position: sticky;
            top: 6rem;
            max-height: calc(100vh - 7.25rem);
            overflow-y: auto;
            overflow-x: hidden;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
            padding-right: 0.35rem;
        }

        .penugasan-detail-panel::-webkit-scrollbar {
            width: 7px;
        }

        .penugasan-detail-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .penugasan-detail-panel::-webkit-scrollbar-thumb {
            background: #a8bac8;
            border-radius: 999px;
        }
    }
</style>
<div class="space-y-6">
    @if(session('success'))
        <div class="flex items-start justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            <div class="flex items-start gap-2">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-lg leading-none text-emerald-700">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <div class="flex items-start gap-2">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-950 md:text-3xl">Penugasan</h1>
            <p class="mt-1 text-sm text-slate-500">
                Materi hanya untuk dipelajari. Yang wajib dikumpulkan adalah <strong>Tugas Materi</strong> dan <strong>Laporan Mingguan</strong>.
            </p>
        </div>

        @if($currentWeek)
            <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">
                <span class="material-symbols-outlined text-[18px]">lock_open</span>
                Sedang dikerjakan: Minggu {{ $currentWeek }}
            </div>
        @elseif($assignments->isNotEmpty())
            <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">
                <span class="material-symbols-outlined text-[18px]">task_alt</span>
                Semua tugas & laporan selesai
            </div>
        @endif
    </header>

    <div class="penugasan-two-column">
        {{-- KIRI: daftar penugasan --}}
        <aside class="penugasan-list-panel space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Data Penugasan</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Pilih materi/tugas untuk melihat detail di sebelah kanan.</p>
                </div>
                <form method="GET" action="{{ route('peserta-magang.penugasan.index') }}">
                    <select name="minggu" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 focus:border-blue-500 focus:ring-blue-100">
                        <option value="all" @selected($selectedMinggu === 'all')>Semua Minggu</option>
                        @foreach($availableWeeks as $week)
                            <option value="{{ $week }}" @selected((string) $selectedMinggu === (string) $week)>Minggu {{ $week }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="space-y-3">
                @forelse($assignments as $assignment)
                    @php
                        $task = $assignment->tugas;
                        $submission = $submissions->get($assignment->tugas_id);
                        $week = (int) ($task?->minggu_ke ?? 0);
                        $category = (string) ($task?->kategori_tugas ?? 'tugas');
                        $isMaterial = $category === 'materi';
                        $isCollectable = in_array($category, ['tugas', 'laporan'], true);
                        $reviewStatus = (string) ($submission?->status_review ?? '');
                        $isApproved = $submission && in_array($reviewStatus, ['', 'disetujui'], true);
                        $isWaitingReview = $submission && $reviewStatus === 'menunggu_review';
                        $needsRevision = $submission && $reviewStatus === 'perlu_revisi';
                        $isCompleted = $isCollectable && ($assignment->status === 'selesai' || $isApproved);
                        $isActive = $assignment->status === 'aktif' && !$isCompleted;
                        $isLocked = $assignment->status === 'terjadwal' && !$isCompleted;
                        $isSkipped = $assignment->status === 'dilewati';
                        $isSelected = $detailAssignment?->id_penugasan === $assignment->id_penugasan;
                    @endphp

                    <a href="{{ route('peserta-magang.penugasan.index', array_filter(['penugasan_id' => $assignment->id_penugasan, 'minggu' => $selectedMinggu])) }}"
                       class="block rounded-2xl border p-4 transition {{ $isSelected ? 'border-blue-300 bg-blue-50/70 shadow-sm' : 'border-slate-200 bg-white hover:border-sky-200 hover:shadow-sm' }} {{ $isLocked ? 'opacity-75' : '' }}">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            @if($week > 0)
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide text-slate-600">Minggu {{ $week }}</span>
                            @endif

                            @if($isMaterial)
                                <span class="inline-flex items-center gap-1 rounded-full bg-cyan-50 px-2.5 py-1 text-[11px] font-bold text-cyan-700 ring-1 ring-cyan-200">
                                    <span class="material-symbols-outlined text-[14px]">menu_book</span>Materi
                                </span>
                            @elseif($category === 'laporan')
                                <span class="inline-flex items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-[11px] font-bold text-violet-700 ring-1 ring-violet-200">
                                    <span class="material-symbols-outlined text-[14px]">description</span>Laporan Mingguan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 ring-1 ring-amber-200">
                                    <span class="material-symbols-outlined text-[14px]">assignment</span>Tugas Materi
                                </span>
                            @endif

                            @if($needsRevision)
                                <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2.5 py-1 text-[11px] font-bold text-orange-700 ring-1 ring-orange-200">
                                    <span class="material-symbols-outlined text-[14px]">edit_note</span>Perlu Revisi
                                </span>
                            @elseif($isWaitingReview)
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 ring-1 ring-amber-200">
                                    <span class="material-symbols-outlined text-[14px]">hourglass_top</span>Menunggu Koreksi
                                </span>
                            @elseif($isCompleted)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>Disetujui
                                </span>
                            @elseif($isLocked)
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-500 ring-1 ring-slate-200">
                                    <span class="material-symbols-outlined text-[14px]">lock</span>Terkunci
                                </span>
                            @elseif($isActive && $isCollectable)
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700 ring-1 ring-blue-200">
                                    <span class="material-symbols-outlined text-[14px]">lock_open</span>Terbuka
                                </span>
                            @elseif($isSkipped)
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 ring-1 ring-amber-200">Dilewati</span>
                            @endif
                        </div>

                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] {{ $isMaterial ? 'text-cyan-700' : ($category === 'laporan' ? 'text-violet-700' : 'text-amber-700') }}">
                            {{ $task?->materi ?: ($isMaterial ? 'Materi' : ($category === 'laporan' ? 'Laporan Mingguan' : 'Tugas Materi')) }}
                        </p>
                        <h3 class="mt-1.5 text-base leading-6 text-slate-900 {{ $isMaterial ? 'font-extrabold' : 'font-normal' }}">{{ $task?->judul ?? 'Penugasan' }}</h3>

                        @if($isMaterial)
                            <p class="mt-2 text-xs font-medium text-cyan-700">Untuk dipelajari · tidak perlu dikumpulkan</p>
                        @elseif($isLocked)
                            <p class="mt-2 text-xs text-slate-500">Selesaikan tugas/laporan minggu sebelumnya terlebih dahulu.</p>
                        @else
                            <p class="mt-2 text-xs text-slate-500">
                                @if($assignment->deadline)
                                    Deadline {{ $assignment->deadline->translatedFormat('d M Y, H:i') }}
                                @else
                                    Tanpa deadline
                                @endif
                            </p>
                        @endif
                    </a>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
                        <span class="material-symbols-outlined text-[46px] text-slate-300">assignment</span>
                        <p class="mt-2 text-sm font-semibold text-slate-600">Belum ada penugasan untuk Anda.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- KANAN: detail + pengumpulan --}}
        <section class="penugasan-detail-panel">
            @if($detailAssignment && $detailAssignment->tugas)
                @php
                    $detailTask = $detailAssignment->tugas;
                    $detailSubmission = $submissions->get($detailAssignment->tugas_id);
                    $detailWeek = (int) ($detailTask->minggu_ke ?? 0);
                    $detailCategory = (string) ($detailTask->kategori_tugas ?? 'tugas');
                    $detailIsMaterial = $detailCategory === 'materi';
                    $detailIsCollectable = in_array($detailCategory, ['tugas', 'laporan'], true);
                    $detailReviewStatus = (string) ($detailSubmission?->status_review ?? '');
                    $detailApproved = $detailSubmission && in_array($detailReviewStatus, ['', 'disetujui'], true);
                    $detailWaitingReview = $detailSubmission && $detailReviewStatus === 'menunggu_review';
                    $detailNeedsRevision = $detailSubmission && $detailReviewStatus === 'perlu_revisi';
                    $detailCompleted = $detailIsCollectable && ($detailAssignment->status === 'selesai' || $detailApproved);
                    $detailActive = $detailAssignment->status === 'aktif' && !$detailCompleted;
                    $detailLocked = $detailAssignment->status === 'terjadwal' && !$detailCompleted;
                @endphp

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-sky-50 to-cyan-50 px-6 py-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-extrabold uppercase tracking-[0.16em] text-sky-700">
                                        {{ $detailIsMaterial ? 'Detail Materi' : 'Detail Penugasan' }}
                                    </span>
                                    @if($detailWeek > 0)
                                        <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">Minggu {{ $detailWeek }}</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-bold uppercase tracking-[0.14em] {{ $detailIsMaterial ? 'text-cyan-700' : ($detailCategory === 'laporan' ? 'text-violet-700' : 'text-amber-700') }}">
                                    {{ $detailTask->materi ?: ($detailIsMaterial ? 'Materi' : ($detailCategory === 'laporan' ? 'Laporan Mingguan' : 'Tugas Materi')) }}
                                </p>
                                <h2 class="mt-1 text-xl leading-7 text-slate-950 {{ $detailIsMaterial ? 'font-extrabold' : 'font-normal' }}">{{ $detailTask->judul }}</h2>
                            </div>

                            @if($detailIsCollectable)
                                <div class="shrink-0 text-right">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Deadline</p>
                                    <p class="mt-1 text-sm font-bold {{ $detailAssignment->deadline && $detailAssignment->deadline->isPast() && !$detailCompleted ? 'text-rose-600' : 'text-slate-700' }}">
                                        {{ $detailAssignment->deadline?->translatedFormat('d F Y, H:i') ?? '-' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($detailLocked)
                        <div class="p-8">
                            <div class="mx-auto max-w-lg rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center">
                                <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-white text-slate-400 shadow-sm ring-1 ring-slate-200">
                                    <span class="material-symbols-outlined text-[32px]">lock</span>
                                </div>
                                <h3 class="mt-4 text-lg font-extrabold text-slate-900">Minggu {{ $detailWeek }} masih terkunci</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500">
                                    Selesaikan seluruh <strong>Tugas Materi</strong> dan <strong>Laporan Mingguan</strong>
                                    pada Minggu {{ $currentWeek ?? max(1, $detailWeek - 1) }} terlebih dahulu.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="space-y-6 p-6">
                            @if($detailIsMaterial)
                                <div class="rounded-2xl border border-cyan-100 bg-cyan-50/70 p-5">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-cyan-700">menu_book</span>
                                        <div>
                                            <p class="font-bold text-cyan-900">Materi pembelajaran</p>
                                            <p class="mt-1 text-sm leading-6 text-cyan-800">
                                                Materi ini hanya perlu dipelajari. <strong>Tidak ada file yang perlu dikumpulkan</strong> dari item ini.
                                                Pengumpulan dilakukan pada item berlabel <strong>Tugas Materi</strong> atau <strong>Laporan Mingguan</strong>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div>
                                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-500">Instruksi Pengerjaan</h3>
                                    <div class="mt-3 whitespace-pre-line rounded-2xl bg-slate-50 p-5 text-sm leading-7 text-slate-700 ring-1 ring-slate-100">
                                        {{ $detailTask->keterangan ?: 'Kerjakan sesuai judul dan arahan penugasan yang diberikan admin.' }}
                                    </div>
                                </div>
                            @endif

                            @if($detailTask->file_tugas)
                                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-blue-100 bg-blue-50/70 p-4">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-blue-600">description</span>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $detailIsMaterial ? 'Lampiran Materi' : 'Lampiran Penugasan' }}</p>
                                            <p class="text-xs text-slate-500">Dokumen pendukung dari admin.</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $detailTask->file_tugas) }}" target="_blank" class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-blue-700 shadow-sm ring-1 ring-blue-200 hover:bg-blue-50">Buka File</a>
                                </div>
                            @endif

                            @if($detailCategory === 'laporan' && $detailAssignment->templateLaporan)
                                <div class="rounded-2xl border border-violet-100 bg-violet-50 p-4">
                                    <p class="text-sm font-bold text-violet-800">Template Laporan Mingguan</p>
                                    <p class="mt-1 text-xs leading-5 text-violet-600">Gunakan template laporan yang telah ditentukan admin, lalu unggah hasilnya pada form pengumpulan di bawah.</p>
                                </div>
                            @endif

                            @if($detailIsMaterial)
                                {{-- Materi sengaja tidak memiliki form upload. --}}
                            @elseif($detailNeedsRevision)
                                <div class="rounded-2xl border border-orange-200 bg-orange-50 p-5">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-orange-600">edit_note</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-orange-900">Admin meminta revisi</p>
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-orange-800">{{ $detailSubmission->catatan_revisi ?: 'Silakan perbaiki tugas sesuai arahan Admin.' }}</p>
                                            <p class="mt-2 text-xs text-orange-700">Minggu berikutnya tetap terkunci sampai file revisi dikirim dan disetujui Admin.</p>
                                            <a href="{{ route('peserta-magang.penugasan.pengumpulan.file', $detailSubmission) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-orange-800 underline underline-offset-2">
                                                <span class="material-symbols-outlined text-[16px]">open_in_new</span>Buka file terakhir
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('peserta-magang.penugasan.store', $detailTask->id_tugas) }}" method="POST" enctype="multipart/form-data" class="rounded-2xl border border-orange-200 bg-white p-5">
                                    @csrf
                                    <label class="block text-sm font-bold text-slate-800">Upload File Revisi</label>
                                    <p class="mt-1 text-xs text-slate-500">PDF, DOC/DOCX, XLS/XLSX, ZIP, atau RAR. Maksimal 25 MB.</p>
                                    <input type="file" name="file_jawaban" required accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" class="revision-file-input">
                                    <button type="submit" class="revision-submit-btn">
                                        <span class="material-symbols-outlined text-[18px]">upload</span>Kirim File Revisi
                                    </button>
                                </form>
                            @elseif($detailWaitingReview)
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-amber-600">hourglass_top</span>
                                        <div>
                                            <p class="font-bold text-amber-900">Menunggu koreksi Admin</p>
                                            <p class="mt-1 text-sm leading-6 text-amber-800">File sudah berhasil dikirim {{ $detailSubmission?->dikumpulkan_pada?->translatedFormat('d F Y, H:i') ?? '-' }}. Anda belum perlu mengirim ulang sampai Admin memberikan hasil koreksi.</p>
                                            @if((int) $detailSubmission->revisi_ke > 0)
                                                <p class="mt-2 text-xs font-semibold text-amber-700">File revisi ke-{{ $detailSubmission->revisi_ke }} sedang diperiksa.</p>
                                            @endif
                                            <a href="{{ route('peserta-magang.penugasan.pengumpulan.file', $detailSubmission) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 underline underline-offset-2">
                                                <span class="material-symbols-outlined text-[16px]">open_in_new</span>Buka file yang dikirim
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @elseif($detailCompleted)
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-emerald-600">verified</span>
                                        <div>
                                            <p class="font-bold text-emerald-800">{{ $detailCategory === 'laporan' ? 'Laporan sudah disetujui Admin' : 'Tugas sudah disetujui Admin' }}</p>
                                            <p class="mt-1 text-sm text-emerald-700">Tugas ini sudah dihitung selesai untuk progres mingguan.</p>
                                            @if($detailSubmission)
                                                <a href="{{ route('peserta-magang.penugasan.pengumpulan.file', $detailSubmission) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 underline underline-offset-2">
                                                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>Buka file terakhir
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @elseif($detailActive)
                                <form action="{{ route('peserta-magang.penugasan.store', $detailTask->id_tugas) }}" method="POST" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    @csrf
                                    <label class="block text-sm font-bold text-slate-800">
                                        {{ $detailCategory === 'laporan' ? 'Upload Laporan Mingguan' : 'Upload Hasil Tugas Materi' }}
                                    </label>
                                    <p class="mt-1 text-xs text-slate-500">Setelah dikirim, tugas akan menunggu koreksi Admin. PDF, DOC/DOCX, XLS/XLSX, ZIP, atau RAR. Maksimal 25 MB.</p>
                                    <input type="file" name="file_jawaban" required accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" class="mt-4 block w-full rounded-xl border border-slate-200 bg-white p-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-bold file:text-white hover:file:bg-blue-700">
                                    <button type="submit" class="mt-4 inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-blue-700">
                                        <span class="material-symbols-outlined text-[18px]">upload</span>
                                        {{ $detailCategory === 'laporan' ? 'Kumpulkan Laporan' : 'Kumpulkan Tugas' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                    <span class="material-symbols-outlined text-[52px] text-slate-300">assignment</span>
                    <p class="mt-3 text-sm font-semibold text-slate-500">Pilih materi atau tugas di sebelah kiri untuk melihat detail.</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
