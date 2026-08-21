@php
    $user = auth()->user();
    $role = $user?->role ?? 'peserta';

    $portal = match ($role) {
        'superadmin' => ['name' => 'Natusi Admin', 'subtitle' => 'PORTAL DIREKTUR UTAMA'],
        'admin' => ['name' => 'Natusi Admin', 'subtitle' => 'ADMIN PORTAL'],
        'admin_peserta' => ['name' => 'Natusi Admin', 'subtitle' => 'ADMIN PESERTA PORTAL'],
        'admin_karyawan' => ['name' => 'Natusi Admin', 'subtitle' => 'ADMIN KARYAWAN PORTAL'],
        'karyawan' => ['name' => 'CV Natusi', 'subtitle' => 'KARYAWAN PORTAL'],
        default => ['name' => 'CV Natusi', 'subtitle' => 'INTERNSHIP PORTAL'],
    };

    $menus = match ($role) {
        'superadmin' => [
            ['label' => 'Dashboard', 'route' => 'superadmin.dashboard', 'match' => 'superadmin.dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Kelola Admin', 'route' => 'superadmin.admin', 'match' => 'superadmin.admin*', 'icon' => 'users', 'tour' => 'manage-admin'],
            ['label' => 'Aturan Perusahaan', 'route' => 'superadmin.aturan.index', 'match' => 'superadmin.aturan.*', 'icon' => 'rules', 'tour' => 'company-rules'],
            ['label' => 'Kelola Divisi', 'route' => 'superadmin.divisi.index', 'match' => 'superadmin.divisi.*', 'icon' => 'users', 'tour' => 'manage-divisi'],
            ['label' => 'Jam Absensi', 'route' => 'superadmin.jam-absensi.index', 'match' => 'superadmin.jam-absensi.*', 'icon' => 'clock', 'tour' => 'attendance-hours'],
            ['label' => 'Metode Pembayaran', 'route' => 'superadmin.metode-pembayaran.index', 'match' => 'superadmin.metode-pembayaran.*', 'icon' => 'bank', 'tour' => 'payment-methods'],
        ],
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Permintaan Magang', 'route' => 'admin.permintaan.index', 'match' => 'admin.permintaan.*', 'icon' => 'inbox', 'tour' => 'internship-requests'],
            ['label' => 'Permintaan Lamaran', 'route' => 'admin.permintaan-lamaran.index', 'match' => 'admin.permintaan-lamaran.*', 'icon' => 'inbox', 'tour' => 'permintaan-lamaran'],
            [
                'label' => 'Karyawan',
                'icon' => 'users',
                'match' => 'admin.karyawan.*',
                'tour' => 'karyawan',
                'children' => [
                    ['label' => 'Data Karyawan', 'route' => 'admin.karyawan.index', 'match' => 'admin.karyawan.*', 'tour' => 'data-karyawan'],
                    ['label' => 'Pengumuman', 'route' => 'admin-karyawan.pengumuman.index', 'match' => 'admin-karyawan.pengumuman.*', 'icon' => 'announcement', 'tour' => 'pengumuman'],
                    ['label' => 'Absensi Karyawan', 'route' => 'admin.absensi-karyawan.index', 'match' => 'admin.absensi-karyawan.*', 'tour' => 'absensi-karyawan'],
                    ['label' => 'Pembayaran Gaji', 'route' => 'admin.pembayaran-karyawan.index', 'match' => 'admin.pembayaran-karyawan.*', 'tour' => 'pembayaran-karyawan'],
                    ['label' => 'Pengajuan Resign', 'route' => 'admin.resign.index', 'match' => 'admin.resign.*', 'tour' => 'resign'],
                ],
            ],
            [
                'label' => 'Peserta Magang',
                'icon' => 'users',
                'match' => 'admin.peserta.*',
                'tour' => 'peserta-magang',
                'children' => [
                    ['label' => 'Data Peserta Magang', 'route' => 'admin.peserta.index', 'match' => 'admin.peserta.*', 'tour' => 'internship-participants'],
                    ['label' => 'Absensi Peserta Magang', 'route' => 'admin.absensi.index', 'match' => 'admin.absensi.*', 'tour' => 'attendance-data'],
                    ['label' => 'Pembayaran Sumbangan', 'route' => 'admin.pembayaran.index', 'match' => 'admin.pembayaran.*', 'tour' => 'pembayaran-peserta'],
                    ['label' => 'Kelola Tugas', 'route' => 'admin.tugas.index', 'match' => 'admin.tugas.*', 'tour' => 'manage-tasks'],
                    ['label' => 'Pengumpulan Tugas', 'route' => 'admin.pengumpulan-tugas.index', 'match' => 'admin.pengumpulan-tugas.*', 'tour' => 'task-submissions'],
                ],
            ],
            [
                'label' => 'Laporan',
                'icon' => 'rules',
                'match' => 'admin.laporan.*',
                'tour' => 'reports',
                'children' => [
                    ['label' => 'Peserta Magang', 'route' => 'admin.laporan-peserta.index', 'match' => 'admin.laporan-peserta.*', 'tour' => 'report-participants'],
                    ['label' => 'Karyawan', 'route' => 'admin.laporan-karyawan.index', 'match' => 'admin.laporan-karyawan.*', 'tour' => 'report-karyawan'],
                    ['label' => 'Absensi Peserta', 'route' => 'admin.laporan.absensi', 'match' => 'admin.laporan.absensi', 'tour' => 'report-attendance'],
                    ['label' => 'Absensi Karyawan', 'route' => 'admin.laporan.absensi-karyawan', 'match' => 'admin.laporan.absensi-karyawan', 'tour' => 'report-attendance-karyawan'],
                    ['label' => 'Penugasan', 'route' => 'admin.laporan.penugasan', 'match' => 'admin.laporan.penugasan', 'tour' => 'report-tasks'],
                    ['label' => 'Pembayaran Peserta', 'route' => 'admin.laporan.pembayaran', 'match' => 'admin.laporan.pembayaran', 'tour' => 'report-payments'],
                    ['label' => 'Pembayaran Karyawan', 'route' => 'admin.laporan.pembayaran-karyawan', 'match' => 'admin.laporan.pembayaran-karyawan', 'tour' => 'report-payments-karyawan'],
                ],
            ],
        ],
        'admin_peserta' => [
            ['label' => 'Dashboard', 'route' => 'admin-peserta.dashboard', 'match' => 'admin-peserta.dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Permintaan Magang', 'route' => 'admin-peserta.permintaan.index', 'match' => 'admin-peserta.permintaan.*', 'icon' => 'inbox', 'tour' => 'internship-requests'],
            ['label' => 'Data Peserta Magang', 'route' => 'admin-peserta.peserta.index', 'match' => 'admin-peserta.peserta.*', 'icon' => 'users', 'tour' => 'internship-participants'],
            ['label' => 'Absensi Peserta', 'route' => 'admin-peserta.absensi.index', 'match' => 'admin-peserta.absensi.*', 'icon' => 'attendance-user', 'tour' => 'attendance-data'],
            ['label' => 'Pembayaran Sumbangan', 'route' => 'admin-peserta.pembayaran.index', 'match' => 'admin-peserta.pembayaran.*', 'icon' => 'payment', 'tour' => 'pembayaran-peserta'],
            [
                'label' => 'Kelola Tugas',
                'icon' => 'tasks',
                'match' => 'admin-peserta.tugas.*|admin-peserta.laporan-mingguan.*|admin-peserta.pengumpulan-tugas.*',
                'tour' => 'manage-tasks',
                'children' => [
                    ['label' => 'Tugas Mingguan', 'route' => 'admin-peserta.tugas.index', 'match' => 'admin-peserta.tugas.*', 'tour' => 'weekly-tasks'],
                    ['label' => 'Laporan Mingguan', 'route' => 'admin-peserta.laporan-mingguan.index', 'match' => 'admin-peserta.laporan-mingguan.*', 'tour' => 'weekly-reports'],
                    ['label' => 'Data Penugasan', 'route' => 'admin-peserta.pengumpulan-tugas.index', 'match' => 'admin-peserta.pengumpulan-tugas.*', 'tour' => 'task-submissions'],
                ],
            ],
            ['label' => 'Kelola Jurusan', 'route' => 'admin-peserta.jurusan.index', 'match' => 'admin-peserta.jurusan.*', 'icon' => 'rules', 'tour' => 'manage-jurusan'],
            ['label' => 'Kelola Sertifikat', 'route' => 'admin-peserta.sertifikat.index', 'match' => 'admin-peserta.sertifikat.*', 'icon' => 'report', 'tour' => 'manage-sertifikat'],
            [
                'label' => 'Laporan',
                'icon' => 'rules',
                'match' => 'admin-peserta.laporan.*',
                'tour' => 'reports',
                'children' => [
                    ['label' => 'Laporan Peserta', 'route' => 'admin-peserta.laporan-peserta.index', 'match' => 'admin-peserta.laporan-peserta.*', 'tour' => 'report-participants'],
                    ['label' => 'Laporan Absensi', 'route' => 'admin-peserta.laporan.absensi', 'match' => 'admin-peserta.laporan.absensi', 'tour' => 'report-attendance'],
                    ['label' => 'Laporan Penugasan', 'route' => 'admin-peserta.laporan.penugasan', 'match' => 'admin-peserta.laporan.penugasan', 'tour' => 'report-tasks'],
                    ['label' => 'Laporan Pembayaran', 'route' => 'admin-peserta.laporan.pembayaran', 'match' => 'admin-peserta.laporan.pembayaran', 'tour' => 'report-payments'],
                ],
            ],
        ],
        'admin_karyawan' => [
            ['label' => 'Dashboard', 'route' => 'admin-karyawan.dashboard', 'match' => 'admin-karyawan.dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Permintaan Lamaran', 'route' => 'admin-karyawan.permintaan-lamaran.index', 'match' => 'admin-karyawan.permintaan-lamaran.*', 'icon' => 'inbox', 'tour' => 'permintaan-lamaran'],
            ['label' => 'Data Karyawan', 'route' => 'admin-karyawan.karyawan.index', 'match' => 'admin-karyawan.karyawan.*', 'icon' => 'users', 'tour' => 'data-karyawan'],
            ['label' => 'Pengumuman', 'route' => 'admin-karyawan.pengumuman.index', 'match' => 'admin-karyawan.pengumuman.*', 'icon' => 'announcement', 'tour' => 'pengumuman'],
            ['label' => 'Aturan Perusahaan', 'route' => 'admin-karyawan.aturan.index', 'match' => 'admin-karyawan.aturan.*', 'icon' => 'rules', 'tour' => 'aturan'],
            ['label' => 'Absensi Karyawan', 'route' => 'admin-karyawan.absensi-karyawan.index', 'match' => 'admin-karyawan.absensi-karyawan.*', 'icon' => 'attendance-user', 'tour' => 'absensi-karyawan'],
            ['label' => 'Pembayaran Gaji', 'route' => 'admin-karyawan.pembayaran-karyawan.index', 'match' => 'admin-karyawan.pembayaran-karyawan.*', 'icon' => 'payment', 'tour' => 'pembayaran-karyawan'],
            ['label' => 'Pengajuan Resign', 'route' => 'admin-karyawan.resign.index', 'match' => 'admin-karyawan.resign.*', 'icon' => 'clock', 'tour' => 'resign'],
            ['label' => 'Pengajuan Cuti', 'route' => 'admin-karyawan.cuti.index', 'match' => 'admin-karyawan.cuti.*', 'icon' => 'calendar', 'tour' => 'cuti'],
            [
                'label' => 'Laporan',
                'icon' => 'rules',
                'match' => 'admin-karyawan.laporan.*',
                'tour' => 'reports',
                'children' => [
                    ['label' => 'Laporan Karyawan', 'route' => 'admin-karyawan.laporan.karyawan', 'match' => 'admin-karyawan.laporan.karyawan*', 'tour' => 'report-karyawan'],
                    ['label' => 'Laporan Absensi Karyawan', 'route' => 'admin-karyawan.laporan.absensi', 'match' => 'admin-karyawan.laporan.absensi*', 'tour' => 'report-attendance-karyawan'],
                ],
            ],
        ],
        'karyawan' => [
            ['label' => 'Dashboard', 'route' => 'karyawan.dashboard', 'match' => 'karyawan.dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Absensi', 'route' => 'karyawan.absensi.index', 'match' => 'karyawan.absensi.*', 'icon' => 'attendance-user', 'tour' => 'absensi'],
            ['label' => 'Pengumuman', 'route' => 'karyawan.pengumuman.index', 'match' => 'karyawan.pengumuman.*', 'icon' => 'announcement', 'tour' => 'pengumuman'],
            [
                'label' => 'Pengajuan',
                'icon' => 'tasks',
                'match' => 'karyawan.cuti.*|karyawan.resign.*',
                'tour' => 'pengajuan',
                'children' => [
                    ['label' => 'Ajukan Cuti', 'route' => 'karyawan.cuti.index', 'match' => 'karyawan.cuti.*', 'tour' => 'cuti'],
                    ['label' => 'Ajukan Resign', 'route' => 'karyawan.resign.create', 'match' => 'karyawan.resign.create', 'tour' => 'resign-create'],
                ],
            ],
            ['label' => 'Slip Gaji', 'route' => 'karyawan.payslip.index', 'match' => 'karyawan.payslip.*', 'icon' => 'payment', 'tour' => 'payslip'],
            ['label' => 'Aturan Perusahaan', 'route' => 'karyawan.aturan.index', 'match' => 'karyawan.aturan.*', 'icon' => 'rules', 'tour' => 'aturan'],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'peserta-magang.dashboard', 'match' => 'peserta-magang.dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Absensi', 'route' => 'peserta-magang.absensi.index', 'match' => 'peserta-magang.absensi.*', 'icon' => 'attendance-user', 'tour' => 'attendance'],
            ['label' => 'Penugasan', 'route' => 'peserta-magang.penugasan.index', 'match' => 'peserta-magang.penugasan.*', 'icon' => 'assignment', 'tour' => 'assignments'],
            ['label' => 'Aturan Laporan Mingguan', 'route' => 'peserta-magang.laporan-mingguan.index', 'match' => 'peserta-magang.laporan-mingguan.*', 'icon' => 'report', 'tour' => 'weekly-report-rules'],
            ['label' => 'Pembayaran', 'route' => 'peserta-magang.pembayaran.index', 'match' => 'peserta-magang.pembayaran.*', 'icon' => 'payment', 'tour' => 'payments'],
            ['label' => 'Sertifikat Saya', 'route' => 'peserta-magang.sertifikat.index', 'match' => 'peserta-magang.sertifikat.*', 'icon' => 'report', 'tour' => 'certificate'],
            ['label' => 'Aturan Perusahaan', 'route' => 'peserta-magang.aturan.index', 'match' => 'peserta-magang.aturan.*', 'icon' => 'rules', 'tour' => 'company-rules'],
        ],
    };

    $homeRoute = match (true) {
        $role === 'superadmin' && Route::has('superadmin.dashboard') => route('superadmin.dashboard'),
        $role === 'admin_peserta' && Route::has('admin-peserta.dashboard') => route('admin-peserta.dashboard'),
        $role === 'admin_karyawan' && Route::has('admin-karyawan.dashboard') => route('admin-karyawan.dashboard'),
        $role === 'karyawan' && Route::has('karyawan.dashboard') => route('karyawan.dashboard'),
        default => route('dashboard'),
    };

    $openGroup = null;
    foreach ($menus as $i => $menu) {
        if (isset($menu['children'])) {
            $childActive = collect($menu['children'])->contains(fn ($c) => request()->routeIs($c['match']));
            if ($childActive) {
                $openGroup = $i;
            }
        }
    }
@endphp

<style>
    /* Sidebar scrollbar styling */
    .natusi-sidebar-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: rgba(125, 211, 252, 0.50) rgba(255, 255, 255, 0.06);
        scrollbar-gutter: stable;
        overscroll-behavior: contain;
    }
    .natusi-sidebar-scrollbar::-webkit-scrollbar { width: 5px; }
    .natusi-sidebar-scrollbar::-webkit-scrollbar-track {
        margin-block: 6px;
        border-radius: 9999px;
        background: transparent;
    }
    .natusi-sidebar-scrollbar::-webkit-scrollbar-thumb {
        min-height: 36px;
        border-radius: 9999px;
        background: rgba(125, 211, 252, 0.35);
        background-clip: padding-box;
        transition: background 0.2s ease;
    }
    .natusi-sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(125, 211, 252, 0.65);
        background-clip: padding-box;
    }

    /* Active indicator glow */
    .sidebar-active-glow {
        box-shadow: 
            0 0 20px rgba(6, 95, 137, 0.15),
            0 10px 28px rgba(0, 32, 58, 0.22);
    }
</style>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-[245px] -translate-x-full flex-col overflow-hidden border-r border-white/[0.06] px-3 py-5 shadow-[12px_0_40px_rgba(4,47,78,0.25)] transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    style="background-color:#063551; background-image:linear-gradient(180deg,#063551 0%,#075177 52%,#052b45 100%);"
    aria-label="Navigasi portal"
>
    <!-- Decorative background elements -->
    <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full border-[28px] border-white/[0.03]"></div>
    <div class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-sky-300/[0.05] blur-3xl"></div>
    <div class="pointer-events-none absolute left-1/2 top-1/3 h-36 w-36 -translate-x-1/2 rounded-full bg-gradient-to-br from-sky-400/[0.03] to-transparent blur-2xl"></div>

    <div class="relative z-10 flex min-h-0 flex-1 flex-col">
        <div class="flex items-center justify-between px-2">
            <a href="{{ $homeRoute }}" class="group flex min-w-0 items-center gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-2xl bg-white shadow-[0_8px_24px_rgba(0,0,0,0.15)] ring-1 ring-white/50 transition duration-200 group-hover:-translate-y-0.5">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo CV Natusi" class="h-9 w-9 object-contain">
                </span>
                <span class="min-w-0">
                    <strong class="block truncate text-[17px] font-bold leading-5 text-white">{{ $portal['name'] }}</strong>
                    <span class="mt-0.5 block truncate text-[9px] font-semibold tracking-[0.14em] text-sky-100/75">{{ $portal['subtitle'] }}</span>
                </span>
            </a>

            <button type="button" class="rounded-xl p-2 text-white/70 transition hover:bg-white/10 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="mt-8 px-4">
            <p class="text-[9px] font-bold uppercase tracking-[0.22em] text-sky-100/55">Menu Utama</p>
        </div>

        <nav
            x-data="{ openGroup: {{ $openGroup !== null ? $openGroup : 'null' }} }"
            class="natusi-sidebar-scrollbar mt-3 flex-1 space-y-1.5 overflow-y-auto pr-2"
            aria-label="Menu utama"
        >
            @foreach ($menus as $i => $menu)
                @if (isset($menu['children']))
                    @php
                        $groupActive = collect($menu['children'])->contains(fn ($c) => request()->routeIs($c['match']));
                    @endphp

                    <div>
                        <button
                            type="button"
                            data-tour="{{ $menu['tour'] ?? '' }}"
                            @click.stop="openGroup = (openGroup === {{ $i }} ? null : {{ $i }})"
                            @class([
                                'group flex min-h-[48px] w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200',
                                'bg-white font-semibold text-[#05658f] sidebar-active-glow' => $groupActive,
                                'font-medium text-sky-50/85 hover:translate-x-0.5 hover:bg-white/10 hover:text-white' => ! $groupActive,
                            ])
                        >
                            <span @class([
                                'grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-all duration-200',
                                'bg-gradient-to-br from-sky-100 to-cyan-50 text-[#0573a3] shadow-sm ring-1 ring-sky-100' => $groupActive,
                                'bg-white/10 text-white ring-1 ring-white/10 group-hover:bg-white/15 group-hover:scale-105' => ! $groupActive,
                            ])>
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>

                            <span class="min-w-0 flex-1 truncate text-left">{{ $menu['label'] }}</span>

                            <svg
                                class="h-4 w-4 shrink-0 transition-transform duration-200"
                                :class="openGroup === {{ $i }} ? 'rotate-180' : ''"
                                viewBox="0 0 24 24" fill="none"
                            >
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div
                            x-show="openGroup === {{ $i }}"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="mt-1 space-y-1 pl-[18px]"
                        >
                            <div class="ml-[17px] space-y-1 border-l border-white/10 pl-3">
                                @foreach ($menu['children'] as $child)
                                    @php
                                        $childAvailable = Route::has($child['route']);
                                        $childActive = $childAvailable && request()->routeIs($child['match']);
                                        $childHref = '#';
                                        if ($childAvailable) {
                                            try {
                                                $childHref = route($child['route']);
                                            } catch (\Illuminate\Routing\Exceptions\UrlGenerationException) {
                                                $childAvailable = false;
                                            }
                                        }
                                    @endphp

                                    <a
                                        href="{{ $childHref }}"
                                        data-tour="{{ $child['tour'] ?? '' }}"
                                        @if (! $childAvailable) aria-disabled="true" title="Halaman ini belum dibuat" @endif
                                        @click="sidebarOpen = false"
                                        @class([
                                            'flex min-h-[40px] items-center gap-2 rounded-lg px-3 py-2 text-[13px] transition duration-200',
                                            'bg-white/95 font-semibold text-[#05658f]' => $childActive,
                                            'font-medium text-sky-50/70 hover:bg-white/10 hover:text-white' => ! $childActive && $childAvailable,
                                            'cursor-not-allowed text-sky-100/30' => ! $childAvailable,
                                        ])
                                    >
                                        <span class="min-w-0 flex-1 truncate">{{ $child['label'] }}</span>

                                        @if (! $childAvailable)
                                            <span class="ml-auto rounded-md border border-white/10 bg-white/[0.06] px-1.5 py-1 text-[7px] font-bold uppercase tracking-wide text-white/40">Soon</span>
                                        @elseif ($childActive)
                                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400 shadow-[0_0_0_4px_rgba(52,211,153,0.15)]"></span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        $available = Route::has($menu['route']);
                        $active = $available && request()->routeIs($menu['match']);
                        $href = '#';
                        if ($available) {
                            try {
                                $href = route($menu['route']);
                            } catch (\Illuminate\Routing\Exceptions\UrlGenerationException) {
                                $available = false;
                            }
                        }
                    @endphp

                    <a
                        href="{{ $href }}"
                        data-tour="{{ $menu['tour'] ?? '' }}"
                        @if (! $available) aria-disabled="true" title="Halaman ini belum dibuat" @endif
                        @click="sidebarOpen = false"
                        @class([
                            'group flex min-h-[48px] items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200',
                            'bg-white font-semibold text-[#05658f] sidebar-active-glow' => $active,
                            'font-medium text-sky-50/85 hover:translate-x-0.5 hover:bg-white/10 hover:text-white' => ! $active && $available,
                            'cursor-not-allowed text-sky-100/35' => ! $available,
                        ])
                    >
                        <span @class([
                            'grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-all duration-200',
                            'bg-gradient-to-br from-sky-100 to-cyan-50 text-[#0573a3] shadow-sm ring-1 ring-sky-100' => $active,
                            'bg-white/10 text-white ring-1 ring-white/10 group-hover:bg-white/15 group-hover:scale-105' => ! $active && $available,
                            'bg-white/[0.04] text-white/35' => ! $available,
                        ])>
                            @switch($menu['icon'])
                                @case('dashboard')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" stroke="currentColor" stroke-width="1.7"/></svg>
                                    @break
                                @case('attendance-user')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.7"/><path d="m16 11 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @break
                                @case('assignment')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-3 9h4m-4 4h4m-6-4h.01M10 18h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @break
                                @case('report')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @break
                                @case('payment')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><rect x="2" y="6" width="20" height="12" rx="2" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.7"/><path d="M6 12h.01M18 12h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    @break
                                @case('users')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    @break
                                @case('rules')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M5 19h14M8 15l7-7 3 3-7 7H8v-3ZM13 6l3-3 3 3-3 3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @break
                                @case('clock')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    @break
                                @case('bank')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M3 9h18M5 9V7l7-4 7 4v2M6 9v8M10 9v8M14 9v8M18 9v8M4 17h16M3 21h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @break
                                @case('inbox')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4V5Zm0 9h4l2 2h4l2-2h4" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                                    @break
                                @case('tasks')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M9 6h11M9 12h11M9 18h11M4 6l1 1 2-2M4 12l1 1 2-2M4 18l1 1 2-2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @break
                                @case('announcement')
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M3 11v2a2 2 0 0 0 2 2h1l3 4v-4h8a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H8L5 2v3H5a2 2 0 0 0-2 2v4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                                    @break
                                @default
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/></svg>
                            @endswitch
                        </span>

                        <span class="min-w-0 flex-1 truncate text-left">{{ $menu['label'] }}</span>

                        @if (! $available)
                            <span class="rounded-md border border-white/10 bg-white/[0.06] px-1.5 py-1 text-[7px] font-bold uppercase tracking-wide text-white/40">Soon</span>
                        @endif
                    </a>
                @endif
            @endforeach

            <!-- Kelola Profil (Di atas Garis Pemisah) -->
            @php
                $profileActive = request()->routeIs('profile.*');
                $profileHref = Route::has('profile.edit') ? route('profile.edit') : '#';
            @endphp
            <a
                href="{{ $profileHref }}"
                data-tour="profile"
                @click="sidebarOpen = false"
                @class([
                    'group flex min-h-[48px] items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200',
                    'bg-white font-semibold text-[#05658f] sidebar-active-glow' => $profileActive,
                    'font-medium text-sky-50/85 hover:translate-x-0.5 hover:bg-white/10 hover:text-white' => ! $profileActive,
                ])
            >
                <span @class([
                    'grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-all duration-200',
                    'bg-gradient-to-br from-sky-100 to-cyan-50 text-[#0573a3] shadow-sm ring-1 ring-sky-100' => $profileActive,
                    'bg-white/10 text-white ring-1 ring-white/10 group-hover:bg-white/15 group-hover:scale-105' => ! $profileActive,
                ])>
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7"/><path d="M6 20v-1a6 6 0 0 1 12 0v1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </span>
                <span class="min-w-0 flex-1 truncate text-left">Kelola Profil</span>
            </a>

            <!-- Garis Pemisah (Divider) -->
            <div class="my-3 border-t border-white/10"></div>

            <!-- Support (Di bawah Garis Pemisah) -->
            <button
                type="button"
                id="natusi-support-trigger"
                class="group flex min-h-[48px] w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-sky-50/85 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white"
            >
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white/10 text-white ring-1 ring-white/10 transition-all duration-200 group-hover:scale-105 group-hover:bg-white/15">
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span class="min-w-0 flex-1 truncate text-left font-semibold">Support</span>
            </button>

            <!-- Logout (Di bawah Garis Pemisah) -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button
                    type="submit"
                    class="group flex min-h-[48px] w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-sky-50/85 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white"
                >
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white/10 text-white ring-1 ring-white/10 transition-all duration-200 group-hover:scale-105 group-hover:bg-white/15">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="min-w-0 flex-1 truncate text-left font-semibold">Logout</span>
                </button>
            </form>
        </nav>

        <!-- Footer Text -->
        <div class="mt-4 px-2 text-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-sky-100/40">CV NATUSI PORTAL &copy; V1.0</span>
        </div>
    </div>
</aside>
