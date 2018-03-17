<?php

namespace App\Providers;

use App\Events\Invoices\UserOrderCreated;
use App\Events\Invoices\UserPreOrderCreated;
use App\Listeners\DefineUserPriceGroup;
use App\Listeners\Invoices\SendUserOrderInvoiceNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        Registered::class => [
            DefineUserPriceGroup::class . '@onUserRegister',
        ],

        UserOrderCreated::class => [
            SendUserOrderInvoiceNotifications::class,
        ],

        UserPreOrderCreated::class => [
            SendUserOrderInvoiceNotifications::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
