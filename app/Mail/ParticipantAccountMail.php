<?php

namespace App\Mail;

use App\Models\PermintaanMagang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipantAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $credential,
        public PermintaanMagang $permintaan,
        public string $loginUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[CV Natusi] Pengajuan Magang Diterima - Akun Peserta Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.participant-account',
        );
    }
}
