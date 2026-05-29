<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectTalentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $mailMessage;
    public $applicant;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $mailMessage, $applicant)
    {
        $this->subject = $subject;
        $this->mailMessage = $mailMessage;
        $this->applicant = $applicant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.direct_talent',
            with: [
                'mailMessage' => $this->mailMessage,
                'applicant' => $this->applicant,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
