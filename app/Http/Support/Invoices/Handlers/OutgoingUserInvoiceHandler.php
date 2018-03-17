<?php
/**
 * User invoice handler with delivery methods.
 */

namespace App\Http\Support\Invoices\Handlers;


final class OutgoingUserInvoiceHandler extends StorageProductInvoiceHandler
{
    /**
     * Create user delivery.
     *
     * @param array $deliveryData
     * @param string $deliveryDay
     */
    public function addUserDelivery(array $deliveryData, string $deliveryDay)
    {
        $userDelivery = $this->invoice->userInvoice->userDelivery()->create([
            'name' => $deliveryData['name'],
            'phone' => $deliveryData['phone'],
            'address' => $deliveryData['address'],
            'message' => $deliveryData['message'],
            'planned_arrival' => $deliveryDay,
        ]);

        $this->invoice->userInvoice->user_deliveries_id = $userDelivery->id;
        $this->invoice->userInvoice->save();

        $this->invoice->userInvoice->setRelation('userDelivery', $userDelivery);
    }

    /**
     * Relate user pre order invoice with vendor invoice
     *
     * @param int $vendorInvoiceId
     */
    public function bindVendorInvoice(int $vendorInvoiceId)
    {
        $this->invoice->userInvoice->vendor_invoices_id = $vendorInvoiceId;
        $this->invoice->userInvoice->save();
    }
}