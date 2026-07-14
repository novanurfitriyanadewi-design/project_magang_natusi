@php
    $user = auth()->user();
    $role = $user?->role ?? 'peserta';

    $portal = match ($role) {
        'superadmin' => ['name' => 'Natusi Admin', 'subtitle' => 'SUPER ADMIN PORTAL'],
        'admin' => ['name' => 'Natusi Admin', 'subtitle' => 'ADMIN PORTAL'],
        default => ['name' => 'Natusi Magang', 'subtitle' => 'PESERTA MAGANG PORTAL'],
    };

    $menus = match ($role) {
        'superadmin' => [
            ['label' => 'Dashboard', 'route' => 'superadmin.dashboard', 'match' => 'superadmin.dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Kelola Admin', 'route' => 'superadmin.admin', 'match' => 'superadmin.admin*', 'icon' => 'users', 'tour' => 'manage-admin'],
            ['label' => 'Aturan Perusahaan', 'route' => 'superadmin.aturan.index', 'match' => 'superadmin.aturan.*', 'icon' => 'rules', 'tour' => 'company-rules'],
            ['label' => 'Jam Absensi', 'route' => 'superadmin.jam-absensi.index', 'match' => 'superadmin.jam-absensi.*', 'icon' => 'clock', 'tour' => 'attendance-hours'],
            ['label' => 'Metode Pembayaran', 'route' => 'superadmin.metode-pembayaran.index', 'match' => 'superadmin.metode-pembayaran.*', 'icon' => 'bank', 'tour' => 'payment-methods'],
            ['label' => 'Kelola Profil', 'route' => 'profile.edit', 'match' => 'profile.*', 'icon' => 'profile', 'tour' => 'profile'],
        ],
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Permintaan Magang', 'route' => 'admin.permintaan.index', 'match' => 'admin.permintaan.*', 'icon' => 'inbox', 'tour' => 'internship-requests'],
            ['label' => 'Peserta Magang', 'route' => 'admin.peserta.index', 'match' => 'admin.peserta.*', 'icon' => 'users', 'tour' => 'internship-participants'],
            ['label' => 'Kelola Tugas', 'route' => 'admin.tugas.index', 'match' => 'admin.tugas.*', 'icon' => 'tasks', 'tour' => 'manage-tasks'],
            ['label' => 'Kelola Profil', 'route' => 'profile.edit', 'match' => 'profile.*', 'icon' => 'profile', 'tour' => 'profile'],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard', 'tour' => 'dashboard'],
            ['label' => 'Tugas', 'route' => 'peserta.tugas.index', 'match' => 'peserta.tugas.*', 'icon' => 'tasks', 'tour' => 'tasks'],
            ['label' => 'Absensi', 'route' => 'peserta.absensi.index', 'match' => 'peserta.absensi.*', 'icon' => 'clock', 'tour' => 'attendance'],
            ['label' => 'Laporan Mingguan', 'route' => 'peserta.laporan.index', 'match' => 'peserta.laporan.*', 'icon' => 'report', 'tour' => 'weekly-report'],
            ['label' => 'Kelola Profil', 'route' => 'profile.edit', 'match' => 'profile.*', 'icon' => 'profile', 'tour' => 'profile'],
        ],
    };

    $homeRoute = $role === 'superadmin' && Route::has('superadmin.dashboard')
        ? route('superadmin.dashboard')
        : route('dashboard');
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-[245px] -translate-x-full flex-col overflow-hidden border-r border-white/10 bg-gradient-to-b from-[#073b60] via-[#075f89] to-[#062f50] px-3 py-5 shadow-[12px_0_40px_rgba(4,47,78,0.20)] transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    aria-label="Navigasi portal"
>
    <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full border-[28px] border-white/[0.04]"></div>
    <div class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-sky-300/[0.06] blur-2xl"></div>

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

        <nav class="mt-3 flex-1 space-y-1.5 overflow-y-auto pr-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Menu utama">
            @foreach ($menus as $menu)
                @php
                    $available = Route::has($menu['route']);
                    $active = $available && request()->routeIs($menu['match']);
                    $href = $available ? route($menu['route']) : '#';
                @endphp

                <a
                    href="{{ $href }}"
                    data-tour="{{ $menu['tour'] ?? '' }}"
                    @if (! $available) aria-disabled="true" title="Halaman ini belum dibuat" @endif
                    @click="sidebarOpen = false"
                    @class([
                        'group flex min-h-[48px] items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                        'bg-white font-semibold text-[#05658f] shadow-[0_10px_28px_rgba(0,32,58,0.22)]' => $active,
                        'font-medium text-sky-50/85 hover:translate-x-0.5 hover:bg-white/10 hover:text-white' => ! $active && $available,
                        'cursor-not-allowed text-sky-100/35' => ! $available,
                    ])
                >
                    <span @class([
                        'grid h-8 w-8 shrink-0 place-items-center rounded-lg transition duration-200',
                        'bg-gradient-to-br from-sky-100 to-cyan-50 text-[#0573a3] shadow-sm ring-1 ring-sky-100' => $active,
                        'bg-white/10 text-white ring-1 ring-white/10 group-hover:bg-white/15' => ! $active && $available,
                        'bg-white/[0.04] text-white/35' => ! $available,
                    ])>
                        @switch($menu['icon'])
                            @case('dashboard')
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" stroke="currentColor" stroke-width="1.7"/></svg>
                                @break
                            @case('users')
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                @break
                            @case('profile')
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M18 8v6M15 11h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
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
                            @default
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l3 3v15H6V3Zm3 7h6M9 14h6M9 18h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @endswitch
                    </span>

                    <span class="min-w-0 flex-1 truncate">{{ $menu['label'] }}</span>

                    @if (! $available)
                        <span class="ml-auto rounded-md border border-white/10 bg-white/[0.06] px-1.5 py-1 text-[7px] font-bold uppercase tracking-wide text-white/40">Soon</span>
                    @elseif ($active)
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400 shadow-[0_0_0_4px_rgba(52,211,153,0.15)]"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="mt-4 space-y-1 border-t border-white/10 pt-4">
            <button
                type="button"
                data-tour-support
                onclick="window.NatusiTour?.start()"
                class="
                    group flex w-full items-center gap-3
                    rounded-xl px-3 py-2.5
                    text-sm font-medium text-sky-50/80
                    transition duration-200
                    hover:bg-white/10 hover:text-white
                "
            >
                <span
                    class="
                        grid h-8 w-8 shrink-0 place-items-center
                        rounded-lg bg-white/10 ring-1 ring-white/10
                        transition group-hover:bg-white/15
                    "
                >
                    <svg
                        class="h-[18px] w-[18px]"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="8"
                            stroke="currentColor"
                            stroke-width="1.7"
                        />
                        <path
                            d="M9.7 9a2.4 2.4 0 0 1 4.6.9c0 1.8-2.3 2-2.3 3.6M12 17h.01"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <span>Support</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-200 transition duration-200 hover:bg-rose-500/15 hover:text-white">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-rose-400/10 ring-1 ring-rose-300/10 transition group-hover:bg-rose-400/20">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M10 5H5v14h5M14 8l4 4-4 4M8 12h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span>Logout</span>
                </button>
            </form>
        </div>

        <p class="mt-4 text-center text-[8px] font-medium tracking-[0.13em] text-white/25">CV NATUSI PORTAL • V1.0</p>
    </div>
</aside>