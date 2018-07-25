<?php
/**
 * User invoices repository.
 */

namespace App\Http\Support\Invoices\Repositories\User;


use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserInvoiceRepository extends InvoiceRepository
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * Get user invoices.
     *
     * @param int $userId
     * @return Collection
     */
    public function getInvoices(int $userId): Collection
    {
        $this->userId = $userId;

        $query = static::makeQuery();

        $query = static::addRelations($query);

        return $query->get();
    }

    /**
     * Get invoice query.
     *
     * @param int $userId
     * @return Builder
     */
    public function getQuery(int $userId): Builder
    {
        $this->userId = $userId;

        $query = static::makeQuery();

        $query = static::addRelations($query);

        return $query;
    }

    /**
     * Make invoice query.
     *
     * @return Builder
     */
    protected function makeQuery(): Builder
    {
        return parent::makeQuery()
            ->whereHas('userInvoices', function ($query) {
                $query->where('users_id', $this->userId);
            });
    }

    /**
     * Add default user relations to query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function addRelations(Builder $query): Builder
    {
        return $query->with('userInvoice');
    }
}