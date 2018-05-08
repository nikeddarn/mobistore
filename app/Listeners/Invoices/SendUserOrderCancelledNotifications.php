<?php

namespace App\Listeners\Invoices;

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Events\Invoices\UserOrderCreated;
use App\Notifications\Invoices\UserOrderCancelledNotification;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserOrderCancelledNotifications implements UserRolesInterface, InvoiceTypes
{
    /**
     * Handle the event.
     *
     * @param  UserOrderCreated $event
     * @return void
     * @throws Exception
     */
    public function handle($event)
    {
        // retrieve user
        if ($event->invoice->invoice_types_id === self::PRE_ORDER) {
            // retrieve via related vendor invoice
            $user = $event->invoice->vendorInvoice->userInvoice->first()->user;
        }elseif ($event->invoice->invoice_types_id === self::ORDER){
            // retrieve via user invoice
            $user = $event->invoice->userInvoice->user;
        }else{
            // wrong invoice type
            throw new Exception('Wrong invoice type: ' . $event->invoice->invoiceType);
        }

        // notify user
        $user->notify(new UserOrderCancelledNotification($event->invoice));
    }
}
