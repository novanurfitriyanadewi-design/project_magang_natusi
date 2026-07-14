<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Magang') | CV Natusi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        /*
         * Menjaga layar tetap gelap ketika guided tour
         * sedang berpindah halaman, sehingga tidak berkedip.
         */
        html.natusi-tour-loading::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 2147482999;
            background: rgba(2, 6, 23, 0.78);
            pointer-events: none;
        }
    </style>


    @stack('styles')
</head>

<body
    class="
        h-screen overflow-hidden
        bg-gradient-to-br
        from-slate-50 via-blue-50/70 to-cyan-50/50
        font-['Inter'] text-slate-900 antialiased
    "
>
    <div
        class="relative h-screen overflow-hidden"
        x-data="{
            sidebarOpen: false,
            profileOpen: false,
            query: '',

            matches(text) {
                return String(text ?? '')
                    .toLowerCase()
                    .includes(this.query.toLowerCase());
            },
        }"
        @keydown.escape.window="
            sidebarOpen = false;
            profileOpen = false;
        "
        @natusi-tour-open-sidebar.window="sidebarOpen = true"
        @natusi-tour-close-sidebar.window="sidebarOpen = false"
    >
        <div
            class="
                pointer-events-none fixed
                -right-28 top-20 h-80 w-80
                rounded-full bg-sky-200/35 blur-3xl
            "
        ></div>

        <div
            class="
                pointer-events-none fixed
                bottom-8 left-52 h-72 w-72
                rounded-full bg-indigo-200/25 blur-3xl
            "
        ></div>

        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="
                fixed inset-0 z-40
                bg-slate-950/45 backdrop-blur-sm
                lg:hidden
            "
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        @include('partials.sidebar')
        @include('partials.header')

        <div class="relative z-10 h-screen lg:pl-[245px]">
            <div
                class="
                    h-screen overflow-y-auto pt-[72px]
                    [scrollbar-gutter:stable]
                "
            >
                <main
                    class="
                        min-h-[calc(100vh-72px-49px)]
                        px-4 py-5 sm:px-6
                        lg:px-7 lg:py-6
                    "
                >
                    @if (session('success'))
                        <div
                            role="status"
                            class="
                                mb-5 rounded-2xl
                                border border-emerald-200/80
                                bg-gradient-to-r
                                from-emerald-50 to-teal-50
                                px-4 py-3 text-sm font-medium
                                text-emerald-700 shadow-sm
                            "
                        >
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div
                            role="alert"
                            class="
                                mb-5 rounded-2xl
                                border border-rose-200/80
                                bg-gradient-to-r
                                from-rose-50 to-orange-50
                                px-4 py-3 text-sm font-medium
                                text-rose-700 shadow-sm
                            "
                        >
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </main>

                <footer
                    class="
                        border-t border-slate-200/70
                        bg-white/55 px-6 py-4 text-center
                        text-[10px] tracking-wide text-slate-500
                        backdrop-blur-xl
                    "
                >
                    © {{ date('Y') }} CV Natusi Internship Portal.
                    Seluruh hak cipta dilindungi.
                </footer>
            </div>
        </div>
    </div>

    @include('partials.delete-confirmation')
    @include('partials.support-tour')

    @stack('scripts')
</body>
</html>
