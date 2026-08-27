<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun Karyawan CV Natusi</title>
</head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
            <div style="padding:28px 26px;background:#075c80;color:#ffffff;">
                <div style="font-size:12px;letter-spacing:.12em;text-transform:uppercase;opacity:.85;">CV Natusi | Human Resources</div>
                <h1 style="margin:10px 0 0;font-size:24px;line-height:1.35;">Selamat, Anda resmi bergabung</h1>
            </div>
            <div style="padding:28px 26px;">
                <p style="margin:0 0 14px;font-size:16px;">Yth. <strong>{{ $permintaan->nama_pemohon }}</strong>,</p>
                <p style="margin:0;line-height:1.75;color:#334155;">Dengan senang hati kami informasikan bahwa lamaran Anda untuk posisi <strong>{{ $permintaan->posisi }}</strong> telah disetujui. Akun karyawan Anda telah aktif dan dapat digunakan untuk mengakses Portal CV Natusi.</p>
                <div style="margin:24px 0;padding:20px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;">
                    <div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#0369a1;font-weight:700;">Kredensial Login</div>
                    <p style="margin:14px 0 8px;color:#475569;">Username: <strong style="color:#0f172a;">{{ $username }}</strong></p>
                    <p style="margin:0;color:#475569;">Password: <strong style="color:#047857;">{{ $password }}</strong></p>
                </div>
                <p style="margin:0 0 20px;line-height:1.7;color:#475569;">Silakan gunakan username atau email pendaftaran beserta password di atas untuk masuk. Demi keamanan, segera ubah password setelah login dan jangan membagikan kredensial ini kepada siapa pun.</p>
                <a href="{{ $loginUrl }}" style="display:inline-block;background:#087da5;color:#ffffff;text-decoration:none;font-weight:700;padding:13px 22px;border-radius:10px;">Masuk ke Portal CV Natusi</a>
                <p style="margin:24px 0 0;padding:14px 16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;font-size:13px;line-height:1.6;color:#9a3412;">Jika email ini belum terlihat, silakan periksa folder Spam atau Promosi. Email ini dikirim otomatis oleh CV Natusi dan tidak perlu dibalas.</p>
            </div>
        </div>
    </div>
</body>
</html>