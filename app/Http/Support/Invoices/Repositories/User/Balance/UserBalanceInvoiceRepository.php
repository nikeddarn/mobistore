<?php
/**
 * User balance invoices repository.
 */

namespace App\Http\Support\Invoices\Repositories\User;


use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use Illuminate\Database\Eloquent\Builder;

class UserBalanceInvoiceRepository extends UserInvoiceRepository
{
    /**
     * Make invoice query.
     *
     * @return Builder
     */
    protected function makeQuery(): Builder
    {
        return parent::makeQuery()
            ->where('invoice_status_id', InvoiceStatusInterface::FINISHED);
    }

    /**
     * Add default user relations to query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function addRelations(Builder $query): Builder
    {
        return parent::addRelations($query)->with('invoiceProducts.product', 'invoiceDefectProducts.reclamation.product', 'invoiceType');
    }
}