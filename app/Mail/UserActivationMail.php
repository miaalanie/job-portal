<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $companyName;
    public $picName;
    public $email;
    public $password;

    public function __construct(User $user, $companyName, $picName, $email, $password)
    {
        $this->user = $user;
        $this->companyName = $companyName;
        $this->picName = $picName;
        $this->email = $email;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aktivasi Akun Perusahaan - FindTalen',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user_activation',
        );
    }
}
