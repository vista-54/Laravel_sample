<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterVerify extends Mailable
{
    use Queueable, SerializesModels;

    public $department;
    public $code;

    /**
     * Create a new message instance.
     *
     * @param $department
     * @param $code
     */
    public function __construct($department, $code)
    {
        $this->department = $department;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('NextCard - Thank you for registering');
        return $this->view('mail.verify', ['name' => $this->department, 'verification_code' => $this->code]);
    }
}
