<?php
/**
 * Retrieve vendor invoice with products and reclamations.
 */

namespace App\Http\Support\Invoices\Repositories\Vendor;

class VendorAccountInvoiceRepository extends VendorInvoiceRepository
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

            ->with(['invoiceProduct.product.vendorProduct' => function ($query) {
                $query->where('vendors_id', $this->vendorId);
            }])

            ->with(['invoiceDefectProduct.product.vendorProduct' => function ($query) {
                $query->where('vendors_id', $this->vendorId);
            }]);
    }
}