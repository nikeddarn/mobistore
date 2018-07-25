<?php
/**
 * User active payment invoices repository.
 */

namespace App\Http\Support\Invoices\Repositories\User\Payment;


use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use Illuminate\Database\Eloquent\Builder;

class UserActivePaymentInvoiceRepository extends UserPaymentInvoiceRepository
{
    /**
     * Make invoice query.
     *
     * @return Builder
     */
    protected function makeQuery(): Builder
    {
        return parent::makeQuery()
            ->where('invoice_status_id', InvoiceStatusInterface::PROCESSING);
    }

    /**
     * Add relations to query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function addRelations(Builder $query): Builder
    {
        return $query;
    }
}