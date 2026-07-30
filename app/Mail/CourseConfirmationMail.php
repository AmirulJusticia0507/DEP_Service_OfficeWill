<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
        public string $courseName,
        public string $deadline,
        public string $courseUrl,
        public string $status = 'TERKONFIRMASI'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pendaftaran Kursus - ' . $this->courseName,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.course-confirmation',
        );
    }
}
