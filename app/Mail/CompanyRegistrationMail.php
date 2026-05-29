<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $user;
    public $password;

    public function __construct($company, $user, $password)
    {
        $this->company = $company;
        $this->user = $user;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang di FindTalen - Akun Perusahaan Anda Telah Aktif',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company_registration',
        );
    }
}
