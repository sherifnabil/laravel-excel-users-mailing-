<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HelloUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user
    ) {
        //
    }

    public function build()
    {
        return $this
            ->from("noreply@example.com", "Aoo")
            ->view('mail');
    }
}
