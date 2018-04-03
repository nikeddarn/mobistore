<?php
/**
 * Retrieve user invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\User;


class UserAccountInvoiceRepository extends UserInvoiceRepository
{
    /**
     * Eager loading relations.
     *
     * @return void
     */
    protected function addRelations()
    {
        parent::addRelations();
        $this->retrieveQuery->with('invoiceProduct.product', 'invoiceReclamation.product', 'storageInvoice');
    }
}