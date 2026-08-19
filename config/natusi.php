<?php

return [
    // Aktifkan setelah kredensial SMTP perusahaan tersedia.
    'email_notifications_enabled' => env('NATUSI_EMAIL_NOTIFICATIONS_ENABLED', false),

    // Email resmi/PIC magang CV Natusi. Semua notifikasi admin diarahkan ke sini.
    'admin_magang_email' => env('NATUSI_ADMIN_MAGANG_EMAIL', 'info@natusi.co.id'),
];
