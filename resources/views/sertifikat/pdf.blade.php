@php
    use Illuminate\Support\Str;

    $education = Str::lower((string) ($sertifikat->peserta?->tingkat_pendidikan ?? ''));
    $isSmk = Str::contains($education, 'smk');

    $namaPeserta = Str::upper((string) ($sertifikat->peserta?->user?->nama ?? 'PESERTA MAGANG'));
    $divisiNama = $sertifikat->divisi?->nama_divisi ?? 'Software Development';
    $predikat = Str::lower((string) ($sertifikat->predikat ?? 'Sangat Baik'));
    $nomor = $sertifikat->nomor_sertifikat ?? '-';

    if ($sertifikat->peserta?->tgl_mulai && $sertifikat->peserta?->tgl_selesai) {
        $periode = $sertifikat->peserta->tgl_mulai->translatedFormat('d F Y') . ' s.d ' . $sertifikat->peserta->tgl_selesai->translatedFormat('d F Y');
    } else {
        $periode = '22 Juni s.d 21 Agustus 2026';
    }

    $primary = $isSmk ? '#14532d' : '#101d78';
    $secondary = $isSmk ? '#15803d' : '#2439a8';
    $softLine = $isSmk ? '#d7efdf' : '#dce7f8';
    $gold = '#b38a28';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; width: 297mm; height: 210mm; }
        body { font-family: DejaVu Sans, sans-serif; background: #ffffff; color: {{ $primary }}; }

        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background: #ffffff;
        }

        .frame-1 {
            position: absolute;
            left: 4.5mm; right: 4.5mm; top: 4.5mm; bottom: 4.5mm;
            border: 0.35mm solid {{ $softLine }};
            border-radius: 4mm;
        }
        .frame-2 {
            position: absolute;
            left: 7.5mm; right: 7.5mm; top: 7.5mm; bottom: 7.5mm;
            border: 0.25mm dashed {{ $softLine }};
            border-radius: 3mm;
        }

        .corner-tl {
            position: absolute;
            left: -15mm; top: -20mm;
            width: 90mm; height: 72mm;
            background: {{ $primary }};
            border-right: 1.2mm solid {{ $gold }};
            border-bottom: 1.2mm solid {{ $gold }};
            border-bottom-right-radius: 72mm;
        }
        .corner-tl-line {
            position: absolute;
            left: -9mm; top: -16mm;
            width: 86mm; height: 68mm;
            border-right: 0.55mm solid {{ $gold }};
            border-bottom: 0.55mm solid {{ $gold }};
            border-bottom-right-radius: 68mm;
        }
        .corner-br {
            position: absolute;
            right: -15mm; bottom: -20mm;
            width: 90mm; height: 72mm;
            background: {{ $primary }};
            border-left: 1.2mm solid {{ $gold }};
            border-top: 1.2mm solid {{ $gold }};
            border-top-left-radius: 72mm;
        }
        .corner-br-line {
            position: absolute;
            right: -9mm; bottom: -16mm;
            width: 86mm; height: 68mm;
            border-left: 0.55mm solid {{ $gold }};
            border-top: 0.55mm solid {{ $gold }};
            border-top-left-radius: 68mm;
        }

        .wave-tr, .wave-bl {
            position: absolute;
            width: 56mm; height: 56mm;
            border: 0.45mm solid {{ $softLine }};
            border-radius: 50%;
        }
        .wave-tr { right: -17mm; top: -11mm; }
        .wave-tr.w2 { right: -13mm; top: -8mm; }
        .wave-tr.w3 { right: -9mm; top: -5mm; }
        .wave-tr.w4 { right: -5mm; top: -2mm; }
        .wave-bl { left: -18mm; bottom: -13mm; }
        .wave-bl.w2 { left: -14mm; bottom: -10mm; }
        .wave-bl.w3 { left: -10mm; bottom: -7mm; }
        .wave-bl.w4 { left: -6mm; bottom: -4mm; }

        .dots-right, .dots-left { position: absolute; font-size: 0; }
        .dots-right { right: 12mm; top: 34mm; width: 24mm; }
        .dots-left { left: 12mm; bottom: 32mm; width: 28mm; }
        .dot {
            display: inline-block;
            width: 2mm; height: 2mm;
            margin: 1.35mm;
            border-radius: 50%;
            background: {{ $softLine }};
        }

        .content {
            position: absolute;
            z-index: 10;
            left: 43mm;
            right: 43mm;
            top: 9mm;
            bottom: 9mm;
            text-align: center;
        }
        .logo { width: 18mm; height: 18mm; object-fit: contain; }
        .tagline { margin-top: 1.7mm; font-size: 9.2pt; font-weight: 600; }
        .rule { width: 82%; height: 0.35mm; margin: 1.8mm auto 0; background: {{ $primary }}; opacity: .32; }
        .title { margin: 4mm 0 0; font-size: 40pt; line-height: 1; letter-spacing: 5mm; font-weight: 800; }
        .given { margin-top: 1mm; font-size: 11pt; }
        .name { margin-top: 8mm; font-size: 27pt; line-height: 1.05; letter-spacing: 1.2mm; font-weight: 800; }
        .name-rule { width: 78%; height: 0.4mm; margin: 3mm auto 0; background: {{ $primary }}; opacity: .55; }
        .body-copy {
            width: 87%;
            margin: 4.5mm auto 0;
            font-size: 12.2pt;
            line-height: 1.55;
            font-weight: 500;
        }
        .smk-label {
            display: inline-block;
            margin-top: 3.5mm;
            padding: 1.7mm 5mm;
            border: 0.35mm solid {{ $softLine }};
            border-radius: 10mm;
            background: #f6fff8;
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: 0.45mm;
        }

        .signature {
            position: absolute;
            left: 50%;
            bottom: 9mm;
            width: 76mm;
            margin-left: -38mm;
            text-align: center;
        }
        .qr {
            width: 25mm;
            height: 25mm;
            padding: 1mm;
            border: 0.3mm solid {{ $softLine }};
            border-radius: 2mm;
            background: white;
        }
        .qr-caption { margin-top: 1mm; font-size: 6.8pt; color: #64748b; }
        .director { margin-top: 2mm; font-size: 11pt; font-weight: 600; }
        .role { margin-top: 0.7mm; font-size: 12pt; font-weight: 800; }
        .number { position: absolute; right: 11mm; bottom: 7mm; font-size: 7pt; opacity: .68; }
    </style>
</head>
<body>
<div class="certificate">
    <div class="frame-1"></div>
    <div class="frame-2"></div>

    <div class="corner-tl"></div>
    <div class="corner-tl-line"></div>
    <div class="corner-br"></div>
    <div class="corner-br-line"></div>

    <div class="wave-tr"></div><div class="wave-tr w2"></div><div class="wave-tr w3"></div><div class="wave-tr w4"></div>
    <div class="wave-bl"></div><div class="wave-bl w2"></div><div class="wave-bl w3"></div><div class="wave-bl w4"></div>

    @if ($isSmk)
        <div class="dots-right">
            @for ($i = 0; $i < 15; $i++)<span class="dot"></span>@endfor
        </div>
        <div class="dots-left">
            @for ($i = 0; $i < 18; $i++)<span class="dot"></span>@endfor
        </div>
    @endif

    <div class="content">
        <img src="{{ $logoDataUri }}" class="logo" alt="Logo Natusi">
        <div class="tagline">Software House | Hardware Supplier | IT Consultant | Network Installation</div>
        <div class="rule"></div>

        <div class="title">SERTIFIKAT</div>
        <div class="given">Diberikan kepada</div>

        <div class="name">{{ $namaPeserta }}</div>
        <div class="name-rule"></div>

        <div class="body-copy">
            Telah melaksanakan {{ $isSmk ? 'Program Magang / Praktik Kerja Lapangan / PRAKERIN' : 'Program Magang / Praktik Kerja Lapangan' }}
            di CV. Natusi pada divisi <strong>{{ $divisiNama }}</strong> dengan predikat <strong>{{ $predikat }}</strong>
            selama periode <strong>{{ $periode }}</strong>.
        </div>

        @if ($isSmk)
            <div class="smk-label">SERTIFIKAT SMK · PKL / PRAKERIN</div>
        @endif
    </div>

    <div class="signature">
        <img src="{{ $qrDataUri }}" class="qr" alt="QR tanda tangan direktur">
        <div class="qr-caption">Pindai QR untuk membuka tanda tangan digital direktur</div>
        <div class="director">Arif Rakhman Hadi, S.Kom</div>
        <div class="role">Direktur CV.Natusi</div>
    </div>

    <div class="number">No. {{ $nomor }}</div>
</div>
</body>
</html>
