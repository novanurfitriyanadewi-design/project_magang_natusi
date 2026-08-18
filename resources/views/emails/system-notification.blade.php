<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $notifikasi->judul }}</title>
</head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,.08);">
            <div style="padding:20px 24px;background:#075c80;color:#ffffff;">
                <div style="font-size:12px;letter-spacing:.08em;text-transform:uppercase;opacity:.85;">Portal CV Natusi</div>
                <h1 style="margin:6px 0 0;font-size:21px;line-height:1.35;">{{ $notifikasi->judul }}</h1>
            </div>
            <div style="padding:24px;">
                @if($notifikasi->user?->nama)
                    <p style="margin:0 0 14px;">Halo {{ $notifikasi->user->nama }},</p>
                @endif
                <p style="margin:0;white-space:pre-line;line-height:1.7;color:#334155;">{{ $notifikasi->pesan }}</p>
                <p style="margin:22px 0 0;font-size:12px;line-height:1.6;color:#64748b;">
                    Email ini dikirim otomatis karena ada pembaruan pada portal magang CV Natusi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
