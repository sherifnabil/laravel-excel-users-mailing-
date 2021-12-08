<?php

namespace App\Jobs;

use App\Mail\HelloUser;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 20;

    public function __construct(
        public $userMail,
        public $mailObj
    ) {
        //
    }


    public function handle()
    {
        Mail::to($this->userMail)->send(new HelloUser($this->mailObj));
    }
}
