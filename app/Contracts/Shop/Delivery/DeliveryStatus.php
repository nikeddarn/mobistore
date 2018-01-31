<?php
/**
 * Delivery status.
 */

namespace App\Contracts\Shop\Delivery;


interface DeliveryStatus
{
    const PROCESSING = 1;

    const ORDERED = 2;

    const PARTIALLY_ORDERED = 3;

    const SHIPPING = 4;

    const CUSTOMER_DELIVERY = 5;

    const DELIVERED = 6;

}