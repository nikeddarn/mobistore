<?php

namespace App\Notifications\Invoices;

use App\Channels\SmsChannel;
use App\Messages\SmsMessage;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserOrderPartiallyCollectedNotification extends Notification
{
    use Queueable;

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
        return ['database', SmsChannel::class];
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
     */
    public function toDatabase($notifiable)
    {
        $plannedArrival = $this->invoice->vendorInvoice->userInvoice->userDelivery->planned_arrival;

        return [
            'title' => trans('messages.invoice.' . $this->invoice->invoice_types_id . '.partially_collected.title'),
            'message' => trans('messages.invoice.' . $this->invoice->invoice_types_id . '.partially_collected.message', [
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
     */
    public function toSms($notifiable)
    {
        $plannedArrival = $this->invoice->vendorInvoice->userInvoice->userDelivery->planned_arrival;

        return (new SmsMessage())->setText(
            trans('messages.invoice.' . $this->invoice->invoice_types_id . '.partially_collected.title') .
            trans('messages.invoice.' . $this->invoice->invoice_types_id . '.partially_collected.message', [
                'id' => $this->invoice->id,
                'delivery' => $plannedArrival ? $plannedArrival->format('d-m-Y') : trans('shop.delivery.time_undefined'),
            ])
        );
    }
}
