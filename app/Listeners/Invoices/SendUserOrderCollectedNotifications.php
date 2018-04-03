<?php

namespace App\Listeners\Invoices;

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Events\Invoices\UserOrderCreated;
use App\Models\User;
use App\Notifications\Invoices\StorageOrderCreatedNotification;
use App\Notifications\Invoices\UserOrderCancelledNotification;
use App\Notifications\Invoices\UserOrderCollectedNotification;
use App\Notifications\Invoices\UserOrderCreatedNotification;
use App\Notifications\Invoices\VendorOrderCreatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserOrderCollectedNotifications implements UserRolesInterface, InvoiceTypes
{
    /**
     * @var User
     */
    private $user;

    /**
     * Create the event listener.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param  UserOrderCreated $event
     * @return void
     */
    public function handle($event)
    {
        // notify user
        $user = $event->invoice->vendorInvoice->userInvoice->load('user')->user;
        $user->notify(new UserOrderCollectedNotification($event->invoice));
    }
}
