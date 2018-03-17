<?php

/*
 * Sending an sms via https://alphasms.ua/api/soap.php?wsdl .
 */

namespace App\Channels\Phone\Providers;

use App\Contracts\Channels\SmsChannelSenderInterface;
use App\Contracts\Channels\SmsTypesInterface;
use App\Messages\SmsMessage;
use SoapClient;
use Carbon\Carbon;

class AlphaSmsSender implements SmsChannelSenderInterface, SmsTypesInterface {

    /**
     * Send an sms.
     *
     * @param SmsMessage $message
     * @return bool Description
     */
    public function send(SmsMessage $message){
        $client = $this->createClient();

        $result = $client->send($this->createAuthData(), $this->createMessageData($message));

        return $result[0]->data === '1';
    }

    /**
     * Create Soap client.
     *
     * @return SoapClient
     */
    private function createClient()
    {
        $wsdlUrl = config('channels.phone.alphasms.wsdl');

        return new SoapClient($wsdlUrl, [
            'trace' => 1,
        ]);
    }

    /**
     * Create soap client auth data.
     *
     * @return array
     */
    private function createAuthData(){
        return [
            'login' => config('channels.phone.alphasms.login'),
            'password' => config('channels.phone.alphasms.password'),
            'key' => config('channels.phone.alphasms.key'),
        ];
    }

    /**
     * Create message data.
     *
     * @param SmsMessage $message
     * @return array
     */
    public function createMessageData(SmsMessage $message){
        return [
            'message' => [
                'id' => $message->id ? $message->id : random_int(1, PHP_INT_MAX),
                'recipient' => $message->recipient[0] === '+' ? $message->recipient : '+' . $message->recipient,
                'sender' => $message->sender ? $message->sender : config('channels.phone.alphasms.sender'),
                'date_beg' => $message->beginTransfer ? $message->beginTransfer : Carbon::now()->toDateTimeString(),
                'date_end' => $message->endTransfer ? $message->endTransfer : Carbon::now()->addHour()->toDateTimeString(),
                'url' => $message->url,
                'type' => $message->type ? $message->type : self::SIMPLE,
                'text' => $message->text,
            ],
        ];
    }
}
