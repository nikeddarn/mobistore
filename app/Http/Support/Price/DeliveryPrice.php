<?php
/**
 * User delivery price definer.
 */

namespace App\Http\Support\Price;


use Illuminate\Contracts\Auth\Authenticatable;

class DeliveryPrice
{
    /**
     * Calculate minimum invoice sum for free shipping.
     *
     * @param  $user
     * @return float
     */
    public function getFreeDeliveryMinSum(Authenticatable $user = null)
    {
        if ($user && $user->price_group >= config('shop.delivery.free_delivery_price_group')){
            return  0;
        }

        return config('shop.delivery.free_delivery_invoice_sum');
    }

    /**
     * Get delivery price depends on user and invoice sum.
     *
     * @param Authenticatable $user
     * @param float $invoiceSum
     * @return float
     */
    public function getDeliveryPrice(Authenticatable $user, float $invoiceSum)
    {
        if (($user && $user->price_group >= config('shop.delivery.free_delivery_price_group')) || $invoiceSum >= config('shop.delivery.free_delivery_invoice_sum')){
            return  0;
        }

        return config('shop.delivery.local_delivery_price');
    }
}