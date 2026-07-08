<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login | CV NATUSI Internship Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .form-input-focus:focus-within {
            border-color: #006191;
            box-shadow: 0 0 0 1px #006191;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#006191",
                        "on-primary": "#ffffff",
                        secondary: "#bb0014",
                        background: "#f8f9ff",
                        "on-surface": "#0b1c30",
                        "on-surface-variant": "#3f4850",
                        "surface-container-lowest": "#ffffff",
                        outline: "#6f7881",
                        "outline-variant": "#bec7d2",
                    },
                    borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem" },
                    fontFamily: {
                        "headline-lg": ["Manrope"],
                        "headline-md": ["Manrope"],
                        "label-bold": ["Inter"],
                        "body-md": ["Inter"],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background text-on-surface font-body-md min-h-screen flex flex-col">

    <header class="w-full top-0 sticky z-50 bg-white flex items-center justify-between px-5 h-14">
        <div class="flex items-center gap-2">
            <img class="w-8 h-8 object-contain" src="{{ asset('images/logo.jpeg') }}" alt="Logo CV Natusi">
            <h1 class="font-headline-md text-lg text-primary tracking-tight">CV NATUSI</h1>
        </div>
    </header>

    <main class="flex-grow flex flex-col items-center justify-center px-5 py-6">
        <div class="w-full max-w-md bg-white border border-outline-variant rounded-lg overflow-hidden shadow-sm">
            <div class="h-1 w-full bg-primary"></div>
            <div class="p-6 flex flex-col items-center">

                <div class="mb-4">
                    <img alt="CV Natusi Logo" class="w-16 h-16 object-contain"
                         src="{{ asset('images/logo.jpeg') }}">
                </div>

                <h2 class="text-2xl font-bold text-on-surface text-center mb-1">Internship Portal</h2>
                <p class="text-on-surface-variant text-center mb-6">Silakan masuk ke akun Anda</p>

                {{-- Notifikasi error umum --}}
                @if ($errors->any())
                    <div class="w-full mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="w-full flex flex-col gap-4" method="POST" action="{{ route('login') }}">
                @csrf

                    {{-- Email --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase">Email / Username</label>
                        <div class="flex items-center gap-3 px-4 py-3 border border-outline-variant rounded bg-surface-container-lowest form-input-focus transition-all">
                            <span class="material-symbols-outlined text-outline">person</span>
                            <input class="bg-transparent border-none outline-none w-full text-sm focus:ring-0 p-0"
                                   name="email" type="text" value="{{ old('email') }}"
                                   placeholder="name@company.com" required autofocus>
                        </div>
                        @error('email')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase">Kata Sandi</label>
                        <div class="flex items-center gap-3 px-4 py-3 border border-outline-variant rounded bg-surface-container-lowest form-input-focus transition-all">
                            <span class="material-symbols-outlined text-outline">lock</span>
                            <input class="bg-transparent border-none outline-none w-full text-sm focus:ring-0 p-0"
                                   id="password-input" name="password" type="password"
                                   placeholder="Masukkan kata sandi" required>
                            <button class="flex items-center justify-center text-outline hover:text-primary transition-colors"
                                    onclick="togglePassword()" type="button">
                                <span class="material-symbols-outlined" id="password-toggle-icon">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Helpers --}}
                    <div class="flex items-center justify-between mt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary"
                                   type="checkbox" name="remember">
                            <span class="text-xs font-semibold text-on-surface-variant">Ingat Saya</span>
                        </label>
                        <a class="text-xs font-semibold text-primary hover:underline" href="#">Lupa Kata Sandi?</a>
                    </div>

                    <button class="mt-2 w-full bg-primary text-white py-3 px-4 rounded font-semibold uppercase tracking-widest hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2"
                            type="submit">
                        Masuk
                        <span class="material-symbols-outlined">login</span>
                    </button>

                    <div class="flex items-center gap-2 my-2">
                        <div class="flex-grow h-[1px] bg-outline-variant"></div>
                        <span class="text-[11px] text-outline uppercase">Atau</span>
                        <div class="flex-grow h-[1px] bg-outline-variant"></div>
                    </div>

                    <p class="text-center text-sm text-on-surface-variant">
                        Belum punya akun?
                        <a class="text-secondary font-semibold hover:underline" href="{{ route('register.pelamar') }}">Ajukan magang</a>
                        atau
                        <a class="text-secondary font-semibold hover:underline" href="{{ route('register.karyawan') }}">Daftar sebagai karyawan</a>
                    </p>
                </form>
            </div>
        </div>
    </main>

    <footer class="w-full py-4 px-5 flex flex-col md:flex-row items-center justify-between gap-2 border-t border-outline-variant bg-white">
        <div class="flex items-center gap-4">
            <span class="text-xs font-semibold">CV NATUSI</span>
            <span class="text-[11px] text-on-surface-variant">© 2024 CV Natusi Internship Portal</span>
        </div>
    </footer>

    <script>
        function togglePassword() {
            const input = document.getElementById('password-input');
            const icon = document.getElementById('password-toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerText = 'visibility_off';
            } else {
                input.type = 'password';
                icon.innerText = 'visibility';
            }
        }
    </script>
</body>
</html>