<?php
/**
 * User reclamation invoices repository.
 */

namespace App\Http\Support\Invoices\Repositories\User\Reclamation;


use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Repositories\User\UserInvoiceRepository;
use Illuminate\Database\Eloquent\Builder;

class UserReclamationInvoiceRepository extends UserInvoiceRepository
{
    /**
     *  Array of retrieving invoice types
     */
    const invoiceTypes = [
        InvoiceTypes::USER_RECLAMATION,
        InvoiceTypes::USER_RETURN_RECLAMATION,
        InvoiceTypes::USER_EXCHANGE_RECLAMATION,
        InvoiceTypes::USER_WRITE_OFF_RECLAMATION,
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
        return $query->with('reclamations.product.primaryImage');
    }
}