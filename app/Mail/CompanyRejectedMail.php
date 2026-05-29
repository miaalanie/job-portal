<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reason;

    public function __construct($user, $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Update Status Validasi Akun Perusahaan')
                    ->view('emails.company_rejected');
    }
}
