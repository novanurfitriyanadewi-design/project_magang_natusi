@extends('layouts.portal')

@section('title', 'Dashboard Super Admin')

@section('content')
    @php
        $adminRoute = Route::has('superadmin.admin') ? route('superadmin.admin') : '#';
        $profileRoute = Route::has('profile.edit') ? route('profile.edit') : '#';
        $rulesRoute = Route::has('superadmin.aturan.index') ? route('superadmin.aturan.index') : '#';
        $attendanceRoute = Route::has('superadmin.jam-absensi.index') ? route('superadmin.jam-absensi.index') : '#';
        $paymentRoute = Route::has('superadmin.metode-pembayaran.index') ? route('superadmin.metode-pembayaran.index') : '#';

        $totalAdmins = $totalAdmins ?? 0;
        $totalUsers = $totalUsers ?? 0;
        $activeRules = $activeRules ?? 0;
        $activeSchedule = $activeSchedule ?? null;
        $latestAdmins = $latestAdmins ?? collect();

        $scheduleText = $activeSchedule
            ? substr((string) $activeSchedule->jam_mulai, 0, 5).' - '.substr((string) $activeSchedule->jam_selesai, 0, 5)
            : 'Belum diatur';
    @endphp

    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]"></span>
                Sistem Aktif
            </span>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                Halo, {{ auth()->user()->nama ?? 'Super Admin' }} 👋
            </h1>

            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                Pantau kondisi portal, kelola administrator, dan atur kebutuhan operasional magang dari satu tempat.
            </p>

            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-[11px] font-medium text-slate-400">
                <span>Diperbarui {{ now()->translatedFormat('d M Y, H:i') }}</span>
                <span class="hidden h-1 w-1 rounded-full bg-slate-300 sm:block"></span>
                <span>Akses Super Administrator</span>
            </div>
        </div>
    </section>

    <section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 to-cyan-500 p-5 text-white shadow-[0_16px_36px_rgba(2,132,199,0.18)]">
            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full border-[18px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-sky-100">Total Admin</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ $totalAdmins }}</p>
                    <p class="mt-1 text-sm text-sky-100">Akun admin operasional</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
            </div>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-500 p-5 text-white shadow-[0_16px_36px_rgba(79,70,229,0.18)]">
            <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-indigo-100">Total Pengguna</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ $totalUsers }}</p>
                    <p class="mt-1 text-sm text-indigo-100">Semua akun terdaftar</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><circle cx="17" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M2.8 19c.5-3.5 2.2-5.2 5.2-5.2s4.8 1.7 5.2 5.2M14 14.5c2.8-.5 5 .9 5.7 3.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
            </div>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-500 p-5 text-white shadow-[0_16px_36px_rgba(13,148,136,0.18)]">
            <div class="absolute -right-6 -top-10 h-32 w-32 rounded-[36px] border border-white/15"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-emerald-100">Aturan Aktif</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ $activeRules }}</p>
                    <p class="mt-1 text-sm text-emerald-100">Kebijakan perusahaan aktif</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M5 19h14M8 15l7-7 3 3-7 7H8v-3ZM13 6l3-3 3 3-3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-800 to-cyan-700 p-5 text-white shadow-[0_16px_36px_rgba(30,64,175,0.18)]">
            <div class="absolute -bottom-20 left-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-blue-100">Jam Absensi</p>
                    <p class="mt-3 truncate text-xl font-extrabold">{{ $scheduleText }}</p>
                    <p class="mt-2 text-sm text-blue-100">Jadwal operasional aktif</p>
                </div>
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
            </div>
        </article>
    </section>

    <section class="mt-5 grid gap-5 xl:grid-cols-[0.9fr_1.2fr]">
        <article class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200/80 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Admin Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Akun yang terakhir ditambahkan.</p>
                </div>
                <a href="{{ $adminRoute }}" class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5">Lihat semua</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($latestAdmins as $admin)
                    <div class="flex items-center gap-3 px-5 py-4 transition hover:bg-sky-50/50">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200">
                            {{ strtoupper(mb_substr((string) ($admin->nama ?? 'AD'), 0, 2)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900">{{ $admin->nama ?? 'Admin' }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $admin->email ?? '-' }}</p>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $admin->created_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="px-5 py-14 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <p class="mt-3 font-bold text-slate-800">Belum ada akun admin</p>
                        <p class="mt-1 text-sm text-slate-500">Tambahkan admin pertama melalui menu Kelola Admin.</p>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-white/80 bg-white/90 p-5 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Panduan Menu Super Admin</h2>
                    <p class="mt-1 text-sm text-slate-500">Akses cepat dan fungsi utama setiap menu.</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-lg">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M6 4h12v16H6V4Zm3 4h6M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <a href="{{ $adminRoute }}"
                   x-show="matches('kelola admin tambah ubah cari hapus akun')"
                   aria-label="Buka menu Kelola Admin"
                   class="group relative overflow-hidden rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 to-white p-4 transition hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-sky-100"></div>
                    <span class="relative grid h-10 w-10 place-items-center rounded-xl bg-sky-600 text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                    <h3 class="relative mt-4 font-bold text-slate-900">Kelola Admin</h3>
                    <p class="relative mt-1 text-sm leading-6 text-slate-500">Tambah, ubah, cari, dan hapus akun admin dengan hak akses yang sama.</p>
                </a>

                <a href="{{ $profileRoute }}"
                   x-show="matches('kelola profil identitas email username keamanan')"
                   aria-label="Buka menu Kelola Profil"
                   class="group relative overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-4 transition hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-100"></div>
                    <span class="relative grid h-10 w-10 place-items-center rounded-xl bg-indigo-600 text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M5.5 19c.6-3.5 2.8-5.3 6.5-5.3s5.9 1.8 6.5 5.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                    <h3 class="relative mt-4 font-bold text-slate-900">Kelola Profil</h3>
                    <p class="relative mt-1 text-sm leading-6 text-slate-500">Perbarui identitas, email, username, dan keamanan akun.</p>
                </a>

                <a href="{{ $rulesRoute }}"
                   x-show="matches('aturan perusahaan kebijakan')"
                   aria-label="Buka menu Aturan Perusahaan"
                   class="group relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-4 transition hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-emerald-100"></div>
                    <span class="relative grid h-10 w-10 place-items-center rounded-xl bg-emerald-600 text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 19h14M8 15l7-7 3 3-7 7H8v-3ZM13 6l3-3 3 3-3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3 class="relative mt-4 font-bold text-slate-900">Aturan Perusahaan</h3>
                    <p class="relative mt-1 text-sm leading-6 text-slate-500">Susun dan aktifkan kebijakan yang berlaku bagi seluruh pengguna portal.</p>
                </a>

                <a href="{{ $attendanceRoute }}"
                   x-show="matches('jam absensi jadwal operasional')"
                   aria-label="Buka menu Jam Absensi"
                   class="group relative overflow-hidden rounded-2xl border border-cyan-100 bg-gradient-to-br from-cyan-50 to-white p-4 transition hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-cyan-100"></div>
                    <span class="relative grid h-10 w-10 place-items-center rounded-xl bg-cyan-600 text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                    <h3 class="relative mt-4 font-bold text-slate-900">Jam Absensi</h3>
                    <p class="relative mt-1 text-sm leading-6 text-slate-500">Atur rentang waktu absensi agar jadwal operasional lebih terkontrol.</p>
                </a>

                <a href="{{ $paymentRoute }}"
                   x-show="matches('metode pembayaran rekening bank nominal administrasi')"
                   aria-label="Buka menu Metode Pembayaran"
                   class="group relative overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-4 transition hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-blue-100"></div>
                    <span class="relative grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M3 9h18M5 9V7l7-4 7 4v2M6 9v8M10 9v8M14 9v8M18 9v8M4 17h16M3 21h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3 class="relative mt-4 font-bold text-slate-900">Metode Pembayaran</h3>
                    <p class="relative mt-1 text-sm leading-6 text-slate-500">Kelola nominal administrasi, rekening bank resmi, dan riwayat perubahannya.</p>
                </a>
            </div>
        </article>
    </section>
@endsection