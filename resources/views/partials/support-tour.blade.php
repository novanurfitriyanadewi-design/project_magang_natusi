@auth
    @php
        $tourRole = auth()->user()?->role ?? 'peserta';

        $tourSteps = [];

        $addTourStep = function (
            string $routeName,
            string $selector,
            string $title,
            string $description
        ) use (&$tourSteps): void {
            if (! Route::has($routeName)) {
                return;
            }

            $tourSteps[] = [
                'url' => route($routeName),
                'selector' => $selector,
                'title' => $title,
                'description' => $description,
            ];
        };

        if ($tourRole === 'superadmin') {
            $addTourStep(
                'superadmin.dashboard',
                '[data-tour="dashboard"]',
                'Dashboard Super Admin',
                'Di sini kamu bisa lihat ringkasan admin, pengguna, aturan aktif, jadwal absensi, dan aktivitas terbaru dalam satu tempat.'
            );

            $addTourStep(
                'superadmin.admin',
                '[data-tour="manage-admin"]',
                'Kelola Admin',
                'Lewat menu ini kamu bisa tambah, cari, edit, atau hapus akun admin dengan lebih gampang.'
            );

            $addTourStep(
                'superadmin.aturan.index',
                '[data-tour="company-rules"]',
                'Aturan Perusahaan',
                'Di sini kamu bisa bikin, baca, edit, atau hapus aturan perusahaan. Aturan yang disimpan langsung berlaku.'
            );

            $addTourStep(
                'superadmin.jam-absensi.index',
                '[data-tour="attendance-hours"]',
                'Jam Absensi',
                'Atur jam buka dan tutup absensi di sini. Saat absensi sedang berlangsung, pengaturannya otomatis dikunci.'
            );


            $addTourStep(
                'superadmin.metode-pembayaran.index',
                '[data-tour="payment-methods"]',
                'Metode Pembayaran',
                'Atur nominal administrasi, rekening bank resmi, dan lihat riwayat perubahannya dari menu ini.'
            );

            $addTourStep(
                'profile.edit',
                '[data-tour="profile"]',
                'Kelola Profil',
                'Mau ganti nama, email, foto, atau kata sandi? Semua bisa kamu atur dari menu ini.'
            );
        } elseif ($tourRole === 'admin') {
            $addTourStep(
                'dashboard',
                '[data-tour="dashboard"]',
                'Dashboard Admin',
                'Di sini kamu bisa lihat ringkasan aktivitas dan hal penting yang perlu segera ditangani.'
            );

            $addTourStep(
                'admin.permintaan.index',
                '[data-tour="internship-requests"]',
                'Permintaan Magang',
                'Cek permintaan magang baru, lihat data pelamar, lalu tentukan langkah selanjutnya dari menu ini.'
            );

            $addTourStep(
                'admin.peserta.index',
                '[data-tour="internship-participants"]',
                'Peserta Magang',
                'Semua data peserta magang bisa kamu lihat dan kelola dari menu ini.'
            );

            $addTourStep(
                'admin.tugas.index',
                '[data-tour="manage-tasks"]',
                'Kelola Tugas',
                'Bikin tugas, atur deadline, dan pantau progres peserta magang dari sini.'
            );

            $addTourStep(
                'profile.edit',
                '[data-tour="profile"]',
                'Kelola Profil',
                'Di sini kamu bisa memperbarui identitas, email, foto profil, dan keamanan akun.'
            );
        } else {
            $addTourStep(
                'dashboard',
                '[data-tour="dashboard"]',
                'Dashboard Peserta',
                'Di sini kamu bisa lihat ringkasan tugas, absensi, dan info penting selama magang.'
            );

            $addTourStep(
                'peserta.tugas.index',
                '[data-tour="tasks"]',
                'Tugas',
                'Cek tugas, deadline, dan status pekerjaan yang diberikan admin lewat menu ini.'
            );

            $addTourStep(
                'peserta.absensi.index',
                '[data-tour="attendance"]',
                'Absensi',
                'Catat kehadiranmu lewat menu ini sesuai jadwal absensi yang berlaku.'
            );

            $addTourStep(
                'peserta.laporan.index',
                '[data-tour="weekly-report"]',
                'Laporan Mingguan',
                'Bagikan perkembangan kegiatan dan hasil kerja mingguanmu dari menu ini.'
            );

            $addTourStep(
                'profile.edit',
                '[data-tour="profile"]',
                'Kelola Profil',
                'Mau ganti identitas, foto, email, atau kata sandi? Semuanya ada di sini.'
            );
        }
    @endphp

    @if (count($tourSteps) > 0)
        <div
            id="natusi-tour-root"
            class="hidden"
            aria-live="polite"
        >
            <div
                id="natusi-tour-blocker"
                class="fixed inset-0"
                style="z-index: 2147483000;"
                aria-hidden="true"
            ></div>

            <div
                id="natusi-tour-spotlight"
                class="
                    pointer-events-none fixed rounded-2xl
                    border-2 border-white
                    transition-[left,top,width,height]
                    duration-200 ease-out
                "
                style="
                    z-index: 2147483001;
                    box-shadow:
                        0 0 0 9999px rgba(2, 6, 23, 0.78),
                        0 0 0 6px rgba(56, 189, 248, 0.30),
                        0 20px 55px rgba(2, 132, 199, 0.28);
                "
                aria-hidden="true"
            ></div>

            <section
                id="natusi-tour-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="natusi-tour-title"
                class="
                    fixed w-[min(400px,calc(100vw-24px))]
                    overflow-hidden rounded-[28px]
                    border border-white/80
                    bg-white/95 backdrop-blur-xl
                    transition-[left,top,opacity,transform]
                    duration-200 ease-out
                    shadow-[0_28px_80px_rgba(2,6,23,0.30)]
                "
                style="z-index: 2147483002;"
            >
                <header
                    class="
                        relative overflow-hidden
                        bg-gradient-to-br
                        from-sky-50 via-white to-violet-50
                        px-5 pb-4 pt-5
                    "
                >
                    <div
                        class="
                            pointer-events-none absolute
                            -right-9 -top-10 h-28 w-28
                            rounded-full bg-sky-200/50 blur-2xl
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            -bottom-10 left-20 h-20 w-20
                            rounded-full bg-violet-200/40 blur-2xl
                        "
                    ></div>

                    <div
                        class="
                            relative flex items-start
                            justify-between gap-4
                        "
                    >
                        <div class="flex min-w-0 items-start gap-3">
                            <span
                                class="
                                    grid h-12 w-12 shrink-0
                                    place-items-center rounded-2xl
                                    bg-gradient-to-br
                                    from-sky-500 to-blue-600
                                    text-white shadow-lg
                                    ring-4 ring-white
                                "
                            >
                                <svg
                                    class="h-6 w-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M5 5.5h14v10H9l-4 3v-13Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M9 9h6M9 12h4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <div class="min-w-0">
                                <span
                                    id="natusi-tour-progress"
                                    class="
                                        inline-flex rounded-full
                                        bg-sky-100 px-2.5 py-1
                                        text-[10px] font-extrabold
                                        tracking-[0.08em] text-sky-700
                                    "
                                >
                                    Yuk, kenalan 👋
                                </span>

                                <h2
                                    id="natusi-tour-title"
                                    class="
                                        mt-2 text-xl font-extrabold
                                        leading-tight text-slate-950
                                    "
                                >
                                    Panduan
                                </h2>
                            </div>
                        </div>

                        <button
                            type="button"
                            id="natusi-tour-close"
                            class="
                                grid h-9 w-9 shrink-0 place-items-center
                                rounded-xl bg-white text-slate-400
                                shadow-sm ring-1 ring-slate-100
                                transition
                                hover:bg-rose-50 hover:text-rose-500
                            "
                            aria-label="Tutup panduan"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="m6 6 12 12M18 6 6 18"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </button>
                    </div>
                </header>

                <div class="px-5 pb-5 pt-2">
                    <div
                        class="
                            rounded-2xl border border-slate-100
                            bg-slate-50/80 px-4 py-4
                        "
                    >
                        <p
                            id="natusi-tour-description"
                            class="
                                text-sm leading-6
                                text-slate-600
                            "
                        ></p>
                    </div>

                    <div
                        class="
                            mt-3 flex items-center gap-2
                            rounded-2xl bg-amber-50
                            px-3 py-2.5
                            text-xs text-amber-700
                            ring-1 ring-amber-100
                        "
                    >
                        <span class="text-base" aria-hidden="true">⚡</span>

                        <span>
                            Psst... tekan <strong>Enter</strong>
                            biar lanjut lebih cepat.
                        </span>
                    </div>

                    <div
                        id="natusi-tour-dots"
                        class="
                            mt-4 flex items-center
                            justify-center gap-1.5
                        "
                        aria-hidden="true"
                    ></div>
                </div>

                <footer
                    class="
                        flex flex-wrap items-center
                        justify-between gap-3
                        bg-gradient-to-r
                        from-slate-50 to-sky-50/70
                        px-5 py-4
                    "
                >
                    <button
                        type="button"
                        id="natusi-tour-skip"
                        class="
                            rounded-full px-3 py-2
                            text-sm font-bold text-slate-500
                            transition
                            hover:bg-white hover:text-rose-600
                        "
                    >
                        Nanti aja
                    </button>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            id="natusi-tour-prev"
                            class="
                                rounded-full border border-sky-100
                                bg-white px-4 py-2.5
                                text-sm font-bold text-sky-700
                                shadow-sm transition
                                hover:-translate-y-0.5
                                hover:bg-sky-50
                                disabled:cursor-not-allowed
                                disabled:opacity-35
                                disabled:hover:translate-y-0
                            "
                        >
                            ← Balik
                        </button>

                        <button
                            type="button"
                            id="natusi-tour-next"
                            class="
                                rounded-full
                                bg-gradient-to-r
                                from-sky-500 to-blue-600
                                px-5 py-2.5
                                text-sm font-bold text-white
                                shadow-[0_8px_20px_rgba(2,132,199,0.24)]
                                transition
                                hover:-translate-y-0.5
                                hover:shadow-[0_12px_26px_rgba(2,132,199,0.30)]
                            "
                        >
                            Lanjut yuk →
                        </button>
                    </div>
                </footer>
            </section>
        </div>

        <script>
            (() => {
                const steps = @json($tourSteps);
                const role = @json($tourRole);

                if (!Array.isArray(steps) || steps.length === 0) {
                    return;
                }

                const storage = {
                    active: `natusi-tour:${role}:active`,
                    index: `natusi-tour:${role}:index`,
                    sidebarScroll: `natusi-tour:${role}:sidebar-scroll`,
                };

                const root = document.getElementById('natusi-tour-root');
                const blocker = document.getElementById(
                    'natusi-tour-blocker'
                );
                const spotlight = document.getElementById(
                    'natusi-tour-spotlight'
                );
                const card = document.getElementById('natusi-tour-card');
                const title = document.getElementById('natusi-tour-title');
                const description = document.getElementById(
                    'natusi-tour-description'
                );
                const progress = document.getElementById(
                    'natusi-tour-progress'
                );
                const dots = document.getElementById('natusi-tour-dots');
                const previousButton = document.getElementById(
                    'natusi-tour-prev'
                );
                const nextButton = document.getElementById(
                    'natusi-tour-next'
                );
                const skipButton = document.getElementById(
                    'natusi-tour-skip'
                );
                const closeButton = document.getElementById(
                    'natusi-tour-close'
                );

                let currentTarget = null;
                let currentIndex = 0;
                let actionLocked = false;
                let repositionFrame = null;

                const sidebarNav = () => document.querySelector(
                    'aside[aria-label="Navigasi portal"] nav'
                );

                const rememberSidebarPosition = () => {
                    const nav = sidebarNav();

                    if (!nav) {
                        return;
                    }

                    sessionStorage.setItem(
                        storage.sidebarScroll,
                        String(nav.scrollTop)
                    );
                };

                const restoreSidebarPosition = () => {
                    const nav = sidebarNav();

                    if (!nav) {
                        sessionStorage.removeItem(
                            storage.sidebarScroll
                        );
                        return;
                    }

                    const stored = Number(
                        sessionStorage.getItem(
                            storage.sidebarScroll
                        ) ?? 0
                    );

                    nav.scrollTop = Number.isFinite(stored)
                        ? stored
                        : 0;

                    sessionStorage.removeItem(
                        storage.sidebarScroll
                    );
                };

                const showRoot = () => {
                    root.classList.remove('hidden');
                    document.documentElement.classList.add(
                        'natusi-tour-active'
                    );
                };

                const hideRoot = () => {
                    root.classList.add('hidden');
                    document.documentElement.classList.remove(
                        'natusi-tour-active'
                    );
                };

                const renderDots = (index) => {
                    dots.innerHTML = '';

                    steps.forEach((step, stepIndex) => {
                        const dot = document.createElement('span');

                        dot.className = [
                            'h-1.5 rounded-full transition-all duration-200',
                            stepIndex === index
                                ? 'w-7 bg-sky-600'
                                : 'w-1.5 bg-slate-200',
                        ].join(' ');

                        dots.appendChild(dot);
                    });
                };

                const calculateCardPosition = (rect) => {
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    const cardWidth = Math.min(
                        390,
                        viewportWidth - 24
                    );
                    const gap = 18;
                    const margin = 12;

                    let left = rect.right + gap;
                    let top = rect.top;

                    if (
                        left + cardWidth
                        > viewportWidth - margin
                    ) {
                        left = rect.left;
                        top = rect.bottom + gap;
                    }

                    if (
                        left + cardWidth
                        > viewportWidth - margin
                    ) {
                        left = (
                            viewportWidth
                            - cardWidth
                            - margin
                        );
                    }

                    if (left < margin) {
                        left = margin;
                    }

                    const cardHeight = card.offsetHeight || 340;

                    if (
                        top + cardHeight
                        > viewportHeight - margin
                    ) {
                        top = (
                            viewportHeight
                            - cardHeight
                            - margin
                        );
                    }

                    if (top < margin) {
                        top = margin;
                    }

                    card.style.left = `${Math.round(left)}px`;
                    card.style.top = `${Math.round(top)}px`;
                };

                const positionTour = () => {
                    if (
                        !currentTarget
                        || root.classList.contains('hidden')
                    ) {
                        return;
                    }

                    const rect = currentTarget.getBoundingClientRect();
                    const padding = 7;

                    spotlight.style.left = `${Math.round(
                        rect.left - padding
                    )}px`;

                    spotlight.style.top = `${Math.round(
                        rect.top - padding
                    )}px`;

                    spotlight.style.width = `${Math.round(
                        rect.width + (padding * 2)
                    )}px`;

                    spotlight.style.height = `${Math.round(
                        rect.height + (padding * 2)
                    )}px`;

                    calculateCardPosition(rect);
                };

                const findAvailableStep = (
                    startIndex,
                    direction = 1
                ) => {
                    let index = startIndex;

                    while (
                        index >= 0
                        && index < steps.length
                    ) {
                        const target = document.querySelector(
                            steps[index].selector
                        );

                        if (target) {
                            return {
                                index,
                                target,
                            };
                        }

                        index += direction;
                    }

                    return null;
                };

                const renderStep = (
                    requestedIndex,
                    direction = 1
                ) => {
                    const result = findAvailableStep(
                        requestedIndex,
                        direction
                    );

                    if (!result) {
                        finish();
                        return;
                    }

                    currentIndex = result.index;
                    currentTarget = result.target;

                    const step = steps[currentIndex];

                    title.textContent = step.title;
                    description.textContent = step.description;
                    progress.textContent = (
                        `Yuk, kenalan ${currentIndex + 1}/${steps.length} 👋`
                    );

                    previousButton.disabled = currentIndex === 0;

                    nextButton.textContent = (
                        currentIndex === steps.length - 1
                            ? 'Beres 🎉'
                            : 'Lanjut yuk →'
                    );

                    renderDots(currentIndex);

                    positionTour();

                    actionLocked = false;
                };

                const finish = () => {
                    if (actionLocked) {
                        return;
                    }

                    actionLocked = true;

                    hideRoot();
                    restoreSidebarPosition();

                    sessionStorage.removeItem(storage.active);
                    sessionStorage.removeItem(storage.index);

                    currentTarget = null;

                    if (window.innerWidth < 1024) {
                        window.dispatchEvent(
                            new CustomEvent(
                                'natusi-tour-close-sidebar'
                            )
                        );
                    }

                    actionLocked = false;
                };

                const next = () => {
                    if (actionLocked) {
                        return;
                    }

                    if (currentIndex >= steps.length - 1) {
                        finish();
                        return;
                    }

                    actionLocked = true;
                    sessionStorage.setItem(
                        storage.index,
                        String(currentIndex + 1)
                    );

                    renderStep(currentIndex + 1, 1);
                };

                const previous = () => {
                    if (
                        actionLocked
                        || currentIndex <= 0
                    ) {
                        return;
                    }

                    actionLocked = true;
                    sessionStorage.setItem(
                        storage.index,
                        String(currentIndex - 1)
                    );

                    renderStep(currentIndex - 1, -1);
                };

                const startTour = () => {
                    rememberSidebarPosition();

                    sessionStorage.setItem(storage.active, '1');
                    sessionStorage.setItem(storage.index, '0');

                    currentIndex = 0;
                    actionLocked = false;

                    if (window.innerWidth < 1024) {
                        window.dispatchEvent(
                            new CustomEvent(
                                'natusi-tour-open-sidebar'
                            )
                        );

                        window.requestAnimationFrame(() => {
                            window.requestAnimationFrame(() => {
                                showRoot();
                                renderStep(0, 1);
                            });
                        });

                        return;
                    }

                    showRoot();
                    renderStep(0, 1);
                };

                window.NatusiTour = {
                    start: startTour,
                    stop: finish,
                    next,
                    previous,
                };

                nextButton.addEventListener('click', next);
                previousButton.addEventListener('click', previous);
                skipButton.addEventListener('click', finish);
                closeButton.addEventListener('click', finish);

                blocker.addEventListener('click', () => {});

                document.addEventListener('keydown', (event) => {
                    if (
                        root.classList.contains('hidden')
                    ) {
                        return;
                    }

                    if (
                        event.key === 'Enter'
                        || event.key === 'ArrowRight'
                    ) {
                        event.preventDefault();
                        next();
                        return;
                    }

                    if (event.key === 'ArrowLeft') {
                        event.preventDefault();
                        previous();
                        return;
                    }

                    if (event.key === 'Escape') {
                        event.preventDefault();
                        finish();
                    }
                });

                window.addEventListener('resize', () => {
                    if (repositionFrame) {
                        window.cancelAnimationFrame(
                            repositionFrame
                        );
                    }

                    repositionFrame = window.requestAnimationFrame(
                        positionTour
                    );
                });

                window.addEventListener(
                    'scroll',
                    positionTour,
                    true
                );

                sessionStorage.removeItem(storage.active);
                sessionStorage.removeItem(storage.index);
            })();
        </script>
    @endif
@endauth