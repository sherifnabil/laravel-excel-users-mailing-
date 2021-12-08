<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Mail\HelloUser;
use App\Models\UserEmails;
use App\Models\FailedEmail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\UserEmailRequest;
use Illuminate\Support\Facades\Artisan;

class UserEmailsController extends Controller
{
    private $failedEmails = [];

    public function store(UserEmailRequest $request)
    {
        $mails = Excel::toArray(new UserEmails, request()->file('file'));
        $existingEmails = UserEmails::pluck('email')->toArray();
        $skippedEmails = [...$existingEmails];

        foreach ($mails[0] as $mail) {
            if (!($this->validEmail($mail[1])) || in_array($mail[1], $skippedEmails)) {
                continue;
            }
            $skippedEmails[] = $mail[1];
            $mailObj = $this->mutateData($mail);

            $userMail = UserEmails::create($mailObj);
            $this->sendMail($userMail, $mailObj);
        }
        $this->logFailedEmails();

        return response()->json([
            'message'   =>  'Uploaded Succefully'
        ]);
    }

    private function mutateData(array $mail): array
    {
        $formatedMail = [
            'name' => $mail[0],
            'email' => $mail[1]
        ];
        return $formatedMail;
    }

    private function validEmail(string $email): mixed
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function sendMail(UserEmails $userMail, array $mailObj): void
    {
        try {
            SendEmail::dispatch($userMail, $mailObj);
        } catch (\Throwable $th) {
            $this->failedEmails[] = $mailObj;
        }
    }

    private function logFailedEmails()
    {
        if (!count($this->failedEmails)) {
            return;
        }
        foreach ($this->failedEmails as  $email) {
            FailedEmail::create($email);
        }
    }
}
