<?php

/**
 * Define notifications channels.
 */

use App\Channels\SmsChannel;
use App\Notifications\Invoices\StorageOrderCreatedNotification;
use App\Notifications\Invoices\UserOrderCancelledNotification;
use App\Notifications\Invoices\UserOrderCollectedNotification;
use App\Notifications\Invoices\UserOrderCreatedNotification;
use App\Notifications\Invoices\UserOrderPartiallyCollectedNotification;
use App\Notifications\Invoices\VendorOrderCreatedNotification;

return [
    // 'invoice created' notifications
    UserOrderCreatedNotification::class => ['database'],
    VendorOrderCreatedNotification::class => [SmsChannel::class],
    StorageOrderCreatedNotification::class => [SmsChannel::class],

    // 'invoice collected' notifications
    UserOrderCollectedNotification::class => ['database'],
    UserOrderCancelledNotification::class => ['database', SmsChannel::class],
    UserOrderPartiallyCollectedNotification::class => ['database', SmsChannel::class],
];