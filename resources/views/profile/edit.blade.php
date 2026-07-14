@extends('layouts.portal')

@section('title', 'Kelola Profil')

@section('content')
    @php
        $user = auth()->user();

        $displayName = $user->nama
            ?? $user->name
            ?? $user->username
            ?? 'Pengguna';

        $roleLabel = match ($user->role ?? '') {
            'superadmin' => 'Super Admin Internal',
            'admin' => 'Admin Internal',
            'karyawan' => 'Karyawan',
            'pelamar' => 'Pelamar',
            default => ucfirst((string) ($user->role ?? 'Pengguna')),
        };

        $isActive = (bool) ($user->aktif ?? true);

        $lastLogin = $user->last_login_at
            ? \Carbon\Carbon::parse($user->last_login_at)
                ->translatedFormat('d M Y, H:i')
            : 'Belum tercatat';

        $photoUrl = (
            filled($user->foto_profil)
            && Route::has('profile.photo.show')
        )
            ? route('profile.photo.show')
                . '?v='
                . ($user->updated_at?->timestamp ?? 1)
            : null;

        $initials = collect(
            preg_split('/\s+/', trim($displayName))
        )
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    @endphp

    <div
        x-data="{
            photoPreview: @js($photoUrl),
            photoName: '',
            showCurrentPassword: false,
            showNewPassword: false,
            showConfirmPassword: false,

            previewPhoto(event) {
                const file = event.target.files[0];

                if (! file) {
                    return;
                }

                this.photoName = file.name;

                const reader = new FileReader();

                reader.onload = (loadEvent) => {
                    this.photoPreview = loadEvent.target.result;
                };

                reader.readAsDataURL(file);
            },
        }"
    >
        <section>
            <span
                class="
                    inline-flex items-center gap-2 rounded-full
                    bg-sky-100 px-3 py-1
                    text-[10px] font-bold uppercase
                    tracking-[0.16em] text-sky-700
                "
            >
                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                Pengaturan Akun
            </span>

            <h1
                class="
                    mt-3 text-2xl font-extrabold tracking-tight
                    text-slate-950 sm:text-3xl
                "
            >
                Kelola Profil
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi pribadi, foto profil, dan keamanan akun Anda.
            </p>
        </section>

        @if (session('status'))
            <div
                class="
                    mt-5 flex items-start gap-3 rounded-2xl
                    border border-emerald-200 bg-emerald-50
                    px-4 py-3 text-sm text-emerald-700
                "
            >
                <svg
                    class="mt-0.5 h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="m5 12 4 4L19 6"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

                <p>
                    {{
                        match (session('status')) {
                            'profile-updated' => 'Informasi profil berhasil diperbarui.',
                            'photo-updated' => 'Foto profil berhasil diperbarui.',
                            'photo-removed' => 'Foto profil berhasil dihapus.',
                            'password-updated' => 'Kata sandi berhasil diperbarui.',
                            default => session('status'),
                        }
                    }}
                </p>
            </div>
        @endif

        <section
            class="
                mt-5 grid items-start gap-5
                lg:grid-cols-[300px_minmax(0,1fr)]
            "
        >
            <div class="space-y-5">
                {{-- Foto profil --}}
                <article
                    class="
                        overflow-hidden rounded-3xl
                        border border-sky-100/90 bg-white/95
                        shadow-[0_20px_50px_rgba(15,52,94,0.09)]
                    "
                >
                    <header
                        class="
                            border-b border-sky-100
                            bg-gradient-to-r
                            from-sky-50 via-blue-50 to-cyan-50
                            px-5 py-4 text-center
                        "
                    >
                        <p
                            class="
                                text-[10px] font-bold uppercase
                                tracking-[0.18em] text-sky-700
                            "
                        >
                            Identitas Visual
                        </p>

                        <h2 class="mt-1 text-lg font-extrabold text-slate-950">
                            Foto Profil
                        </h2>
                    </header>

                    <div class="p-5 text-center">
                        <div class="relative mx-auto h-32 w-32">
                            <template x-if="photoPreview">
                                <img
                                    :src="photoPreview"
                                    alt="Pratinjau foto profil"
                                    class="
                                        h-32 w-32 rounded-full object-cover
                                        ring-4 ring-sky-100
                                        shadow-[0_12px_30px_rgba(2,132,199,0.20)]
                                    "
                                    x-on:error="photoPreview = null"
                                >
                            </template>

                            <template x-if="! photoPreview">
                                <div
                                    class="
                                        grid h-32 w-32 place-items-center
                                        rounded-full
                                        bg-gradient-to-br
                                        from-sky-500 to-blue-700
                                        text-3xl font-extrabold text-white
                                        ring-4 ring-sky-100
                                        shadow-[0_12px_30px_rgba(2,132,199,0.20)]
                                    "
                                >
                                    {{ $initials ?: 'NA' }}
                                </div>
                            </template>

                            <span
                                class="
                                    absolute bottom-1 right-1
                                    grid h-10 w-10 place-items-center
                                    rounded-2xl bg-gradient-to-r
                                    from-sky-600 to-blue-600
                                    text-white shadow-lg
                                    ring-4 ring-white
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M4 8h3l1.5-2h7L17 8h3v11H4V8Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                    <circle
                                        cx="12"
                                        cy="13"
                                        r="3"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                </svg>
                            </span>
                        </div>

                        <p class="mt-4 text-sm font-bold text-slate-900">
                            {{ $displayName }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            JPG, PNG, atau WEBP. Maksimal 2 MB.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('profile.photo.update') }}"
                            enctype="multipart/form-data"
                            class="mt-5 space-y-3"
                        >
                            @csrf
                            @method('PATCH')

                            <label
                                for="profile-photo"
                                class="
                                    inline-flex w-full cursor-pointer
                                    items-center justify-center gap-2
                                    rounded-xl
                                    bg-gradient-to-r
                                    from-sky-600 to-blue-600
                                    px-4 py-2.5 text-sm
                                    font-bold text-white
                                    shadow-[0_10px_24px_rgba(2,132,199,0.24)]
                                    transition hover:-translate-y-0.5
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M12 16V5M8 9l4-4 4 4M5 19h14"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                                Pilih Foto
                            </label>

                            <input
                                id="profile-photo"
                                name="foto_profil"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                required
                                @change="previewPhoto($event)"
                            >

                            <template x-if="photoName">
                                <p
                                    class="
                                        truncate rounded-xl
                                        bg-sky-50 px-3 py-2
                                        text-xs text-sky-700
                                    "
                                    x-text="photoName"
                                ></p>
                            </template>

                            @error('foto_profil')
                                <p class="text-xs text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <button
                                type="submit"
                                :disabled="! photoName"
                                class="
                                    w-full rounded-xl
                                    border border-sky-100 bg-sky-50
                                    px-4 py-2.5 text-sm
                                    font-bold text-sky-700
                                    transition hover:bg-sky-100
                                    disabled:cursor-not-allowed
                                    disabled:opacity-50
                                "
                            >
                                Simpan Foto
                            </button>
                        </form>

                        @if ($user->foto_profil)
                            <button
                                type="button"
                                class="
                                    mt-3 w-full rounded-xl
                                    border border-rose-100
                                    bg-rose-50 px-4 py-2.5
                                    text-sm font-bold text-rose-600
                                    transition hover:bg-rose-100
                                "
                                @click="$dispatch(
                                    'open-delete-confirm',
                                    {
                                        action: @js(
                                            route('profile.photo.destroy')
                                        ),
                                        title: 'Hapus Foto Profil?',
                                        name: 'Foto profil saat ini',
                                        description:
                                            'Foto profilmu akan dihapus dan tampilan akan kembali memakai inisial nama.',
                                        confirmText:
                                            'Ya, Hapus Foto',
                                    }
                                )"
                            >
                                Hapus Foto
                            </button>
                        @endif
                    </div>
                </article>

                {{-- Status akun --}}
                <article
                    class="
                        overflow-hidden rounded-3xl
                        border border-sky-100/90 bg-white/95
                        p-5
                        shadow-[0_20px_50px_rgba(15,52,94,0.09)]
                    "
                >
                    <div class="flex items-center justify-between gap-4">
                        <span
                            class="
                                text-[10px] font-bold uppercase
                                tracking-[0.16em] text-slate-500
                            "
                        >
                            Status Akun
                        </span>

                        <span
                            @class([
                                'inline-flex items-center gap-2 rounded-full px-3 py-1 text-[10px] font-bold uppercase ring-1',
                                'bg-emerald-50 text-emerald-700 ring-emerald-100' => $isActive,
                                'bg-rose-50 text-rose-700 ring-rose-100' => ! $isActive,
                            ])
                        >
                            <span
                                @class([
                                    'h-1.5 w-1.5 rounded-full',
                                    'bg-emerald-500' => $isActive,
                                    'bg-rose-500' => ! $isActive,
                                ])
                            ></span>

                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div
                        class="
                            mt-4 flex items-center justify-between
                            gap-4 border-t border-slate-100 pt-4
                        "
                    >
                        <span
                            class="
                                text-[10px] font-bold uppercase
                                tracking-[0.16em] text-slate-500
                            "
                        >
                            Login Terakhir
                        </span>

                        <span class="text-right text-xs font-bold text-slate-700">
                            {{ $lastLogin }}
                        </span>
                    </div>
                </article>
            </div>

            <div class="space-y-5">
                {{-- Informasi dasar --}}
                <article
                    class="
                        overflow-hidden rounded-3xl
                        border border-sky-100/90 bg-white/95
                        shadow-[0_20px_50px_rgba(15,52,94,0.09)]
                    "
                >
                    <header
                        class="
                            flex items-start justify-between gap-4
                            border-b border-sky-100
                            bg-gradient-to-r
                            from-sky-50 via-blue-50 to-cyan-50
                            px-6 py-5
                        "
                    >
                        <div>
                            <p
                                class="
                                    text-[10px] font-bold uppercase
                                    tracking-[0.18em] text-sky-700
                                "
                            >
                                Data Pribadi
                            </p>

                            <h2 class="mt-1 text-xl font-extrabold text-slate-950">
                                Informasi Dasar
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Perbarui nama dan alamat email akun Anda.
                            </p>
                        </div>

                        <span
                            class="
                                shrink-0 rounded-full bg-sky-100
                                px-3 py-1.5 text-[10px]
                                font-bold uppercase text-sky-700
                            "
                        >
                            {{ str_replace('_', ' ', $user->role ?? 'Pengguna') }}
                        </span>
                    </header>

                    <form
                        method="POST"
                        action="{{ route('profile.update') }}"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="space-y-5 p-6">
                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        for="profile-name"
                                        class="
                                            mb-2 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Nama lengkap
                                    </label>

                                    <input
                                        id="profile-name"
                                        name="nama"
                                        type="text"
                                        value="{{ old('nama', $displayName) }}"
                                        required
                                        autocomplete="name"
                                        class="
                                            h-12 w-full rounded-xl
                                            border-slate-300 bg-white
                                            px-4 text-slate-700
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                    >

                                    @error('nama')
                                        <p class="mt-1 text-xs text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        for="profile-email"
                                        class="
                                            mb-2 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Email perusahaan
                                    </label>

                                    <input
                                        id="profile-email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email', $user->email) }}"
                                        required
                                        autocomplete="email"
                                        class="
                                            h-12 w-full rounded-xl
                                            border-slate-300 bg-white
                                            px-4 text-slate-700
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                    >

                                    @error('email')
                                        <p class="mt-1 text-xs text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label
                                    for="profile-role"
                                    class="
                                        mb-2 block text-sm
                                        font-bold text-slate-700
                                    "
                                >
                                    Jabatan dan hak akses
                                </label>

                                <div class="relative">
                                    <input
                                        id="profile-role"
                                        type="text"
                                        value="{{ $roleLabel }}"
                                        readonly
                                        class="
                                            h-12 w-full cursor-not-allowed
                                            rounded-xl border-slate-200
                                            bg-slate-100 px-4
                                            text-slate-500
                                        "
                                    >

                                    <svg
                                        class="
                                            pointer-events-none absolute
                                            right-4 top-1/2 h-5 w-5
                                            -translate-y-1/2 text-slate-400
                                        "
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="6"
                                            y="10"
                                            width="12"
                                            height="10"
                                            rx="2"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />
                                        <path
                                            d="M9 10V7a3 3 0 0 1 6 0v3"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </div>

                                <p class="mt-2 text-xs text-slate-500">
                                    Jabatan tidak dapat diubah melalui halaman profil.
                                </p>
                            </div>
                        </div>

                        <footer
                            class="
                                flex justify-end border-t
                                border-sky-100 bg-slate-50/60
                                px-6 py-4
                            "
                        >
                            <button
                                type="submit"
                                class="
                                    rounded-xl
                                    bg-gradient-to-r
                                    from-sky-600 to-blue-600
                                    px-5 py-2.5 text-sm
                                    font-bold text-white
                                    shadow-[0_10px_24px_rgba(2,132,199,0.24)]
                                    transition hover:-translate-y-0.5
                                "
                            >
                                Simpan Informasi
                            </button>
                        </footer>
                    </form>
                </article>

                {{-- Ubah kata sandi --}}
                <article
                    class="
                        overflow-hidden rounded-3xl
                        border border-sky-100/90 bg-white/95
                        shadow-[0_20px_50px_rgba(15,52,94,0.09)]
                    "
                >
                    <header
                        class="
                            flex items-start gap-4
                            border-b border-sky-100
                            bg-gradient-to-r
                            from-sky-50 via-blue-50 to-cyan-50
                            px-6 py-5
                        "
                    >
                        <span
                            class="
                                grid h-11 w-11 shrink-0 place-items-center
                                rounded-2xl bg-white text-sky-700
                                shadow-sm ring-1 ring-sky-100
                            "
                        >
                            <svg
                                class="h-6 w-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M4 12a8 8 0 1 0 2.3-5.7L4 8.5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                                <path
                                    d="M4 4v4.5h4.5M12 8v5l3 2"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>

                        <div>
                            <p
                                class="
                                    text-[10px] font-bold uppercase
                                    tracking-[0.18em] text-sky-700
                                "
                            >
                                Keamanan
                            </p>

                            <h2 class="mt-1 text-xl font-extrabold text-slate-950">
                                Ubah Kata Sandi
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Gunakan kata sandi baru yang kuat dan tidak mudah ditebak.
                            </p>
                        </div>
                    </header>

                    <form
                        method="POST"
                        action="{{ route('password.update') }}"
                    >
                        @csrf
                        @method('PUT')

                        <div class="space-y-5 p-6">
                            <div>
                                <label
                                    for="current-password"
                                    class="
                                        mb-2 block text-sm
                                        font-bold text-slate-700
                                    "
                                >
                                    Kata sandi saat ini
                                </label>

                                <div class="relative">
                                    <input
                                        id="current-password"
                                        name="current_password"
                                        :type="showCurrentPassword ? 'text' : 'password'"
                                        autocomplete="current-password"
                                        required
                                        class="
                                            h-12 w-full rounded-xl
                                            border-slate-300 bg-white
                                            px-4 pr-12 text-slate-700
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                        "
                                        placeholder="Masukkan kata sandi saat ini"
                                    >

                                    <button
                                        type="button"
                                        class="
                                            absolute right-3 top-1/2
                                            grid h-8 w-8 -translate-y-1/2
                                            place-items-center rounded-lg
                                            text-slate-400 transition
                                            hover:bg-sky-50 hover:text-sky-700
                                        "
                                        @click="showCurrentPassword = ! showCurrentPassword"
                                        aria-label="Tampilkan atau sembunyikan kata sandi saat ini"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M3 12s3.5-5 9-5 9 5 9 5-3.5 5-9 5-9-5-9-5Z"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            />
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="2.5"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                @error('current_password', 'updatePassword')
                                    <p class="mt-1 text-xs text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        for="new-password"
                                        class="
                                            mb-2 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Kata sandi baru
                                    </label>

                                    <div class="relative">
                                        <input
                                            id="new-password"
                                            name="password"
                                            :type="showNewPassword ? 'text' : 'password'"
                                            autocomplete="new-password"
                                            required
                                            class="
                                                h-12 w-full rounded-xl
                                                border-slate-300 bg-white
                                                px-4 pr-12 text-slate-700
                                                focus:border-sky-500
                                                focus:ring-sky-500
                                            "
                                            placeholder="Minimal 8 karakter"
                                        >

                                        <button
                                            type="button"
                                            class="
                                                absolute right-3 top-1/2
                                                grid h-8 w-8 -translate-y-1/2
                                                place-items-center rounded-lg
                                                text-slate-400 transition
                                                hover:bg-sky-50 hover:text-sky-700
                                            "
                                            @click="showNewPassword = ! showNewPassword"
                                            aria-label="Tampilkan atau sembunyikan kata sandi baru"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M3 12s3.5-5 9-5 9 5 9 5-3.5 5-9 5-9-5-9-5Z"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                />
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.5"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                />
                                            </svg>
                                        </button>
                                    </div>

                                    @error('password', 'updatePassword')
                                        <p class="mt-1 text-xs text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        for="confirm-password"
                                        class="
                                            mb-2 block text-sm
                                            font-bold text-slate-700
                                        "
                                    >
                                        Konfirmasi kata sandi
                                    </label>

                                    <div class="relative">
                                        <input
                                            id="confirm-password"
                                            name="password_confirmation"
                                            :type="showConfirmPassword ? 'text' : 'password'"
                                            autocomplete="new-password"
                                            required
                                            class="
                                                h-12 w-full rounded-xl
                                                border-slate-300 bg-white
                                                px-4 pr-12 text-slate-700
                                                focus:border-sky-500
                                                focus:ring-sky-500
                                            "
                                            placeholder="Ulangi kata sandi baru"
                                        >

                                        <button
                                            type="button"
                                            class="
                                                absolute right-3 top-1/2
                                                grid h-8 w-8 -translate-y-1/2
                                                place-items-center rounded-lg
                                                text-slate-400 transition
                                                hover:bg-sky-50 hover:text-sky-700
                                            "
                                            @click="showConfirmPassword = ! showConfirmPassword"
                                            aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M3 12s3.5-5 9-5 9 5 9 5-3.5 5-9 5-9-5-9-5Z"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                />
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.5"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <footer
                            class="
                                flex justify-end border-t
                                border-sky-100 bg-slate-50/60
                                px-6 py-4
                            "
                        >
                            <button
                                type="submit"
                                class="
                                    rounded-xl
                                    bg-gradient-to-r
                                    from-sky-600 to-blue-600
                                    px-5 py-2.5 text-sm
                                    font-bold text-white
                                    shadow-[0_10px_24px_rgba(2,132,199,0.24)]
                                    transition hover:-translate-y-0.5
                                "
                            >
                                Simpan Kata Sandi
                            </button>
                        </footer>
                    </form>
                </article>
            </div>
        </section>

        {{-- Informasi keamanan --}}
        <section
            class="
                relative mt-5 overflow-hidden rounded-3xl
                border border-rose-300
                bg-gradient-to-r
                from-rose-100 via-red-50 to-orange-100
                px-6 py-5
                shadow-[0_18px_44px_rgba(225,29,72,0.18)]
            "
        >
            <div
                class="
                    pointer-events-none absolute
                    -right-14 -top-16 h-40 w-40
                    rounded-full bg-rose-400/20 blur-2xl
                "
            ></div>

            <div
                class="
                    pointer-events-none absolute
                    -bottom-14 left-1/3 h-32 w-32
                    rounded-full bg-orange-300/20 blur-2xl
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
                        bg-gradient-to-br from-rose-600 to-red-700
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
                                tracking-[0.18em] text-rose-800
                            "
                        >
                            Tingkatkan Keamanan Akun
                        </p>

                        <span
                            class="
                                inline-flex rounded-full
                                bg-rose-600 px-2.5 py-1
                                text-[9px] font-extrabold uppercase
                                tracking-[0.12em] text-white
                                shadow-sm
                            "
                        >
                            Penting
                        </span>
                    </div>

                    <h2
                        class="
                            mt-2 text-lg font-extrabold
                            text-slate-950 sm:text-xl
                        "
                    >
                        Lindungi akses administratif Anda
                    </h2>

                    <p
                        class="
                            mt-2 max-w-5xl text-sm
                            leading-6 text-slate-700
                        "
                    >
                        Gunakan kata sandi unik, jangan membagikan kredensial,
                        dan selalu keluar dari akun ketika menggunakan perangkat bersama.
                        Jabatan serta hak akses hanya dapat diubah oleh pengelola sistem.
                    </p>
                </div>
            </div>
        </section>
    </div>
@endsection
