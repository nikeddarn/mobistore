<?php
/**
 * User invoices retriever.
 */

namespace App\Http\Support\Invoices\Repositories\User;

use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use App\Contracts\Shop\Invoices\Repositories\OwnerInvoiceRepository;

class UserInvoiceRepository extends InvoiceRepository implements OwnerInvoiceRepository
{
    /**
     * @param int $ownerId User's id
     * @param int|null $invoiceType
     * @param int $limit
     */
    public function getLastInvoices(int $ownerId, int $invoiceType = null, $limit = 1)
    {
        parent::buildRetrieveInvoiceQueryWithLimit($limit);
        $this->setUserIdConstraint($ownerId);
        $this->setInvoiceTypeConstraint($invoiceType);
        static::addRelations();
    }

    /**
     * Prepare query to retrieve invoice by its id.
     *
     * @param int $invoiceId
     * @return void
     */
    protected function buildRetrieveQueryByInvoiceId(int $invoiceId)
    {
        parent::buildRetrieveQueryByInvoiceId($invoiceId);
        $this->setInvoiceHasUserConstraint();
        static::addRelations();
    }

    /**
     * Set has user with id.
     *
     * @param int|null $userId
     * @return void
     */
    private function setUserIdConstraint(int $userId)
    {
        $this->retrieveQuery->whereHas('userInvoice', function ($query) use ($userId) {
            $query->where('users_id', $userId);
        });
    }

    /**
     * Set invoice has user constraint.
     *
     * @return void
     */
    private function setInvoiceHasUserConstraint()
    {
        $this->retrieveQuery->has('userInvoice');
    }

    /**
     * Set invoice type constraint.
     *
     * @param int $invoiceType
     * @return void
     */
    private function setInvoiceTypeConstraint(int $invoiceType)
    {
        $this->retrieveQuery->where('invoice_types_id', $invoiceType);
    }

    /**
     * Eager loading relations.
     *
     * @return void
     */
    protected function addRelations()
    {
        $this->retrieveQuery->with('userInvoice');
    }
}