<?php

namespace App\Notifications\Invoices;

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Messages\SmsMessage;
use App\Models\Invoice;
use Carbon\Carbon;
use Exception;
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
        return config('notifications.' . __class__);
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
        $plannedArrival = $this->getPlannedArrivalDate();

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
     * @throws Exception
     */
    public function toSms($notifiable)
    {
        // get invoice planned arrival
        $plannedArrival = $this->getPlannedArrivalDate();

        return (new SmsMessage())->setText(
            trans('messages.invoice.' . $this->invoice->invoice_types_id . '.partially_collected.title') . ' ' .
            trans('messages.invoice.' . $this->invoice->invoice_types_id . '.partially_collected.message', [
                'id' => $this->invoice->id,
                'delivery' => $plannedArrival ? $plannedArrival->format('d-m-Y') : trans('shop.delivery.time_undefined'),
            ])
        );
    }

    /**
     * Get planned arrival date of invoice as Carbon.
     *
     * @return Carbon|null
     * @throws Exception
     */
    private function getPlannedArrivalDate()
    {
        // retrieve user invoice
        if ($this->invoice->invoice_types_id === InvoiceTypes::PRE_ORDER) {
            // retrieve via related vendor invoice
            $userInvoice = $this->invoice->vendorInvoice->userInvoice->first();
        }elseif ($this->invoice->invoice_types_id === InvoiceTypes::ORDER){
            // retrieve directly
            $userInvoice = $this->invoice->userInvoice;
        }else{
            // wrong invoice type
            throw new Exception('Wrong invoice type: ' . $this->invoice->invoiceType);
        }

        // get invoice planned arrival
        return $userInvoice->userDelivery->planned_arrival;
    }
}
