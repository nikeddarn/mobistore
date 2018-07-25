<?php

/**
 * Define notifications channels.
 */

use App\Channels\InvoiceMessageDatabaseChannel;
use App\Channels\SmsChannel;
use App\Notifications\Delivery\DeliveryDateChangedNotification;
use App\Notifications\Invoices\StorageOrderCreatedNotification;
use App\Notifications\Invoices\UserOrderCancelledNotification;
use App\Notifications\Invoices\UserOrderCollectedNotification;
use App\Notifications\Invoices\UserOrderCreatedNotification;
use App\Notifications\Invoices\UserOrderPartiallyCollectedNotification;
use App\Notifications\Invoices\VendorOrderCreatedNotification;

return [

    // channels for each notification
    'channels' => [
        // 'invoice created' notifications
        UserOrderCreatedNotification::class => [InvoiceMessageDatabaseChannel::class],
        VendorOrderCreatedNotification::class => [SmsChannel::class],
        StorageOrderCreatedNotification::class => [SmsChannel::class],

        // 'invoice collected' notifications
        UserOrderCollectedNotification::class => [InvoiceMessageDatabaseChannel::class],
        UserOrderCancelledNotification::class => [InvoiceMessageDatabaseChannel::class, SmsChannel::class],
        UserOrderPartiallyCollectedNotification::class => [InvoiceMessageDatabaseChannel::class, SmsChannel::class],

        // delivery notifications
        DeliveryDateChangedNotification::class => [InvoiceMessageDatabaseChannel::class],
    ],

    'show_unread_notification_days' => 20,
];