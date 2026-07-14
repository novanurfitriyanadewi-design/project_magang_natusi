@auth
    <div
        x-data="{
            open: false,
            action: '',
            title: 'Hapus Data?',
            itemName: '',
            description: '',
            confirmText: 'Ya, Hapus',

            showDeleteCard(detail = {}) {
                this.action = detail.action ?? '';
                this.title = detail.title ?? 'Hapus Data?';
                this.itemName = detail.name ?? '';
                this.description = detail.description
                    ?? 'Data yang dipilih akan dihapus dari sistem.';
                this.confirmText = detail.confirmText ?? 'Ya, Hapus';
                this.open = true;

                this.$nextTick(() => {
                    this.$refs.cancelButton?.focus();
                });
            },

            closeDeleteCard() {
                this.open = false;
            },
        }"
        @open-delete-confirm.window="showDeleteCard($event.detail)"
        @keydown.escape.window="closeDeleteCard()"
        x-effect="
            document.body.classList.toggle(
                'overflow-hidden',
                open
            )
        "
    >
        <template x-teleport="body">
            <div
                x-cloak
                x-show="open"
                class="fixed inset-0"
                style="z-index: 2147483600;"
                role="dialog"
                aria-modal="true"
                aria-labelledby="delete-confirmation-title"
            >
                <div
                    x-show="open"
                    x-transition.opacity.duration.150ms
                    class="
                        absolute inset-0
                        bg-slate-950/65 backdrop-blur-[2px]
                    "
                    @click="closeDeleteCard()"
                    aria-hidden="true"
                ></div>

                <div
                    class="
                        relative flex min-h-full items-center
                        justify-center px-4 py-6
                    "
                >
                    <section
                        x-show="open"
                        x-transition:enter="
                            transition ease-out duration-180
                        "
                        x-transition:enter-start="
                            opacity-0 translate-y-3 scale-95
                        "
                        x-transition:enter-end="
                            opacity-100 translate-y-0 scale-100
                        "
                        x-transition:leave="
                            transition ease-in duration-120
                        "
                        x-transition:leave-start="
                            opacity-100 translate-y-0 scale-100
                        "
                        x-transition:leave-end="
                            opacity-0 translate-y-2 scale-95
                        "
                        @click.stop
                        class="
                            relative w-full max-w-md
                            overflow-hidden rounded-[28px]
                            border border-rose-100
                            bg-white
                            shadow-[0_30px_90px_rgba(15,23,42,0.38)]
                        "
                    >
                        <header
                            class="
                                relative overflow-hidden
                                bg-gradient-to-br
                                from-rose-50 via-white to-orange-50
                                px-6 pb-5 pt-6
                            "
                        >
                            <div
                                class="
                                    pointer-events-none absolute
                                    -right-12 -top-14 h-40 w-40
                                    rounded-full bg-rose-200/55 blur-3xl
                                "
                            ></div>

                            <div
                                class="
                                    pointer-events-none absolute
                                    -bottom-16 left-16 h-32 w-32
                                    rounded-full bg-orange-200/45 blur-3xl
                                "
                            ></div>

                            <button
                                type="button"
                                class="
                                    absolute right-4 top-4
                                    grid h-9 w-9 place-items-center
                                    rounded-xl bg-white/85
                                    text-slate-400 shadow-sm
                                    ring-1 ring-slate-100 transition
                                    hover:bg-rose-50 hover:text-rose-600
                                "
                                @click="closeDeleteCard()"
                                aria-label="Tutup konfirmasi hapus"
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

                            <div
                                class="
                                    relative flex flex-col
                                    items-center text-center
                                "
                            >
                                <span
                                    class="
                                        grid h-16 w-16 place-items-center
                                        rounded-[22px]
                                        bg-gradient-to-br
                                        from-rose-500 to-red-600
                                        text-white shadow-lg
                                        shadow-rose-200/80
                                        ring-8 ring-white
                                    "
                                >
                                    <svg
                                        class="h-8 w-8"
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
                                </span>

                                <span
                                    class="
                                        mt-5 inline-flex rounded-full
                                        bg-rose-100 px-3 py-1
                                        text-[10px] font-extrabold
                                        uppercase tracking-[0.12em]
                                        text-rose-700
                                    "
                                >
                                    Konfirmasi dulu
                                </span>

                                <h2
                                    id="delete-confirmation-title"
                                    class="
                                        mt-2 text-2xl font-extrabold
                                        tracking-tight text-slate-950
                                    "
                                    x-text="title"
                                ></h2>

                                <p
                                    class="
                                        mt-2 max-w-sm text-sm
                                        leading-6 text-slate-600
                                    "
                                    x-text="description"
                                ></p>
                            </div>
                        </header>

                        <div class="px-6 pb-5">
                            <div
                                x-show="itemName"
                                class="
                                    flex items-center gap-3
                                    rounded-2xl border border-slate-100
                                    bg-slate-50 px-4 py-3
                                "
                            >
                                <span
                                    class="
                                        grid h-10 w-10 shrink-0
                                        place-items-center rounded-xl
                                        bg-white text-rose-600 shadow-sm
                                        ring-1 ring-rose-100
                                    "
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M7 7h10M7 12h10M7 17h6"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </span>

                                <div class="min-w-0 text-left">
                                    <p
                                        class="
                                            text-[10px] font-bold
                                            uppercase tracking-[0.12em]
                                            text-slate-400
                                        "
                                    >
                                        Data yang akan dihapus
                                    </p>

                                    <p
                                        class="
                                            mt-1 truncate text-sm
                                            font-extrabold text-slate-800
                                        "
                                        x-text="itemName"
                                    ></p>
                                </div>
                            </div>

                            <div
                                class="
                                    mt-3 flex items-start gap-2.5
                                    rounded-2xl border border-amber-200
                                    bg-amber-50 px-4 py-3
                                    text-xs leading-5 text-amber-800
                                "
                            >
                                <svg
                                    class="mt-0.5 h-4 w-4 shrink-0"
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
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>

                                <span>
                                    Setelah dihapus, data ini
                                    <strong>nggak bisa dikembalikan lagi.</strong>
                                </span>
                            </div>
                        </div>

                        <footer
                            class="
                                flex flex-col-reverse gap-2
                                border-t border-slate-100
                                bg-slate-50/80 px-6 py-4
                                sm:flex-row sm:justify-end
                            "
                        >
                            <button
                                x-ref="cancelButton"
                                type="button"
                                class="
                                    rounded-xl border border-slate-200
                                    bg-white px-5 py-2.5
                                    text-sm font-bold text-slate-600
                                    shadow-sm transition
                                    hover:bg-slate-100
                                "
                                @click="closeDeleteCard()"
                            >
                                Batal
                            </button>

                            <form
                                method="POST"
                                :action="action"
                                class="contents"
                                @submit="open = false"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="
                                        inline-flex items-center
                                        justify-center gap-2
                                        rounded-xl
                                        bg-gradient-to-r
                                        from-rose-600 to-red-600
                                        px-5 py-2.5 text-sm
                                        font-extrabold text-white
                                        shadow-[0_10px_24px_rgba(225,29,72,0.26)]
                                        transition
                                        hover:-translate-y-0.5
                                        hover:from-rose-700
                                        hover:to-red-700
                                    "
                                >
                                    <svg
                                        class="h-4 w-4"
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

                                    <span x-text="confirmText"></span>
                                </button>
                            </form>
                        </footer>
                    </section>
                </div>
            </div>
        </template>
    </div>
@endauth
