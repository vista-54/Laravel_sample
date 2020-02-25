<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMailApp extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;

    /**
     * Create a new message instance.
     *
     * @param $department
     * @param $code
     */
    public function __construct($department, $code)
    {
        $this->user = $department;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject =  $this->user->user->full_name . ' - Reset your password';
        return $this->view('mail.passwordResetApp', ['name' => $this->user->user->full_name, 'verification_code' => $this->code]);
    }
}
