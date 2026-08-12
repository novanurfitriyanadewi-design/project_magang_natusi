@extends('layouts.portal')

@section('title', 'Dashboard Peserta Magang')

@section('content')

<section class="mb-6">
    <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl mb-1">
        Selamat Datang, {{ $user->nama }} 
    </h1>
    <p class="text-sm text-slate-500">
        Senang melihat Anda kembali. Berikut adalah rangkuman aktivitas magang Anda hari ini.
    </p>
</section>

<div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-500 to-slate-600 p-5 text-white shadow-lg shadow-slate-200/40 transition hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-slate-200">Absensi Status</p>
                <p class="mt-2 text-2xl font-bold">
                    {{ $absensi['hadir_hari_ini'] ? 'Hadir Hari Ini' : 'Belum Absen' }}
                </p>
                <div class="mt-1 flex items-center gap-2">
                    <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase {{ $absensi['status'] === 'on_track' ? 'bg-white/20' : 'bg-rose-500/80' }}">
                        {{ $absensi['status'] === 'on_track' ? 'On Track' : 'Perlu Perhatian' }}
                    </span>
                    <span class="text-sm text-slate-200">{{ $absensi['total_hadir'] }}/{{ $absensi['total_hari_kerja'] }} Hari</span>
                </div>
            </div>
            <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <span class="material-symbols-outlined">how_to_reg</span>
            </div>
        </div>
        <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-5 text-white shadow-lg shadow-blue-200/40 transition hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-blue-100">Penugasan Status</p>
                <p class="mt-2 text-2xl font-bold">{{ $penugasan['aktif'] }} Tugas Aktif</p>
                @if ($penugasan['mendekati_deadline'] > 0)
                    <p class="mt-1 flex items-center gap-1 text-sm font-semibold text-amber-100">
                        <span class="material-symbols-outlined text-sm">error</span>
                        {{ $penugasan['mendekati_deadline'] }} Mendekati Deadline
                    </p>
                @else
                    <p class="mt-1 text-sm font-semibold text-blue-100">Aman, tidak ada deadline dekat</p>
                @endif
            </div>
            <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <span class="material-symbols-outlined">assignment</span>
            </div>
        </div>
        <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-lg shadow-emerald-200/40 transition hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-emerald-100">Pembayaran</p>
                <p class="mt-2 text-2xl font-bold">{{ $pembayaran['status'] }}</p>
                <p class="mt-1 text-sm text-emerald-100">Periode: {{ $pembayaran['periode'] }}</p>
            </div>
            <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
        <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 p-5 text-white shadow-lg shadow-rose-200/40 transition hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-rose-100">Laporan Mingguan</p>
                <p class="mt-2 text-2xl font-bold">
                    {{ $laporanMingguan['sudah_dikirim'] ? 'Sudah Dikirim' : 'Belum Dikirim' }}
                </p>
                <p class="mt-1 text-sm font-semibold text-rose-100">
                    Minggu ke-{{ $laporanMingguan['minggu_ke'] }}
                    {{ $laporanMingguan['sudah_dikirim'] ? 'Selesai' : 'Menunggu' }}
                </p>
            </div>
            <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <span class="material-symbols-outlined">description</span>
            </div>
        </div>
        <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-1 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Visualisasi Progress Penugasan</h3>
                <form method="GET" action="{{ route('peserta-magang.dashboard') }}">
                    <select name="rentang" onchange="this.form.submit()" class="cursor-pointer border-none bg-transparent text-sm font-semibold text-blue-600 focus:ring-0">
                        <option value="bulan" {{ ($rentang ?? 'bulan') === 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="minggu" {{ ($rentang ?? '') === 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    </select>
                </form>
            </div>
            <p class="mb-4 text-sm text-slate-500">Progress penyelesaian tugas harian Anda.</p>
            <div class="flex h-64 flex-col justify-between">
                <div class="flex h-full items-end gap-6 pb-6">
                    @foreach ($progressHarian as $hari)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <div class="w-full rounded-t-lg {{ $hari['is_today'] ? 'bg-blue-600 shadow-lg' : 'bg-blue-100 transition-all duration-500 hover:bg-blue-600' }}"
                                    style="height: {{ $hari['persentase'] }}%"></div>
                            <span class="text-[10px] font-semibold {{ $hari['is_today'] ? 'font-bold text-blue-600' : 'text-slate-400' }}">
                                {{ $hari['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-around border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-blue-600"></div>
                        <span class="text-sm text-slate-500">Tugas Selesai</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-blue-100"></div>
                        <span class="text-sm text-slate-500">Tugas Tertunda</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative flex items-center justify-between overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 text-white shadow-lg shadow-blue-200/40">
            <div class="relative z-10">
                <h4 class="mb-2 text-lg font-semibold">Ingin Mempelajari Aturan Perusahaan?</h4>
                <p class="mb-4 max-w-md text-sm text-blue-100">
                    Pastikan Anda memahami hak dan kewajiban selama menjalani masa magang di CV Natusi.
                </p>
                @if (Route::has('peserta-magang.aturan-perusahaan'))
                    <a href="{{ route('peserta-magang.aturan-perusahaan') }}"
                        class="inline-block rounded-lg bg-white px-6 py-2 text-sm font-semibold text-blue-600 transition-colors hover:bg-blue-50">
                        Lihat Dokumen Aturan
                    </a>
                @endif
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 rotate-12 text-[120px] text-blue-500 opacity-30">gavel</span>
            <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
        </div>
    </div>

    <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h3 class="text-lg font-semibold text-slate-900">Pengumuman Terbaru</h3>
        </div>
        <div class="max-h-[500px] flex-1 overflow-y-auto">
            @forelse ($pengumuman as $item)
                <div class="cursor-pointer border-b border-slate-100 p-5 transition-colors hover:bg-slate-50">
                    <span class="mb-1 block text-xs font-semibold text-blue-600">
                        {{ ucfirst($item->kategori) }} • {{ $item->created_at->diffForHumans() }}
                    </span>
                    <h4 class="mb-2 text-sm font-semibold text-slate-900">{{ $item->judul }}</h4>
                    <p class="line-clamp-2 text-sm text-slate-500">{{ $item->isi }}</p>
                </div>
            @empty
                <div class="p-5 text-center text-sm text-slate-500">
                    Belum ada pengumuman terbaru.
                </div>
            @endforelse
        </div>
        <div class="p-4 text-center">
            @if (Route::has('peserta-magang.pengumuman.index'))
                <a href="{{ route('peserta-magang.pengumuman.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    Lihat Semua Pengumuman
                </a>
            @endif
        </div>
    </div>
</div>

@endsection