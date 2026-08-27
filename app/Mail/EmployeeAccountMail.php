<?php

namespace App\Mail;

use App\Models\PermintaanLamaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermintaanLamaran $permintaan,
        public string $username,
        public string $password,
        public string $loginUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[CV Natusi] Selamat, Lamaran Anda Diterima - Akun Karyawan',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.employee-account');
    }
}