<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 12px; line-height: 1.6; }
        .header { border-bottom: 2px solid #05658f; padding-bottom: 12px; text-align: center; }
        .brand { color: #05658f; font-size: 18px; font-weight: bold; }
        .title { margin: 28px 0 2px; text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; }
        .number { text-align: center; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin: 14px 0 20px; }
        td { padding: 5px 0; vertical-align: top; }
        td:first-child { width: 32%; }
        .signature { margin-top: 42px; margin-left: 64%; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">CV NATUSI</div>
        <div>Surat Izin Cuti Karyawan</div>
    </div>

    <div class="title">SURAT IZIN CUTI</div>
    <div class="number">Nomor: {{ 'CUTI/' . $cuti->id . '/' . $cuti->created_at->format('Y') }}</div>

    <p>Dengan ini menerangkan bahwa:</p>
    <table>
        <tr><td>Nama Karyawan</td><td>: {{ $cuti->karyawan?->nama_karyawan ?? '-' }}</td></tr>
        <tr><td>NIP</td><td>: {{ $cuti->karyawan?->nip ?? '-' }}</td></tr>
        <tr><td>Jabatan</td><td>: {{ $cuti->karyawan?->jabatan ?? '-' }}</td></tr>
        <tr><td>Divisi</td><td>: {{ $cuti->karyawan?->divisi?->nama_divisi ?? '-' }}</td></tr>
        <tr><td>Jenis Cuti</td><td>: {{ $cuti->jenis_label }}</td></tr>
        <tr><td>Periode</td><td>: {{ $cuti->tanggal_mulai->translatedFormat('d F Y') }} sampai {{ $cuti->tanggal_selesai->translatedFormat('d F Y') }} ({{ $cuti->jumlah_hari }} hari)</td></tr>
        <tr><td>Alasan</td><td>: {{ $cuti->alasan }}</td></tr>
    </table>

    <p>Demikian surat izin cuti ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>

    <div class="signature">
        <div>{{ $cuti->created_at->translatedFormat('d F Y') }}</div>
        <br><br><br>
        <strong>HRD CV NATUSI</strong>
    </div>
</body>
</html>
