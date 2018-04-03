<?php
/**
 * Retrieve vendor invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\Vendor;

class VendorReclamationInvoiceRepository extends VendorInvoiceRepository
{
    /**
     * Eager loading relations.
     *
     * @return void
     */
    protected function addRelations()
    {
        parent::addRelations();

        $this->retrieveQuery

            ->with('storageInvoice')

            ->with(['invoiceReclamation.product.vendorProduct' => function ($query) {
                $query->where('vendors_id', $this->vendorId);
            }]);
    }
}