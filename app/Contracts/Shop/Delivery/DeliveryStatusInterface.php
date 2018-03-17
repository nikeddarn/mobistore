<?php
/**
 * Constants of delivery status
 */

namespace App\Contracts\Shop\Delivery;


interface DeliveryStatusInterface
{
    const PROCESSING = 1;

    const COLLECTED = 2;

    const STORAGE_DELIVERING = 3;

    const USER_DELIVERING = 4;

    const POST_DELIVERING = 5;

    const DELIVERED = 6;
}