<?php

namespace App\Providers;

use App\Events\Invoices\UserOrderCancelled;
use App\Events\Invoices\UserOrderCollected;
use App\Events\Invoices\UserOrderCreated;
use App\Events\Invoices\UserOrderPartiallyCollected;
use App\Listeners\DefineUserPriceGroup;
use App\Listeners\Invoices\SendUserOrderCancelledNotifications;
use App\Listeners\Invoices\SendUserOrderCollectedNotifications;
use App\Listeners\Invoices\SendUserOrderCreatedNotifications;
use App\Listeners\Invoices\SendUserOrderPartiallyCollectedNotifications;
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
            SendUserOrderCreatedNotifications::class,
        ],

        UserOrderCollected::class => [
            SendUserOrderCollectedNotifications::class,
        ],

        UserOrderCancelled::class => [
            SendUserOrderCancelledNotifications::class,
        ],

        UserOrderPartiallyCollected::class => [
            SendUserOrderPartiallyCollectedNotifications::class,
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
