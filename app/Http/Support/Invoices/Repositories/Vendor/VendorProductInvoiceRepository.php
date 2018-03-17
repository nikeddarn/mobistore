<?php
/**
 * Retrieve vendor invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\User;


use App\Http\Support\Invoices\Repositories\Vendor\VendorInvoiceRepository;

class VendorProductInvoiceRepository extends VendorInvoiceRepository
{
    /**
     * Eager loading relations.
     *
     * @return void
     */
    protected function addRelations()
    {
        parent::addRelations();
        $this->retrieveQuery->with('invoiceProduct')->with('storageInvoice');
    }
}