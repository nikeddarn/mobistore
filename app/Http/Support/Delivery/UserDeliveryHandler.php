<?php
/**
 * User delivery handler.
 */

namespace App\Http\Support\Delivery;


use Carbon\Carbon;

class UserDeliveryHandler
{
    /**
     * Update user delivery status.
     *
     * @param int $deliveryStatus
     * @return bool
     */
    public function updateDeliveryStatus(int $deliveryStatus): bool
    {
        $this->invoice->userInvoice->delivery_status_id = $deliveryStatus;
        return $this->invoice->userInvoice->save();
    }

    /**
     * Update user delivery date.
     *
     * @param Carbon $deliveryDate
     * @return bool
     */
    public function updateDeliveryDate(Carbon $deliveryDate = null): bool
    {
        $userDelivery = $this->invoice->userInvoice->userDelivery;

        if ($userDelivery) {
            $this->invoice->userInvoice->userDelivery->planned_arrival = $deliveryDate ? $deliveryDate->toDateString() : null;

            return $this->invoice->userInvoice->userDelivery->save();
        }

        return false;
    }
}