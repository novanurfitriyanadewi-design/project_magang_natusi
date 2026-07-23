@extends('layouts.portal')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        // ======================================================================
        // 1) PESERTA MAGANG
        // ======================================================================
        $totalInterns = $totalInterns ?? 0;
        $activeInterns = $activeInterns ?? 0;
        $totalApplications = $totalApplications ?? 0;
        $pendingApplications = $pendingApplications ?? 0;
        $paidParticipantsThisMonth = $paidParticipantsThisMonth ?? 0;
        $todayAttendanceCount = $todayAttendanceCount ?? 0;
        $attendanceBreakdown = $attendanceBreakdown ?? ['hadir' => 0, 'terlambat' => 0, 'izin' => 0];
        $monthlyApplications = collect($monthlyApplications ?? []);
        $latestAttendance = $latestAttendance ?? collect();
        $latestTaskSubmissions = $latestTaskSubmissions ?? collect();

        $applicationRoute = Route::has('admin.permintaan.index') ? route('admin.permintaan.index') : '#';
        $participantRoute = Route::has('admin.peserta.index') ? route('admin.peserta.index') : '#';
        $attendanceRoute = Route::has('admin.absensi.index') ? route('admin.absensi.index') : '#';
        $paymentRoute = Route::has('admin.pembayaran.index') ? route('admin.pembayaran.index') : '#';
        $submissionRoute = Route::has('admin.pengumpulan-tugas.index') ? route('admin.pengumpulan-tugas.index') : '#';

        $chartWidth = 820;
        $chartHeight = 300;
        $paddingLeft = 54;
        $paddingRight = 28;
        $paddingTop = 30;
        $paddingBottom = 52;
        $plotWidth = $chartWidth - $paddingLeft - $paddingRight;
        $plotHeight = $chartHeight - $paddingTop - $paddingBottom;
        $chartMax = max(1, (int) $monthlyApplications->max('total'));
        $roundedChartMax = max(4, (int) ceil($chartMax / 4) * 4);
        $chartCount = max(1, $monthlyApplications->count());
        $xStep = $chartCount > 1 ? $plotWidth / ($chartCount - 1) : 0;

        $chartPoints = $monthlyApplications->values()->map(function ($item, $index) use ($paddingLeft, $paddingTop, $plotHeight, $roundedChartMax, $xStep) {
            $x = $paddingLeft + ($index * $xStep);
            $y = $paddingTop + $plotHeight - (((int) $item['total'] / $roundedChartMax) * $plotHeight);

            return [
                'x' => round($x, 2),
                'y' => round($y, 2),
                'total' => (int) $item['total'],
                'label' => $item['label'],
                'year' => $item['year'],
            ];
        });

        $polylinePoints = $chartPoints->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ');
        $areaPoints = $chartPoints->isNotEmpty()
            ? $paddingLeft.','.($paddingTop + $plotHeight).' '.$polylinePoints.' '.($paddingLeft + $plotWidth).','.($paddingTop + $plotHeight)
            : '';

        $present = (int) ($attendanceBreakdown['hadir'] ?? 0);
        $late = (int) ($attendanceBreakdown['terlambat'] ?? 0);
        $leave = (int) ($attendanceBreakdown['izin'] ?? 0);
        $attendanceTotal = $present + $late + $leave;
        $attendanceDivisor = max(1, $attendanceTotal);
        $presentPercent = round(($present / $attendanceDivisor) * 100, 1);
        $latePercent = round(($late / $attendanceDivisor) * 100, 1);
        $leavePercent = round(($leave / $attendanceDivisor) * 100, 1);
        $presentEnd = $presentPercent;
        $lateEnd = $presentPercent + $latePercent;
        $leaveEnd = min(100, $presentPercent + $latePercent + $leavePercent);

        // ======================================================================
        // 2) KARYAWAN
        // ======================================================================
        $totalKaryawan = $totalKaryawan ?? 0;
        $activeKaryawan = $activeKaryawan ?? 0;
        $totalDivisi = $totalDivisi ?? 0;
        $totalLamaranKaryawan = $totalLamaranKaryawan ?? 0;
        $pendingLamaranKaryawan = $pendingLamaranKaryawan ?? 0;
        $todayKaryawanAttendanceCount = $todayKaryawanAttendanceCount ?? 0;
        $karyawanAttendanceBreakdown = $karyawanAttendanceBreakdown ?? ['hadir' => 0, 'terlambat' => 0, 'izin' => 0];
        // Contoh isi $weeklyKaryawanAttendance dari controller:
        // [['label' => 'Sen', 'percent' => 90], ['label' => 'Sel', 'percent' => 95], ...] (7 hari, Sen-Min)
        $weeklyKaryawanAttendance = collect($weeklyKaryawanAttendance ?? []);
        $latestLamaranKaryawan = $latestLamaranKaryawan ?? collect();
        $latestResign = $latestResign ?? collect();

        $karyawanRoute = Route::has('admin.karyawan.index') ? route('admin.karyawan.index') : '#';
        $divisiRoute = Route::has('admin.divisi.index') ? route('admin.divisi.index') : '#';
        $lamaranKaryawanRoute = Route::has('admin.lamaran-karyawan.index') ? route('admin.lamaran-karyawan.index') : '#';
        $absensiKaryawanRoute = Route::has('admin.absensi-karyawan.index') ? route('admin.absensi-karyawan.index') : '#';
        $resignRoute = Route::has('admin.pengajuan-resign.index') ? route('admin.pengajuan-resign.index') : '#';

        $todayDayIndex = now()->dayOfWeekIso; // 1 = Senin ... 7 = Minggu

        $karyawanPresent = (int) ($karyawanAttendanceBreakdown['hadir'] ?? 0);
        $karyawanLate = (int) ($karyawanAttendanceBreakdown['terlambat'] ?? 0);
        $karyawanLeave = (int) ($karyawanAttendanceBreakdown['izin'] ?? 0);
        $karyawanAttendanceTotal = $karyawanPresent + $karyawanLate + $karyawanLeave;
        $karyawanAttendanceDivisor = max(1, $karyawanAttendanceTotal);
        $karyawanPresentPercent = round(($karyawanPresent / $karyawanAttendanceDivisor) * 100, 1);
        $karyawanLatePercent = round(($karyawanLate / $karyawanAttendanceDivisor) * 100, 1);
        $karyawanLeavePercent = round(($karyawanLeave / $karyawanAttendanceDivisor) * 100, 1);
        $karyawanPresentEnd = $karyawanPresentPercent;
        $karyawanLateEnd = $karyawanPresentPercent + $karyawanLatePercent;
        $karyawanLeaveEnd = min(100, $karyawanPresentPercent + $karyawanLatePercent + $karyawanLeavePercent);

        $karyawanAttendanceRate = $totalKaryawan > 0 ? round(($todayKaryawanAttendanceCount / $totalKaryawan) * 100, 1) : 0;
    @endphp

    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                Halo, {{ auth()->user()->nama ?? 'Admin' }} 👋
            </h1>

            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                Pantau operasional peserta magang dan karyawan dalam satu dashboard.
            </p>

            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-[11px] font-medium text-slate-400">
                <span>Diperbarui {{ now()->translatedFormat('d M Y, H:i') }}</span>
                <span class="hidden h-1 w-1 rounded-full bg-slate-300 sm:block"></span>
                <span>Akses Administrator</span>
            </div>
        </div>
    </section>

    {{-- ================= TAB SWITCHER ================= --}}
    <div class="mt-6 inline-flex items-center gap-1 rounded-2xl bg-slate-100 p-1" role="tablist" aria-label="Pilih dashboard">
        <button
            type="button"
            class="dashboard-tab-btn active-tab rounded-xl bg-white px-5 py-2 text-sm font-bold text-slate-900 shadow-sm transition"
            data-tab-target="magang"
            role="tab"
            aria-selected="true"
        >
            Peserta Magang
        </button>
        <button
            type="button"
            class="dashboard-tab-btn rounded-xl px-5 py-2 text-sm font-bold text-slate-500 transition hover:text-slate-700"
            data-tab-target="karyawan"
            role="tab"
            aria-selected="false"
        >
            Karyawan
        </button>
    </div>

    {{-- ======================================================================
         PANEL 1: PESERTA MAGANG
    ====================================================================== --}}
    <div id="tab-panel-magang" class="dashboard-tab-panel">

        <section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ $participantRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 to-cyan-500 p-5 text-white shadow-[0_16px_36px_rgba(2,132,199,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full border-[18px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-sky-100">Peserta Magang</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($totalInterns) }}</p>
                        <p class="mt-1 text-sm text-sky-100">{{ number_format($activeInterns) }} peserta aktif</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><circle cx="17" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M2.8 19c.5-3.5 2.2-5.2 5.2-5.2s4.8 1.7 5.2 5.2M14 14.5c2.8-.5 5 .9 5.7 3.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ $applicationRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-500 p-5 text-white shadow-[0_16px_36px_rgba(79,70,229,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-indigo-100">Pengajuan Magang</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($totalApplications) }}</p>
                        <p class="mt-1 text-sm text-indigo-100">{{ number_format($pendingApplications) }} menunggu ditinjau</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4V5Zm0 9h4l2 2h4l2-2h4" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 9h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ $paymentRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-500 p-5 text-white shadow-[0_16px_36px_rgba(13,148,136,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -right-6 -top-10 h-32 w-32 rounded-[36px] border border-white/15"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-emerald-100">Pembayaran Bulanan</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($paidParticipantsThisMonth) }}</p>
                        <p class="mt-1 text-sm text-emerald-100">Peserta lunas bulan ini</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M3 9h18M5 9V7l7-4 7 4v2M6 9v8M10 9v8M14 9v8M18 9v8M4 17h16M3 21h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ $attendanceRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-800 to-cyan-700 p-5 text-white shadow-[0_16px_36px_rgba(30,64,175,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -bottom-20 left-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-blue-100">Absensi Hari Ini</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($todayAttendanceCount) }}</p>
                        <p class="mt-1 text-sm text-blue-100">Data masuk {{ now()->format('d/m/Y') }}</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="m7.5 16 1.6 1.5 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
            </a>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.65fr_0.85fr]">
            <article class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
                <div class="flex flex-col gap-3 border-b border-slate-200/80 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Pengajuan Magang per Bulan</h2>
                        <p class="mt-1 text-sm text-slate-500">Tren jumlah pengajuan selama 12 bulan terakhir.</p>
                    </div>
                    <a href="{{ $applicationRoute }}" class="inline-flex w-fit items-center gap-1 rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5">
                        Lihat pengajuan
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                <div class="overflow-x-auto px-4 pb-4 pt-5 sm:px-6">
                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="min-w-[760px]" role="img" aria-label="Grafik pengajuan magang per bulan">
                        <defs>
                            <linearGradient id="applicationArea" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0284c7" stop-opacity="0.28"/>
                                <stop offset="100%" stop-color="#0284c7" stop-opacity="0.02"/>
                            </linearGradient>
                            <filter id="pointShadow" x="-100%" y="-100%" width="300%" height="300%">
                                <feDropShadow dx="0" dy="3" stdDeviation="3" flood-color="#0369a1" flood-opacity="0.2"/>
                            </filter>
                        </defs>

                        @for ($step = 0; $step <= 4; $step++)
                            @php
                                $gridY = $paddingTop + (($plotHeight / 4) * $step);
                                $gridValue = (int) round($roundedChartMax - (($roundedChartMax / 4) * $step));
                            @endphp
                            <line x1="{{ $paddingLeft }}" y1="{{ $gridY }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $gridY }}" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="5 6"/>
                            <text x="{{ $paddingLeft - 14 }}" y="{{ $gridY + 4 }}" text-anchor="end" fill="#94a3b8" font-size="11" font-weight="600">{{ $gridValue }}</text>
                        @endfor

                        @if ($chartPoints->isNotEmpty())
                            <polygon points="{{ $areaPoints }}" fill="url(#applicationArea)"/>
                            <polyline points="{{ $polylinePoints }}" fill="none" stroke="#0284c7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>

                            @foreach ($chartPoints as $point)
                                <line x1="{{ $point['x'] }}" y1="{{ $paddingTop + $plotHeight }}" x2="{{ $point['x'] }}" y2="{{ $point['y'] }}" stroke="#bae6fd" stroke-width="1" stroke-dasharray="3 5"/>
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="6" fill="#ffffff" stroke="#0284c7" stroke-width="3" filter="url(#pointShadow)"/>
                                <text x="{{ $point['x'] }}" y="{{ max(15, $point['y'] - 13) }}" text-anchor="middle" fill="#0f172a" font-size="12" font-weight="800">{{ $point['total'] }}</text>
                                <text x="{{ $point['x'] }}" y="{{ $paddingTop + $plotHeight + 25 }}" text-anchor="middle" fill="#64748b" font-size="11" font-weight="700">{{ $point['label'] }}</text>
                                @if ($loop->first || $point['label'] === 'Jan')
                                    <text x="{{ $point['x'] }}" y="{{ $paddingTop + $plotHeight + 41 }}" text-anchor="middle" fill="#cbd5e1" font-size="9" font-weight="600">{{ $point['year'] }}</text>
                                @endif
                            @endforeach
                        @endif
                    </svg>
                </div>
            </article>

            <article class="rounded-3xl border border-white/80 bg-white/90 p-5 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur sm:p-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Status Absensi Hari Ini</h2>
                    <p class="mt-1 text-sm text-slate-500">Komposisi hadir, terlambat, dan izin.</p>
                </div>

                <div class="mt-6 flex flex-col items-center">
                    <div
                        class="relative grid h-48 w-48 place-items-center rounded-full shadow-[0_18px_45px_rgba(15,52,94,0.12)]"
                        style="background: conic-gradient(#0ea5e9 0% {{ $presentEnd }}%, #f59e0b {{ $presentEnd }}% {{ $lateEnd }}%, #8b5cf6 {{ $lateEnd }}% {{ $leaveEnd }}%, #e2e8f0 {{ $leaveEnd }}% 100%);"
                        role="img"
                        aria-label="Diagram lingkaran status absensi hari ini"
                    >
                        <div class="grid h-32 w-32 place-items-center rounded-full bg-white text-center shadow-inner ring-1 ring-slate-100">
                            <div>
                                <p class="text-3xl font-extrabold text-slate-950">{{ $attendanceTotal }}</p>
                                <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Total Absensi</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 w-full space-y-3">
                        <div class="flex items-center justify-between rounded-2xl bg-sky-50 px-4 py-3 ring-1 ring-sky-100">
                            <div class="flex items-center gap-3">
                                <span class="h-3 w-3 rounded-full bg-sky-500 shadow-[0_0_0_4px_rgba(14,165,233,0.12)]"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Hadir</p>
                                    <p class="text-[11px] text-slate-500">Datang tepat waktu</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-extrabold text-slate-950">{{ $present }}</p>
                                <p class="text-[10px] font-bold text-sky-600">{{ $presentPercent }}%</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-amber-50 px-4 py-3 ring-1 ring-amber-100">
                            <div class="flex items-center gap-3">
                                <span class="h-3 w-3 rounded-full bg-amber-500 shadow-[0_0_0_4px_rgba(245,158,11,0.12)]"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Terlambat</p>
                                    <p class="text-[11px] text-slate-500">Melewati jam masuk</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-extrabold text-slate-950">{{ $late }}</p>
                                <p class="text-[10px] font-bold text-amber-600">{{ $latePercent }}%</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-violet-50 px-4 py-3 ring-1 ring-violet-100">
                            <div class="flex items-center gap-3">
                                <span class="h-3 w-3 rounded-full bg-violet-500 shadow-[0_0_0_4px_rgba(139,92,246,0.12)]"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Izin</p>
                                    <p class="text-[11px] text-slate-500">Tidak hadir dengan izin</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-extrabold text-slate-950">{{ $leave }}</p>
                                <p class="text-[10px] font-bold text-violet-600">{{ $leavePercent }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-2">
            <article class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200/80 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Absensi Terbaru</h2>
                        <p class="mt-1 text-sm text-slate-500">Aktivitas absensi peserta yang paling baru.</p>
                    </div>
                    <a href="{{ $attendanceRoute }}" class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5">Lihat semua</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($latestAttendance as $attendance)
                        @php
                            $participantName = $attendance->peserta?->user?->nama ?? 'Peserta Magang';
                            $initials = collect(preg_split('/\s+/', trim($participantName)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                            $status = strtolower((string) $attendance->status);
                            $statusClasses = match ($status) {
                                'hadir' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                'terlambat' => 'bg-amber-100 text-amber-700 ring-amber-200',
                                'izin' => 'bg-violet-100 text-violet-700 ring-violet-200',
                                'sakit' => 'bg-blue-100 text-blue-700 ring-blue-200',
                                default => 'bg-rose-100 text-rose-700 ring-rose-200',
                            };
                        @endphp
                        <div class="flex items-center gap-3 px-5 py-4 transition hover:bg-sky-50/50">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200">
                                {{ $initials ?: 'PM' }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-bold text-slate-900">{{ $participantName }}</p>
                                    <span class="rounded-full px-2 py-1 text-[9px] font-extrabold uppercase tracking-wide ring-1 {{ $statusClasses }}">{{ $attendance->status }}</span>
                                </div>
                                <p class="mt-1 truncate text-xs text-slate-500">
                                    {{ $attendance->tanggal?->format('d M Y') ?? '-' }}
                                    <span class="mx-1 text-slate-300">•</span>
                                    {{ $attendance->jam ? substr((string) $attendance->jam, 0, 5).' WIB' : 'Jam belum tercatat' }}
                                </p>
                            </div>
                            <span class="hidden text-[10px] font-medium text-slate-400 sm:block">{{ $attendance->created_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-14 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <p class="mt-3 font-bold text-slate-800">Belum ada data absensi</p>
                            <p class="mt-1 text-sm text-slate-500">Data terbaru akan tampil setelah peserta melakukan absensi.</p>
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200/80 bg-gradient-to-r from-indigo-50 to-violet-50 px-5 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Pengumpulan Tugas Terbaru</h2>
                        <p class="mt-1 text-sm text-slate-500">Berkas tugas peserta yang terakhir dikumpulkan.</p>
                    </div>
                    <a href="{{ $submissionRoute }}" class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-indigo-700 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5">Lihat semua</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($latestTaskSubmissions as $submission)
                        @php
                            $participantName = $submission->peserta?->user?->nama ?? 'Peserta Magang';
                            $initials = collect(preg_split('/\s+/', trim($participantName)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                            $submissionStatus = strtolower((string) $submission->status);
                            $submissionStatusClasses = match ($submissionStatus) {
                                'dinilai' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                'telat' => 'bg-rose-100 text-rose-700 ring-rose-200',
                                default => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
                            };
                            $submittedAt = $submission->dikumpulkan_pada ?? $submission->created_at;
                        @endphp
                        <div class="flex items-center gap-3 px-5 py-4 transition hover:bg-indigo-50/40">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-indigo-100 to-violet-100 text-xs font-extrabold text-indigo-700 ring-1 ring-indigo-200">
                                {{ $initials ?: 'PM' }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-bold text-slate-900">{{ $participantName }}</p>
                                    <span class="rounded-full px-2 py-1 text-[9px] font-extrabold uppercase tracking-wide ring-1 {{ $submissionStatusClasses }}">{{ $submission->status }}</span>
                                </div>
                                <p class="mt-1 truncate text-xs font-semibold text-slate-600">{{ $submission->tugas?->judul ?? 'Tugas Magang' }}</p>
                                <p class="mt-0.5 truncate text-[11px] text-slate-400">
                                    {{ $submittedAt?->format('d M Y, H:i') ?? 'Waktu belum tercatat' }}
                                </p>
                            </div>
                            <span class="hidden text-[10px] font-medium text-slate-400 sm:block">{{ $submittedAt?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-14 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-indigo-50 text-indigo-500">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l3 3v15H6V3Zm3 7h6M9 14h6M9 18h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <p class="mt-3 font-bold text-slate-800">Belum ada pengumpulan tugas</p>
                            <p class="mt-1 text-sm text-slate-500">Tugas terbaru akan muncul setelah peserta mengunggah jawaban.</p>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>

    {{-- ======================================================================
         PANEL 2: KARYAWAN
    ====================================================================== --}}
    <div id="tab-panel-karyawan" class="dashboard-tab-panel hidden">

        <section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ $karyawanRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 to-purple-500 p-5 text-white shadow-[0_16px_36px_rgba(124,58,237,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full border-[18px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-violet-100">Total Karyawan</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($totalKaryawan) }}</p>
                        <p class="mt-1 text-sm text-violet-100">{{ number_format($activeKaryawan) }} karyawan aktif</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><circle cx="17" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M2.8 19c.5-3.5 2.2-5.2 5.2-5.2s4.8 1.7 5.2 5.2M14 14.5c2.8-.5 5 .9 5.7 3.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ $lamaranKaryawanRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-600 to-orange-500 p-5 text-white shadow-[0_16px_36px_rgba(217,119,6,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-amber-100">Permintaan Lamaran</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($totalLamaranKaryawan) }}</p>
                        <p class="mt-1 text-sm text-amber-100">{{ number_format($pendingLamaranKaryawan) }} menunggu ditinjau</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4V5Zm0 9h4l2 2h4l2-2h4" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 9h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ $absensiKaryawanRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-600 to-pink-500 p-5 text-white shadow-[0_16px_36px_rgba(225,29,72,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -bottom-20 left-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-rose-100">Kehadiran Hari Ini</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($todayKaryawanAttendanceCount) }}</p>
                        <p class="mt-1 text-sm text-rose-100">dari {{ number_format($totalKaryawan) }} karyawan ({{ $karyawanAttendanceRate }}%)</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="m7.5 16 1.6 1.5 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ $divisiRoute }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-slate-500 p-5 text-white shadow-[0_16px_36px_rgba(51,65,85,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -right-6 -top-10 h-32 w-32 rounded-[36px] border border-white/15"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-200">Total Divisi</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($totalDivisi) }}</p>
                        <p class="mt-1 text-sm text-slate-200">Unit kerja aktif</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M4 21V5a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v16M12 21v-8a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v8M4 21h16M7 8h1M7 12h1M7 16h1M15 12h1M15 16h1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </a>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.65fr_0.85fr]">
            <article class="rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
                <div class="flex flex-col gap-3 border-b border-slate-200/80 bg-gradient-to-r from-violet-50 to-purple-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Kehadiran Minggu Ini</h2>
                        <p class="mt-1 text-sm text-slate-500">Persentase kehadiran karyawan per hari (Senin–Minggu).</p>
                    </div>
                    <a href="{{ $absensiKaryawanRoute }}" class="inline-flex w-fit items-center gap-1 rounded-xl bg-white px-4 py-2 text-xs font-bold text-violet-700 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5">
                        Lihat absensi
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                <div class="px-5 py-6 sm:px-6">
                    @if ($weeklyKaryawanAttendance->isNotEmpty())
                        <div class="flex items-end gap-3 px-1" style="height: 180px;">
                            @foreach ($weeklyKaryawanAttendance as $day)
                                @php $isToday = ($loop->iteration === $todayDayIndex); @endphp
                                <div class="flex flex-1 flex-col items-center gap-2">
                                    <span class="text-[10px] font-extrabold text-slate-700">{{ $day['percent'] }}%</span>
                                    <div class="flex w-full items-end justify-center rounded-t-lg bg-slate-100" style="height: 130px;">
                                        <div
                                            class="w-full rounded-t-lg {{ $isToday ? 'bg-violet-600' : 'bg-violet-300' }}"
                                            style="height: {{ max(4, (int) $day['percent']) }}%;"
                                        ></div>
                                    </div>
                                    <span class="text-[11px] font-bold {{ $isToday ? 'text-violet-700' : 'text-slate-400' }}">{{ $day['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-5 py-14 text-center">
                            <p class="font-bold text-slate-800">Belum ada data kehadiran minggu ini</p>
                            <p class="mt-1 text-sm text-slate-500">Grafik akan tampil setelah data absensi karyawan tercatat.</p>
                        </div>
                    @endif
                </div>
            </article>

            <article class="rounded-3xl border border-white/80 bg-white/90 p-5 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur sm:p-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Status Absensi Karyawan</h2>
                    <p class="mt-1 text-sm text-slate-500">Komposisi hadir, terlambat, dan izin hari ini.</p>
                </div>

                <div class="mt-6 flex flex-col items-center">
                    <div
                        class="relative grid h-48 w-48 place-items-center rounded-full shadow-[0_18px_45px_rgba(15,52,94,0.12)]"
                        style="background: conic-gradient(#7c3aed 0% {{ $karyawanPresentEnd }}%, #f59e0b {{ $karyawanPresentEnd }}% {{ $karyawanLateEnd }}%, #ec4899 {{ $karyawanLateEnd }}% {{ $karyawanLeaveEnd }}%, #e2e8f0 {{ $karyawanLeaveEnd }}% 100%);"
                        role="img"
                        aria-label="Diagram lingkaran status absensi karyawan"
                    >
                        <div class="grid h-32 w-32 place-items-center rounded-full bg-white text-center shadow-inner ring-1 ring-slate-100">
                            <div>
                                <p class="text-3xl font-extrabold text-slate-950">{{ $karyawanAttendanceTotal }}</p>
                                <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Total Absensi</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 w-full space-y-3">
                        <div class="flex items-center justify-between rounded-2xl bg-violet-50 px-4 py-3 ring-1 ring-violet-100">
                            <div class="flex items-center gap-3">
                                <span class="h-3 w-3 rounded-full bg-violet-600 shadow-[0_0_0_4px_rgba(124,58,237,0.12)]"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Hadir</p>
                                    <p class="text-[11px] text-slate-500">Datang tepat waktu</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-extrabold text-slate-950">{{ $karyawanPresent }}</p>
                                <p class="text-[10px] font-bold text-violet-700">{{ $karyawanPresentPercent }}%</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-amber-50 px-4 py-3 ring-1 ring-amber-100">
                            <div class="flex items-center gap-3">
                                <span class="h-3 w-3 rounded-full bg-amber-500 shadow-[0_0_0_4px_rgba(245,158,11,0.12)]"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Terlambat</p>
                                    <p class="text-[11px] text-slate-500">Melewati jam masuk</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-extrabold text-slate-950">{{ $karyawanLate }}</p>
                                <p class="text-[10px] font-bold text-amber-600">{{ $karyawanLatePercent }}%</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-pink-50 px-4 py-3 ring-1 ring-pink-100">
                            <div class="flex items-center gap-3">
                                <span class="h-3 w-3 rounded-full bg-pink-500 shadow-[0_0_0_4px_rgba(236,72,153,0.12)]"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Izin</p>
                                    <p class="text-[11px] text-slate-500">Tidak hadir dengan izin</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-extrabold text-slate-950">{{ $karyawanLeave }}</p>
                                <p class="text-[10px] font-bold text-pink-600">{{ $karyawanLeavePercent }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-2">
            <article class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200/80 bg-gradient-to-r from-amber-50 to-orange-50 px-5 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Permintaan Lamaran Terbaru</h2>
                        <p class="mt-1 text-sm text-slate-500">Lamaran kerja karyawan yang baru masuk.</p>
                    </div>
                    <a href="{{ $lamaranKaryawanRoute }}" class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-amber-700 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5">Lihat semua</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($latestLamaranKaryawan as $lamaran)
                        @php
                            $candidateName = $lamaran->user?->nama ?? $lamaran->nama ?? 'Kandidat';
                            $initials = collect(preg_split('/\s+/', trim($candidateName)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                            $lamaranStatus = strtolower((string) $lamaran->status);
                            $lamaranStatusClasses = match ($lamaranStatus) {
                                'diterima' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                'interview' => 'bg-orange-100 text-orange-700 ring-orange-200',
                                'ditolak' => 'bg-rose-100 text-rose-700 ring-rose-200',
                                default => 'bg-amber-100 text-amber-700 ring-amber-200',
                            };
                        @endphp
                        <div class="flex items-center gap-3 px-5 py-4 transition hover:bg-amber-50/50">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 text-xs font-extrabold text-amber-700 ring-1 ring-amber-200">
                                {{ $initials ?: 'KY' }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-bold text-slate-900">{{ $candidateName }}</p>
                                    <span class="rounded-full px-2 py-1 text-[9px] font-extrabold uppercase tracking-wide ring-1 {{ $lamaranStatusClasses }}">{{ $lamaran->status }}</span>
                                </div>
                                <p class="mt-1 truncate text-xs text-slate-500">
                                    {{ $lamaran->posisi ?? $lamaran->divisi?->nama ?? 'Posisi belum ditentukan' }}
                                    <span class="mx-1 text-slate-300">•</span>
                                    {{ $lamaran->created_at?->format('d M Y') ?? '-' }}
                                </p>
                            </div>
                            <span class="hidden text-[10px] font-medium text-slate-400 sm:block">{{ $lamaran->created_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-14 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-amber-50 text-amber-500">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4V5Zm0 9h4l2 2h4l2-2h4" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            </span>
                            <p class="mt-3 font-bold text-slate-800">Belum ada lamaran masuk</p>
                            <p class="mt-1 text-sm text-slate-500">Lamaran terbaru akan tampil di sini.</p>
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200/80 bg-gradient-to-r from-rose-50 to-pink-50 px-5 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Pengajuan Resign Terbaru</h2>
                        <p class="mt-1 text-sm text-slate-500">Permohonan pengunduran diri karyawan.</p>
                    </div>
                    <a href="{{ $resignRoute }}" class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-rose-700 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5">Lihat semua</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($latestResign as $resign)
                        @php
                            $employeeName = $resign->karyawan?->user?->nama ?? 'Karyawan';
                            $initials = collect(preg_split('/\s+/', trim($employeeName)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                            $resignStatus = strtolower((string) $resign->status);
                            $resignStatusClasses = match ($resignStatus) {
                                'disetujui' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                'ditolak' => 'bg-rose-100 text-rose-700 ring-rose-200',
                                default => 'bg-slate-100 text-slate-700 ring-slate-200',
                            };
                        @endphp
                        <div class="flex items-center gap-3 px-5 py-4 transition hover:bg-rose-50/40">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-rose-100 to-pink-100 text-xs font-extrabold text-rose-700 ring-1 ring-rose-200">
                                {{ $initials ?: 'KY' }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-bold text-slate-900">{{ $employeeName }}</p>
                                    <span class="rounded-full px-2 py-1 text-[9px] font-extrabold uppercase tracking-wide ring-1 {{ $resignStatusClasses }}">{{ $resign->status }}</span>
                                </div>
                                <p class="mt-1 truncate text-xs text-slate-500">
                                    {{ $resign->divisi?->nama ?? 'Divisi belum tercatat' }}
                                    <span class="mx-1 text-slate-300">•</span>
                                    {{ $resign->created_at?->format('d M Y') ?? '-' }}
                                </p>
                            </div>
                            <span class="hidden text-[10px] font-medium text-slate-400 sm:block">{{ $resign->created_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-14 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-rose-50 text-rose-500">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l3 3v15H6V3Zm3 7h6M9 14h6M9 18h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <p class="mt-3 font-bold text-slate-800">Belum ada pengajuan resign</p>
                            <p class="mt-1 text-sm text-slate-500">Pengajuan terbaru akan tampil di sini.</p>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>

    <script>
        (function () {
            const buttons = document.querySelectorAll('.dashboard-tab-btn');
            const panels = document.querySelectorAll('.dashboard-tab-panel');

            buttons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    buttons.forEach((b) => {
                        b.classList.remove('bg-white', 'shadow-sm', 'text-slate-900');
                        b.classList.add('text-slate-500');
                        b.setAttribute('aria-selected', 'false');
                    });
                    btn.classList.add('bg-white', 'shadow-sm', 'text-slate-900');
                    btn.classList.remove('text-slate-500');
                    btn.setAttribute('aria-selected', 'true');

                    panels.forEach((panel) => panel.classList.add('hidden'));
                    document.getElementById('tab-panel-' + btn.dataset.tabTarget).classList.remove('hidden');
                });
            });
        })();
    </script>
@endsection