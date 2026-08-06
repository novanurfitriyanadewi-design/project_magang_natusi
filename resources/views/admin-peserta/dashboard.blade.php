@extends('layouts.portal')

@section('title', 'Dashboard Admin')

@section('content')

    {{-- Welcome Header --}}
    <section class="mb-6">
        <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl mb-1">Halo, {{ $user->nama ?? 'Admin' }}</h1>
        <p class="text-sm text-slate-500 mb-2">Pantau operasional peserta magang dalam satu dashboard.</p>
    </section>

    {{-- Bento Grid Stats --}}
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Card 1: Peserta Magang --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 p-5 text-white shadow-lg shadow-cyan-200/40 transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-cyan-100">Peserta Magang</p>
                    <p class="mt-2 text-3xl font-bold">{{ $pesertaMagang['total'] }}</p>
                    <p class="mt-1 text-sm text-cyan-100">{{ $pesertaMagang['aktif'] }} peserta aktif</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
        </div>

        {{-- Card 2: Pengajuan Magang --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-5 text-white shadow-lg shadow-indigo-200/40 transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-indigo-100">Pengajuan Magang</p>
                    <p class="mt-2 text-3xl font-bold">{{ $pengajuanMagang['total'] }}</p>
                    <p class="mt-1 text-sm text-indigo-100">{{ $pengajuanMagang['menunggu'] }} menunggu ditinjau</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
        </div>

        {{-- Card 3: Pembayaran Bulanan --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-lg shadow-emerald-200/40 transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-emerald-100">Pembayaran Bulanan</p>
                    <p class="mt-2 text-3xl font-bold">{{ $pembayaranBulanan['total'] }}</p>
                    <p class="mt-1 text-sm text-emerald-100">Peserta lunas bulan ini</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
        </div>

        {{-- Card 4: Absensi Hari Ini --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 to-blue-800 p-5 text-white shadow-lg shadow-blue-200/40 transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-blue-200">Absensi Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold">{{ $absensiHariIni['total'] }}</p>
                    <p class="mt-1 text-sm text-blue-200">Data masuk {{ now()->format('d/m/Y') }}</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
        </div>
    </div>

    {{-- Chart Row --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Pengajuan Magang per Bulan --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-lg font-semibold text-slate-900">Pengajuan Magang per Bulan</h3>
                @if (Route::has('admin.pengajuan-magang.index'))
                    <a href="{{ route('admin.pengajuan-magang.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:underline">
                        Lihat pengajuan
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
            <p class="text-sm text-slate-500 mb-4">Tren jumlah pengajuan selama 12 bulan terakhir.</p>
            <canvas id="pengajuanChart" height="180"></canvas>
        </div>

        {{-- Status Absensi Hari Ini --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900 mb-1">Status Absensi Hari Ini</h3>
            <p class="text-sm text-slate-500 mb-4">Komposisi hadir, terlambat, dan izin.</p>
            <div class="flex items-center justify-center">
                <canvas id="absensiDonutChart" height="180"></canvas>
            </div>
            <div class="mt-4 flex justify-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-blue-800"></span> Hadir</span>
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-amber-400"></span> Terlambat</span>
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-slate-300"></span> Izin</span>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
    (function() {
        // Pengajuan Chart
        const pengajuanCtx = document.getElementById('pengajuanChart');
        if (pengajuanCtx) {
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
        }

        // Absensi Donut Chart
        const absensiCtx = document.getElementById('absensiDonutChart');
        if (absensiCtx) {
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
        }
    })();
</script>
@endpush