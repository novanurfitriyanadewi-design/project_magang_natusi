@extends('layouts.portal')

@section('title', 'Kelola Jam Absensi')

@section('content')
    <style>
        .natusi-time-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.65;
            filter:
                invert(41%)
                sepia(86%)
                saturate(1354%)
                hue-rotate(170deg)
                brightness(91%)
                contrast(92%);
        }

        .natusi-time-input:disabled::-webkit-calendar-picker-indicator {
            cursor: not-allowed;
            opacity: 0.3;
        }
    </style>

    <div
        x-data="{
            clock: '',
            dateLabel: '',
            timer: null,

            init() {
                this.updateClock();

                this.timer = setInterval(() => {
                    this.updateClock();
                }, 1000);
            },

            updateClock() {
                const now = new Date();

                this.clock = new Intl.DateTimeFormat('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                }).format(now);

                this.dateLabel = new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                }).format(now);
            },
        }"
    >
        {{-- Judul halaman --}}
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
                Operasional Harian
            </span>

            <h1
                class="
                    mt-3 text-2xl font-extrabold tracking-tight
                    text-slate-950 sm:text-3xl
                "
            >
                Kelola Jam Absensi
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Atur rentang waktu pendaftaran kehadiran harian
                untuk peserta magang dan karyawan.
            </p>
        </section>

        {{-- Ringkasan --}}
        <section class="mt-5 grid gap-4 md:grid-cols-3">
            <article
                class="
                    relative overflow-hidden rounded-2xl
                    bg-gradient-to-br from-blue-600 to-sky-500
                    p-5 text-white
                    shadow-[0_16px_38px_rgba(37,99,235,0.20)]
                "
            >
                <div
                    class="
                        absolute -right-8 -top-8 h-32 w-32
                        rounded-full border-[20px] border-white/10
                    "
                ></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p
                            class="
                                text-[11px] font-bold uppercase
                                tracking-[0.16em] text-blue-100
                            "
                        >
                            Jam Buka Absensi
                        </p>

                        <p class="mt-3 text-4xl font-extrabold">
                            {{ $openTime }}
                        </p>

                        <p class="mt-1 text-sm text-blue-100">
                            Waktu mulai pendaftaran
                        </p>
                    </div>

                    <span
                        class="
                            grid h-12 w-12 place-items-center
                            rounded-2xl bg-white/15 ring-1 ring-white/20
                        "
                    >
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M5 7h14M7 3v4M17 3v4M5 7v13h14V7"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M9 12h6M12 9v6"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </div>
            </article>

            <article
                class="
                    relative overflow-hidden rounded-2xl
                    bg-gradient-to-br from-sky-600 to-cyan-500
                    p-5 text-white
                    shadow-[0_16px_38px_rgba(2,132,199,0.20)]
                "
            >
                <div
                    class="
                        absolute -bottom-12 -right-8 h-36 w-36
                        rounded-full border-[22px] border-white/10
                    "
                ></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p
                            class="
                                text-[11px] font-bold uppercase
                                tracking-[0.16em] text-sky-100
                            "
                        >
                            Jam Tutup Absensi
                        </p>

                        <p class="mt-3 text-4xl font-extrabold">
                            {{ $closeTime }}
                        </p>

                        <p class="mt-1 text-sm text-sky-100">
                            Batas akhir pendaftaran
                        </p>
                    </div>

                    <span
                        class="
                            grid h-12 w-12 place-items-center
                            rounded-2xl bg-white/15 ring-1 ring-white/20
                        "
                    >
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="8"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M12 7v5l3 2"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </div>
            </article>

            <article
                class="
                    relative overflow-hidden rounded-2xl
                    bg-gradient-to-br
                    from-blue-800 via-sky-700 to-cyan-600
                    p-5 text-white
                    shadow-[0_16px_38px_rgba(30,64,175,0.20)]
                "
            >
                <div
                    class="
                        absolute -right-10 -top-12 h-40 w-40
                        rounded-full border-[22px] border-white/10
                    "
                ></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p
                            class="
                                text-[11px] font-bold uppercase
                                tracking-[0.16em] text-cyan-100
                            "
                        >
                            Status Absensi
                        </p>

                        <p class="mt-3 truncate text-xl font-extrabold">
                            {{ $isActive ? 'Sedang Aktif' : 'Tidak Aktif' }}
                        </p>

                        <p class="mt-2 text-sm text-cyan-100">
                            {{
                                $isActive
                                    ? 'Pengaturan sementara dikunci'
                                    : 'Pengaturan dapat diperbarui'
                            }}
                        </p>
                    </div>

                    <span
                        class="
                            grid h-12 w-12 shrink-0 place-items-center
                            rounded-2xl bg-white/15 ring-1 ring-white/20
                        "
                    >
                        @if ($isActive)
                            <svg
                                class="h-6 w-6"
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
                        @else
                            <svg
                                class="h-6 w-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="m5 16.5-.8 3.3 3.3-.8L18 8.5 15.5 6 5 16.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="m13.8 7.7 2.5 2.5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                            </svg>
                        @endif
                    </span>
                </div>
            </article>
        </section>

        <section
            class="
                mt-5 grid items-stretch gap-5
                lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.85fr)]
            "
        >
            {{-- Form pengaturan --}}
            <article
                class="
                    h-full overflow-hidden rounded-3xl
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
                            Pengaturan Waktu
                        </p>

                        <h2 class="mt-1 text-xl font-extrabold text-slate-950">
                            Rentang Jam Absensi
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Perubahan berlaku pada pendaftaran kehadiran berikutnya.
                        </p>
                    </div>

                    <span
                        class="
                            grid h-12 w-12 shrink-0 place-items-center
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
                            <circle
                                cx="12"
                                cy="12"
                                r="8"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M12 7v5l3 2"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </header>

                <form
                    method="POST"
                    action="{{ route('superadmin.jam-absensi.update') }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="space-y-5 p-6">
                        @if ($errors->any())
                            <div
                                class="
                                    rounded-2xl border border-rose-200
                                    bg-rose-50 px-4 py-3
                                    text-sm text-rose-700
                                "
                            >
                                <p class="font-bold">
                                    Pengaturan belum dapat disimpan.
                                </p>

                                <ul class="mt-1 list-inside list-disc">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($isActive)
                            <div
                                class="
                                    flex items-start gap-3 rounded-2xl
                                    border border-amber-200
                                    bg-amber-50 px-4 py-3
                                "
                            >
                                <span
                                    class="
                                        mt-0.5 grid h-9 w-9 shrink-0
                                        place-items-center rounded-xl
                                        bg-white text-amber-600
                                        ring-1 ring-amber-200
                                    "
                                >
                                    <svg
                                        class="h-5 w-5"
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
                                </span>

                                <div>
                                    <p class="text-sm font-bold text-amber-800">
                                        Pengaturan sedang dikunci
                                    </p>

                                    <p class="mt-1 text-sm leading-6 text-amber-700">
                                        Jam absensi tidak dapat diubah selama
                                        periode {{ $openTime }}–{{ $closeTime }}
                                        sedang berlangsung.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label
                                    for="jam-buka"
                                    class="
                                        mb-2 block text-sm font-bold
                                        text-slate-700
                                    "
                                >
                                    Jam buka absensi
                                </label>

                                <div class="relative">
                                    <svg
                                        class="
                                            pointer-events-none absolute
                                            left-4 top-1/2 h-5 w-5
                                            -translate-y-1/2 text-sky-600
                                        "
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="8"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />
                                        <path
                                            d="M12 7v5l3 2"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    <input
                                        id="jam-buka"
                                        name="jam_buka"
                                        type="time"
                                        value="{{ old('jam_buka', $openTime) }}"
                                        required
                                        @disabled($isActive)
                                        class="
                                            natusi-time-input
                                            h-12 w-full rounded-xl
                                            border-slate-300 bg-white
                                            pl-12 pr-4 text-slate-700
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                            disabled:cursor-not-allowed
                                            disabled:bg-slate-100
                                            disabled:text-slate-400
                                        "
                                    >
                                </div>

                                <p class="mt-2 text-xs text-slate-500">
                                    Default sistem: 07:30 WIB
                                </p>
                            </div>

                            <div>
                                <label
                                    for="jam-tutup"
                                    class="
                                        mb-2 block text-sm font-bold
                                        text-slate-700
                                    "
                                >
                                    Jam tutup absensi
                                </label>

                                <div class="relative">
                                    <svg
                                        class="
                                            pointer-events-none absolute
                                            left-4 top-1/2 h-5 w-5
                                            -translate-y-1/2 text-sky-600
                                        "
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="8"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />
                                        <path
                                            d="M12 7v5l3 2"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    <input
                                        id="jam-tutup"
                                        name="jam_tutup"
                                        type="time"
                                        value="{{ old('jam_tutup', $closeTime) }}"
                                        required
                                        @disabled($isActive)
                                        class="
                                            natusi-time-input
                                            h-12 w-full rounded-xl
                                            border-slate-300 bg-white
                                            pl-12 pr-4 text-slate-700
                                            focus:border-sky-500
                                            focus:ring-sky-500
                                            disabled:cursor-not-allowed
                                            disabled:bg-slate-100
                                            disabled:text-slate-400
                                        "
                                    >
                                </div>

                                <p class="mt-2 text-xs text-slate-500">
                                    Default sistem: 09:00 WIB
                                </p>
                            </div>
                        </div>
                    </div>

                    <footer
                        class="
                            flex flex-col-reverse gap-3
                            border-t border-sky-100
                            bg-slate-50/60 px-6 py-4
                            sm:flex-row sm:justify-end
                        "
                    >
                        <button
                            type="submit"
                            form="reset-attendance-form"
                            @disabled($isActive)
                            class="
                                rounded-xl border border-sky-100
                                bg-white px-4 py-2.5
                                text-sm font-bold text-sky-700
                                shadow-sm transition
                                hover:bg-sky-50
                                disabled:cursor-not-allowed
                                disabled:opacity-45
                            "
                        >
                            Reset ke Default
                        </button>

                        <button
                            type="submit"
                            @disabled($isActive)
                            class="
                                inline-flex items-center justify-center gap-2
                                rounded-xl
                                bg-gradient-to-r
                                from-sky-600 to-blue-600
                                px-5 py-2.5
                                text-sm font-bold text-white
                                shadow-[0_10px_24px_rgba(2,132,199,0.24)]
                                transition hover:-translate-y-0.5
                                disabled:cursor-not-allowed
                                disabled:opacity-45
                                disabled:hover:translate-y-0
                            "
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M5 4h12l2 2v14H5V4Zm3 0v5h8V4M8 16h8"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            Simpan Perubahan
                        </button>
                    </footer>
                </form>

                <form
                    id="reset-attendance-form"
                    method="POST"
                    action="{{ route('superadmin.jam-absensi.reset') }}"
                    class="hidden"
                >
                    @csrf
                    @method('PATCH')
                </form>
            </article>

            {{-- Status --}}
            <aside
                class="
                    relative h-full overflow-hidden rounded-3xl
                    bg-gradient-to-br
                    from-blue-800 via-sky-700 to-cyan-600
                    p-6 text-white
                    shadow-[0_20px_50px_rgba(30,64,175,0.22)]
                "
            >
                <div
                    class="
                        pointer-events-none absolute
                        -right-16 -top-20 h-52 w-52
                        rounded-full border-[30px] border-white/[0.08]
                    "
                ></div>

                <div class="relative">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p
                                class="
                                    text-[10px] font-bold uppercase
                                    tracking-[0.18em] text-cyan-100
                                "
                            >
                                Status Sistem
                            </p>

                            <h2 class="mt-1 text-xl font-extrabold">
                                {{ $statusLabel }}
                            </h2>
                        </div>

                        <span
                            @class([
                                'inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] ring-1',
                                'bg-emerald-400/15 text-emerald-100 ring-emerald-200/20' => $isActive,
                                'bg-white/10 text-sky-100 ring-white/15' => ! $isActive,
                            ])
                        >
                            <span
                                @class([
                                    'h-2 w-2 rounded-full',
                                    'bg-emerald-400 shadow-[0_0_0_5px_rgba(52,211,153,0.12)]' => $isActive,
                                    'bg-sky-200/70' => ! $isActive,
                                ])
                            ></span>

                            {{ $isActive ? 'Online' : 'Standby' }}
                        </span>
                    </div>

                    <div
                        class="
                            mt-6 rounded-2xl bg-white/10
                            p-5 ring-1 ring-white/15
                            backdrop-blur-sm
                        "
                    >
                        <p class="text-xs font-semibold text-sky-100">
                            Waktu sekarang
                        </p>

                        <p
                            class="
                                mt-2 font-mono text-4xl font-extrabold
                                tracking-tight
                            "
                            x-text="clock"
                        >
                            {{ $now->format('H:i:s') }}
                        </p>

                        <p
                            class="mt-1 text-sm text-sky-100/80"
                            x-text="dateLabel"
                        >
                            {{ $now->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <div
                            class="
                                flex items-center justify-between
                                text-xs text-sky-100
                            "
                        >
                            <span>Progres periode absensi</span>
                            <span class="font-bold">{{ $progress }}%</span>
                        </div>

                        <div
                            class="
                                mt-2 h-2 overflow-hidden rounded-full
                                bg-white/15
                            "
                        >
                            <div
                                class="
                                    h-full rounded-full
                                    bg-gradient-to-r
                                    from-cyan-300 to-emerald-300
                                    transition-all duration-500
                                "
                                style="width: {{ $progress }}%"
                            ></div>
                        </div>
                    </div>

                    <dl
                        class="
                            mt-auto divide-y divide-white/10
                            rounded-2xl border border-white/10
                            bg-white/[0.06] px-4
                        "
                    >
                        <div
                            class="
                                flex items-center justify-between gap-4
                                py-3 text-sm
                            "
                        >
                            <dt class="text-sky-100/75">Rentang aktif</dt>
                            <dd class="font-bold">
                                {{ $openTime }}–{{ $closeTime }}
                            </dd>
                        </div>

                        <div
                            class="
                                flex items-center justify-between gap-4
                                py-3 text-sm
                            "
                        >
                            <dt class="text-sky-100/75">Terakhir diperbarui</dt>
                            <dd class="text-right font-bold">
                                {{ $setting->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                            </dd>
                        </div>

                        <div
                            class="
                                flex items-center justify-between gap-4
                                py-3 text-sm
                            "
                        >
                            <dt class="text-sky-100/75">Diperbarui oleh</dt>
                            <dd class="text-right font-bold">
                                {{ $setting->diperbarui_oleh ?? 'Sistem' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </section>

        {{-- Catatan penting --}}
        <section
            class="
                relative mt-5 overflow-hidden rounded-3xl
                border border-amber-300
                bg-gradient-to-r
                from-amber-100 via-orange-100 to-rose-100
                px-6 py-5
                shadow-[0_16px_40px_rgba(245,158,11,0.22)]
            "
        >
            <div
                class="
                    pointer-events-none absolute
                    -right-14 -top-16 h-40 w-40
                    rounded-full bg-orange-300/25 blur-2xl
                "
            ></div>

            <div
                class="
                    pointer-events-none absolute
                    -bottom-16 left-1/3 h-32 w-32
                    rounded-full bg-rose-300/20 blur-2xl
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
                        rounded-2xl bg-gradient-to-br
                        from-amber-500 to-orange-600
                        text-white shadow-lg
                        ring-4 ring-white/70
                    "
                >
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M12 3 2.8 19h18.4L12 3Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M12 9v4M12 16.5h.01"
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
                                tracking-[0.18em] text-orange-800
                            "
                        >
                            Catatan Penting
                        </p>

                        <span
                            class="
                                inline-flex rounded-full
                                bg-orange-600 px-2.5 py-1
                                text-[9px] font-extrabold
                                uppercase tracking-[0.12em]
                                text-white shadow-sm
                            "
                        >
                            Perhatian
                        </span>
                    </div>

                    <h2
                        class="
                            mt-2 text-lg font-extrabold
                            text-slate-950 sm:text-xl
                        "
                    >
                        Jam absensi tidak dapat diubah saat periode aktif
                    </h2>

                    <p
                        class="
                            mt-2 max-w-5xl text-sm
                            leading-6 text-slate-700
                        "
                    >
                        Ketika waktu sekarang berada di antara
                        <strong>{{ $openTime }}</strong> dan
                        <strong>{{ $closeTime }}</strong>, sistem otomatis
                        mengunci input, tombol simpan, dan tombol reset.
                        Perubahan baru dapat dilakukan setelah periode
                        absensi berakhir.
                    </p>
                </div>
            </div>
        </section>
    </div>
@endsection