<?php
/**
 * Retrieve vendor invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\Vendor;

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

        $this->retrieveQuery->with(['invoiceProduct.product.vendorProduct' => function ($query) {
                $query->where('vendors_id', $this->vendorId);
            }]);
    }
}