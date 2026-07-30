<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
        public string $courseName,
        public string $deadline,
        public string $courseUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Anda mendapat tugas kursus baru',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.course-assigned',
        );
    }
}
