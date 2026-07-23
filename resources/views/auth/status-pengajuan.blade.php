<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Status Pengajuan Magang | CV Natusi Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="min-h-screen bg-[#f6f8ff] font-['Inter'] text-slate-900 antialiased">
@php
    $status = strtolower($permintaan->status ?? 'menunggu');
    $notifications = $notifications ?? collect();
    $unreadNotificationCount = $unreadNotificationCount ?? 0;
    $isAuthenticated = auth()->check();

    $statusMeta = match ($status) {
        'disetujui' => [
            'title' => 'PENGAJUAN DISETUJUI',
            'badge' => 'DISETUJUI',
            'icon' => '✓',
            'border' => 'border-l-emerald-500',
            'icon_bg' => 'bg-emerald-100 text-emerald-700',
            'badge_class' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'information_title' => 'Selamat, Anda Diterima',
            'information' => 'Akun peserta magang telah dibuat otomatis. Lihat notifikasi lonceng atau kartu akun di bawah untuk memperoleh username dan password awal.',
        ],
        'ditolak' => [
            'title' => 'PENGAJUAN BELUM DISETUJUI',
            'badge' => 'DITOLAK',
            'icon' => '!',
            'border' => 'border-l-rose-500',
            'icon_bg' => 'bg-rose-100 text-rose-700',
            'badge_class' => 'border-rose-200 bg-rose-50 text-rose-700',
            'information_title' => 'Pengajuan Belum Dapat Disetujui',
            'information' => 'Silakan hubungi Admin CV Natusi apabila memerlukan informasi lebih lanjut mengenai hasil pengajuan.',
        ],
        default => [
            'title' => 'MENUNGGU KONFIRMASI',
            'badge' => 'PENDING',
            'icon' => '◷',
            'border' => 'border-l-sky-600',
            'icon_bg' => 'bg-sky-100 text-sky-700',
            'badge_class' => 'border-amber-200 bg-amber-50 text-amber-700',
            'information_title' => 'Pengajuan Sedang Diperiksa',
            'information' => 'Data pengajuan telah diterima. Admin akan memeriksa informasi yang dikirim sebelum memberikan keputusan.',
        ],
    };
@endphp

<div class="flex min-h-screen flex-col">
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ $isAuthenticated ? route('pengajuan.status') : route('login') }}" class="flex items-center gap-3">
                <span class="grid h-9 w-9 place-items-center overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo CV Natusi" class="h-8 w-8 object-contain">
                </span>
                <span class="text-sm font-extrabold text-sky-700">CV Natusi Portal</span>
            </a>

            <div class="flex items-center gap-2">
                @if($isAuthenticated)
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            class="relative grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700"
                            aria-label="Notifikasi"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M18 8.5a6 6 0 0 0-12 0v3.8c0 1.5-.5 2.9-1.5 4.2h15a6.9 6.9 0 0 1-1.5-4.2V8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9.8 19a2.4 2.4 0 0 0 4.4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>

                            @if($unreadNotificationCount > 0)
                                <span class="absolute -right-1 -top-1 inline-grid min-h-5 min-w-5 place-items-center rounded-full bg-rose-500 px-1 text-[9px] font-black text-white ring-2 ring-white">
                                    {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                                </span>
                            @endif
                        </button>

                        <div
                            x-cloak
                            x-show="open"
                            x-transition.origin.top.right
                            @click.outside="open = false"
                            class="absolute right-0 top-12 z-50 w-[min(92vw,410px)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_22px_55px_rgba(15,23,42,0.20)]"
                        >
                            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">Notifikasi</p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">{{ $unreadNotificationCount }} belum dibaca</p>
                                </div>

                                @if($unreadNotificationCount > 0)
                                    <form method="POST" action="{{ route('notifikasi.read-all') }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-[11px] font-extrabold text-sky-700 hover:text-sky-900">Baca semua</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-[430px] overflow-y-auto">
                                @forelse($notifications as $notification)
                                    <div class="border-b border-slate-100 px-4 py-3.5 last:border-b-0 {{ $notification->dibaca ? 'bg-white' : 'bg-sky-50/60' }}">
                                        <div class="flex items-start gap-3">
                                            <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $notification->tipe === 'sukses' ? 'bg-emerald-100 text-emerald-700' : ($notification->tipe === 'peringatan' ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-700') }}">
                                                {{ $notification->tipe === 'sukses' ? '✓' : ($notification->tipe === 'peringatan' ? '!' : 'i') }}
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-start justify-between gap-3">
                                                    <p class="text-xs font-extrabold leading-5 text-slate-900">{{ $notification->judul }}</p>
                                                    @if(! $notification->dibaca)
                                                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-500"></span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 break-words text-xs leading-5 text-slate-600">{{ $notification->pesan }}</p>
                                                <div class="mt-2 flex items-center justify-between gap-3">
                                                    <span class="text-[10px] font-semibold text-slate-400">{{ $notification->created_at?->diffForHumans() }}</span>
                                                    @if(! $notification->dibaca)
                                                        <form method="POST" action="{{ route('notifikasi.read', $notification) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-[10px] font-extrabold text-sky-700 hover:text-sky-900">Tandai dibaca</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-6 py-10 text-center">
                                        <p class="text-sm font-extrabold text-slate-700">Belum ada notifikasi</p>
                                        <p class="mt-1 text-xs text-slate-500">Hasil pengajuan akan muncul di sini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('pengajuan.status') }}" class="hidden rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50 sm:inline-flex">
                        Muat Ulang Status
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-extrabold text-white transition hover:bg-slate-800">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-extrabold text-white">Masuk</a>
                @endif
            </div>
        </div>
    </header>

    <main class="flex-1">
        <div class="mx-auto grid max-w-7xl gap-5 px-4 py-7 sm:px-6 lg:grid-cols-[minmax(0,1fr)_330px] lg:px-8">
            <div class="space-y-5">
                @if(session('success'))
                    <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <section class="overflow-hidden rounded-2xl border border-slate-200 border-l-4 {{ $statusMeta['border'] }} bg-white shadow-[0_12px_32px_rgba(15,23,42,0.06)]">
                    <div class="flex flex-col gap-4 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl text-2xl font-black {{ $statusMeta['icon_bg'] }}">{{ $statusMeta['icon'] }}</span>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Status Pengajuan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-3">
                                    <h1 class="text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">{{ $statusMeta['title'] }}</h1>
                                    <span class="inline-flex rounded-full border px-3 py-1 text-[10px] font-extrabold {{ $statusMeta['badge_class'] }}">{{ $statusMeta['badge'] }}</span>
                                </div>
                            </div>
                        </div>

                        <span class="text-xs font-semibold text-slate-400">ID #{{ str_pad($permintaan->id_permintaan, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </section>

                <section class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-5 shadow-sm">
                    <div class="flex items-start gap-4">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sky-700 font-black text-white">i</span>
                        <div>
                            <h2 class="text-base font-extrabold text-sky-800">{{ $statusMeta['information_title'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $statusMeta['information'] }}</p>
                        </div>
                    </div>
                </section>

                @if($status === 'disetujui' && $permintaan->akun_dibuat && filled($permintaan->username_peserta) && filled($permintaan->password_awal))
                    <section class="overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-[0_16px_38px_rgba(16,185,129,0.10)]">
                        <header class="border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-5">
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-emerald-700">Akun Peserta Magang</p>
                            <h2 class="mt-1 text-lg font-extrabold text-slate-950">Simpan username dan password berikut</h2>
                        </header>

                        <div class="grid gap-4 px-6 py-6 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Username</p>
                                <p class="mt-2 break-all font-mono text-lg font-black text-slate-900">{{ $permintaan->username_peserta }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Password Awal</p>
                                <p class="mt-2 break-all font-mono text-lg font-black text-slate-900">{{ $permintaan->password_awal }}</p>
                            </div>
                        </div>

                        <div class="border-t border-emerald-100 bg-emerald-50 px-6 py-4 text-xs leading-5 text-emerald-800">
                            Keluar dari akun pelamar, lalu masuk kembali menggunakan username dan password peserta di atas. Setelah berhasil masuk, segera ubah password awal melalui menu profil.
                        </div>
                    </section>
                @endif

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_12px_32px_rgba(15,23,42,0.06)]">
                    <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-6 py-5">
                        <h2 class="text-lg font-extrabold text-slate-950">Detail Pengajuan</h2>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-500">Dikirim {{ $permintaan->created_at?->translatedFormat('d M Y, H:i') }}</span>
                    </header>

                    <div class="grid gap-x-10 gap-y-6 px-6 py-6 sm:grid-cols-2">
                        @foreach([
                            ['Nama Lengkap', $permintaan->nama_pemohon],
                            ['Alamat Email', $permintaan->email],
                            ['Asal Sekolah / Universitas', $permintaan->nama_sekolah],
                            ['Jurusan', $permintaan->jurusan],
                            ['NIS / NIM', $permintaan->no_induk],
                            ['Nomor Telepon', $permintaan->no_hp],
                        ] as [$label, $value])
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">{{ $label }}</p>
                                <p class="mt-2 break-words text-sm font-semibold text-slate-800">{{ filled($value) ? $value : '-' }}</p>
                            </div>
                        @endforeach

                        <div class="sm:col-span-2">
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Deskripsi / Pertanyaan</p>
                            <div class="mt-2 min-h-20 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm italic leading-6 text-slate-600">
                                {{ filled($permintaan->pesan) ? $permintaan->pesan : 'Tidak ada deskripsi atau pertanyaan tambahan.' }}
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-5">
                <section class="rounded-2xl border border-slate-200 bg-white px-5 py-5 shadow-[0_12px_32px_rgba(15,23,42,0.06)]">
                    <h2 class="text-base font-extrabold text-slate-950">Cara Memeriksa Status</h2>
                    <ol class="mt-4 space-y-3 text-xs leading-5 text-slate-600">
                        <li><strong>1.</strong> Masuk menggunakan email dan kata sandi yang dibuat saat pendaftaran.</li>
                        <li><strong>2.</strong> Sistem langsung membuka halaman status pengajuan ini.</li>
                        <li><strong>3.</strong> Setelah diterima, lonceng menampilkan akun peserta hasil generate.</li>
                        <li><strong>4.</strong> Masuk kembali memakai username dan password peserta.</li>
                    </ol>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white px-5 py-5 shadow-[0_12px_32px_rgba(15,23,42,0.06)]">
                    <h2 class="text-base font-extrabold text-slate-950">Butuh Bantuan?</h2>
                    <p class="mt-3 text-xs leading-5 text-slate-500">Hubungi tim HR apabila Anda memiliki kendala teknis atau pertanyaan mengenai pengajuan.</p>
                    <a href="mailto:{{ config('mail.from.address') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-sky-600 bg-white px-4 py-3 text-xs font-extrabold text-sky-700 transition hover:bg-sky-50">Hubungi HRD</a>
                </section>
            </aside>
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-[10px] text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ now()->year }} <strong>CV Natusi</strong>. Portal Pendaftaran Magang.</p>
            <p>Status diperbarui setiap kali halaman dimuat ulang.</p>
        </div>
    </footer>
</div>
</body>
</html>
