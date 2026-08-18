<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public URL untuk QR sertifikat
    |--------------------------------------------------------------------------
    |
    | URL ini HARUS bisa dibuka oleh HP yang melakukan scan. Jangan gunakan
    | 127.0.0.1 atau localhost karena alamat tersebut mengarah ke perangkat
    | yang melakukan scan, bukan ke komputer/server Laravel.
    |
    | Contoh lokal satu Wi-Fi:
    | NATUSI_QR_PUBLIC_URL=http://192.168.1.10:8000
    |
    | Contoh production:
    | NATUSI_QR_PUBLIC_URL=https://magang.natusi.co.id
    |
    */
    'qr_public_url' => env('NATUSI_QR_PUBLIC_URL', env('APP_URL')),
];
