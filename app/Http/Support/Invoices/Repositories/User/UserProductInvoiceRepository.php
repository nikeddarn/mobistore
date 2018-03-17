<?php
/**
 * Retrieve user invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\User;


class UserProductInvoiceRepository extends UserInvoiceRepository
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