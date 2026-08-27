<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Magang Dan Karyawan') | CV Natusi</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    {{-- Asset portal dibuat statis agar Alpine tidak mati saat Vite/manifest berubah --}}
    <link rel="stylesheet" href="{{ asset('static/portal-app.css') }}">
    <link rel="stylesheet" href="{{ asset('static/peserta-ui.css') }}">
    <script defer src="{{ asset('static/alpine.min.js') }}"></script>

    @stack('styles')

    <style>
        [x-cloak]{
            display:none !important;
        }

        html{
            font-family:'Inter',sans-serif;
            -webkit-text-size-adjust:100%;
            text-size-adjust:100%;
        }

        body{
            margin:0;
            min-height:100dvh;
            background:#f8fafc;
            color:#0f172a;
            overflow-x:hidden;
        }

        img, svg, video, canvas, table{
            max-width:100%;
            height:auto;
        }

        .table-responsive{
            width:100%;
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }

        .material-symbols-outlined{
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
        }

        .headline{
            font-family:'Manrope',sans-serif;
        }

        html.natusi-tour-loading::before{
            content:'';
            position:fixed;
            inset:0;
            z-index:99999;
            background:rgba(15,23,42,.7);
            pointer-events:none;
        }
    </style>

</head>

<body class="{{ auth()->check() && auth()->user()->role === 'peserta' ? 'peserta-ui ' : '' }}bg-slate-50">

<div
    x-data="{
        sidebarOpen:false,
        profileOpen:false,
        query:'',

        matches(text){
            return String(text ?? '')
                .toLowerCase()
                .includes(this.query.toLowerCase());
        }
    }"
    @natusi-tour-open-sidebar.window="sidebarOpen = true"
    @natusi-tour-close-sidebar.window="sidebarOpen = false"
    class="relative min-h-screen"
>

    {{-- Overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-40 bg-black/50 lg:hidden"
        @click="sidebarOpen=false"
    ></div>

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Header --}}
    @include('partials.header')

    {{-- Content --}}
    <div class="lg:pl-[245px]">

        <main
            class="min-h-screen bg-slate-50 px-4 pb-6 pt-[72px] sm:px-6 lg:px-8"
        >

            @if(session('success'))

                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 sm:text-base">

                    {{ session('success') }}

                </div>

            @endif

            @if(session('error'))

                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 sm:text-base">

                    {{ session('error') }}

                </div>

            @endif

            @yield('content')

        </main>

        <footer class="border-t bg-white px-4 py-4 text-center text-xs text-slate-500">

            © {{ date('Y') }} CV Natusi Internship Portal

        </footer>

    </div>

</div>

@include('partials.delete-confirmation')
@include('partials.support-tour')

@stack('scripts')

</body>
</html>