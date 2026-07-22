@extends('layouts.portal')

@section('title', 'Tugas Saya')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-slate-500">
        <span>Magang</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="font-medium text-slate-700">Tugas Saya</span>
    </nav>

    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-950">Tugas Saya</h1>
            <p class="mt-1 text-sm text-slate-500">
                Jadwal dan deadline di bawah dihitung khusus dari tanggal mulai magang Anda,
                {{ optional($peserta->tgl_mulai)->translatedFormat('d F Y') ?: 'yang belum ditentukan' }}.
            </p>
        </div>

        <form method="GET">
            <select name="jenis_tugas" onchange="this.form.submit()"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm">
                <option value="">Semua jenis tugas</option>
                <option value="harian" @selected(request('jenis_tugas') === 'harian')>Harian</option>
                <option value="mingguan" @selected(request('jenis_tugas') === 'mingguan')>Mingguan</option>
                <option value="akhir" @selected(request('jenis_tugas') === 'akhir')>Akhir</option>
            </select>
        </form>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $activeCount = $penugasan->where('status', 'aktif')->count();
            $scheduledCount = $penugasan->where('status', 'terjadwal')->count();
            $doneCount = $penugasan->where('status', 'selesai')->count();
            $skippedCount = $penugasan->where('status', 'dilewati')->count();
        @endphp
        @foreach ([
            ['label' => 'Aktif', 'value' => $activeCount, 'icon' => 'assignment', 'class' => 'bg-blue-50 text-blue-700'],
            ['label' => 'Terjadwal', 'value' => $scheduledCount, 'icon' => 'schedule', 'class' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Selesai', 'value' => $doneCount, 'icon' => 'task_alt', 'class' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Dilewati', 'value' => $skippedCount, 'icon' => 'skip_next', 'class' => 'bg-slate-100 text-slate-600'],
        ] as $stat)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl {{ $stat['class'] }}">
                        <span class="material-symbols-outlined text-[21px]">{{ $stat['icon'] }}</span>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="space-y-4">
        @forelse ($penugasan as $item)
            @php
                $task = $item->tugas;
                $submission = $pengumpulan->get($item->tugas_id);
                $isLate = !$submission && $item->deadline && now()->greaterThan($item->deadline);
                $statusLabel = match(true) {
                    $submission !== null => $submission->status === 'telat' ? 'Dikumpulkan Terlambat' : 'Sudah Dikumpulkan',
                    $item->status === 'dilewati' => 'Dilewati',
                    $item->status === 'terjadwal' => 'Belum Tersedia',
                    $isLate => 'Melewati Deadline',
                    default => 'Aktif',
                };
                $statusClass = match(true) {
                    $submission !== null && $submission->status !== 'telat' => 'bg-emerald-100 text-emerald-700',
                    $submission !== null && $submission->status === 'telat' => 'bg-orange-100 text-orange-700',
                    $item->status === 'dilewati' => 'bg-slate-100 text-slate-600',
                    $item->status === 'terjadwal' => 'bg-amber-100 text-amber-700',
                    $isLate => 'bg-red-100 text-red-700',
                    default => 'bg-blue-100 text-blue-700',
                };
                $categoryIcon = match($task?->kategori_tugas) {
                    'materi' => 'menu_book',
                    'laporan' => 'docs',
                    default => 'assignment',
                };
                $categoryTone = match($task?->kategori_tugas) {
                    'materi' => 'bg-blue-50 text-blue-700',
                    'laporan' => 'bg-purple-50 text-purple-700',
                    default => 'bg-amber-50 text-amber-700',
                };
            @endphp

            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_270px] lg:p-6">
                    <div>
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl {{ $categoryTone }}">
                                    <span class="material-symbols-outlined text-[23px]">{{ $categoryIcon }}</span>
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-lg font-bold text-slate-900">{{ $task?->judul ?? 'Tugas' }}</h2>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $task?->kode_tugas ?: 'Tanpa kode' }} · {{ ucfirst($task?->jenis_tugas ?? '-') }} · Minggu {{ $task?->minggu_ke ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if ($task?->materi)
                            <div class="mt-5 rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Materi / Deskripsi</p>
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $task->materi }}</p>
                            </div>
                        @endif

                        @if ($item->keterangan)
                            <div class="mt-4 flex gap-2 rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-sm text-blue-800">
                                <span class="material-symbols-outlined text-[19px]">info</span>
                                <p>{{ $item->keterangan }}</p>
                            </div>
                        @endif

                        @if ($task?->kategori_tugas === 'laporan')
                            <div class="mt-5 rounded-2xl border border-purple-200 bg-purple-50/60 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-purple-900">Ketentuan Pembuatan Laporan</p>
                                        <p class="mt-0.5 text-xs text-purple-600">Ketentuan ini tampil terpisah dari file Word.</p>
                                    </div>
                                    @if ($item->templateLaporan)
                                        <a href="{{ route('peserta.tugas.template-laporan.download', $item) }}"
                                            class="inline-flex items-center gap-2 rounded-xl bg-purple-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-purple-600">
                                            <span class="material-symbols-outlined text-[17px]">download</span>
                                            Unduh Word
                                        </a>
                                    @endif
                                </div>
                                <div class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">
                                    {{ $item->ketentuan_laporan ?: 'Ketentuan laporan belum ditambahkan oleh admin.' }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <aside class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <div class="space-y-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Tersedia</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $item->tersedia_pada?->translatedFormat('d M Y, H:i') ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Deadline</p>
                                <p class="mt-1 text-sm font-semibold {{ $isLate ? 'text-red-600' : 'text-slate-800' }}">
                                    {{ $item->deadline?->translatedFormat('d M Y, H:i') ?? '-' }}
                                </p>
                            </div>

                            @if ($task?->file_tugas)
                                <a href="{{ route('peserta.tugas.file.download', $item) }}"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-3 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-50">
                                    <span class="material-symbols-outlined text-[17px]">download</span>
                                    Unduh File Tugas
                                </a>
                            @endif

                            @if ($submission)
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
                                    <p class="font-bold">Terkirim {{ $submission->dikumpulkan_pada?->translatedFormat('d M Y, H:i') }}</p>
                                    <p class="mt-1">Status: {{ ucfirst($submission->status) }}</p>
                                </div>
                            @elseif ($item->status === 'aktif')
                                <form action="{{ route('peserta.tugas.submit', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="mb-1.5 block text-xs font-bold text-slate-600">Unggah Jawaban</label>
                                        <input type="file" name="file_jawaban" required
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                                            class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-2.5 file:py-1.5 file:text-[11px] file:font-semibold file:text-blue-700">
                                    </div>
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-900 px-3 py-2.5 text-xs font-bold text-white hover:bg-blue-800">
                                        <span class="material-symbols-outlined text-[17px]">upload</span>
                                        Kumpulkan Tugas
                                    </button>
                                </form>
                            @elseif ($item->status === 'terjadwal')
                                <p class="rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-700">Tugas akan terbuka otomatis sesuai jadwal.</p>
                            @elseif ($item->status === 'dilewati')
                                <p class="rounded-xl bg-slate-100 p-3 text-xs leading-5 text-slate-500">Tugas ini dilewati karena deadline pada minggu pertama sudah berakhir sebelum tanggal mulai magang Anda.</p>
                            @endif
                        </div>
                    </aside>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <span class="material-symbols-outlined text-5xl text-slate-300">assignment</span>
                <h2 class="mt-3 text-base font-bold text-slate-700">Belum ada penugasan</h2>
                <p class="mt-1 text-sm text-slate-400">Jadwal akan tampil setelah admin mengunggah template penugasan.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection
