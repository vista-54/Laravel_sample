<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientRegister extends Mailable
{
    use Queueable, SerializesModels;

    public $client;

    /**
     * Create a new message instance.
     *
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->client->user->fullname . ' - Thank you for registering')->view('mail.client_greeting', ['client' => $this->client]);
    }
}

