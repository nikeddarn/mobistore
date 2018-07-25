<?php
/**
 * User payment invoices repository.
 */

namespace App\Http\Support\Invoices\Repositories\User\Payment;


use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Repositories\User\UserInvoiceRepository;
use Illuminate\Database\Eloquent\Builder;

class UserPaymentInvoiceRepository extends UserInvoiceRepository
{
    /**
     *  Array of retrieving invoice types
     */
    const invoiceTypes = [
        InvoiceTypes::USER_PAYMENT,
        InvoiceTypes::USER_RETURN_PAYMENT,
    ];

    /**
     * Make invoice query.
     *
     * @return Builder
     */
    protected function makeQuery(): Builder
    {
        return parent::makeQuery()
            ->whereIn('invoice_types_id', self::invoiceTypes);
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