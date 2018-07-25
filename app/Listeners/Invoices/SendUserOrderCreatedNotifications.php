<?php

namespace App\Listeners\Invoices;

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Events\Invoices\UserOrderCreated;
use App\Models\User;
use App\Notifications\Invoices\StorageOrderCreatedNotification;
use App\Notifications\Invoices\UserOrderCreatedNotification;
use App\Notifications\Invoices\VendorOrderCreatedNotification;
use Exception;
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
        $user->notify(new UserOrderCreatedNotification($event->invoice));

         // notify vendor manager
        if ($event->invoice->invoice_types_id === self::USER_PRE_ORDER) {
            $vendorManager = $this->user->whereHas('userRole', function ($query) {
                $query->where('roles_id', self::VENDOR_MANAGER);
            })->get()->sortBy('general')->last();
            $vendorManager->notify(new VendorOrderCreatedNotification($event->invoice));
        }

        // notify storekeeper
        if ($event->invoice->invoice_types_id === self::USER_ORDER) {
            $storekeeper = $this->user->whereHas('userRole', function ($query) {
                $query->where('roles_id', self::STOREKEEPER);
            })->get()->sortBy('general')->last();
            $storekeeper->notify(new StorageOrderCreatedNotification($event->invoice));
        }
    }
}
