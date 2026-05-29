<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyEventRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $register;
    public $perusahaan;
    public $even;

    /**
     * Create a new message instance.
     */
    public function __construct($register)
    {
        $this->register = $register;
        $this->perusahaan = $register->perusahaan;
        $this->even = $register->even;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Event Sukses - ' . $this->even->namaperiode,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.company_event_registered',
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
