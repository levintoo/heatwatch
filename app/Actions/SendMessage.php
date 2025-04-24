<?php

namespace App\Actions;

use AfricasTalking\SDK\AfricasTalking;

class SendMessage
{
    public function handle($message, $recipient)
    {
        $credentials = config('services.at');

        $AT = new AfricasTalking($credentials['username'], $credentials['api_key']);

        $sms = $AT->sms();

        $sms->send([
            'to' => '254700814223',
            'message' => $message,
            'username' => $credentials['username'],
            'from' => $credentials['sender_id'],
        ]);
    }
}
