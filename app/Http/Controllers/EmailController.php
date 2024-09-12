<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DrinkRemainderEmail;

class EmailController extends Controller
{
    public function sendReminderEmail() {
        $email = env('MAIL_FROM_ADDRESS', null);
        $bcc = [];

        if(count($bcc) > 0) {
            $response = Mail::to($email)->bcc($bcc)->send(new DrinkRemainderEmail());
        }
    }
}
