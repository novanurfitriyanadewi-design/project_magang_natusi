<?php

namespace App\Mail;

use App\Models\Notifikasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Notifikasi $notifikasi)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[CV Natusi] '.$this->notifikasi->judul,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.system-notification',
        );
    }
}
