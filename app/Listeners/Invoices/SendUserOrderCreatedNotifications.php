<?php

namespace App\Listeners\Invoices;

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Events\Invoices\UserOrderCreated;
use App\Models\User;
use App\Notifications\Invoices\StorageOrderCreatedNotification;
use App\Notifications\Invoices\UserOrderCreatedNotification;
use App\Notifications\Invoices\VendorOrderCreatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserOrderCreatedNotifications implements UserRolesInterface, InvoiceTypes
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
        $user = auth('web')->user();
        $user->notify(new UserOrderCreatedNotification($event->invoice));

         // notify vendor manager
        if ($event->invoice->invoice_types_id === self::PRE_ORDER) {
            $vendorManager = $this->user->whereHas('userRole', function ($query) {
                $query->where('roles_id', self::VENDOR_MANAGER);
            })->get()->sortBy('general')->last();
            $vendorManager->notify(new VendorOrderCreatedNotification($event->invoice));
        }

        // notify storekeeper
        if ($event->invoice->invoice_types_id === self::ORDER) {
            $storekeeper = $this->user->whereHas('userRole', function ($query) {
                $query->where('roles_id', self::STOREKEEPER);
            })->get()->sortBy('general')->last();
            $storekeeper->notify(new StorageOrderCreatedNotification($event->invoice));
        }
    }
}
