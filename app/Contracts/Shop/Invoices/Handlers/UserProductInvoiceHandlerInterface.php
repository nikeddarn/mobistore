<?php
/**
 * Methods for handling product invoice.
 */

namespace App\Contracts\Shop\Invoices\Handlers;

interface UserProductInvoiceHandlerInterface extends ProductInvoiceHandlerInterface
{
    /**
     * Append user delivery id to user invoice.
     *
     * @param int $userDeliveryId
     * @param int $deliveryTypeId
     */
    public function appendUserDelivery(int $userDeliveryId, int $deliveryTypeId);
}