<?php

namespace CloudCastle\SmsServices\Facades;
use CloudCastle\SmsServices\SmsProvider;

class SmsFacade
{

    public static function sendSms(array|string $phones, string $message):array
    {
        $results = [];
        $provider = (new SmsProvider())->getProvider();
        $messages = $provider->sendSms($phones, $message);
        foreach ($messages as $msg) {
            $results[] = $msg->save();
        }
        return $results;
    }

    public static function call(string $phone):array
    {
        $results = [];
        $provider = (new SmsProvider())->getProvider();
        $messages = $provider->call($phone);
        foreach ($messages as $message){
            $results[] = $message->save();
        }
        return $results;
    }

}
