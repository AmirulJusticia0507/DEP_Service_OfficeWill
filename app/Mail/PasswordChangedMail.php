<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password akun DEP Service Anda telah diubah',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.password-changed',
        );
    }
}
