<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $email, $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $token) {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $email = $this->email;
        $token = $this->token;

        return $this->from('laravelapi@email.com.br')
            ->markdown('mail.reset', compact('email', 'token'))
            ->subject('Password Reset');
    }
}
