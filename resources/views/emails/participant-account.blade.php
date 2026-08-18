<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Peserta Magang CV Natusi</title>
</head>
<body style="margin:0;padding:0;background:#f3f7fb;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f7fb;padding:28px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #dbe7f1;">
                <tr>
                    <td style="background:#086b8f;padding:28px 32px;color:#ffffff;">
                        <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;opacity:.9;">Portal CV Natusi</div>
                        <div style="font-size:25px;font-weight:700;margin-top:8px;">Selamat, Pengajuan Magang Diterima</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px 32px;">
                        <p style="margin:0 0 16px;font-size:17px;line-height:1.6;">Halo <strong>{{ $credential['nama'] }}</strong>,</p>
                        <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#475569;">
                            Pengajuan magang dari <strong>{{ $permintaan->nama_sekolah }}</strong> telah disetujui. Akun Portal Peserta Magang Anda sudah dibuat. Gunakan akun berikut untuk masuk ke sistem dan mengakses penugasan, laporan mingguan, pembayaran, serta sertifikat.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;">
                            <tr>
                                <td style="padding:18px 20px;border-bottom:1px solid #e2e8f0;">
                                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:#64748b;font-weight:700;">Email Login</div>
                                    <div style="margin-top:6px;font-size:18px;font-weight:700;color:#0f172a;">{{ $credential['login_email'] ?? $credential['email'] ?? $credential['username'] }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:18px 20px;">
                                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:#64748b;font-weight:700;">Password Awal</div>
                                    <div style="margin-top:6px;font-size:18px;font-weight:700;color:#047857;">{{ $credential['password'] }}</div>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:22px 0 18px;font-size:14px;line-height:1.7;color:#475569;">
                            Buka Portal CV Natusi melalui tombol di bawah, kemudian login menggunakan <strong>email dan password awal di atas</strong>. Setelah berhasil masuk, segera ganti password apabila sistem meminta Anda melakukannya.
                        </p>

                        <p style="margin:0 0 24px;">
                            <a href="{{ $loginUrl }}" style="display:inline-block;background:#087da5;color:#ffffff;text-decoration:none;font-weight:700;padding:13px 22px;border-radius:10px;">Buka Portal Peserta Magang</a>
                        </p>

                        <div style="padding:14px 16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;font-size:13px;line-height:1.6;color:#9a3412;">
                            Simpan kredensial ini dengan aman dan jangan membagikannya kepada orang lain. Email ini khusus untuk akun peserta atas nama {{ $credential['nama'] }}.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 32px;background:#f8fafc;color:#64748b;font-size:12px;line-height:1.6;">
                        Email ini dikirim otomatis oleh CV Natusi Portal karena pengajuan magang Anda telah disetujui.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
