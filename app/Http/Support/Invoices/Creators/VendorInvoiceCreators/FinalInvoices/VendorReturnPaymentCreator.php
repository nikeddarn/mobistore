<?php
/**
 * Create Vendor order invoice.
 */

namespace App\Http\Support\Invoices\Creators\VendorInvoiceCreators\FinalInvoices;


use App\Contracts\Shop\Invoices\Creators\InvoiceCreatorInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Creators\VendorInvoiceCreators\StorageVendorInvoiceCreator;

class VendorReturnPaymentCreator extends StorageVendorInvoiceCreator implements InvoiceCreatorInterface
{
    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return array_merge(parent::getInvoiceData(), [
            'invoice_types_id' => InvoiceTypes::VENDOR_RETURN_PAYMENT,
        ]);
    }

    /**
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getVendorInvoiceDataInvoiceData():array
    {
        return array_merge(parent::getVendorInvoiceData(), [
            'direction' => InvoiceDirections::OUTGOING,
        ]);
    }

    /**
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getStorageInvoiceData():array
    {
        return array_merge(parent::getStorageInvoiceData(), [
            'direction' => InvoiceDirections::INCOMING,
        ]);
    }
}