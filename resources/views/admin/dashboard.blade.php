@extends('layouts.portal')

@section('title', 'Dashboard - Natusi Admin')
@section('page-title', 'Internship Portal')

@section('content')
    {{-- Welcome Header --}}
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
        <div>
            <h3 class="text-3xl font-bold tracking-tight text-slate-900" style="font-family: 'Manrope', sans-serif;">
                Dashboard Overview
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Monitoring internship activities and administrative tasks for CV Natusi.
            </p>
        </div>
    </div>

    {{-- Key Metrics --}}
    <section class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative flex flex-col gap-3 overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1 bg-[#006191]"></div>
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">ACTIVE INTERNS</span>
                <div class="rounded-lg bg-[#006191]/10 p-2 text-[#006191]">
                    <span class="material-symbols-outlined">groups</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold" style="font-family: 'Manrope', sans-serif;">
                    {{ $activeInterns ?? 0 }}
                </span>
                <span class="text-xs font-bold text-green-600">
                    +{{ $newInternsThisMonth ?? 0 }} this month
                </span>
            </div>
        </div>

        <div class="relative flex flex-col gap-3 overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1 bg-[#d32f2f]"></div>
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">PENDING REQUESTS</span>
                <div class="rounded-lg bg-[#d32f2f]/10 p-2 text-[#d32f2f]">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold" style="font-family: 'Manrope', sans-serif;">
                    {{ $pendingRequests ?? 0 }}
                </span>
                <span class="text-xs font-bold text-[#d32f2f]">Needs Review</span>
            </div>
        </div>

        <div class="relative flex flex-col gap-3 overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1 bg-indigo-500"></div>
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">TODAY'S ATTENDANCE</span>
                <div class="rounded-lg bg-indigo-50 p-2 text-indigo-500">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold" style="font-family: 'Manrope', sans-serif;">
                    {{ $attendanceRate ?? 0 }}%
                </span>
                <span class="text-xs text-slate-400">
                    {{ $presentToday ?? 0 }}/{{ $activeInterns ?? 0 }} Present
                </span>
            </div>
        </div>

        <div class="relative flex flex-col gap-3 overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1 bg-sky-200"></div>
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">TASK REVIEWS</span>
                <div class="rounded-lg bg-sky-50 p-2 text-sky-600">
                    <span class="material-symbols-outlined">assignment_turned_in</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold" style="font-family: 'Manrope', sans-serif;">
                    {{ str_pad($pendingTaskReviews ?? 0, 2, '0', STR_PAD_LEFT) }}
                </span>
                <span class="text-xs text-slate-400">Assignments pending</span>
            </div>
        </div>
    </section>

    {{-- Main Section --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="space-y-6 lg:col-span-2">
            {{-- Laporan Magang Summary --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-6 w-1 rounded-full bg-[#006191]"></div>
                        <h4 class="text-lg font-semibold text-slate-900" style="font-family: 'Manrope', sans-serif;">
                            Laporan Magang Summary
                        </h4>
                    </div>

                    @if (Route::has('admin.laporan.peserta'))
                        <a href="{{ route('admin.laporan.peserta') }}"
                           class="text-[11px] font-semibold tracking-wider text-[#006191] hover:underline">
                            View All
                        </a>
                    @endif
                </div>

                <div class="p-5">
                    <div class="space-y-4">
                        @forelse ($recentReports ?? [] as $report)
                            <div class="group flex items-center gap-4 rounded-lg p-3 transition-colors hover:bg-slate-50">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                    <span class="material-symbols-outlined text-[#006191]">description</span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h5 class="truncate text-sm font-semibold text-slate-900">
                                        {{ $report->title ?? 'Tanpa judul' }}
                                    </h5>
                                    <p class="truncate text-sm text-slate-500">
                                        Submitted by: {{ $report->intern_name ?? '-' }}
                                        &bull;
                                        {{ !empty($report->submitted_at) ? \Illuminate\Support\Carbon::parse($report->submitted_at)->diffForHumans() : '-' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="rounded px-2 py-1 text-[10px] font-bold uppercase
                                        {{ ($report->status ?? '') === 'submitted'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ($report->status ?? '') === 'submitted' ? 'SUBMITTED' : 'IN REVIEW' }}
                                    </span>
                                    <button type="button"
                                            class="rounded p-1 transition-colors hover:bg-white group-hover:shadow-sm"
                                            aria-label="Lihat laporan">
                                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm italic text-slate-500">Belum ada laporan yang masuk.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Weekly Attendance Overview --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-6 w-1 rounded-full bg-[#006191]"></div>
                        <h4 class="text-lg font-semibold text-slate-900" style="font-family: 'Manrope', sans-serif;">
                            Weekly Attendance Overview
                        </h4>
                    </div>
                </div>

                <div class="p-5">
                    @php
                        $days = $weeklyAttendance ?? [
                            'Mon' => 95,
                            'Tue' => 92,
                            'Wed' => 98,
                            'Thu' => 94,
                            'Fri' => 88,
                        ];
                    @endphp

                    <div class="flex h-40 items-end justify-between gap-4 px-2">
                        @foreach ($days as $day => $percent)
                            @php
                                $safePercent = max(0, min(100, (int) $percent));
                            @endphp
                            <div class="flex flex-1 flex-col items-center gap-2">
                                <div class="group relative h-32 w-full overflow-hidden rounded-t-lg bg-[#006191]/10">
                                    <div class="absolute inset-x-0 bottom-0 rounded-t-lg bg-[#006191] transition-all"
                                         style="height: {{ $safePercent }}%"></div>
                                    <div class="absolute left-1/2 top-1 -translate-x-1/2 rounded bg-slate-900 px-2 py-1 text-[10px] text-white opacity-0 transition-opacity group-hover:opacity-100">
                                        {{ $safePercent }}%
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500">{{ $day }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-200 pt-5 sm:grid-cols-4">
                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full bg-red-500"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-slate-500">Absen</span>
                                <span class="text-sm font-bold text-slate-900">{{ $absentCount ?? 0 }} Orang</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full border border-yellow-700 bg-yellow-100"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-slate-500">Izin/Sakit</span>
                                <span class="text-sm font-bold text-slate-900">{{ $leaveCount ?? 0 }} Orang</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full bg-slate-300"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-slate-500">Belum Absen</span>
                                <span class="text-sm font-bold text-slate-900">{{ $notYetCount ?? 0 }} Orang</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full bg-[#006191]"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-slate-500">Hadir</span>
                                <span class="text-sm font-bold text-slate-900">{{ $presentToday ?? 0 }} Orang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sidebar Content --}}
        <aside class="space-y-6">
            {{-- Monthly Payment Info --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4">
                    <div class="h-6 w-1 rounded-full bg-[#006191]"></div>
                    <h4 class="text-lg font-semibold text-slate-900" style="font-family: 'Manrope', sans-serif;">
                        Monthly Payment Info
                    </h4>
                </div>

                <div class="space-y-4 p-5">
                    <div class="flex flex-col gap-1">
                        <span class="text-[11px] font-bold text-slate-500">TOTAL COLLECTED</span>
                        <span class="text-2xl font-extrabold text-[#006191]">
                            Rp {{ number_format($totalCollected ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg border border-[#006191]/10 bg-[#006191]/5 p-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#006191]">pending_actions</span>
                            <span class="text-sm font-medium text-slate-700">
                                {{ $pendingPayments ?? 0 }} Pending Verification
                            </span>
                        </div>
                        <span class="material-symbols-outlined text-[18px] text-[#006191]">arrow_forward</span>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4">
                    <div class="h-6 w-1 rounded-full bg-red-500"></div>
                    <h4 class="text-lg font-semibold text-slate-900" style="font-family: 'Manrope', sans-serif;">
                        Quick Links
                    </h4>
                </div>

                <div class="space-y-2 p-5">
                    @php
                        $quickLinks = [
                            ['label' => 'Laporan Absensi', 'icon' => 'calendar_today', 'route' => 'admin.laporan.absensi'],
                            ['label' => 'Laporan Penugasan', 'icon' => 'task', 'route' => 'admin.laporan.penugasan'],
                            ['label' => 'Performance Analytics', 'icon' => 'analytics', 'route' => 'admin.analytics'],
                        ];
                    @endphp

                    @foreach ($quickLinks as $link)
                        @php
                            $routeExists = Route::has($link['route']);
                        @endphp
                        <a href="{{ $routeExists ? route($link['route']) : '#' }}"
                           class="group flex items-center justify-between rounded-lg border border-transparent bg-slate-50 p-4 transition-all hover:border-slate-200 hover:bg-slate-100 {{ $routeExists ? '' : 'pointer-events-none opacity-50' }}">
                            <div class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-[#006191]">{{ $link['icon'] }}</span>
                                <span class="text-sm font-semibold text-slate-700">{{ $link['label'] }}</span>
                            </div>
                            <span class="material-symbols-outlined text-slate-400 opacity-0 transition-opacity group-hover:opacity-100">
                                arrow_forward
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Attendance Alerts --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h4 class="text-lg font-semibold text-slate-900" style="font-family: 'Manrope', sans-serif;">
                        Attendance Alerts
                    </h4>
                </div>

                <div class="space-y-4 p-5">
                    @forelse ($attendanceAlerts ?? [] as $alert)
                        @php
                            $isWarning = ($alert['type'] ?? '') === 'warning';
                        @endphp
                        <div class="flex items-start gap-3 rounded-lg border-l-4 p-3
                            {{ $isWarning
                                ? 'border-red-500 bg-red-50'
                                : 'border-[#006191] bg-sky-50' }}">
                            <span class="material-symbols-outlined mt-1 {{ $isWarning ? 'text-red-500' : 'text-[#006191]' }}">
                                {{ $isWarning ? 'warning' : 'info' }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $alert['title'] ?? 'Informasi' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $alert['message'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm italic text-slate-500">Tidak ada notifikasi saat ini.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>

    {{-- Recent Task Review Table --}}
    <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h4 class="text-lg font-semibold text-slate-900" style="font-family: 'Manrope', sans-serif;">
                Recent Task Reviews
            </h4>

            @if (Route::has('admin.pengumpulan-tugas.index'))
                <a href="{{ route('admin.pengumpulan-tugas.index') }}"
                   class="flex items-center gap-1 text-sm font-semibold text-[#006191] hover:underline">
                    Show More
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500">INTERN NAME</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500">TASK TITLE</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500">DEADLINE</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500">STATUS</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($taskReviews ?? [] as $task)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-[#006191]">
                                        {{ $task->initials ?? '?' }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">
                                        {{ $task->intern_name ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $task->title ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">
                                {{ !empty($task->deadline) ? \Illuminate\Support\Carbon::parse($task->deadline)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded px-2 py-1 text-[10px] font-bold uppercase
                                    {{ ($task->status ?? '') === 'completed'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-sky-100 text-[#006191]' }}">
                                    {{ ($task->status ?? '') === 'completed' ? 'Completed' : 'In Review' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button"
                                        class="text-sm font-semibold hover:underline
                                            {{ ($task->status ?? '') === 'completed' ? 'text-slate-500' : 'text-[#006191]' }}">
                                    {{ ($task->status ?? '') === 'completed' ? 'Details' : 'Review' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-sm italic text-slate-500">
                                Belum ada tugas yang perlu direview.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-5 py-3">
            <span class="text-xs italic text-slate-500">
                Showing {{ count($taskReviews ?? []) }} of {{ $pendingTaskReviews ?? 0 }} pending reviews
            </span>
        </div>
    </section>
@endsection