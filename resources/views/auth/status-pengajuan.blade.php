<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $user = auth()->user();
        $isEmployee = auth()->check() && (
            ($user->role ?? '') === 'karyawan' ||
            ($user->role ?? '') === 'pelamar_karyawan'
        );
    @endphp

    <title>Status Pengajuan {{ $isEmployee ? 'Karyawan' : 'Magang' }} | CV Natusi Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="min-h-screen bg-[#f6f8ff] font-['Inter'] text-slate-900 antialiased">
@php
    $permintaan = $permintaan ?? null;
    $status = strtolower($permintaan->status ?? 'menunggu');
    $notifications = $notifications ?? collect();
    $unreadNotificationCount = $unreadNotificationCount ?? 0;
    $isAuthenticated = auth()->check();

    // Deteksi dari TIPE MODEL $permintaan, bukan role user saja
    $isEmployee = $isEmployee
        || $permintaan instanceof \App\Models\PermintaanLamaran
        || session('register_role') === 'karyawan'
        || ($permintaan->role ?? '') === 'karyawan';

    $jadwalFormatted = null;
    if (!empty($permintaan?->jadwal_interview)) {
        try {
            $jadwalFormatted = \Illuminate\Support\Carbon::parse($permintaan->jadwal_interview)->translatedFormat('d M Y, H:i');
        } catch (\Exception $e) {
            $jadwalFormatted = $permintaan->jadwal_interview;
        }
    }

    // Warna badge berdasarkan status
    $badgeColors = [
        'menunggu' => 'bg-amber-100 text-amber-800 border-amber-200',
        'interview' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
        'perlu_revisi' => 'bg-orange-100 text-orange-800 border-orange-200',
    ];
    $badgeColor = $badgeColors[$status] ?? 'bg-slate-100 text-slate-700 border-slate-200';

    // Label badge
    $badgeLabels = [
        'menunggu' => 'MENUNGGU',
        'interview' => 'INTERVIEW',
        'disetujui' => 'DISETUJUI',
        'ditolak' => 'DITOLAK',
        'perlu_revisi' => 'PERLU REVISI',
    ];
    $badgeLabel = $badgeLabels[$status] ?? 'STATUS';

    $statusMeta = match ($status) {
        'interview' => [
            'title' => 'DIUNDANG INTERVIEW',
            'icon' => '🎤',
            'border' => 'border-l-indigo-500',
            'icon_bg' => 'bg-indigo-100 text-indigo-700',
            'information_title' => 'Anda Diundang untuk Interview',
            'information' => $jadwalFormatted
                ? 'Silakan datang ke kantor pada ' . $jadwalFormatted . ' WIB di ' . ($permintaan->lokasi_interview ?? 'kantor CV Natusi') . '. Mohon datang tepat waktu dan membawa berkas pendukung.'
                : 'Tim HRD CV Natusi akan segera menghubungi Anda untuk menentukan jadwal interview.',
        ],
        'disetujui' => [
            'title' => 'PENGAJUAN DISETUJUI',
            'icon' => '✓',
            'border' => 'border-l-emerald-500',
            'icon_bg' => 'bg-emerald-100 text-emerald-700',
            'information_title' => $isEmployee ? 'Selamat, Lamaran Anda Diterima' : 'Selamat, Anda Diterima',
            'information' => $isEmployee 
                ? 'Akun karyawan Anda telah aktif/dibuat. Silakan periksa notifikasi lonceng atau kartu akun di bawah untuk memperoleh kredensial masuk portal kerja.' 
                : 'Akun peserta magang telah dibuat otomatis. Lihat notifikasi lonceng atau kartu akun di bawah untuk memperoleh username dan password awal.',
        ],
        'ditolak' => [
            'title' => 'PENGAJUAN BELUM DISETUJUI',
            'icon' => '!',
            'border' => 'border-l-rose-500',
            'icon_bg' => 'bg-rose-100 text-rose-700',
            'information_title' => 'Pengajuan Belum Dapat Disetujui',
            'information' => $permintaan->alasan_penolakan ?: 'Silakan hubungi Tim HRD CV Natusi apabila memerlukan informasi lebih lanjut mengenai hasil evaluasi lamaran Anda.',
        ],
        'perlu_revisi' => [
            'title' => 'PERLU REVISI BERKAS',
            'icon' => '!',
            'border' => 'border-l-orange-500',
            'icon_bg' => 'bg-orange-100 text-orange-700',
            'information_title' => 'Admin Meminta Revisi',
            'information' => $permintaan->catatan_revisi ?: 'Silakan unggah berkas yang diminta oleh Admin.',
        ],
        default => [
            'title' => 'MENUNGGU KONFIRMASI',
            'icon' => '◷',
            'border' => 'border-l-amber-500',
            'icon_bg' => 'bg-amber-100 text-amber-700',
            'information_title' => 'Berkas Sedang Dievaluasi',
            'information' => $isEmployee 
                ? 'Berkas lamaran kerja Anda telah diterima. Tim HRD CV Natusi sedang melakukan verifikasi dan seleksi kualifikasi.' 
                : 'Data pengajuan telah diterima. Admin akan memeriksa informasi yang dikirim sebelum memberikan keputusan.',
        ],
    };
@endphp

<div class="flex min-h-screen flex-col">
    {{-- Header --}}
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

                @if(!$permintaan)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center text-amber-800">
                        <p class="font-bold">Data Pengajuan Tidak Ditemukan</p>
                        <p class="text-xs mt-1">Anda belum mengajukan pendaftaran atau data sedang diproses.</p>
                    </div>
                @else
                    {{-- Card Status Utama --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 border-l-4 {{ $statusMeta['border'] }} bg-gradient-to-br from-white to-slate-50 shadow-[0_12px_32px_rgba(15,23,42,0.06)]">
                        <div class="flex flex-col gap-4 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl text-2xl font-black {{ $statusMeta['icon_bg'] }}">{{ $statusMeta['icon'] }}</span>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                                        Status Pengajuan {{ $isEmployee ? 'Karyawan' : 'Magang' }}
                                    </p>
                                    <div class="mt-1 flex flex-wrap items-center gap-3">
                                        <h1 class="text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">{{ $statusMeta['title'] }}</h1>
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[10px] font-extrabold {{ $badgeColor }}">{{ $badgeLabel }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-400">ID #{{ str_pad($permintaan->id_permintaan ?? $permintaan->id ?? 0, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </section>

                    {{-- Banner Informasi --}}
                    <section class="rounded-2xl border border-sky-200 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-5 shadow-sm">
                        <div class="flex items-start gap-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sky-700 font-black text-white">i</span>
                            <div>
                                <h2 class="text-base font-extrabold text-sky-800">{{ $statusMeta['information_title'] }}</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $statusMeta['information'] }}</p>
                            </div>
                        </div>
                    </section>

                    {{-- Kartu Jadwal Interview --}}
                    @if($status === 'interview' && !empty($permintaan->jadwal_interview))
                        <section class="overflow-hidden rounded-2xl border border-indigo-200 bg-gradient-to-br from-white to-indigo-50/60 shadow-[0_16px_38px_rgba(79,70,229,0.08)]">
                            <header class="border-b border-indigo-100 bg-gradient-to-r from-indigo-50 to-violet-50 px-6 py-5">
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-indigo-700">Jadwal Interview</p>
                                <h2 class="mt-1 text-lg font-extrabold text-slate-950">Catat waktu dan lokasi berikut</h2>
                            </header>

                            <div class="grid gap-4 px-6 py-6 sm:grid-cols-2">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Tanggal &amp; Jam</p>
                                    <p class="mt-2 text-lg font-black text-slate-900">{{ $jadwalFormatted }} WIB</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Lokasi</p>
                                    <p class="mt-2 text-lg font-black text-slate-900">{{ $permintaan->lokasi_interview ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="border-t border-indigo-100 bg-indigo-50 px-6 py-4 text-xs leading-5 text-indigo-800">
                                Mohon datang 15 menit lebih awal dan membawa dokumen pendukung (KTP, CV, portofolio jika ada).
                            </div>
                        </section>
                    @endif

                    @if($status === 'perlu_revisi' && !$isEmployee)
                        <form method="POST" action="{{ route('pengajuan.revisi.upload', $permintaan) }}" enctype="multipart/form-data" class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                            @csrf
                            <p class="text-sm font-bold text-amber-900">Unggah Berkas Revisi</p>
                            <input name="jenis_berkas" required placeholder="Jenis berkas, mis. surat pengantar" class="mt-3 w-full rounded-lg border-amber-200 text-sm" />
                            <input type="file" name="berkas" required accept=".pdf,.jpg,.jpeg,.png" class="mt-3 block w-full text-sm" />
                            @error('berkas')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                            <button class="mt-3 rounded-lg bg-amber-600 px-4 py-2 text-sm font-bold text-white">Kirim Berkas Baru</button>
                        </form>
                    @endif

                    {{-- Kartu Kredensial --}}
                    @if($status === 'disetujui')
                        @php
                            $usernameAkun = $permintaan->username_karyawan ?? $permintaan->username_peserta ?? $permintaan->email ?? null;
                            $passwordAkun = $permintaan->password_karyawan ?? $permintaan->password_awal ?? null;
                        @endphp

                        <section class="overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50/60 shadow-[0_16px_38px_rgba(16,185,129,0.08)]">
                            <header class="border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-5">
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-emerald-700">
                                    Akun {{ $isEmployee ? 'Karyawan' : 'Peserta Magang' }} Baru
                                </p>
                                <h2 class="mt-1 text-lg font-extrabold text-slate-950">Simpan kredensial berikut untuk login</h2>
                            </header>

                            <div class="grid gap-4 px-6 py-6 sm:grid-cols-2">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Username / Email Login</p>
                                    <p class="mt-2 break-all font-mono text-lg font-black text-slate-900">{{ $usernameAkun ?? '-' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Password Baru</p>
                                    <p class="mt-2 break-all font-mono text-lg font-black text-emerald-700 bg-emerald-100 px-3 py-1 rounded-lg inline-block">
                                        {{ $passwordAkun ?? 'Password telah diset' }}
                                    </p>
                                </div>
                            </div>

                            <div class="border-t border-emerald-100 bg-emerald-50 px-6 py-4 flex items-center justify-end">
                                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-extrabold text-white transition hover:bg-emerald-700 shadow-sm">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </section>
                    @endif

                    {{-- Detail Berkas --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50/70 shadow-[0_12px_32px_rgba(15,23,42,0.06)]">
                        <header class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/60 px-6 py-5">
                            <h2 class="text-lg font-extrabold text-slate-950">Detail Pengajuan</h2>
                            <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-slate-500 shadow-sm ring-1 ring-slate-200">
                                Dikirim {{ $permintaan->created_at ? \Illuminate\Support\Carbon::parse($permintaan->created_at)->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </header>

                        <div class="grid gap-x-10 gap-y-6 px-6 py-6 sm:grid-cols-2">
                            @php
                                $details = [
                                    ['label' => 'Nama Lengkap', 'value' => $permintaan->nama_pemohon ?? null],
                                    ['label' => 'Alamat Email', 'value' => $permintaan->email ?? null],
                                    [
                                        'label' => $isEmployee ? 'Pendidikan Terakhir' : 'Asal Sekolah / Universitas',
                                        'value' => $isEmployee
                                            ? ($permintaan->pendidikan_terakhir ?? null)
                                            : ($permintaan->nama_sekolah ?? null),
                                    ],
                                    [
                                        'label' => $isEmployee ? 'Posisi Yang Dilamar' : 'Jurusan',
                                        'value' => $isEmployee
                                            ? ($permintaan->posisi ?? null)
                                            : ($permintaan->jurusan ?? null),
                                    ],
                                ];

                                if ($isEmployee) {
                                    $details[] = [
                                        'label' => 'Bidang / Keahlian',
                                        'value' => $permintaan->bidang_keahlian ?? null,
                                    ];
                                } else {
                                    $details[] = [
                                        'label' => 'NIS / NIM',
                                        'value' => $permintaan->no_induk ?? null,
                                    ];
                                }

                                // Nomor Telepon
                                $details[] = ['label' => 'Nomor Telepon / WA', 'value' => $permintaan->no_hp ?? null];

                                // Khusus karyawan: NIK diletakkan persis setelah nomor telepon / WA
                                if ($isEmployee) {
                                    $details[] = [
                                        'label' => 'NIK (Nomor Induk Kependudukan)',
                                        'value' => $permintaan->nik ?? null,
                                    ];
                                }
                            @endphp

                            @foreach($details as $item)
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">{{ $item['label'] }}</p>
                                    <p class="mt-2 break-words text-sm font-semibold text-slate-800">{{ filled($item['value']) ? $item['value'] : '-' }}</p>
                                </div>
                            @endforeach

                            <div class="sm:col-span-2">
                                <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">
                                    {{ $isEmployee ? 'Ringkasan Diri / Pengalaman' : 'Deskripsi / Pertanyaan' }}
                                </p>
                                <div class="mt-2 min-h-20 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm italic leading-6 text-slate-600">
                                    {{ filled($permintaan->pesan ?? null) ? $permintaan->pesan : 'Tidak ada catatan tambahan.' }}
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-5">
                {{-- Card "Cara Memeriksa Status" --}}
                <section class="overflow-hidden rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50/80 to-orange-50/80 px-5 py-5 shadow-[0_12px_32px_rgba(180,83,9,0.12)]">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex rounded-full bg-amber-600 px-3 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-white">Penting</span>
                    </div>
                    <h2 class="mt-2 text-base font-extrabold text-amber-900">Cara Memeriksa Status</h2>
                    <ol class="mt-4 space-y-3 text-xs leading-5 text-slate-700">
                        <li class="flex items-start gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-slate-200 text-xs font-black text-slate-700">1</span>
                            <span><strong>Masuk</strong> menggunakan email dan kata sandi yang dibuat saat pendaftaran.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-slate-200 text-xs font-black text-slate-700">2</span>
                            <span>Sistem langsung membuka halaman status pengajuan ini.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-slate-200 text-xs font-black text-slate-700">3</span>
                            <span>Jika dipanggil interview, <strong>jadwal dan lokasi</strong> akan tampil di halaman ini.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-slate-200 text-xs font-black text-slate-700">4</span>
                            <span>Setelah diterima, lonceng atau halaman ini menampilkan <strong>akun resmi</strong> Anda.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-slate-200 text-xs font-black text-slate-700">5</span>
                            <span>Masuk kembali memakai <strong>username dan password</strong> tersebut.</span>
                        </li>
                    </ol>
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50/70 px-3 py-2 text-center text-[11px] font-semibold text-amber-800">
                        ⚡ Periksa secara berkala – status diperbarui setiap kali halaman dimuat ulang.
                    </div>
                </section>

                {{-- Card "Butuh Bantuan?" --}}
                <section class="overflow-hidden rounded-2xl border-2 border-sky-200 bg-gradient-to-br from-sky-50 to-blue-100/60 px-5 py-5 shadow-[0_12px_32px_rgba(2,132,199,0.15)]">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-extrabold text-sky-800">Butuh Bantuan?</h2>
                    </div>
                    <p class="mt-3 text-xs leading-5 text-slate-600">
                        Jika Anda mengalami kendala teknis atau memiliki pertanyaan, hubungi tim HRD kami.
                    </p>

                    <div class="mt-4 space-y-2">
                        {{-- Email --}}
                        <div class="flex items-center gap-2 rounded-lg border border-sky-100 bg-white/80 px-3 py-2 text-xs font-semibold text-slate-700">
                            <span class="text-sky-600">📧</span>
                            <a href="mailto:{{ config('mail.from.address') }}" class="hover:text-sky-700 hover:underline">
                                {{ config('mail.from.address', 'hrd@natusi.co.id') }}
                            </a>
                        </div>

                        {{-- Telepon / WhatsApp --}}
                        <div class="flex items-center gap-2 rounded-lg border border-sky-100 bg-white/80 px-3 py-2 text-xs font-semibold text-slate-700">
                            <span class="text-emerald-600">💬</span>
                            <a href="https://wa.me/6281234567890" target="_blank" class="hover:text-emerald-700 hover:underline">
                                +62 812-3456-7890 (HRD)
                            </a>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-slate-200 bg-white py-6">
        <div class="mx-auto max-w-7xl px-4 text-center text-xs text-slate-500 sm:px-6 lg:px-8">
            &copy; {{ date('Y') }} CV Natusi. All rights reserved.
        </div>
    </footer>
</div>
</body>
</html>