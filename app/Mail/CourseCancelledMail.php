<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
        public string $courseName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kursus DEP Service Anda telah dibatalkan',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.course-cancelled',
        );
    }
}
