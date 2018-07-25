<?php
/**
 * Invoice message database channel.
 */

namespace App\Channels;


use Illuminate\Notifications\Notification;
use RuntimeException;

class InvoiceMessageDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function send($notifiable, Notification $notification)
    {
        // get invoice id
        if (method_exists($notification,'getInvoiceId')){
            $invoiceId = $notification->getInvoiceId();
        }else{
            $invoiceId = null;
        }

        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification->id,
            // add invoice's id over standard database channel parameters
            'invoices_id' => $invoiceId,
            'type' => get_class($notification),
            'data' => $this->getData($notifiable, $notification),
            'read_at' => null,
        ]);
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toDatabase')) {
            return is_array($data = $notification->toDatabase($notifiable))
                ? $data : $data->data;
        }

        if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException(
            'Notification is missing toDatabase / toArray method.'
        );
    }
}