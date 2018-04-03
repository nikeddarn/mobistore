<?php
/**
 * Retrieve user invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\User;


class UserReclamationInvoiceRepository extends UserInvoiceRepository
{
    /**
     * Eager loading relations.
     *
     * @return void
     */
    protected function addRelations()
    {
        parent::addRelations();
        $this->retrieveQuery->with('invoiceReclamation.product', 'storageInvoice');
    }
}