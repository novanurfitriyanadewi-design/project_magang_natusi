<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Magang') | CV Natusi</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        [x-cloak]{
            display:none !important;
        }

        html{
            font-family:'Inter',sans-serif;
        }

        body{
            margin:0;
            min-height:100vh;
            background:#f8fafc;
            color:#0f172a;
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

<body class="bg-slate-50">

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
            class="pt-[72px] min-h-screen bg-slate-50 px-6 py-6"
        >

            @if(session('success'))

                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">

                    {{ session('success') }}

                </div>

            @endif

            @if(session('error'))

                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">

                    {{ session('error') }}

                </div>

            @endif

            @yield('content')

        </main>

        <footer class="border-t bg-white py-4 text-center text-xs text-slate-500">

            © {{ date('Y') }} CV Natusi Internship Portal

        </footer>

    </div>

</div>

@include('partials.delete-confirmation')
@include('partials.support-tour')

@stack('scripts')

</body>
</html>