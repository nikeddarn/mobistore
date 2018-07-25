<?php

namespace App\Listeners\Invoices;

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Events\Invoices\UserOrderCreated;
use App\Notifications\Invoices\UserOrderCollectedNotification;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserOrderCollectedNotifications implements InvoiceTypes
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
        if ($event->invoice->userInvoice){
            // retrieve via user invoice
            $user = $event->invoice->userInvoice->user;
        }else{
            // wrong invoice type
            throw new Exception('Wrong invoice type: no UserInvoice model');
        }

        // notify user
        $user->notify(new UserOrderCollectedNotification($event->invoice));
    }
}
