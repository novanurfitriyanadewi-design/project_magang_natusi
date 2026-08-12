@extends('layouts.portal')

@section('title', 'Kelola Tugas Magang')

@section('content')
<div x-data="taskTemplatePage()" class="space-y-6">

    <header>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Kelola Tugas Magang</h1>
        <p class="mt-1 text-sm text-slate-500">Unggah template penugasan dan sistem akan membentuk jadwal serta deadline berbeda untuk setiap peserta berdasarkan tanggal mulai magangnya.</p>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Data belum dapat diproses</span>
            </div>
            <ul class="mt-2 list-disc space-y-1 pl-6 text-xs font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="h-1.5 bg-gradient-to-r from-sky-600 via-blue-500 to-sky-600"></div>
        <div class="border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 lg:flex lg:items-center lg:justify-between">
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-sky-700 shadow-sm ring-1 ring-sky-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Unggah Template Tugas</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Gunakan template tugas mingguan resmi. Sistem membaca ketiga sheet dan menentukan jadwal serta deadline peserta secara otomatis.</p>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-3 lg:mt-0">
                <a href="{{ route('admin-peserta.tugas.template.download') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template Excel
                </a>
            </div>
        </div>

        <div class="grid gap-6 p-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <form action="{{ route('admin-peserta.tugas.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Sheet yang Dibaca Sistem</p>
                            <p class="mt-0.5 text-xs text-slate-500">Satu file Excel memuat seluruh kelompok peserta.</p>
                        </div>
                        <span class="rounded-full bg-sky-50 px-3 py-1 text-[11px] font-bold text-sky-700 ring-1 ring-sky-100">TUGAS MINGGUAN</span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            ['title' => 'SMK RPL', 'subtitle' => 'RPL / PPLG', 'icon' => 'code', 'color' => 'sky'],
                            ['title' => 'SMK TKJ', 'subtitle' => 'TKJ / TJKT', 'icon' => 'lan', 'color' => 'amber'],
                            ['title' => 'SMK SIJA', 'subtitle' => 'SIJA', 'icon' => 'hub', 'color' => 'amber'],
                            ['title' => 'Teknik Informatika', 'subtitle' => 'Minimal magang 1–4 bulan', 'icon' => 'school', 'color' => 'purple'],
                            ['title' => 'Sistem Informasi', 'subtitle' => 'Minimal magang 1–4 bulan', 'icon' => 'school', 'color' => 'purple'],
                            ['title' => 'Pend Teknik Informatika', 'subtitle' => 'Minimal magang 1–4 bulan', 'icon' => 'school', 'color' => 'purple'],
                        ] as $sheet)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full {{ $sheet['color'] === 'sky' ? 'bg-sky-100 text-sky-700' : ($sheet['color'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-purple-100 text-purple-700') }}">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $sheet['title'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $sheet['subtitle'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-xs leading-5 text-sky-800">
                        Sistem membaca kolom <strong>Minggu Ke</strong>, <strong>Materi &amp; Laporan</strong>, <strong>Tugas</strong>,
                        <strong>Hari Tampil</strong>, <strong>Hari Deadline</strong>, dan <strong>Jam Deadline</strong> pada ketiga sheet tersebut.
                    </div>
                </div>

                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-800">Pilih File Tugas Excel</p>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-500">Maks. 10 MB</span>
                    </div>
                    <label class="flex min-h-[190px] cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center transition"
                        :class="dragOver ? 'border-sky-500 bg-sky-50' : 'border-slate-300 bg-slate-50/50 hover:border-sky-400 hover:bg-sky-50/40'"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleDrop($event)">
                        <input x-ref="excelInput" type="file" name="file_template" accept=".xlsx" required class="sr-only" @change="selectFile($event.target.files[0])">
                        <span class="grid h-14 w-14 place-items-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </span>
                        <template x-if="!fileName">
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-sky-700">Klik untuk mengunggah atau drag &amp; drop</p>
                                <p class="mt-1 text-xs text-slate-400">Pastikan file menggunakan template resmi berformat .xlsx</p>
                            </div>
                        </template>
                        <template x-if="fileName">
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-slate-800" x-text="fileName"></p>
                                <p class="mt-1 text-xs text-emerald-600">File siap diproses</p>
                            </div>
                        </template>
                    </label>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-3.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Unggah dan Publikasikan Jadwal
                </button>
            </form>

            <aside class="rounded-2xl border border-sky-100 bg-gradient-to-b from-sky-50 to-white p-5">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-100 text-sky-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Alur Penugasan</h3>
                        <p class="text-xs text-slate-500">Jadwal dihitung per akun peserta.</p>
                    </div>
                </div>
                <ol class="mt-5 space-y-4">
                    @foreach ([
                        ['title' => 'Unduh template', 'text' => 'Gunakan file resmi dengan sheet SMK RPL, SMK TKJ, SMK SIJA, Teknik Informatika, Sistem Informasi, dan Pend Teknik Informatika.'],
                        ['title' => 'Isi jadwal mingguan', 'text' => 'Isi minggu, materi/laporan, tugas, hari tampil, hari deadline, dan jam deadline.'],
                        ['title' => 'Unggah satu kali', 'text' => 'Sistem membaca semua sheet sekaligus dan mencocokkannya dengan jurusan peserta.'],
                        ['title' => 'Deadline otomatis', 'text' => 'Tanggal dan jam deadline dihitung berdasarkan tanggal mulai setiap peserta.'],
                    ] as $index => $step)
                        <li class="flex gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-sky-900 text-xs font-bold text-white">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $step['title'] }}</p>
                                <p class="mt-0.5 text-xs leading-5 text-slate-500">{{ $step['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                    <strong>Contoh:</strong> peserta mulai Rabu akan melewati tugas yang deadline-nya sudah berakhir pada Selasa, sedangkan tugas yang masih aktif langsung tampil pada hari mulai.
                </div>
            </aside>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function taskTemplatePage() {
        return {
            dragOver: false,
            fileName: null,

            selectFile(file) {
                if (!file) { this.fileName = null; return; }
                if (!file.name.toLowerCase().endsWith('.xlsx')) {
                    alert('File harus berformat .xlsx');
                    this.$refs.excelInput.value = '';
                    this.fileName = null;
                    return;
                }
                this.fileName = file.name;
            },

            handleDrop(event) {
                this.dragOver = false;
                const file = event.dataTransfer.files[0];
                if (!file) return;
                const transfer = new DataTransfer();
                transfer.items.add(file);
                this.$refs.excelInput.files = transfer.files;
                this.selectFile(file);
            },
        };
    }
</script>
@endpush