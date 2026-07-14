@extends('layouts.portal')

@section('title', 'Dashboard - Natusi Admin')
@section('page-title', 'Internship Portal')

@section('content')

    {{-- Welcome Header --}}
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
        <div>
            <h3 class="text-3xl font-bold tracking-tight text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Dashboard Overview</h3>
            <p class="text-sm text-slate-500 mt-1">Monitoring internship activities and administrative tasks for CV Natusi.</p>
        </div>
    </div>

    {{-- Key Metrics --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#006191]"></div>
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">ACTIVE INTERNS</span>
                <div class="p-2 bg-[#006191]/10 rounded-lg text-[#006191]">
                    <span class="material-symbols-outlined">groups</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold headline" style="font-family: 'Manrope', sans-serif;">{{ $activeInterns ?? 0 }}</span>
                <span class="text-xs text-green-600 font-bold">+{{ $newInternsThisMonth ?? 0 }} this month</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#d32f2f]"></div>
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">PENDING REQUESTS</span>
                <div class="p-2 bg-[#d32f2f]/10 rounded-lg text-[#d32f2f]">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold headline" style="font-family: 'Manrope', sans-serif;">{{ $pendingRequests ?? 0 }}</span>
                <span class="text-xs text-[#d32f2f] font-bold">Needs Review</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#6366f1]"></div>
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">TODAY'S ATTENDANCE</span>
                <div class="p-2 bg-[#6366f1]/10 rounded-lg text-[#6366f1]">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold headline" style="font-family: 'Manrope', sans-serif;">{{ $attendanceRate ?? 0 }}%</span>
                <span class="text-xs text-slate-400">{{ $presentToday ?? 0 }}/{{ $activeInterns ?? 0 }} Present</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#f59e0b]"></div>
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-semibold tracking-wider text-slate-500">TASK REVIEWS</span>
                <div class="p-2 bg-[#f59e0b]/10 rounded-lg text-[#f59e0b]">
                    <span class="material-symbols-outlined">assignment_turned_in</span>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold headline" style="font-family: 'Manrope', sans-serif;">{{ str_pad($pendingTaskReviews ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="text-xs text-slate-400">Assignments pending</span>
            </div>
        </div>
    </section>

    {{-- Main Interactive Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        {{-- Laporan Magang Summary --}}
        <section class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 bg-[#006191] rounded-full"></div>
                        <h4 class="text-lg font-semibold text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Laporan Magang Summary</h4>
                    </div>
                    @if (Route::has('admin.laporan.peserta'))
                        <a href="{{ route('admin.laporan.peserta') }}" class="text-[#006191] text-[11px] font-semibold tracking-wider hover:underline">View All</a>
                    @endif
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        @forelse ($recentReports ?? [] as $report)
                            <div class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-lg transition-colors group">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[#006191]">description</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-semibold text-slate-900 truncate">{{ $report->title }}</h5>
                                    <p class="text-sm text-slate-500 truncate">Submitted by: {{ $report->intern_name }} &bull; {{ $report->submitted_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-1 text-[10px] font-bold rounded uppercase
                                        {{ $report->status === 'submitted' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $report->status === 'submitted' ? 'Submitted' : 'In Review' }}
                                    </span>
                                    <button class="p-1 hover:bg-white rounded transition-colors group-hover:shadow-sm">
                                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 italic">Belum ada laporan yang masuk.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Weekly Attendance Overview --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 bg-[#006191] rounded-full"></div>
                        <h4 class="text-lg font-semibold text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Weekly Attendance Overview</h4>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-end justify-between h-40 gap-4 px-3">
                        @php
                            $days = $weeklyAttendance ?? [
                                'Mon' => 95, 'Tue' => 92, 'Wed' => 98, 'Thu' => 94, 'Fri' => 88,
                            ];
                        @endphp
                        @foreach ($days as $day => $percent)
                            <div class="flex-1 flex flex-col items-center gap-2">
                                <div class="w-full bg-[#006191]/10 rounded-t-lg relative group h-[85%]">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[#006191] rounded-t-lg transition-all" style="height: {{ $percent }}%"></div>
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">{{ $percent }}%</div>
                                </div>
                                <span class="text-[10px] font-semibold text-slate-500">{{ $day }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-200 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-slate-400"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-semibold text-slate-500 uppercase">Absen</span>
                                <span class="text-sm font-bold text-slate-900">{{ $absentCount ?? 0 }} Orang</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-yellow-100 border border-yellow-700"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-semibold text-slate-500 uppercase">Izin/Sakit</span>
                                <span class="text-sm font-bold text-slate-900">{{ $leaveCount ?? 0 }} Orang</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-semibold text-slate-500 uppercase">Belum Absen</span>
                                <span class="text-sm font-bold text-slate-900">{{ $notYetCount ?? 0 }} Orang</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#006191]"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-semibold text-slate-500 uppercase">Hadir</span>
                                <span class="text-sm font-bold text-slate-900">{{ $presentToday ?? 0 }} Orang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sidebar Content --}}
        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-3">
                    <div class="w-1 h-6 bg-[#006191] rounded-full"></div>
                    <h4 class="text-lg font-semibold text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Monthly Payment Info</h4>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-[11px] font-semibold tracking-wider text-slate-500">TOTAL COLLECTED</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-extrabold text-[#006191]">Rp {{ number_format($totalCollected ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-[#006191]/5 rounded-lg border border-[#006191]/10">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#006191]">pending_actions</span>
                            <span class="text-sm font-medium text-slate-900">{{ $pendingPayments ?? 0 }} Pending Verification</span>
                        </div>
                        <span class="material-symbols-outlined text-[#006191] text-[18px]">arrow_forward</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-3">
                    <div class="w-1 h-6 bg-[#6366f1] rounded-full"></div>
                    <h4 class="text-lg font-semibold text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Quick Links</h4>
                </div>
                <div class="p-5 space-y-1">
                    @php
                        $quickLinks = [
                            ['label' => 'Laporan Absensi', 'icon' => 'calendar_today', 'route' => 'admin.laporan.absensi'],
                            ['label' => 'Laporan Penugasan', 'icon' => 'task', 'route' => 'admin.laporan.penugasan'],
                            ['label' => 'Performance Analytics', 'icon' => 'analytics', 'route' => 'admin.analytics'],
                        ];
                    @endphp
                    @foreach ($quickLinks as $link)
                        <a href="{{ Route::has($link['route']) ? route($link['route']) : '#' }}"
                           class="flex items-center justify-between p-3 bg-white hover:bg-slate-50 rounded-lg border border-transparent hover:border-slate-200 transition-all group {{ Route::has($link['route']) ? '' : 'opacity-50 pointer-events-none' }}">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[#006191]">{{ $link['icon'] }}</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $link['label'] }}</span>
                            </div>
                            <span class="material-symbols-outlined text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h4 class="text-lg font-semibold text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Attendance Alerts</h4>
                </div>
                <div class="p-5 space-y-3">
                    @forelse ($attendanceAlerts ?? [] as $alert)
                        <div class="p-3 rounded-lg border-l-4 {{ $alert['type'] === 'warning' ? 'border-[#d32f2f] bg-[#d32f2f]/5' : 'border-[#006191] bg-[#006191]/5' }} flex items-start gap-3">
                            <span class="material-symbols-outlined {{ $alert['type'] === 'warning' ? 'text-[#d32f2f]' : 'text-[#006191]' }} mt-1">{{ $alert['type'] === 'warning' ? 'warning' : 'info' }}</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $alert['title'] }}</p>
                                <p class="text-xs text-slate-500">{{ $alert['message'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 italic">Tidak ada notifikasi saat ini.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>

    {{-- Recent Task Review Table --}}
    <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-slate-900 headline" style="font-family: 'Manrope', sans-serif;">Recent Task Reviews</h4>
            @if (Route::has('admin.pengumpulan-tugas.index'))
                <a href="{{ route('admin.pengumpulan-tugas.index') }}" class="text-[#006191] text-[11px] font-semibold tracking-wider hover:underline flex items-center gap-1">
                    Show More <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-[11px] font-semibold tracking-wider text-slate-500">INTERN NAME</th>
                        <th class="px-5 py-3 text-[11px] font-semibold tracking-wider text-slate-500">TASK TITLE</th>
                        <th class="px-5 py-3 text-[11px] font-semibold tracking-wider text-slate-500">DEADLINE</th>
                        <th class="px-5 py-3 text-[11px] font-semibold tracking-wider text-slate-500">STATUS</th>
                        <th class="px-5 py-3 text-[11px] font-semibold tracking-wider text-slate-500 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($taskReviews ?? [] as $task)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#006191]/10 text-[#006191] flex items-center justify-center font-bold text-xs">
                                        {{ $task->initials }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-900">{{ $task->intern_name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $task->title }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $task->deadline->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold rounded uppercase
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-[#006191]/10 text-[#006191]' }}">
                                    {{ $task->status === 'completed' ? 'Completed' : 'In Review' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button class="{{ $task->status === 'completed' ? 'text-slate-400' : 'text-[#006191]' }} hover:underline text-sm font-semibold">
                                    {{ $task->status === 'completed' ? 'Details' : 'Review' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500 italic">Belum ada tugas yang perlu direview.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
            <span class="text-[11px] font-semibold tracking-wider text-slate-500 opacity-70 italic">Showing {{ ($taskReviews ?? [])->count() ?? 0 }} of {{ $pendingTaskReviews ?? 0 }} pending reviews</span>
        </div>
    </section>

@endsection