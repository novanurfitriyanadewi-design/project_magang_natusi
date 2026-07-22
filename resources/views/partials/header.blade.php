@php
    $user = auth()->user();

    $userName = $user?->nama
        ?? $user?->name
        ?? 'Pengguna';

    $roleLabel = match ($user?->role) {
        'superadmin' => 'Super Admin',
        'admin' => 'Admin',
        default => 'Peserta Magang',
    };

    $initials = collect(
        preg_split('/\s+/', trim($userName)) ?: []
    )
        ->filter()
        ->take(2)
        ->map(
            fn ($word) => strtoupper(
                mb_substr($word, 0, 1)
            )
        )
        ->implode('');

    $userInformation = $user?->email
        ?: ($user?->username ? '@' . $user->username : '-');

    $isAdminPage = request()->routeIs('superadmin.admin*');
    $isRulePage = request()->routeIs('superadmin.aturan*');
    $isPaymentPage = request()->routeIs('superadmin.metode-pembayaran*');

    $searchAction = match (true) {
        $isAdminPage && Route::has('superadmin.admin')
            => route('superadmin.admin'),

        $isRulePage && Route::has('superadmin.aturan.index')
            => route('superadmin.aturan.index'),

        $isPaymentPage && Route::has('superadmin.metode-pembayaran.index')
            => route('superadmin.metode-pembayaran.index'),

        default => null,
    };

    $searchLabel = match (true) {
        $isAdminPage => 'Cari nama admin',
        $isRulePage => 'Cari aturan',
        $isPaymentPage => 'Cari rekening pembayaran',
        default => 'Cari menu',
    };

    $searchPlaceholder = match (true) {
        $isAdminPage => 'Cari nama admin...',
        $isRulePage => 'Cari nama atau isi aturan...',
        $isPaymentPage => 'Cari bank, rekening, atau pemilik...',
        default => 'Cari menu...',
    };

    $profileUrl = Route::has('profile.edit')
        ? route('profile.edit')
        : '#';

    $showNotification = $user?->role !== 'superadmin';

    $profilePhotoUrl = (
        filled($user?->foto_profil)
        && Route::has('profile.photo.show')
    )
        ? route('profile.photo.show')
            . '?v='
            . ($user?->updated_at?->timestamp ?? 1)
        : null;


    $headerNotifications = collect();
    $unreadNotificationCount = 0;

    if ($showNotification && $user) {
        $headerNotifications = $user->notifikasi()
            ->latest('id_notifikasi')
            ->limit(8)
            ->get();

        $unreadNotificationCount = $user->notifikasi()
            ->where('dibaca', false)
            ->count();
    }
@endphp

<header
    class="
        fixed left-0 right-0 top-0 z-40
        h-[72px]
        border-b border-slate-200/80
        bg-white/95
        shadow-[0_5px_24px_rgba(15,52,94,0.06)]
        backdrop-blur-xl
        lg:left-[245px]
    "
>
    <div
        class="
            flex h-full items-center justify-between
            px-4 sm:px-6 lg:px-7
        "
    >
        <div class="flex items-center">
            <button
                type="button"
                class="
                    grid h-10 w-10 place-items-center
                    rounded-xl border border-slate-200
                    text-slate-600 transition duration-200
                    hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700
                    focus:outline-none focus:ring-4 focus:ring-sky-100
                    lg:hidden
                "
                @click="sidebarOpen = true"
                aria-label="Buka menu"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M4 7h16M4 12h16M4 17h16"
                        stroke="currentColor"
                        stroke-width="1.9"
                        stroke-linecap="round"
                    />
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-1 sm:gap-2">
            @if ($searchAction)
                <form
                    method="GET"
                    action="{{ $searchAction }}"
                    class="relative mr-1 hidden md:block"
                >
                    <label
                        for="header-search"
                        class="sr-only"
                    >
                        {{ $searchLabel }}
                    </label>

                    <svg
                        class="
                            pointer-events-none absolute
                            left-4 top-1/2 h-[18px] w-[18px]
                            -translate-y-1/2 text-slate-400
                        "
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <circle
                            cx="11"
                            cy="11"
                            r="6"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                        <path
                            d="m16 16 4 4"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>

                    <input
                        id="header-search"
                        name="search"
                        type="search"
                        value="{{ request('search') }}"
                        placeholder="{{ $searchPlaceholder }}"
                        autocomplete="off"
                        class="
                            h-11 w-[330px] rounded-full
                            border border-slate-200
                            bg-slate-50/80
                            py-2 pl-11 pr-4
                            text-sm text-slate-700
                            outline-none transition duration-200
                            placeholder:text-slate-400
                            hover:border-slate-300
                            focus:border-sky-400
                            focus:bg-white
                            focus:ring-4 focus:ring-sky-100
                        "
                    >
                </form>
            @elseif (! $isAdminPage)
                <div class="relative mr-1 hidden md:block">
                    <label
                        for="header-menu-search"
                        class="sr-only"
                    >
                        Cari menu
                    </label>

                    <svg
                        class="
                            pointer-events-none absolute
                            left-4 top-1/2 h-[18px] w-[18px]
                            -translate-y-1/2 text-slate-400
                        "
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <circle
                            cx="11"
                            cy="11"
                            r="6"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                        <path
                            d="m16 16 4 4"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>

                    <input
                        id="header-menu-search"
                        type="search"
                        x-model.debounce.250ms="query"
                        placeholder="Cari menu..."
                        autocomplete="off"
                        class="
                            h-11 w-[330px] rounded-full
                            border border-slate-200
                            bg-slate-50/80
                            py-2 pl-11 pr-4
                            text-sm text-slate-700
                            outline-none transition duration-200
                            placeholder:text-slate-400
                            hover:border-slate-300
                            focus:border-sky-400
                            focus:bg-white
                            focus:ring-4 focus:ring-sky-100
                        "
                    >
                </div>
            @endif

            @if ($showNotification)
                <div
                    x-data="{ notificationOpen: false }"
                    class="relative"
                >
                    <div
                        class="
                            flex items-center
                            rounded-2xl border border-slate-200/80
                            bg-slate-50/80 p-1 shadow-sm
                        "
                    >
                        <button
                            type="button"
                            class="
                                group relative grid h-10 w-10 place-items-center
                                rounded-xl text-slate-500
                                transition duration-200
                                hover:bg-white hover:text-sky-700 hover:shadow-sm
                                focus:outline-none focus:ring-2 focus:ring-sky-200
                            "
                            @click.stop="notificationOpen = !notificationOpen"
                            :aria-expanded="notificationOpen"
                            aria-haspopup="menu"
                            aria-label="Notifikasi"
                        >
                            <svg
                                class="h-[20px] w-[20px]"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M18 8.5a6 6 0 0 0-12 0v3.8c0 1.5-.5 2.9-1.5 4.2h15a6.9 6.9 0 0 1-1.5-4.2V8.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M9.8 19a2.4 2.4 0 0 0 4.4 0"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>

                            @if($unreadNotificationCount > 0)
                                <span
                                    class="absolute -right-1 -top-1 inline-grid min-h-5 min-w-5 place-items-center rounded-full bg-rose-500 px-1 text-[9px] font-black text-white ring-2 ring-white"
                                >
                                    {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                                </span>
                            @endif
                        </button>
                    </div>

                    <div
                        x-cloak
                        x-show="notificationOpen"
                        x-transition.origin.top.right
                        @click.outside="notificationOpen = false"
                        class="absolute right-0 top-[54px] z-50 w-[min(92vw,390px)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_22px_55px_rgba(15,23,42,0.20)]"
                        role="menu"
                    >
                        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-4 py-3.5">
                            <div>
                                <p class="text-sm font-extrabold text-slate-900">Notifikasi</p>
                                <p class="mt-0.5 text-[11px] text-slate-500">{{ $unreadNotificationCount }} belum dibaca</p>
                            </div>

                            @if($unreadNotificationCount > 0 && Route::has('notifikasi.read-all'))
                                <form method="POST" action="{{ route('notifikasi.read-all') }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-extrabold text-sky-700 transition hover:text-sky-800">
                                        Tandai semua dibaca
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-[420px] overflow-y-auto">
                            @forelse($headerNotifications as $notification)
                                @php
                                    $notificationTone = match ($notification->tipe) {
                                        'peringatan' => [
                                            'icon' => 'notification_important',
                                            'icon_class' => 'bg-amber-100 text-amber-700',
                                        ],
                                        'sukses' => [
                                            'icon' => 'task_alt',
                                            'icon_class' => 'bg-emerald-100 text-emerald-700',
                                        ],
                                        default => [
                                            'icon' => 'info',
                                            'icon_class' => 'bg-sky-100 text-sky-700',
                                        ],
                                    };
                                @endphp

                                <div @class([
                                    'border-b border-slate-100 px-4 py-3.5 last:border-b-0',
                                    'bg-sky-50/45' => ! $notification->dibaca,
                                    'bg-white' => $notification->dibaca,
                                ])>
                                    <div class="flex gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $notificationTone['icon_class'] }}">
                                            <span class="material-symbols-outlined text-[20px]">{{ $notificationTone['icon'] }}</span>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <p class="text-xs font-extrabold leading-5 text-slate-900">{{ $notification->judul }}</p>
                                                @if(! $notification->dibaca)
                                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-500"></span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs leading-5 text-slate-600">{{ $notification->pesan }}</p>
                                            <div class="mt-2 flex items-center justify-between gap-3">
                                                <span class="text-[10px] font-semibold text-slate-400">{{ $notification->created_at?->diffForHumans() }}</span>

                                                @if(! $notification->dibaca && Route::has('notifikasi.read'))
                                                    <form method="POST" action="{{ route('notifikasi.read', $notification) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-[10px] font-extrabold text-sky-700 transition hover:text-sky-800">
                                                            Tandai dibaca
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-10 text-center">
                                    <span class="material-symbols-outlined text-[42px] text-slate-300">notifications_off</span>
                                    <p class="mt-3 text-sm font-extrabold text-slate-700">Belum ada notifikasi</p>
                                    <p class="mt-1 text-xs text-slate-500">Notifikasi terbaru akan tampil di sini.</p>
                                </div>
                            @endforelse
                        </div>

                        @if($user?->role === 'peserta' && Route::has('peserta.tugas.index'))
                            <a href="{{ route('peserta.tugas.index') }}" class="flex items-center justify-center gap-2 border-t border-slate-100 bg-slate-50 px-4 py-3 text-xs font-extrabold text-sky-700 transition hover:bg-sky-50">
                                Lihat tugas saya
                                <span class="material-symbols-outlined text-[17px]">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div
                    class="
                        mx-1 hidden h-9 w-px
                        bg-slate-200 sm:block
                    "
                ></div>
            @endif

            <div class="relative">
                <button
                    type="button"
                    class="
                        group flex items-center gap-2
                        rounded-xl px-1.5 py-1
                        transition duration-200
                        hover:bg-slate-50
                        focus:outline-none focus:ring-4 focus:ring-sky-100
                    "
                    @click.stop="profileOpen = !profileOpen"
                    :aria-expanded="profileOpen"
                    aria-haspopup="menu"
                >
                    <span class="hidden min-w-0 text-right sm:block">
                        <strong
                            class="
                                block max-w-36 truncate
                                text-xs font-semibold text-slate-900
                            "
                        >
                            {{ $userName }}
                        </strong>

                        <span class="mt-0.5 block text-[10px] text-slate-500">
                            {{ $roleLabel }}
                        </span>
                    </span>

                    <span
                        class="
                            relative grid h-11 w-11 shrink-0
                            place-items-center overflow-hidden rounded-full
                            border-2 border-sky-600
                            bg-gradient-to-br from-sky-50 to-blue-100
                            text-sm font-bold text-sky-700 shadow-sm
                        "
                    >
                        <span>
                            {{ $initials ?: 'U' }}
                        </span>

                        @if ($profilePhotoUrl)
                            <img
                                src="{{ $profilePhotoUrl }}"
                                alt="Foto profil {{ $userName }}"
                                class="
                                    absolute inset-0 h-full w-full
                                    object-cover
                                "
                                onerror="this.remove()"
                            >
                        @endif
                    </span>
                </button>

                <div
                    x-cloak
                    x-show="profileOpen"
                    x-transition.origin.top.right
                    @click.outside="profileOpen = false"
                    class="
                        absolute right-0 z-50 mt-3 w-60
                        overflow-hidden rounded-2xl
                        border border-slate-200 bg-white p-2
                        shadow-[0_20px_55px_rgba(15,52,94,0.18)]
                    "
                    role="menu"
                >
                    <div
                        class="
                            rounded-xl
                            bg-gradient-to-r from-sky-50 to-blue-50
                            px-3 py-3
                        "
                    >
                        <div class="flex items-center gap-3">
                            <span
                                class="
                                    relative grid h-11 w-11 shrink-0
                                    place-items-center overflow-hidden
                                    rounded-full border-2 border-white
                                    bg-gradient-to-br
                                    from-sky-500 to-blue-700
                                    text-sm font-bold text-white
                                    shadow-sm ring-1 ring-sky-100
                                "
                            >
                                <span>
                                    {{ $initials ?: 'U' }}
                                </span>

                                @if ($profilePhotoUrl)
                                    <img
                                        src="{{ $profilePhotoUrl }}"
                                        alt="Foto profil {{ $userName }}"
                                        class="
                                            absolute inset-0 h-full w-full
                                            object-cover
                                        "
                                        onerror="this.remove()"
                                    >
                                @endif
                            </span>

                            <div class="min-w-0">
                                <p
                                    class="
                                        truncate text-sm font-semibold
                                        text-slate-900
                                    "
                                >
                                    {{ $userName }}
                                </p>

                                <p
                                    class="
                                        mt-1 truncate text-xs
                                        text-slate-500
                                    "
                                >
                                    {{ $userInformation }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="
                                mt-3 inline-flex rounded-full
                                bg-sky-100 px-2.5 py-1
                                text-[10px] font-semibold text-sky-700
                            "
                        >
                            {{ $roleLabel }}
                        </span>
                    </div>

                    <div class="mt-2 space-y-1">
                        <a
                            href="{{ $profileUrl }}"
                            class="
                                block rounded-xl px-3 py-2.5
                                text-sm font-medium text-slate-700
                                transition hover:bg-slate-50 hover:text-sky-700
                            "
                            role="menuitem"
                        >
                            Kelola Profil
                        </a>

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="
                                    block w-full rounded-xl
                                    px-3 py-2.5 text-left
                                    text-sm font-medium text-rose-600
                                    transition hover:bg-rose-50
                                "
                                role="menuitem"
                            >
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>