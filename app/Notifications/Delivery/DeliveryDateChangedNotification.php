<?php

namespace App\Notifications\Delivery;

use App\Messages\SmsMessage;
use App\Models\Invoice;
use App\Notifications\Invoices\InvoiceNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeliveryDateChangedNotification extends Notification
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
     * Get data for database notification.
     *
     * @param mixed $notifiable
     * @return array
     * @throws Exception
     */
    public function toDatabase($notifiable)
    {
        // get invoice planned arrival
        $plannedArrival = $this->invoice->userInvoice->userDelivery->planned_arrival;

        return [
            'title' => trans('messages.delivery.date.change.title'),
            'message' => trans('messages.delivery.date.change.message', [
                'id' => $this->invoice->id,
                'delivery' => $plannedArrival ? $plannedArrival->format('d-m-Y') : trans('shop.delivery.time_undefined'),
            ]),
        ];
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed $notifiable
     * @return SmsMessage
     * @throws Exception
     */
    public function toSms($notifiable)
    {
        // get invoice planned arrival
        $plannedArrival = $this->getPlannedArrivalDate();

        return (new SmsMessage())->setText(trans('messages.delivery.date.change.message', [
            'id' => $this->invoice->id,
            'delivery' => $plannedArrival ? $plannedArrival->format('d-m-Y') : trans('shop.delivery.time_undefined'),
        ]));
    }
}
