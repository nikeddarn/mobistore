<?php
/**
 * Sms channel.
 */

namespace App\Channels;


use App\Contracts\Channels\SmsChannelSenderInterface;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * @var SmsChannelSenderInterface
     */
    private $smsChannelSender;

    /**
     * SmsChannel constructor.
     * @param SmsChannelSenderInterface $smsChannelSender
     */
    public function __construct(SmsChannelSenderInterface $smsChannelSender)
    {

        $this->smsChannelSender = $smsChannelSender;
    }
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        if (!$message->recipient){
            $message->setRecipient($notifiable->routeNotificationForSms());
        }

        $this->smsChannelSender->send($message);
    }
}