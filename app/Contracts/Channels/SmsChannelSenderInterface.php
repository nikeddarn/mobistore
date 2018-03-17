<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 11.03.18
 * Time: 18:14
 */

namespace App\Contracts\Channels;


use App\Messages\SmsMessage;

interface SmsChannelSenderInterface
{
    /**
     * Send an sms.
     *
     * @param SmsMessage $message
     *
     * @return bool
     */
    public function send(SmsMessage $message);
}