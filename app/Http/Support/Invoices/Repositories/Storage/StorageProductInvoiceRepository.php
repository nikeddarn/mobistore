<?php
/**
 * Retrieve user invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\Storage;


class StorageProductInvoiceRepository extends StorageInvoiceRepository
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
            ->with('invoiceProduct.product')
            ->with(['storage' => function ($query) {
                $query->wherePivot('storages_id', $this->constraints->storageId);
                $query->with('storageProduct');
            }]);
    }
}