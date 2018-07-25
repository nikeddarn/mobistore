<?php
/**
 * User active reclamation invoices repository.
 */

namespace App\Http\Support\Invoices\Repositories\User\Reclamation;


use Illuminate\Database\Eloquent\Builder;

class UserActiveReclamationInvoiceRepository extends UserReclamationInvoiceRepository
{
    /**
     * Make invoice query.
     *
     * @return Builder
     */
    protected function makeQuery(): Builder
    {
        return parent::makeQuery()
            ->has('reclamations.userActiveReclamation');
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