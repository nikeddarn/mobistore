<?php
/**
 * Constants of delivery status
 */

namespace App\Contracts\Shop\Delivery;


interface DeliveryStatusInterface
{
    const PROCESSING = 1;

    const ORDERED = 2;

    const COLLECTED = 3;

    const DELIVERING = 4;

    const DELIVERED = 5;

    const CANCELLED = 6;
}