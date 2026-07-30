@extends('layouts.portal')

@section('title', 'Dashboard Admin')

@section('content')

    {{-- Welcome Header --}}
    <section class="mb-6">
        <h1 class="headline text-2xl md:text-3xl font-bold text-slate-900 mb-1">
            Halo, {{ $user->nama ?? 'Admin' }} 👋
        </h1>
        <p class="text-sm text-slate-500 mb-2">
            Pantau operasional peserta magang dalam satu dashboard.
        </p>
        <p class="text-xs text-slate-400">
            Diperbarui {{ now()->translatedFormat('d M Y, H:i') }}
            <span class="mx-1">•</span>
            Akses Administrator
        </p>
    </section>

    {{-- Bento Grid Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">

        {{-- Peserta Magang --}}
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 p-5 rounded-xl text-white relative overflow-hidden">
            <div class="flex justify-between items-start mb-6">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-cyan-100">Peserta Magang</span>
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined">groups</span>
                </div>
            </div>
            <span class="text-3xl font-bold block mb-1">{{ $pesertaMagang['total'] }}</span>
            <p class="text-sm text-cyan-100">{{ $pesertaMagang['aktif'] }} peserta aktif</p>
        </div>

        {{-- Pengajuan Magang --}}
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-5 rounded-xl text-white relative overflow-hidden">
            <div class="flex justify-between items-start mb-6">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-indigo-100">Pengajuan Magang</span>
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined">contract</span>
                </div>
            </div>
            <span class="text-3xl font-bold block mb-1">{{ $pengajuanMagang['total'] }}</span>
            <p class="text-sm text-indigo-100">{{ $pengajuanMagang['menunggu'] }} menunggu ditinjau</p>
        </div>

        {{-- Pembayaran Bulanan --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 rounded-xl text-white relative overflow-hidden">
            <div class="flex justify-between items-start mb-6">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100">Pembayaran Bulanan</span>
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined">account_balance</span>
                </div>
            </div>
            <span class="text-3xl font-bold block mb-1">{{ $pembayaranBulanan['total'] }}</span>
            <p class="text-sm text-emerald-100">Peserta lunas bulan ini</p>
        </div>

        {{-- Absensi Hari Ini --}}
        <div class="bg-gradient-to-br from-blue-800 to-blue-900 p-5 rounded-xl text-white relative overflow-hidden">
            <div class="flex justify-between items-start mb-6">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-200">Absensi Hari Ini</span>
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
            </div>
            <span class="text-3xl font-bold block mb-1">{{ $absensiHariIni['total'] }}</span>
            <p class="text-sm text-blue-200">Data masuk {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Chart Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

        {{-- Pengajuan Magang per Bulan --}}
        <div class="lg:col-span-2 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-1">
                <h3 class="headline text-lg font-semibold text-slate-900">Pengajuan Magang per Bulan</h3>
                @if (Route::has('admin.pengajuan-magang.index'))
                    <a href="{{ route('admin.pengajuan-magang.index') }}"
                       class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:underline">
                        Lihat pengajuan
                        <span class="material-symbols-outlined text-base">chevron_right</span>
                    </a>
                @endif
            </div>
            <p class="text-sm text-slate-500 mb-4">Tren jumlah pengajuan selama 12 bulan terakhir.</p>
            <canvas id="pengajuanChart" height="180"></canvas>
        </div>

        {{-- Status Absensi Hari Ini --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <h3 class="headline text-lg font-semibold text-slate-900 mb-1">Status Absensi Hari Ini</h3>
            <p class="text-sm text-slate-500 mb-4">Komposisi hadir, terlambat, dan izin.</p>
            <div class="flex-1 flex items-center justify-center">
                <canvas id="absensiDonutChart" height="180"></canvas>
            </div>
            <div class="flex justify-center gap-4 mt-4 text-xs text-slate-500">
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-800 inline-block"></span> Hadir
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Terlambat
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-slate-300 inline-block"></span> Izin
                </span>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
    const pengajuanCtx = document.getElementById('pengajuanChart');
    new Chart(pengajuanCtx, {
        type: 'bar',
        data: {
            labels: @json($pengajuanPerBulan['labels']),
            datasets: [{
                label: 'Pengajuan',
                data: @json($pengajuanPerBulan['data']),
                backgroundColor: '#4f46e5',
                borderRadius: 6,
                maxBarThickness: 28,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    const absensiCtx = document.getElementById('absensiDonutChart');
    new Chart(absensiCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Terlambat', 'Izin'],
            datasets: [{
                data: [
                    {{ $statusAbsensi['hadir'] }},
                    {{ $statusAbsensi['terlambat'] }},
                    {{ $statusAbsensi['izin'] }},
                ],
                backgroundColor: ['#1e3a8a', '#fbbf24', '#cbd5e1'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush