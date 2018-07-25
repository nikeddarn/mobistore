<?php
/**
 * Storage invoices retriever.
 */

namespace App\Http\Support\Invoices\Repositories\Storage;

use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StorageInvoiceRepository extends InvoiceRepository
{
    /**
     * @var StorageInvoiceConstraints
     */
    protected $constraints;

    /**
     * Get user invoices.
     *
     * @param StorageInvoiceConstraints $constraints
     * @return LengthAwarePaginator|Collection
     */
    public function getInvoices(StorageInvoiceConstraints $constraints)
    {
        $this->constraints = $constraints;

        $this->prepareRetrieveInvoicesQuery($constraints);

        if ($constraints->withRelations !== false) {
            static::addRelations();
        }

        return $constraints->paginate ? $this->retrieveQuery->paginate($constraints->paginate) : $this->retrieveQuery->get();
    }

    /**
     * Get query for retrieve user invoices.
     *
     * @param StorageInvoiceConstraints $constraints
     * @return Builder
     */
    public function getRetrieveInvoicesQuery(StorageInvoiceConstraints $constraints): Builder
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
     * @param StorageInvoiceConstraints $constraints
     */
    private function prepareRetrieveInvoicesQuery(StorageInvoiceConstraints $constraints)
    {
        parent::makeRetrieveInvoiceQuery();

        $this->setStorageIdConstraint($constraints->storageId);

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

        $this->setInvoiceHasStorageConstraint();

        static::addRelations();
    }

    /**
     * Set has user with id.
     *
     * @param int|null $storageId
     * @return void
     */
    private function setStorageIdConstraint(int $storageId)
    {
        $this->retrieveQuery->whereHas('storageInvoice', function ($query) use ($storageId) {
            $query->where('storages_id', $storageId);
        });
    }

    /**
     * Set invoice has user constraint.
     *
     * @return void
     */
    private function setInvoiceHasStorageConstraint()
    {
        $this->retrieveQuery->has('storageInvoice');
    }

    /**
     * Set invoice status constraint.
     *
     * @param $invoiceStatus
     */
    private function setInvoiceStatusConstraint($invoiceStatus)
    {
        $this->retrieveQuery->where('invoice_status_id', $invoiceStatus);
    }

    /**
     * Set invoice type constraint.
     *
     * @param int|array $invoiceType
     * @return void
     */
    private function setInvoiceTypeConstraint($invoiceType)
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
        $this->retrieveQuery->whereHas('storageInvoice', function ($query) use ($direction) {
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
        $this->retrieveQuery->whereHas('storageInvoice', function ($query) use ($implemented) {
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
        $this->retrieveQuery->with('storageInvoice', 'invoiceType', 'invoiceStatus');
    }
}