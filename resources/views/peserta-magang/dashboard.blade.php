@extends('layouts.portal')

@section('title', 'Dashboard Peserta Magang')

@section('content')

    {{-- Welcome Header --}}
    <section class="mb-6">
        <h1 class="headline text-2xl md:text-3xl font-bold text-slate-900 mb-1">
            Selamat Datang, {{ $user->nama }} 👋
        </h1>
        <p class="text-sm text-slate-500">
            Senang melihat Anda kembali. Berikut adalah rangkuman aktivitas magang Anda hari ini.
        </p>
    </section>

    {{-- Bento Grid Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">

        {{-- Absensi Status --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-slate-500"></div>
            <div class="flex justify-between items-start mb-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Absensi Status</span>
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-2xl font-bold text-slate-900">
                    {{ $absensi['hadir_hari_ini'] ? 'Hadir Hari Ini' : 'Belum Absen' }}
                </span>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 {{ $absensi['status'] === 'on_track' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded text-[10px] font-bold uppercase">
                        {{ $absensi['status'] === 'on_track' ? 'On Track' : 'Perlu Perhatian' }}
                    </span>
                    <span class="text-sm text-slate-500">{{ $absensi['total_hadir'] }}/{{ $absensi['total_hari_kerja'] }} Hari</span>
                </div>
            </div>
        </div>

        {{-- Penugasan Status --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div>
            <div class="flex justify-between items-start mb-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Penugasan Status</span>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">assignment</span>
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-2xl font-bold text-blue-600">{{ $penugasan['aktif'] }} Tugas Aktif</span>
                @if ($penugasan['mendekati_deadline'] > 0)
                    <p class="text-sm text-rose-600 font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">error</span>
                        {{ $penugasan['mendekati_deadline'] }} Mendekati Deadline
                    </p>
                @else
                    <p class="text-sm text-green-600 font-semibold">Aman, tidak ada deadline dekat</p>
                @endif
            </div>
        </div>

        {{-- Pembayaran Status --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-800"></div>
            <div class="flex justify-between items-start mb-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pembayaran</span>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-800">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-2xl font-bold text-slate-900">{{ $pembayaran['status'] }}</span>
                <p class="text-sm text-slate-500">Periode: {{ $pembayaran['periode'] }}</p>
            </div>
        </div>

        {{-- Laporan Mingguan Status --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-rose-600"></div>
            <div class="flex justify-between items-start mb-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Laporan Mingguan</span>
                <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                    <span class="material-symbols-outlined">description</span>
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-2xl font-bold text-slate-900">
                    {{ $laporanMingguan['sudah_dikirim'] ? 'Sudah Dikirim' : 'Belum Dikirim' }}
                </span>
                <p class="text-sm {{ $laporanMingguan['sudah_dikirim'] ? 'text-green-600' : 'text-rose-600' }} font-semibold">
                    Minggu ke-{{ $laporanMingguan['minggu_ke'] }}
                    {{ $laporanMingguan['sudah_dikirim'] ? 'Selesai' : 'Menunggu' }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

        {{-- Task Progress Chart Area --}}
        <div class="lg:col-span-2 space-y-3">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="headline text-lg font-semibold text-slate-900">Visualisasi Progress Penugasan</h3>
                    </div>
                    <form method="GET" action="{{ route('peserta-magang.dashboard') }}">
                        <select name="rentang" onchange="this.form.submit()" class="bg-transparent border-none text-sm font-semibold text-blue-600 focus:ring-0 cursor-pointer">
                            <option value="bulan" {{ ($rentang ?? 'bulan') === 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="minggu" {{ ($rentang ?? '') === 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                        </select>
                    </form>
                </div>
                <div class="p-8 h-80 flex flex-col justify-between">
                    <div class="flex items-end gap-6 h-full pb-6">
                        @foreach ($progressHarian as $hari)
                            <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                <div class="w-full {{ $hari['is_today'] ? 'bg-blue-600 shadow-lg' : 'bg-blue-100 transition-all duration-500 hover:bg-blue-600' }} rounded-t-lg"
                                     style="height: {{ $hari['persentase'] }}%"></div>
                                <span class="text-[10px] font-semibold {{ $hari['is_today'] ? 'text-blue-600 font-bold' : 'text-slate-400' }}">
                                    {{ $hari['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-around pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-600"></div>
                            <span class="text-sm text-slate-500">Tugas Selesai</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-100"></div>
                            <span class="text-sm text-slate-500">Tugas Tertunda</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Company Rules Shortcut --}}
            <div class="bg-blue-600 p-6 rounded-xl text-white flex items-center justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="headline text-lg font-semibold mb-2">Ingin Mempelajari Aturan Perusahaan?</h4>
                    <p class="text-blue-100 text-sm mb-4 max-w-md">
                        Pastikan Anda memahami hak dan kewajiban selama menjalani masa magang di CV Natusi.
                    </p>
                    @if (Route::has('peserta-magang.aturan-perusahaan'))
                        <a href="{{ route('peserta-magang.aturan-perusahaan') }}"
                           class="inline-block px-6 py-2 bg-white text-blue-600 rounded-lg text-sm font-semibold hover:bg-blue-50 transition-colors">
                            Lihat Dokumen Aturan
                        </a>
                    @endif
                </div>
                <span class="material-symbols-outlined text-[120px] absolute -right-4 -bottom-4 text-blue-500 opacity-30 rotate-12">gavel</span>
            </div>
        </div>

        {{-- Latest Announcements --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center gap-2 bg-slate-50">
                <div class="w-1 h-6 bg-rose-600 rounded-full"></div>
                <h3 class="headline text-lg font-semibold text-slate-900">Latest Announcements</h3>
            </div>
            <div class="p-0 flex-1 overflow-y-auto max-h-[500px]">
                @forelse ($pengumuman as $item)
                    <div class="p-5 border-b border-slate-100 hover:bg-slate-50 transition-colors cursor-pointer">
                        <span class="text-xs font-semibold text-blue-600 mb-1 block">
                            {{ ucfirst($item->kategori) }} • {{ $item->created_at->diffForHumans() }}
                        </span>
                        <h4 class="text-sm font-semibold text-slate-900 mb-2">{{ $item->judul }}</h4>
                        <p class="text-sm text-slate-500 line-clamp-2">{{ $item->isi }}</p>
                    </div>
                @empty
                    <div class="p-5 text-center text-sm text-slate-500">
                        Belum ada pengumuman terbaru.
                    </div>
                @endforelse
            </div>
            <div class="p-4 text-center">
                @if (Route::has('peserta-magang.pengumuman.index'))
                    <a href="{{ route('peserta-magang.pengumuman.index') }}" class="text-blue-600 text-sm font-semibold hover:underline">
                        Lihat Semua Pengumuman
                    </a>
                @endif
            </div>
        </div>
    </div>

@endsection