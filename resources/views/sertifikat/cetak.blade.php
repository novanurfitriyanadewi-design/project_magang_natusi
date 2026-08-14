<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sertifikat {{ $sertifikat->peserta?->user?->nama ?? 'Peserta' }}</title>
    
    <!-- Google Fonts & Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@700&family=Noto+Serif:ital,wght@0,500;0,600;0,700;1,500&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#00082e',
                        'secondary': '#775a19',
                        'surface': '#f8f9fa',
                        'outline-variant': '#c6c5d1',
                    },
                    fontFamily: {
                        'serif-title': ['Noto Serif', 'serif'],
                        'body-sans': ['Work Sans', 'sans-serif'],
                        'label-caps': ['Hanken Grotesk', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        .cert-container {
            aspect-ratio: 297 / 210;
            max-width: 1100px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-[#0f172a] min-h-screen flex flex-col items-center justify-center py-8 px-4 font-body-sans">

    <!-- Top Toolbar -->
    <div class="w-full max-w-[1100px] mb-6 flex justify-between items-center">
        <a href="javascript:history.back()" class="px-5 py-2.5 bg-white text-slate-800 font-semibold rounded-xl shadow hover:bg-slate-100 transition duration-150 flex items-center gap-2 text-sm">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-6 py-2.5 bg-gradient-to-r from-[#775a19] to-[#b38428] text-white font-semibold rounded-xl shadow-lg hover:opacity-90 transition duration-150 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-base">print</span> Cetak / PDF
        </button>
    </div>

    <!-- Certificate Canvas Layer -->
    <main class="w-full cert-container bg-gradient-to-br from-white via-[#f8f9fa] to-[#edeeef] relative overflow-hidden shadow-2xl rounded-2xl border border-outline-variant/40 flex items-center justify-center">

        <!-- ================= ELEMEN DEKORASI (FLUID WAVE & GOLD ACCENTS) ================= -->
        
        <!-- Background Pattern Dot Overlay -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-5 bg-[radial-gradient(#00082e_1px,transparent_1px)] [background-size:20px_20px]"></div>

        <!-- Watermark Logo di Tengah -->
        <img src="{{ asset('images/logo.jpeg') }}" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 opacity-[0.03] grayscale pointer-events-none z-0" alt="Watermark" />

        <!-- Fluid Wave Top Left (Sudut Kiri Atas) -->
        <svg class="absolute top-0 left-0 w-full h-full pointer-events-none z-0" preserveAspectRatio="none" viewBox="0 0 1200 848" xmlns="http://www.w3.org/2000/svg" style="transform: translate(-3%, -3%);">
            <!-- Navy Fill Multi Layer -->
            <path d="M0 0 L 460 0 C 260 110, 110 260, 0 460 Z" fill="#00082e"></path>
            <!-- Gold Lines & Dashed Accents -->
            <path d="M0 0 L 490 0 C 280 130, 130 280, 0 490 Z" fill="none" stroke="#775a19" stroke-width="4"></path>
            <path d="M0 0 L 530 0 C 300 150, 150 300, 0 530 Z" fill="none" stroke="#775a19" stroke-opacity="0.6" stroke-dasharray="8 5" stroke-width="2"></path>
            <path d="M0 0 L 560 0 C 315 165, 165 315, 0 560 Z" fill="none" stroke="#00082e" stroke-opacity="0.15" stroke-width="1.5"></path>
        </svg>

        <!-- Fluid Wave Bottom Right (Sudut Kanan Bawah) -->
        <svg class="absolute bottom-0 right-0 w-full h-full pointer-events-none z-0" preserveAspectRatio="none" viewBox="0 0 1200 848" xmlns="http://www.w3.org/2000/svg" style="transform: translate(3%, 3%);">
            <!-- Navy Fill Multi Layer -->
            <path d="M1200 848 L 740 848 C 940 738, 1090 588, 1200 388 Z" fill="#00082e"></path>
            <!-- Gold Lines & Dashed Accents -->
            <path d="M1200 848 L 710 848 C 920 718, 1070 568, 1200 358 Z" fill="none" stroke="#775a19" stroke-width="4"></path>
            <path d="M1200 848 L 670 848 C 900 698, 1050 548, 1200 318 Z" fill="none" stroke="#775a19" stroke-opacity="0.6" stroke-dasharray="8 5" stroke-width="2"></path>
            <path d="M1200 848 L 640 848 C 880 680, 1030 530, 1200 290 Z" fill="none" stroke="#00082e" stroke-opacity="0.15" stroke-width="1.5"></path>
        </svg>

        <!-- Frame Inner Border Emas -->
        <div class="absolute inset-6 border border-[#775a19]/30 rounded-lg pointer-events-none z-10"></div>
        <div class="absolute inset-[30px] border border-dashed border-[#00082e]/20 rounded-lg pointer-events-none z-10"></div>

        <!-- Ornamen Sudut Emas (Corner Accent Lines) -->
        <div class="absolute top-[38px] left-[38px] w-8 h-8 border-t-2 border-l-2 border-[#775a19] pointer-events-none z-10"></div>
        <div class="absolute top-[38px] right-[38px] w-8 h-8 border-t-2 border-r-2 border-[#775a19] pointer-events-none z-10"></div>
        <div class="absolute bottom-[38px] left-[38px] w-8 h-8 border-b-2 border-l-2 border-[#775a19] pointer-events-none z-10"></div>
        <div class="absolute bottom-[38px] right-[38px] w-8 h-8 border-b-2 border-r-2 border-[#775a19] pointer-events-none z-10"></div>

        <!-- ================= KONTEN UTAMA (DIPERKETAT DI TENGAH) ================= -->
        <div class="relative z-20 w-full max-w-[650px] flex flex-col items-center text-center py-6 px-4 my-auto">
            
            <!-- Logo & Subtitle -->
            <div class="flex flex-col items-center mb-3 w-full">
                <img class="h-14 w-auto mb-2 object-contain" src="{{ asset('images/logo.jpeg') }}" alt="Logo CV Natusi">
                <p class="font-body-sans text-[11px] md:text-xs text-[#00082e] font-medium tracking-tight max-w-[520px] leading-tight">
                    Software House | Hardware Supplier | IT Consultant | Network Installation
                </p>
                <div class="w-2/3 h-[1px] bg-gradient-to-r from-transparent via-[#775a19]/60 to-transparent my-2"></div>
            </div>

            <!-- Title Section -->
            <div class="flex flex-col items-center mb-2">
                <h1 class="font-serif-title text-3xl md:text-4xl text-[#00082e] uppercase tracking-[0.2em] font-bold">
                    SERTIFIKAT
                </h1>
                <p class="font-body-sans text-[11px] text-[#775a19] tracking-wider uppercase font-semibold mt-0.5">
                    No. {{ $sertifikat->nomor_sertifikat ?? '0003/SERT-MAGANG/CVN/08/2026' }}
                </p>
                <p class="font-serif-title text-xs md:text-sm text-slate-600 italic mt-1.5">
                    Diberikan kepada
                </p>
            </div>

            <!-- Recipient Section -->
            <div class="flex flex-col items-center mb-3 w-full">
                <h2 class="font-serif-title text-2xl md:text-3xl text-[#00082e] uppercase tracking-widest font-bold mb-1">
                    {{ $sertifikat->peserta?->user?->nama ?? 'RAFASYA' }}
                </h2>
                <div class="w-1/2 h-[2px] bg-gradient-to-r from-transparent via-[#00082e] to-transparent"></div>
            </div>

            <!-- Body Text (Aman dari Ornamen Wave) -->
            <div class="max-w-[540px] mx-auto mb-4">
                <p class="font-body-sans text-xs text-[#00082e] leading-relaxed font-normal">
                    Telah melaksanakan Program Magang / Praktik Kerja Lapangan / PRAKERIN di CV. Natusi pada divisi <strong class="font-semibold">{{ $sertifikat->divisi?->nama_divisi ?? 'Software Development' }}</strong> dengan <strong class="font-semibold">{{ $sertifikat->predikat ?? 'sangat baik' }}</strong>
                    @if ($sertifikat->peserta?->tgl_mulai && $sertifikat->peserta?->tgl_selesai)
                        selama periode <strong class="font-semibold">{{ $sertifikat->peserta->tgl_mulai->translatedFormat('d F Y') }}</strong> s.d <strong class="font-semibold">{{ $sertifikat->peserta->tgl_selesai->translatedFormat('d F Y') }}</strong>.
                    @else
                        selama periode <strong class="font-semibold">22 Juni s.d 21 Agustus 2026</strong>.
                    @endif
                </p>
            </div>

            <!-- Footer / Signature Area -->
            <div class="w-full flex flex-col items-center mt-auto">
                <div class="flex flex-col items-center gap-1.5">
                    <div id="qr-ttd" 
                         class="p-1 border border-[#775a19]/40 bg-white rounded-lg shadow-sm"
                         data-qr-text="{{ implode("\n", array_filter([
                            'Sertifikat Resmi CV Natusi',
                            'No: '.($sertifikat->nomor_sertifikat ?? '0003/SERT-MAGANG/CVN/08/2026'),
                            'Nama: '.($sertifikat->peserta?->user?->nama ?? 'RAFASYA'),
                            'Divisi: '.($sertifikat->divisi?->nama_divisi ?? 'Software Development'),
                            'Predikat: '.($sertifikat->predikat ?? 'Sangat Baik'),
                            'Terbit: '.($sertifikat->tanggal_terbit ? $sertifikat->tanggal_terbit->translatedFormat('d F Y') : now()->translatedFormat('d F Y')),
                        ])) }}">
                    </div>
                    <span class="font-label-caps text-[9px] text-[#775a19] uppercase font-bold tracking-widest">
                        Verifikasi Digital
                    </span>
                    <div class="text-center mt-0.5">
                        <p class="font-body-sans text-xs md:text-sm text-[#00082e] font-semibold">
                            Arif Rakhman Hadi, S.Kom
                        </p>
                        <p class="font-label-caps text-[10px] text-[#775a19] uppercase font-bold">
                            Direktur CV. Natusi
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Script QR Code Dynamic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('qr-ttd');
            if (el) {
                new QRCode(el, {
                    text: el.dataset.qrText,
                    width: 64,
                    height: 64,
                    colorDark: "#00082e",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        });
    </script>

</body>
</html>