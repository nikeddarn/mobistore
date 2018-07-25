<?php

namespace App\Notifications\Invoices;

use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Messages\SmsMessage;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StorageOrderCreatedNotification extends Notification
{
    use Queueable;

    use InvoiceNotification;

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * Create a new notification instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('notifications.channels.' . __class__);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed $notifiable
     * @return SmsMessage
     */
    public function toSms($notifiable)
    {
        return (new SmsMessage())->setText(
            trans('messages.manager.' . UserRolesInterface::STOREKEEPER . '.created', [
                    'id' => $this->invoice->id,
                    'store' => $this->invoice->storage->first()->title,
                ]
            )
        );
    }
}
