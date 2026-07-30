@extends('layouts.portal')

@section('title', 'Dashboard Admin Karyawan')

@push('styles')
<style>
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 24px;
    }
    .card-accent-blue::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #006191;
        border-radius: 4px 0 0 4px;
    }
</style>
@endpush

@section('content')

    {{-- Welcome Header --}}
    <section class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-slate-800">
                Selamat Pagi, {{ $user->nama ?? 'Admin' }}! 👋
            </h1>
            <p class="text-base text-slate-500 mt-1">
                Pantau operasional dan kinerjawan karyawan dalam satu dashboard.
            </p>
        </div>
        <div class="text-xs font-medium text-slate-600 bg-white shadow-sm px-3.5 py-2 rounded-xl border border-slate-200/80 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Diperbarui: {{ now()->translatedFormat('d M Y, H:i') }}
            <span class="text-slate-300">•</span>
            Akses Administrator
        </div>
    </section>

    {{-- Bento Grid Main Container --}}
    <div class="bento-grid mb-6">

        {{-- Card 1: Attendance & Overview Summary (Large Card - 8 Cols) --}}
        <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-6 relative card-accent-blue shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Ringkasan Operasional Karyawan</h2>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Status Utama Hari Ini</p>
                </div>
                <span class="bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full text-xs border border-blue-100">
                    {{ now()->translatedFormat('F Y') }}
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

                {{-- Card 1: TOTAL KARYAWAN --}}
                <div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-md transition-all hover:scale-[1.01] bg-gradient-to-br from-violet-600 to-purple-600">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-white/90">Total Karyawan</span>
                        <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl text-white">
                            <span class="material-symbols-outlined text-lg">group</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-white block tracking-tight">{{ $karyawanData['total'] }}</span>
                        <span class="text-xs font-medium text-white/80 block mt-1">Seluruh karyawan terdaftar</span>
                    </div>
                </div>

                {{-- Card 2: KARYAWAN AKTIF --}}
                <div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-md transition-all hover:scale-[1.01] bg-gradient-to-br from-emerald-500 to-teal-600">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-white/90">Karyawan Aktif</span>
                        <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl text-white">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-white block tracking-tight">{{ $karyawanData['aktif'] }}</span>
                        <span class="text-xs font-medium text-white/80 block mt-1">Karyawan dengan status aktif</span>
                    </div>
                </div>

                {{-- Card 3: KARYAWAN NON-AKTIF --}}
                <div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-md transition-all hover:scale-[1.01] bg-gradient-to-br from-rose-500 to-red-600">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-white/90">Karyawan Non-Aktif</span>
                        <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl text-white">
                            <span class="material-symbols-outlined text-lg">cancel</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-white block tracking-tight">{{ $karyawanData['nonaktif'] }}</span>
                        <span class="text-xs font-medium text-white/80 block mt-1">Karyawan tidak aktif</span>
                    </div>
                </div>

                {{-- Card 4: BARU BULAN INI --}}
                <div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-md transition-all hover:scale-[1.01] bg-gradient-to-br from-sky-500 to-blue-600">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-white/90">Baru Bulan Ini</span>
                        <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl text-white">
                            <span class="material-symbols-outlined text-lg">person_add</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-white block tracking-tight">{{ $karyawanData['baru'] }}</span>
                        <span class="text-xs font-medium text-white/80 block mt-1">Bergabung bulan {{ now()->translatedFormat('F Y') }}</span>
                    </div>
                </div>

            </div>

            <div>
                <div class="flex justify-between items-center text-xs font-bold mb-2">
                    <span class="text-slate-700">Tingkat Kehadiran Karyawan</span>
                    <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">{{ $absensiKaryawanData['persentase'] }}</span>
                </div>
                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-1000" style="width: {{ $absensiKaryawanData['persentase'] }}"></div>
                </div>
            </div>
        </div>

        {{-- Card 2: Status Absensi Hari Ini (Donut Chart - 4 Cols) --}}
        <div class="col-span-12 md:col-span-6 lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-bold text-slate-800">Komposisi Absensi</h3>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">pie_chart</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mb-4">Status persentase kehadiran karyawan hari ini.</p>
            </div>

            <div class="flex-1 flex items-center justify-center my-2">
                <canvas id="absensiKaryawanDonutChart" class="max-h-40"></canvas>
            </div>

            <div class="flex justify-center gap-4 mt-4 text-xs font-semibold text-slate-600 border-t border-slate-100 pt-3">
                <span class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#006191] inline-block"></span> Hadir
                </span>
                <span class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#f97316] inline-block"></span> Terlambat
                </span>
                <span class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#ec4899] inline-block"></span> Izin
                </span>
            </div>
        </div>

        {{-- Card 3: Grafik Tren Resign (8 Cols) --}}
        <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-1">
                <h3 class="text-lg font-bold text-slate-800">Pengajuan Resign per Bulan</h3>
                @if (Route::has('admin.karyawan.resign.index'))
                    <a href="{{ route('admin.karyawan.resign.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                        Lihat Detail
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </a>
                @endif
            </div>
            <p class="text-xs text-slate-400 mb-4">Tren riwayat pengajuan resign selama 12 bulan terakhir.</p>
            <div class="w-full">
                <canvas id="resignChart" height="110"></canvas>
            </div>
        </div>

        {{-- Card 4: Quick Action Box dengan Gradien (4 Cols) --}}
        <div class="col-span-12 md:col-span-6 lg:col-span-4 bg-gradient-to-br from-[#006191] to-[#004b71] text-white p-6 rounded-2xl relative overflow-hidden flex flex-col justify-between shadow-sm">
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white mb-3">
                    <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Pusat Kelola Data</h3>
                <p class="text-xs text-blue-100/80 leading-relaxed mb-6">Akses cepat aturan operasional, kebijakan HR, dan verifikasi berkas karyawan.</p>
            </div>
            
            <div class="relative z-10 grid grid-cols-2 gap-2.5">
                @if (Route::has('admin.karyawan.index'))
                    <a href="{{ route('admin.karyawan.index') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/20 px-3 py-2.5 rounded-xl text-xs font-bold text-center transition-all">
                        Data Karyawan
                    </a>
                @endif
                @if (Route::has('admin.aturan.index'))
                    <a href="{{ route('admin.aturan.index') }}" class="bg-white text-[#006191] hover:bg-blue-50 px-3 py-2.5 rounded-xl text-xs font-bold text-center shadow-sm transition-all">
                        Aturan Kantor
                    </a>
                @endif
            </div>

            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[140px] text-white/5 pointer-events-none">badge</span>
        </div>

    </div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Chart 1: Bar Chart Resign
        const resignCtx = document.getElementById('resignChart');
        if (resignCtx) {
            new Chart(resignCtx, {
                type: 'bar',
                data: {
                    labels: @json($resignPerBulanData['labels']),
                    datasets: [{
                        label: 'Pengajuan Resign',
                        data: @json($resignPerBulanData['data']),
                        backgroundColor: '#006191',
                        hoverBackgroundColor: '#004b71',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Chart 2: Donut Chart Absensi
        const absensiKaryawanCtx = document.getElementById('absensiKaryawanDonutChart');
        if (absensiKaryawanCtx) {
            new Chart(absensiKaryawanCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Terlambat', 'Izin'],
                    datasets: [{
                        data: [
                            {{ $statusAbsensiKaryawanData['hadir'] }},
                            {{ $statusAbsensiKaryawanData['terlambat'] }},
                            {{ $statusAbsensiKaryawanData['izin'] }},
                        ],
                        backgroundColor: ['#006191', '#f97316', '#ec4899'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endpush