<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $companyName = auth()->user()->user->business;
        return $this
            ->from(getenv('MAIL_FROM_ADDRESS'), $companyName  . ' x NextCard')
            ->subject('Your invitation for ' . $companyName . ' x NextCard')
            ->view('mail.invite', [
                'company_name' => $companyName
            ]);
    }
}
