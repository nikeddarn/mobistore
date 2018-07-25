<?php
/**
 * User product invoices repository.
 */

namespace App\Http\Support\Invoices\Repositories\User\Product;


use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Repositories\User\UserInvoiceRepository;
use Illuminate\Database\Eloquent\Builder;

class UserProductInvoiceRepository extends UserInvoiceRepository
{
    /**
     *  Array of retrieving invoice types
     */
    const invoiceTypes = [
        InvoiceTypes::USER_ORDER,
        InvoiceTypes::USER_PRE_ORDER,
        InvoiceTypes::USER_RETURN_ORDER,
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
        return $query->with('invoiceProducts.product.primaryImage');
    }
}