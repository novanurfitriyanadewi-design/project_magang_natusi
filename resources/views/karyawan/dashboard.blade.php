@extends('layouts.portal')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="max-w-[1440px] mx-auto w-full">

    {{-- Welcome Header --}}
    <section class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">
                Selamat {{ Carbon\Carbon::now()->hour < 11 ? 'Pagi' : (Carbon\Carbon::now()->hour < 15 ? 'Siang' : (Carbon\Carbon::now()->hour < 18 ? 'Sore' : 'Malam')) }}, {{ explode(' ', $user->name)[0] ?? $user->name }}!
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Berikut ringkasan aktivitas Anda di CV Natusi hari ini, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}.
            </p>
        </div>

        <div class="flex gap-3">
            @if($sudahAbsenHariIni)
                <span class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-2.5 text-xs font-extrabold">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Sudah Absen Hari Ini
                </span>
            @else
                <form method="POST" action="{{ route('karyawan.absensi.clockin') }}">
                    @csrf
                    <button type="submit" class="bg-teal-700 text-white px-6 py-2.5 rounded-lg text-xs font-extrabold flex items-center gap-2 hover:bg-teal-800 transition-all active:scale-95 shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 8v4l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg>
                        Clock In
                    </button>
                </form>
            @endif
        </div>
    </section>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Bento Grid --}}
    <div class="grid grid-cols-12 gap-6">

        {{-- Baris pertama: Ringkasan Kehadiran (full width) --}}
        <div class="col-span-12 bg-white rounded-2xl border border-slate-200 p-6 relative shadow-[0_12px_32px_rgba(15,23,42,0.06)] border-l-4 border-l-teal-600">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-950">Ringkasan Kehadiran</h2>
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mt-1">Progres Bulan Berjalan</p>
                </div>
                <span class="bg-teal-50 text-teal-700 px-3 py-1 rounded-full text-xs font-extrabold">{{ $bulanLabel }}</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
                {{-- Hadir --}}
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-4 rounded-xl text-white relative overflow-hidden shadow-sm">
                    <span class="text-[11px] font-bold text-emerald-100 block mb-1">Hadir</span>
                    <span class="text-2xl font-black block">{{ $jumlahHadir }}</span>
                    <span class="text-[11px] text-emerald-100 block mt-1">Hari kerja</span>
                </div>

                {{-- Telat --}}
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 p-4 rounded-xl text-white relative overflow-hidden shadow-sm">
                    <span class="text-[11px] font-bold text-amber-100 block mb-1">Telat</span>
                    <span class="text-2xl font-black block">{{ $jumlahTelat }}</span>
                    <span class="text-[11px] text-amber-100 block mt-1">Lebih dari 15 menit</span>
                </div>

                {{-- Izin --}}
                <div class="bg-gradient-to-br from-sky-500 to-blue-600 p-4 rounded-xl text-white relative overflow-hidden shadow-sm">
                    <span class="text-[11px] font-bold text-sky-100 block mb-1">Izin</span>
                    <span class="text-2xl font-black block">{{ $jumlahIzin }}</span>
                    <span class="text-[11px] text-sky-100 block mt-1">Cuti terjadwal</span>
                </div>

                {{-- Rata-rata Jam --}}
                <div class="bg-gradient-to-br from-indigo-500 to-violet-600 p-4 rounded-xl text-white relative overflow-hidden shadow-sm">
                    <span class="text-[11px] font-bold text-indigo-100 block mb-1">Rata-rata Jam</span>
                    <span class="text-2xl font-black block">{{ $rataRataJam }}</span>
                    <span class="text-[11px] text-indigo-100 block mt-1">Jam/hari</span>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-extrabold text-slate-700">Progres Target Bulanan</span>
                    <span class="text-xs font-extrabold text-teal-700">{{ $progressPersen }}%</span>
                </div>
                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-teal-600 rounded-full transition-all duration-1000" style="width: {{ $progressPersen }}%"></div>
                </div>
            </div>
        </div>

        {{-- Baris kedua: 3 card sejajar --}}
        <div class="col-span-12 grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Jam Kerja Hari Ini --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-[0_12px_32px_rgba(15,23,42,0.06)] flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-extrabold text-slate-950">Jam Kerja Hari Ini</h3>
                    <svg class="h-5 w-5 text-teal-600" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </div>

                <div class="flex-1 flex flex-col justify-center gap-4">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Jam Masuk</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-1">{{ $jamMasukHariIni ?? '-' }}</p>
                        </div>
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Status</p>
                            <p class="text-sm font-extrabold {{ $sudahAbsenHariIni ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                                {{ $sudahAbsenHariIni ? 'Sudah Absen' : 'Belum Absen' }}
                            </p>
                        </div>
                        <span class="grid h-9 w-9 place-items-center rounded-full {{ $sudahAbsenHariIni ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </div>
                </div>

                <a href="{{ Route::has('karyawan.absensi.index') ? route('karyawan.absensi.index') : '#' }}" class="w-full text-center py-3 border border-teal-600 text-teal-700 hover:bg-teal-50 rounded-lg text-xs font-extrabold transition-all mt-4">
                    Lihat Riwayat Absensi
                </a>
            </div>

            {{-- Status Resign --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-[0_12px_32px_rgba(15,23,42,0.06)] flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-extrabold text-slate-950">Status Resign</h3>
                    <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="none"><path d="M9 4h9a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H9m0-16H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h4m0-16v16m6-8h-9m6-3l3 3-3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>

                @if(!$resignAktif)
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-sm font-extrabold text-slate-800">Tidak Ada Pengajuan Aktif</p>
                        <p class="text-xs text-slate-500 mt-2 px-4">Anda belum memiliki pengajuan resign yang sedang diproses.</p>
                    </div>
                    <a href="{{ Route::has('karyawan.resign.create') ? route('karyawan.resign.create') : '#' }}" class="w-full text-center py-3 border border-indigo-600 text-indigo-600 hover:bg-indigo-50 rounded-lg text-xs font-extrabold transition-all mt-4">
                        Ajukan Resign
                    </a>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-amber-500" viewBox="0 0 24 24" fill="none"><path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/></svg>
                        </div>
                        <p class="text-sm font-extrabold text-slate-800">Sedang Diproses</p>
                        <p class="text-xs text-slate-500 mt-2 px-4">Pengajuan resign Anda tanggal {{ $resignAktif->created_at->translatedFormat('d M Y') }} sedang ditinjau HRD.</p>
                    </div>
                    <a href="{{ Route::has('karyawan.resign.show') ? route('karyawan.resign.show', $resignAktif) : '#' }}" class="w-full text-center py-3 border border-slate-200 text-slate-600 rounded-lg text-xs font-extrabold hover:bg-slate-50 transition-all mt-4">
                        Lihat Detail
                    </a>
                @endif
            </div>

            {{-- Slip Gaji Terakhir --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-[0_12px_32px_rgba(15,23,42,0.06)] flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-extrabold text-slate-950">Slip Gaji Terakhir</h3>
                    <svg class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none"><path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </div>

                @if($slipGajiTerakhir ?? false)
                    <div class="flex-1 flex flex-col justify-center gap-3">
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Periode</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-1">{{ $slipGajiTerakhir->periode_label ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nominal</p>
                            <p class="text-sm font-extrabold text-emerald-700 mt-1">Rp {{ number_format($slipGajiTerakhir->nominal ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <a href="{{ Route::has('karyawan.payslip.index') ? route('karyawan.payslip.index') : '#' }}" class="w-full text-center py-3 border border-blue-600 text-blue-700 hover:bg-blue-50 rounded-lg text-xs font-extrabold transition-all mt-4">
                        Lihat Semua Slip Gaji
                    </a>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none"><path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-sm font-extrabold text-slate-800">Belum Ada Slip Gaji</p>
                        <p class="text-xs text-slate-500 mt-2 px-4">Slip gaji akan muncul setelah pembayaran pertama diproses.</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- Baris ketiga: Pengumuman + Quick Links --}}
        <div class="col-span-12 grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-[0_12px_32px_rgba(15,23,42,0.06)] flex flex-col">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-extrabold text-slate-950 flex items-center gap-2">
                        <svg class="h-5 w-5 text-teal-600" viewBox="0 0 24 24" fill="none"><path d="M4 10v4a1 1 0 0 0 1 1h2l5 4V5L7 9H5a1 1 0 0 0-1 1Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        Pengumuman
                    </h3>
                </div>
                <div class="p-6 space-y-5 flex-1 overflow-y-auto max-h-72">
                    @forelse($pengumuman as $item)
                        <div class="relative pl-4 border-l-2 {{ $loop->first ? 'border-teal-500' : 'border-slate-200' }}">
                            <span class="text-[11px] font-bold {{ $loop->first ? 'text-teal-700' : 'text-slate-400' }} block mb-1">
                                {{ $item->published_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                            </span>
                            <h4 class="text-xs font-extrabold text-slate-900 mb-1">{{ $item->judul }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $item->isi }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada pengumuman terbaru.</p>
                    @endforelse
                </div>
                <div class="p-4 bg-slate-50 text-center rounded-b-2xl">
                    <a href="{{ Route::has('karyawan.pengumuman.index') ? route('karyawan.pengumuman.index') : '#' }}" class="text-teal-700 text-xs font-extrabold hover:underline">Lihat Semua Pengumuman</a>
                </div>
            </div>

            <div class="lg:col-span-4 grid grid-cols-2 gap-4 auto-rows-fr">
                <a href="{{ Route::has('karyawan.cuti.create') ? route('karyawan.cuti.create') : '#' }}" class="bg-gradient-to-br from-amber-500 to-orange-600 text-white p-6 rounded-2xl flex flex-col items-center justify-center text-center hover:scale-[1.02] hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 bg-white/15 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"><rect height="16" rx="2" stroke="currentColor" stroke-width="1.6" width="16" x="4" y="4"/><path d="M4 9h16M9 4v3M15 4v3" stroke="currentColor" stroke-width="1.6"/></svg>
                    </div>
                    <span class="text-xs font-extrabold">Ajukan Cuti</span>
                </a>

                <a href="{{ Route::has('karyawan.reimbursement.index') ? route('karyawan.reimbursement.index') : '#' }}" class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-6 rounded-2xl flex flex-col items-center justify-center text-center hover:scale-[1.02] hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 bg-white/15 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"><path d="M12 8v8m-4-4h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
                    </div>
                    <span class="text-xs font-extrabold">Reimbursement</span>
                </a>

                <a href="{{ Route::has('karyawan.aturan.index') ? route('karyawan.aturan.index') : '#' }}" class="bg-gradient-to-br from-indigo-500 to-violet-600 text-white p-6 rounded-2xl flex flex-col items-center justify-center text-center hover:scale-[1.02] hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 bg-white/15 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l3 3v15H6V3Zm3 7h6M9 14h6M9 18h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <span class="text-xs font-extrabold">Aturan Perusahaan</span>
                </a>

                <a href="{{ Route::has('karyawan.helpdesk.index') ? route('karyawan.helpdesk.index') : '#' }}" class="bg-gradient-to-br from-blue-600 to-sky-600 text-white p-6 rounded-2xl flex flex-col items-center justify-center text-center hover:scale-[1.02] hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 bg-white/15 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.4 2.3c-.7.3-1.4.9-1.4 1.7v.3M12 17h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </div>
                    <span class="text-xs font-extrabold">Bantuan</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection