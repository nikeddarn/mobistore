<?php
/**
 * Create write off vendor reclamation invoice.
 */

namespace App\Http\Support\Invoices\Creators\UserInvoiceCreators\FinalInvoices;


use App\Contracts\Shop\Invoices\Creators\InvoiceCreatorInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Creators\VendorInvoiceCreators\VendorInvoiceCreator;

class VendorWriteOffReclamationCreator  extends VendorInvoiceCreator implements InvoiceCreatorInterface
{
    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return array_merge(parent::getInvoiceData(), [
            'invoice_types_id' => InvoiceTypes::VENDOR_WRITE_OFF_RECLAMATION,
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
            'direction' => InvoiceDirections::INCOMING,
        ]);
    }
}