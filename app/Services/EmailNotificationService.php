<?php

namespace App\Services;

use App\Mail\SystemNotificationMail;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailNotificationService
{
    public function send(Notifikasi $notifikasi): void
    {
        // Fitur email sengaja dibuat dormant sampai konfigurasi perusahaan tersedia.
        if (! config('natusi.email_notifications_enabled')) {
            return;
        }

        // Email penerimaan peserta dengan kredensial dikirim memakai
        // ParticipantAccountMail agar setiap anggota menerima email pribadi
        // berisi username, password awal, dan tautan login. Hindari email ganda.
        if ($notifikasi->kategori === 'akun'
            && $notifikasi->judul === 'Selamat, Pengajuan Magang Diterima') {
            return;
        }

        if ($notifikasi->judul === 'Selamat! Lamaran Karyawan Disetujui') {
            return;
        }

        $notifikasi->loadMissing('user');
        $user = $notifikasi->user;

        if (! $user) {
            return;
        }

        $recipient = $user->role === 'admin_peserta' && config('natusi.admin_magang_email')
            ? config('natusi.admin_magang_email')
            : $user->email;

        if (! $recipient && in_array($user->role, ['admin', 'admin_peserta'], true)) {
            $recipient = config('natusi.admin_magang_email');
        }

        if (! $recipient) {
            return;
        }

        try {
            Mail::to($recipient)->send(new SystemNotificationMail($notifikasi));
        } catch (Throwable $exception) {
            Log::warning('Email notifikasi portal gagal dikirim.', [
                'notification_id' => $notifikasi->id_notifikasi,
                'recipient' => $recipient,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
