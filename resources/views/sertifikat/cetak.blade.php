@php
    use Illuminate\Support\Str;

    $education = Str::lower((string) ($sertifikat->peserta?->tingkat_pendidikan ?? ''));
    $isSmk = Str::contains($education, 'smk');

    $namaPeserta = $sertifikat->peserta?->user?->nama ?? 'Peserta Magang';
    $nomorSertifikat = $sertifikat->nomor_sertifikat ?? '0001/SERT-MAGANG/CVN/' . now()->format('m/Y');
    $divisiNama = $sertifikat->divisi?->nama_divisi ?? 'Software Development';
    $predikat = Str::lower((string) ($sertifikat->predikat ?? 'Sangat Baik'));
    $ttdName = $sertifikat->penandatangan_nama ?? 'Arif Rakhman Hadi, S.Kom';
    $ttdJabatan = $sertifikat->penandatangan_jabatan ?? 'Direktur CV.Natusi';

    if ($sertifikat->peserta?->tgl_mulai && $sertifikat->peserta?->tgl_selesai) {
        $periode = $sertifikat->peserta->tgl_mulai->translatedFormat('d F Y') . ' s.d ' . $sertifikat->peserta->tgl_selesai->translatedFormat('d F Y');
    } else {
        $periode = '22 Juni s.d 21 Agustus 2026';
    }

    $theme = $isSmk
        ? [
            'primary' => '#14532d',
            'secondary' => '#15803d',
            'gold' => '#b48a2c',
            'line' => '#d8f0df',
            'toolbar' => 'from-green-700 to-emerald-500',
            'dot' => '#bbf7d0',
          ]
        : [
            'primary' => '#00082e',
            'secondary' => '#182b86',
            'gold' => '#b48a2c',
            'line' => '#dce8fb',
            'toolbar' => 'from-blue-900 to-blue-700',
            'dot' => '#dbeafe',
          ];

    $qrText = route('peserta-magang.sertifikat.cetak', $sertifikat);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sertifikat {{ $namaPeserta }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Bebas+Neue&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .font-bebas { font-family: 'Bebas Neue', sans-serif; }
        .cert-container { aspect-ratio: 1.414; max-width: 1200px; margin: 0 auto; }
        @media print {
            body { background: white !important; padding: 0 !important; }
            .print-hide { display: none !important; }
            .cert-container { width: 100% !important; max-width: none !important; }
            main { box-shadow: none !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 px-4 py-8">
    <div class="print-hide mx-auto mb-6 flex w-full max-w-[1200px] items-center justify-between">
        <a href="javascript:history.back()" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow hover:bg-slate-100">← Kembali</a>
        <button onclick="window.print()" class="rounded-xl bg-gradient-to-r {{ $theme['toolbar'] }} px-6 py-2.5 text-sm font-bold text-white shadow-lg">Cetak / PDF</button>
    </div>

    <main class="cert-container relative flex w-full items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-white shadow-2xl">
        {{-- Ornamen dibuat kecil seperti frame: hanya di sudut, tidak masuk area konten. --}}
        <svg class="pointer-events-none absolute inset-0 z-0 h-full w-full" fill="none" viewBox="0 0 1200 848" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M40 0H0V40" fill="none" stroke="{{ $theme['primary'] }}" stroke-width="0.5" opacity="0.025" />
                </pattern>
            </defs>
            <rect width="1200" height="848" fill="url(#grid)" />

            {{-- Sudut kiri atas: referensi sekitar 300–350px, tidak lebih --}}
            <path d="M0 0H300Q150 150 0 300Z" fill="{{ $theme['primary'] }}" opacity="0.94" />
            <path d="M0 350C150 350 350 150 350 0" stroke="{{ $theme['gold'] }}" stroke-width="3" opacity="0.9" />
            <path d="M0 320C120 320 320 120 320 0" stroke="{{ $theme['gold'] }}" stroke-width="1.6" opacity="0.65" />
            <path d="M0 290C100 290 290 100 290 0" stroke="{{ $theme['secondary'] }}" stroke-width="1" opacity="0.28" />

            {{-- Sudut kanan bawah: ukuran sama agar menjadi frame --}}
            <path d="M1200 848H900Q1050 698 1200 548Z" fill="{{ $theme['primary'] }}" opacity="0.94" />
            <path d="M1200 498C1050 498 850 698 850 848" stroke="{{ $theme['gold'] }}" stroke-width="3" opacity="0.9" />
            <path d="M1200 528C1080 528 880 728 880 848" stroke="{{ $theme['gold'] }}" stroke-width="1.6" opacity="0.65" />
            <path d="M1200 558C1100 558 910 748 910 848" stroke="{{ $theme['secondary'] }}" stroke-width="1" opacity="0.28" />

            {{-- Garis lembut hanya di pinggir kanan atas dan kiri bawah --}}
            <g opacity="0.95">
                <path d="M825 0C785 76 795 160 855 213C918 269 1034 261 1200 385" stroke="{{ $theme['line'] }}" stroke-width="3" />
                <path d="M850 0C810 76 820 160 880 213C943 269 1059 261 1200 365" stroke="{{ $theme['line'] }}" stroke-width="3" />
                <path d="M875 0C835 76 845 160 905 213C968 269 1084 261 1200 345" stroke="{{ $theme['line'] }}" stroke-width="3" />
                <path d="M0 740C93 740 164 692 220 638C283 577 356 569 432 635" stroke="{{ $theme['line'] }}" stroke-width="3" />
                <path d="M0 766C104 766 179 713 238 658C301 597 374 589 450 655" stroke="{{ $theme['line'] }}" stroke-width="3" />
                <path d="M0 792C115 792 194 734 256 678C319 617 392 609 468 675" stroke="{{ $theme['line'] }}" stroke-width="3" />
            </g>

            {{-- side accent kecil --}}
            <line x1="0" y1="424" x2="55" y2="424" stroke="{{ $theme['gold'] }}" stroke-width="1" opacity="0.55" />
            <line x1="1145" y1="424" x2="1200" y2="424" stroke="{{ $theme['gold'] }}" stroke-width="1" opacity="0.55" />
        </svg>

        @if ($isSmk)
            {{-- Polkadot hanya di sisi/frame, tidak di tengah --}}
            <div class="pointer-events-none absolute right-8 top-8 z-[1] grid grid-cols-5 gap-2 opacity-60">
                @for ($i = 0; $i < 15; $i++)
                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $theme['dot'] }};"></span>
                @endfor
            </div>
            <div class="pointer-events-none absolute bottom-8 left-8 z-[1] grid grid-cols-5 gap-2 opacity-55">
                @for ($i = 0; $i < 15; $i++)
                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $theme['dot'] }};"></span>
                @endfor
            </div>
        @endif

        {{-- Area konten mengikuti referensi: max 900px dan selalu di tengah. --}}
        <div class="relative z-10 flex w-full max-w-[900px] flex-col items-center px-8 py-10 text-center">
            <div class="mb-5 flex w-full flex-col items-center">
                <img class="mb-2 h-20 w-20 object-contain" src="{{ asset('images/logo.jpeg') }}" alt="Logo CV Natusi" />
                <p class="text-[15px] font-medium" style="color: {{ $theme['primary'] }};">
                    Software House | Hardware Supplier | IT Consultant | Network Installation
                </p>
                <div class="mt-2 h-px w-full bg-slate-300/80"></div>
            </div>

            <div class="mb-5 flex flex-col items-center">
                <h1 class="font-bebas text-6xl font-bold uppercase tracking-[0.20em] md:text-[70px]" style="color: {{ $theme['primary'] }};">SERTIFIKAT</h1>
                <p class="mt-1 text-lg font-medium" style="color: {{ $theme['primary'] }};">Diberikan kepada</p>
            </div>

            <div class="mb-5 flex w-full flex-col items-center">
                <h2 class="font-bebas mb-3 text-5xl font-bold uppercase tracking-[0.08em] md:text-[58px]" style="color: {{ $theme['primary'] }};">{{ $namaPeserta }}</h2>
                <div class="h-px w-full bg-slate-300/90"></div>
            </div>

            <div class="mx-auto mb-7 max-w-3xl">
                <p class="text-lg font-medium leading-[1.65]" style="color: {{ $theme['primary'] }};">
                    Telah melaksanakan Program {{ $isSmk ? 'Magang / Praktik Kerja Lapangan / PRAKERIN' : 'Magang / Praktik Kerja Lapangan' }}
                    di CV. Natusi pada divisi <strong>{{ $divisiNama }}</strong> dengan predikat
                    <strong>{{ $predikat }}</strong> selama periode <strong>{{ $periode }}</strong>.
                </p>
            </div>

            <div class="mt-auto flex w-full flex-col items-center">
                <div class="flex flex-col items-center gap-2">
                    <div id="qr-ttd" data-qr-text="{{ $qrText }}" class="rounded-sm border border-slate-300 bg-white p-1 shadow-sm"></div>
                    <div class="text-center">
                        <p class="mb-1 text-lg font-medium" style="color: {{ $theme['primary'] }};">{{ $ttdName }}</p>
                        <p class="text-sm font-bold uppercase tracking-[0.05em]" style="color: {{ $theme['primary'] }};">{{ $ttdJabatan }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-5 right-6 z-10 text-[9px] font-medium opacity-60" style="color: {{ $theme['primary'] }};">No. {{ $nomorSertifikat }}</div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qr = document.getElementById('qr-ttd');
            if (qr && typeof QRCode !== 'undefined') {
                new QRCode(qr, {
                    text: qr.dataset.qrText,
                    width: 70,
                    height: 70,
                    colorDark: @json($theme['primary']),
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M,
                });
            }
        });
    </script>
</body>
</html>
