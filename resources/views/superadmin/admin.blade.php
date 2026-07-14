@extends('layouts.portal')

@section('title', 'Kelola Admin')

@section('content')
    <div
        x-data="{
            addOpen: @js($errors->any() && old('form_context') === 'create'),
            editOpen: @js($errors->any() && old('form_context') === 'edit'),
            editAdmin: {
                action: @js(old('admin_id') ? route('superadmin.admin.update', old('admin_id')) : ''),
                id: @js(old('admin_id', '')),
                nama: @js(old('form_context') === 'edit' ? old('nama', '') : ''),
                username: @js(old('form_context') === 'edit' ? old('username', '') : ''),
                email: @js(old('form_context') === 'edit' ? old('email', '') : ''),
            },
            openEdit(admin) {
                this.editAdmin = admin;
                this.editOpen = true;
            },
            closeModals() {
                this.addOpen = false;
                this.editOpen = false;
            }
        }"
        @keydown.escape.window="closeModals()"
        x-effect="document.body.classList.toggle('overflow-hidden', addOpen || editOpen)"
    >
        <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                    Manajemen Akses
                </span>
                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Daftar Administrator</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola akun admin yang dapat mengoperasikan portal magang CV Natusi.</p>
            </div>
        </section>

        <section class="mt-5 grid gap-4 md:grid-cols-3">
            <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-sky-500 p-5 text-white shadow-[0_16px_38px_rgba(37,99,235,0.20)]">
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full border-[20px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100">Total Admin</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ $totalAdmins ?? $admins->total() }}</p>
                        <p class="mt-1 text-sm text-blue-100">Admin operasional terdaftar</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </article>

            <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 to-blue-600 p-5 text-white shadow-[0_16px_38px_rgba(2,132,199,0.20)]">
                <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-sky-100">Admin Baru Bulan Ini</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ $adminsThisMonth ?? 0 }}</p>
                        <p class="mt-1 text-sm text-sky-100">Akun ditambahkan bulan berjalan</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </article>

            <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-800 via-sky-700 to-cyan-600 p-5 text-white shadow-[0_16px_38px_rgba(30,64,175,0.20)]">
                <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-cyan-100">Admin Terakhir</p>
                        <p class="mt-3 truncate text-xl font-extrabold">{{ $latestAdmin?->nama ?? 'Belum ada' }}</p>
                        <p class="mt-2 text-sm text-cyan-100">{{ $latestAdmin?->created_at?->translatedFormat('d M Y, H:i') ?? 'Tambahkan admin pertama' }}</p>
                    </div>
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M5.5 19c.6-3.5 2.8-5.3 6.5-5.3s5.9 1.8 6.5 5.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </article>
        </section>

        <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
            <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Data Administrator</h2>
                    <p class="mt-1 text-sm text-slate-500">Semua admin memiliki hak akses yang sama dan langsung aktif setelah dibuat.</p>
                </div>

                <button
                    type="button"
                    @click="addOpen = true"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-3 text-sm font-bold text-white shadow-[0_10px_25px_rgba(2,132,199,0.25)] transition duration-200 hover:-translate-y-0.5 hover:from-sky-700 hover:to-blue-700"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Tambah Admin Baru
                </button>
            </div>

            <div
                class="
                    flex flex-col gap-1 border-b border-sky-100
                    bg-white px-5 py-3 text-xs text-slate-500
                    sm:flex-row sm:items-center sm:justify-between
                "
            >
                <span>
                    @if (($search ?? request('search', '')) !== '')
                        Hasil pencarian admin:
                        <strong class="text-slate-700">
                            “{{ $search ?? request('search') }}”
                        </strong>
                    @else
                        Daftar seluruh akun administrator portal.
                    @endif
                </span>

                <span>
                    Menampilkan {{ $admins->firstItem() ?? 0 }}
                    – {{ $admins->lastItem() ?? 0 }}
                    dari {{ $admins->total() }} admin
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Nama Admin</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Username</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Tanggal Dibuat</th>
                            <th scope="col" class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/80">
                        @forelse ($admins as $admin)
                            <tr class="transition duration-200 hover:bg-sky-50/80">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-cyan-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200/80">
                                            {{ strtoupper(mb_substr(trim($admin->nama ?: 'AD'), 0, 2)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="max-w-64 truncate text-sm font-bold text-slate-900">{{ $admin->nama }}</p>
                                            <p class="max-w-64 truncate text-xs text-slate-500">{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-100">{{ '@'.$admin->username }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $admin->created_at?->translatedFormat('d M Y') ?? '-' }}
                                    <span class="block text-xs text-slate-400">{{ $admin->created_at?->format('H:i') }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            class="grid h-9 w-9 place-items-center rounded-xl bg-sky-50 text-sky-700 ring-1 ring-sky-100 transition hover:bg-sky-100"
                                            title="Edit admin"
                                            aria-label="Edit {{ $admin->nama }}"
                                            @click="openEdit({
                                                action: @js(route('superadmin.admin.update', $admin)),
                                                id: @js($admin->id_user),
                                                nama: @js($admin->nama),
                                                username: @js($admin->username),
                                                email: @js($admin->email),
                                            })"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m5 16.5-.8 3.3 3.3-.8L18 8.5 15.5 6 5 16.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m13.8 7.7 2.5 2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                                        </button>

                                        <button
                                            type="button"
                                            class="
                                                grid h-9 w-9 place-items-center
                                                rounded-xl bg-rose-50
                                                text-rose-600
                                                ring-1 ring-rose-100
                                                transition hover:bg-rose-100
                                            "
                                            title="Hapus admin"
                                            aria-label="Hapus {{ $admin->nama }}"
                                            @click="$dispatch(
                                                'open-delete-confirm',
                                                {
                                                    action: @js(
                                                        route(
                                                            'superadmin.admin.destroy',
                                                            $admin
                                                        )
                                                    ),
                                                    title: 'Hapus Admin?',
                                                    name: @js($admin->nama),
                                                    description:
                                                        'Akun admin ini akan dihapus dari portal dan tidak bisa digunakan untuk masuk lagi.',
                                                    confirmText:
                                                        'Ya, Hapus Admin',
                                                }
                                            )"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-16 text-center">
                                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 10h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    </span>
                                    <p class="mt-3 text-sm font-bold text-slate-800">
                                        {{
                                            ($search ?? request('search', '')) !== ''
                                                ? 'Admin tidak ditemukan.'
                                                : 'Belum ada akun admin.'
                                        }}
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{
                                            ($search ?? request('search', '')) !== ''
                                                ? 'Coba gunakan nama admin yang berbeda.'
                                                : 'Klik Tambah Admin Baru untuk membuat akun pertama.'
                                        }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($admins->hasPages())
                <div
                    class="
                        flex flex-col gap-3 border-t border-slate-200/80
                        bg-slate-50/70 px-5 py-4
                        sm:flex-row sm:items-center sm:justify-between
                    "
                >
                    <p class="text-xs text-slate-500">
                        Menampilkan {{ $admins->firstItem() ?? 0 }}
                        – {{ $admins->lastItem() ?? 0 }}
                        dari {{ $admins->total() }} admin
                    </p>

                    <nav
                        class="flex items-center justify-end gap-2"
                        aria-label="Navigasi halaman admin"
                    >
                        @if ($admins->onFirstPage())
                            <span
                                class="
                                    inline-flex h-10 min-w-[42px] items-center justify-center
                                    rounded-xl border border-sky-100
                                    bg-sky-50/70 px-3 text-sm font-semibold text-sky-300
                                "
                                aria-disabled="true"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m15 6-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        @else
                            <a
                                href="{{ $admins->previousPageUrl() }}"
                                class="
                                    inline-flex h-10 min-w-[42px] items-center justify-center
                                    rounded-xl border border-sky-100
                                    bg-sky-50 px-3 text-sm font-semibold text-sky-700
                                    shadow-sm transition duration-200
                                    hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-800
                                "
                                aria-label="Halaman sebelumnya"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m15 6-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @endif

                        @foreach ($admins->getUrlRange(1, $admins->lastPage()) as $page => $url)
                            @if ($page == $admins->currentPage())
                                <span
                                    class="
                                        inline-flex h-10 min-w-[42px] items-center justify-center
                                        rounded-xl bg-gradient-to-r from-sky-500 to-blue-600
                                        px-3 text-sm font-bold text-white
                                        shadow-[0_10px_24px_rgba(2,132,199,0.24)]
                                    "
                                    aria-current="page"
                                >
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $url }}"
                                    class="
                                        inline-flex h-10 min-w-[42px] items-center justify-center
                                        rounded-xl border border-slate-200
                                        bg-white px-3 text-sm font-semibold text-slate-600
                                        shadow-sm transition duration-200
                                        hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-800
                                    "
                                    aria-label="Halaman {{ $page }}"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($admins->hasMorePages())
                            <a
                                href="{{ $admins->nextPageUrl() }}"
                                class="
                                    inline-flex h-10 min-w-[42px] items-center justify-center
                                    rounded-xl border border-sky-100
                                    bg-sky-50 px-3 text-sm font-semibold text-sky-700
                                    shadow-sm transition duration-200
                                    hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-800
                                "
                                aria-label="Halaman berikutnya"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @else
                            <span
                                class="
                                    inline-flex h-10 min-w-[42px] items-center justify-center
                                    rounded-xl border border-sky-100
                                    bg-sky-50/70 px-3 text-sm font-semibold text-sky-300
                                "
                                aria-disabled="true"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </section>

        <section
            class="
                relative mt-5 overflow-hidden rounded-3xl
                border border-sky-300
                bg-gradient-to-r
                from-sky-100 via-blue-50 to-cyan-50
                px-6 py-5
                shadow-[0_18px_44px_rgba(14,165,233,0.16)]
            "
        >
            <div
                class="
                    pointer-events-none absolute
                    -right-14 -top-16 h-40 w-40
                    rounded-full bg-sky-300/25 blur-2xl
                "
            ></div>

            <div
                class="
                    pointer-events-none absolute
                    -bottom-14 left-1/3 h-32 w-32
                    rounded-full bg-cyan-300/20 blur-2xl
                "
            ></div>

            <div
                class="
                    relative flex flex-col gap-4
                    sm:flex-row sm:items-start
                "
            >
                <span
                    class="
                        grid h-14 w-14 shrink-0 place-items-center
                        rounded-2xl
                        bg-gradient-to-br from-sky-600 to-blue-700
                        text-white shadow-lg
                        ring-4 ring-white/75
                    "
                >
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M12 3 5 6v5c0 4.6 2.8 8.1 7 10 4.2-1.9 7-5.4 7-10V6l-7-3Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M12 8v5M12 16h.01"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p
                            class="
                                text-[10px] font-extrabold uppercase
                                tracking-[0.18em] text-sky-800
                            "
                        >
                            Informasi Keamanan
                        </p>

                        <span
                            class="
                                inline-flex rounded-full
                                bg-sky-600 px-2.5 py-1
                                text-[9px] font-extrabold uppercase
                                tracking-[0.12em] text-white
                                shadow-sm
                            "
                        >
                            Panduan
                        </span>
                    </div>

                    <h2
                        class="
                            mt-2 text-lg font-extrabold
                            text-slate-950 sm:text-xl
                        "
                    >
                        Panduan Keamanan Admin
                    </h2>

                    <p
                        class="
                            mt-2 max-w-5xl text-sm
                            leading-6 text-slate-700
                        "
                    >
                        Buat akun hanya untuk personel berwenang. Gunakan kata
                        sandi yang kuat dan segera hapus akses ketika admin
                        sudah tidak lagi bertugas.
                    </p>
                </div>
            </div>
        </section>

        {{-- Modal tambah admin --}}
        <template x-teleport="body">
            <div
                x-cloak
                x-show="addOpen"
                x-transition.opacity
                class="
                    fixed inset-0 overflow-y-auto overscroll-contain
                    bg-slate-950/40
                "
                style="position: fixed; inset: 0; z-index: 2147483647; backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
            >
                <div
                    class="
                        flex min-h-full items-start justify-center
                        px-3 py-5 sm:items-center sm:px-6 sm:py-8
                    "
                    @click.self="addOpen = false"
                >
                    <article
                        x-show="addOpen"
                        x-transition.scale.origin.center
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="create-admin-title"
                        class="
                            relative isolate my-auto flex
                            max-h-[calc(100dvh-3rem)]
                            w-full max-w-lg flex-col
                            overflow-hidden rounded-3xl
                            border border-sky-100 bg-white shadow-2xl
                        "
                    >
                        <header
                            class="
                                flex shrink-0 items-start justify-between
                                gap-4 bg-gradient-to-r
                                from-sky-600 to-blue-600
                                px-6 py-5 text-white
                            "
                        >
                            <div>
                                <h2
                                    id="create-admin-title"
                                    class="text-xl font-extrabold"
                                >
                                    Tambah Admin Baru
                                </h2>

                                <p class="mt-1 text-sm text-sky-100">
                                    Akun langsung aktif setelah disimpan.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="
                                    rounded-xl p-2 text-white/80
                                    transition hover:bg-white/10
                                    hover:text-white
                                "
                                @click="addOpen = false"
                                aria-label="Tutup modal"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                >
                                    <path
                                        d="m6 6 12 12M18 6 6 18"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </header>

                        <form
                            method="POST"
                            action="{{ route('superadmin.admin.store') }}"
                            class="flex min-h-0 flex-1 flex-col"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="form_context"
                                value="create"
                            >

                            <div
                                class="
                                    min-h-0 flex-1 space-y-4
                                    overflow-y-auto p-6
                                "
                            >
                                @if (
                                    $errors->any()
                                    && old('form_context') === 'create'
                                )
                                    <div
                                        class="
                                            rounded-xl
                                            border border-rose-200
                                            bg-rose-50 px-4 py-3
                                            text-sm text-rose-700
                                        "
                                    >
                                        Periksa kembali data yang masih belum valid.
                                    </div>
                                @endif

                                <div>
                                    <label
                                        for="create-nama"
                                        class="
                                            mb-1.5 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Nama lengkap
                                    </label>

                                    <input
                                        id="create-nama"
                                        name="nama"
                                        type="text"
                                        autocomplete="name"
                                        value="{{ old('form_context') === 'create' ? old('nama') : '' }}"
                                        required
                                        class="
                                            w-full rounded-xl
                                            border-slate-300
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                        placeholder="Nama admin"
                                    >

                                    @if (old('form_context') === 'create')
                                        @error('nama')
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label
                                        for="create-username"
                                        class="
                                            mb-1.5 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Username
                                    </label>

                                    <input
                                        id="create-username"
                                        name="username"
                                        type="text"
                                        autocomplete="username"
                                        value="{{ old('form_context') === 'create' ? old('username') : '' }}"
                                        required
                                        class="
                                            w-full rounded-xl
                                            border-slate-300
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                        placeholder="contoh: adminnatusi"
                                    >

                                    @if (old('form_context') === 'create')
                                        @error('username')
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label
                                        for="create-email"
                                        class="
                                            mb-1.5 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Email
                                    </label>

                                    <input
                                        id="create-email"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        value="{{ old('form_context') === 'create' ? old('email') : '' }}"
                                        required
                                        class="
                                            w-full rounded-xl
                                            border-slate-300
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                        placeholder="admin@contoh.com"
                                    >

                                    @if (old('form_context') === 'create')
                                        @error('email')
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                            for="create-password"
                                            class="
                                                mb-1.5 block text-sm
                                                font-bold text-slate-700
                                            "
                                        >
                                            Kata sandi
                                        </label>

                                        <input
                                            id="create-password"
                                            name="password"
                                            type="password"
                                            autocomplete="new-password"
                                            required
                                            class="
                                                w-full rounded-xl
                                                border-slate-300
                                                focus:border-sky-500
                                                focus:ring-sky-500
                                            "
                                            placeholder="Minimal 8 karakter"
                                        >

                                        @if (old('form_context') === 'create')
                                            @error('password')
                                                <p class="mt-1 text-xs text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        @endif
                                    </div>

                                    <div>
                                        <label
                                            for="create-password-confirmation"
                                            class="
                                                mb-1.5 block text-sm
                                                font-bold text-slate-700
                                            "
                                        >
                                            Konfirmasi
                                        </label>

                                        <input
                                            id="create-password-confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            autocomplete="new-password"
                                            required
                                            class="
                                                w-full rounded-xl
                                                border-slate-300
                                                focus:border-sky-500
                                                focus:ring-sky-500
                                            "
                                            placeholder="Ulangi kata sandi"
                                        >
                                    </div>
                                </div>
                            </div>

                            <footer
                                class="
                                    flex shrink-0 justify-end gap-3
                                    border-t border-slate-100
                                    bg-white px-6 py-4
                                "
                            >
                                <button
                                    type="button"
                                    class="
                                        rounded-xl border border-sky-100
                                        bg-sky-50 px-4 py-2.5
                                        text-sm font-bold text-sky-700
                                        transition hover:bg-sky-100
                                    "
                                    @click="addOpen = false"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="
                                        rounded-xl bg-gradient-to-r
                                        from-sky-500 to-blue-600
                                        px-5 py-2.5 text-sm font-bold
                                        text-white
                                        shadow-[0_10px_24px_rgba(2,132,199,0.24)]
                                        transition hover:-translate-y-0.5
                                    "
                                >
                                    Simpan Admin
                                </button>
                            </footer>
                        </form>
                    </article>
                </div>
            </div>
        </template>

        {{-- Modal edit admin --}}
        <template x-teleport="body">
            <div
                x-cloak
                x-show="editOpen"
                x-transition.opacity
                class="
                    fixed inset-0 overflow-y-auto overscroll-contain
                    bg-slate-950/40
                "
                style="position: fixed; inset: 0; z-index: 2147483647; backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
            >
                <div
                    class="
                        flex min-h-full items-start justify-center
                        px-3 py-5 sm:items-center sm:px-6 sm:py-8
                    "
                    @click.self="editOpen = false"
                >
                    <article
                        x-show="editOpen"
                        x-transition.scale.origin.center
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="edit-admin-title"
                        class="
                            relative isolate my-auto flex
                            max-h-[calc(100dvh-3rem)]
                            w-full max-w-lg flex-col
                            overflow-hidden rounded-3xl
                            border border-sky-100 bg-white shadow-2xl
                        "
                    >
                        <header
                            class="
                                flex shrink-0 items-start justify-between
                                gap-4 bg-gradient-to-r
                                from-sky-600 to-blue-600
                                px-6 py-5 text-white
                            "
                        >
                            <div>
                                <h2
                                    id="edit-admin-title"
                                    class="text-xl font-extrabold"
                                >
                                    Edit Data Admin
                                </h2>

                                <p class="mt-1 text-sm text-sky-100">
                                    Kosongkan password jika tidak ingin menggantinya.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="
                                    rounded-xl p-2 text-white/80
                                    transition hover:bg-white/10
                                    hover:text-white
                                "
                                @click="editOpen = false"
                                aria-label="Tutup modal"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                >
                                    <path
                                        d="m6 6 12 12M18 6 6 18"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </header>

                        <form
                            method="POST"
                            :action="editAdmin.action"
                            class="flex min-h-0 flex-1 flex-col"
                        >
                            @csrf
                            @method('PUT')

                            <input
                                type="hidden"
                                name="form_context"
                                value="edit"
                            >

                            <input
                                type="hidden"
                                name="admin_id"
                                :value="editAdmin.id"
                            >

                            <div
                                class="
                                    min-h-0 flex-1 space-y-4
                                    overflow-y-auto p-6
                                "
                            >
                                @if (
                                    $errors->any()
                                    && old('form_context') === 'edit'
                                )
                                    <div
                                        class="
                                            rounded-xl
                                            border border-rose-200
                                            bg-rose-50 px-4 py-3
                                            text-sm text-rose-700
                                        "
                                    >
                                        Periksa kembali data yang masih belum valid.
                                    </div>
                                @endif

                                <div>
                                    <label
                                        for="edit-nama"
                                        class="
                                            mb-1.5 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Nama lengkap
                                    </label>

                                    <input
                                        id="edit-nama"
                                        name="nama"
                                        type="text"
                                        autocomplete="name"
                                        x-model="editAdmin.nama"
                                        required
                                        class="
                                            w-full rounded-xl
                                            border-slate-300
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                    >

                                    @if (old('form_context') === 'edit')
                                        @error('nama')
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label
                                        for="edit-username"
                                        class="
                                            mb-1.5 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Username
                                    </label>

                                    <input
                                        id="edit-username"
                                        name="username"
                                        type="text"
                                        autocomplete="username"
                                        x-model="editAdmin.username"
                                        required
                                        class="
                                            w-full rounded-xl
                                            border-slate-300
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                    >

                                    @if (old('form_context') === 'edit')
                                        @error('username')
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label
                                        for="edit-email"
                                        class="
                                            mb-1.5 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Email
                                    </label>

                                    <input
                                        id="edit-email"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        x-model="editAdmin.email"
                                        required
                                        class="
                                            w-full rounded-xl
                                            border-slate-300
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                    >

                                    @if (old('form_context') === 'edit')
                                        @error('email')
                                            <p class="mt-1 text-xs text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                            for="edit-password"
                                            class="
                                                mb-1.5 block text-sm
                                                font-bold text-slate-700
                                            "
                                        >
                                            Password baru
                                        </label>

                                        <input
                                            id="edit-password"
                                            name="password"
                                            type="password"
                                            autocomplete="new-password"
                                            class="
                                                w-full rounded-xl
                                                border-slate-300
                                                focus:border-sky-500
                                                focus:ring-sky-500
                                            "
                                            placeholder="Opsional"
                                        >

                                        @if (old('form_context') === 'edit')
                                            @error('password')
                                                <p class="mt-1 text-xs text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        @endif
                                    </div>

                                    <div>
                                        <label
                                            for="edit-password-confirmation"
                                            class="
                                                mb-1.5 block text-sm
                                                font-bold text-slate-700
                                            "
                                        >
                                            Konfirmasi
                                        </label>

                                        <input
                                            id="edit-password-confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            autocomplete="new-password"
                                            class="
                                                w-full rounded-xl
                                                border-slate-300
                                                focus:border-sky-500
                                                focus:ring-sky-500
                                            "
                                            placeholder="Ulangi password"
                                        >
                                    </div>
                                </div>
                            </div>

                            <footer
                                class="
                                    flex shrink-0 justify-end gap-3
                                    border-t border-slate-100
                                    bg-white px-6 py-4
                                "
                            >
                                <button
                                    type="button"
                                    class="
                                        rounded-xl border border-sky-100
                                        bg-sky-50 px-4 py-2.5
                                        text-sm font-bold text-sky-700
                                        transition hover:bg-sky-100
                                    "
                                    @click="editOpen = false"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="
                                        rounded-xl bg-gradient-to-r
                                        from-sky-600 to-blue-600
                                        px-5 py-2.5 text-sm font-bold
                                        text-white
                                        shadow-[0_10px_24px_rgba(2,132,199,0.24)]
                                        transition hover:-translate-y-0.5
                                    "
                                >
                                    Simpan Perubahan
                                </button>
                            </footer>
                        </form>
                    </article>
                </div>
            </div>
        </template>
    </div>
@endsection