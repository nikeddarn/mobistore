<?php
/**
 * User invoices retriever.
 */

namespace App\Http\Support\Invoices\Repositories\User;

use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserInvoiceRepository extends InvoiceRepository
{
    /**
     * Get user invoices.
     *
     * @param UserInvoiceConstraints $constraints
     * @return LengthAwarePaginator|Collection
     */
    public function getInvoices(UserInvoiceConstraints $constraints)
    {
        $this->prepareRetrieveInvoicesQuery($constraints);

        if ($constraints->withRelations !== false) {
            static::addRelations();
        }

        return $constraints->paginate ? $this->retrieveQuery->paginate($constraints->paginate) : $this->retrieveQuery->get();
    }

    /**
     * Get query for retrieve user invoices.
     *
     * @param UserInvoiceConstraints $constraints
     * @return Builder
     */
    public function getRetrieveInvoicesQuery(UserInvoiceConstraints $constraints): Builder
    {
        $this->prepareRetrieveInvoicesQuery($constraints);

        if ($constraints->withRelations) {
            static::addRelations();
        }

        return $this->retrieveQuery;
    }

    /**
     * Prepare retrieve invoice query.
     *
     * @param UserInvoiceConstraints $constraints
     */
    private function prepareRetrieveInvoicesQuery(UserInvoiceConstraints $constraints)
    {
        parent::makeRetrieveInvoiceQuery();

        $this->setUserIdConstraint($constraints->userId);

        if ($constraints->invoiceStatus) {
            $this->setInvoiceStatusConstraint($constraints->invoiceStatus);
        }

        if ($constraints->invoiceType) {
            $this->setInvoiceTypeConstraint($constraints->invoiceType);
        }

        if ($constraints->invoiceDirection) {
            $this->setDirectionConstraint($constraints->invoiceDirection);
        }

        if ($constraints->implementedStatus !== null) {
            $this->setImplementedConstraint($constraints->implementedStatus);
        }
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
        if (is_array($invoiceType)) {
            $this->retrieveQuery->whereIn('invoice_types_id', $invoiceType);
        } elseif (is_int($invoiceType)) {
            $this->retrieveQuery->where('invoice_types_id', $invoiceType);
        }
    }

    /**
     * Set invoice direction constraint.
     *
     * @param string $direction
     * @return void
     */
    private function setDirectionConstraint(string $direction)
    {
        $this->retrieveQuery->whereHas('userInvoice', function ($query) use ($direction) {
            $query->where('direction', $direction);
        });
    }

    /**
     * Set invoice implemented constraint.
     *
     * @param int $implemented
     * @return void
     */
    private function setImplementedConstraint(int $implemented)
    {
        $this->retrieveQuery->whereHas('userInvoice', function ($query) use ($implemented) {
            $query->where('implemented', (int)$implemented);
        });
    }

    /**
     * Eager loading relations.
     *
     * @return void
     */
    protected function addRelations()
    {
        $this->retrieveQuery->with('userInvoice', 'invoiceType', 'invoiceStatus');
    }

    /**
     * Destroy invoice.
     *
     * @param Invoice|Model $invoice
     * @return bool
     * @throws \Exception
     */
    protected function removeInvoiceData(Invoice $invoice)
    {
        return parent::removeInvoiceData($invoice);
    }
}