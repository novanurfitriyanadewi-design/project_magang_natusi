@extends('layouts.portal')

@section('title', 'Aturan Laporan Mingguan')

@section('content')
<div class="space-y-6 pb-8">
    <section class="mt-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-950 md:text-3xl">Aturan Laporan Mingguan</h1>
                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Baca ketentuan dari Admin dan unduh template laporan sebelum mengerjakan Laporan Mingguan.
                </p>
            </div>
            <a href="{{ route('peserta-magang.penugasan.index') }}"
               class="inline-flex w-fit items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                Buka Penugasan
            </a>
        </div>
    </section>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
        <div class="border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-5 py-5 sm:px-6">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-sky-700 shadow-sm ring-1 ring-sky-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Template Laporan Mingguan</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Template terbaru yang ditetapkan oleh Admin Peserta Magang.</p>
                </div>
            </div>
        </div>

        @if ($templateLaporan)
            @php
                $ext = strtoupper(pathinfo($templateLaporan->file_word, PATHINFO_EXTENSION) ?: 'DOCX');
            @endphp

            <div class="p-5 sm:p-6">
                <div class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-slate-50/80 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-start gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-blue-100 text-blue-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v6h6M9 13h6M9 17h6" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">File Template</p>
                            <h3 class="mt-1 break-words text-base font-semibold text-slate-900">{{ $templateLaporan->judul }}</h3>
                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                <span>Format {{ $ext }}</span>
                                <span>Diperbarui {{ $templateLaporan->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('peserta-magang.laporan-mingguan.template.download', $templateLaporan) }}"
                       class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M5 19h14" />
                        </svg>
                        Unduh Template
                    </a>
                </div>
            </div>
        @else
            <div class="p-6">
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                    <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-3 text-sm font-semibold text-slate-700">Template laporan belum tersedia.</p>
                    <p class="mt-1 text-xs text-slate-500">Admin Peserta Magang belum mengunggah template aktif.</p>
                </div>
            </div>
        @endif
    </section>

    <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
        <div class="border-b border-sky-100 px-5 py-5 sm:px-6">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-amber-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Aturan & Ketentuan Laporan</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Seluruh ketentuan dari Admin ditampilkan di bawah ini.</p>
                </div>
            </div>
        </div>

        <div class="p-5 sm:p-6">
            @if ($templateLaporan && trim((string) $templateLaporan->ketentuan) !== '')
                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 px-5 py-5 sm:px-6">
                    <div class="whitespace-pre-line break-words text-sm leading-7 text-slate-700">{{ $templateLaporan->ketentuan }}</div>
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center">
                    <p class="text-sm font-semibold text-slate-700">Belum ada aturan atau ketentuan laporan.</p>
                    <p class="mt-1 text-xs text-slate-500">Ketentuan akan muncul setelah Admin menyimpan template laporan.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-blue-100 bg-blue-50/80 px-5 py-4">
        <div class="flex items-start gap-3">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-blue-900">Pengumpulan laporan tetap melalui menu Penugasan.</p>
                <p class="mt-1 text-sm leading-6 text-blue-700">Cari item <strong>Laporan Mingguan</strong> pada minggu yang sedang dikerjakan, kemudian unggah file sesuai template dan ketentuan di atas.</p>
            </div>
        </div>
    </section>
</div>
@endsection
