<?php
/**
 * Vendor invoices retriever.
 */

namespace App\Http\Support\Invoices\Repositories\Vendor;

use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class VendorInvoiceRepository extends InvoiceRepository
{
    /**
     * @var int
     */
    protected $vendorId;

    /**
     * Get vendor invoices.
     *
     * @param  $constraints
     * @return LengthAwarePaginator|Collection
     */
    public function getInvoices(VendorInvoiceConstraints $constraints)
    {
        $this->prepareRetrieveInvoicesQuery($constraints);

        if ($constraints->withRelations !== false){
            static::addRelations();
        }

        return $constraints->paginate ? $this->retrieveQuery->paginate($constraints->paginate) : $this->retrieveQuery->get();
    }

    /**
     * Get query for retrieve vendor invoices.
     *
     * @param VendorInvoiceConstraints $constraints
     * @return Builder
     */
    public function getRetrieveInvoicesQuery(VendorInvoiceConstraints $constraints):Builder
    {
        $this->prepareRetrieveInvoicesQuery($constraints);

        if ($constraints->withRelations){
            static::addRelations();
        }

        return  $this->retrieveQuery;
    }

    /**
     * Prepare retrieve invoice query.
     *
     * @param VendorInvoiceConstraints $constraints
     */
    private function prepareRetrieveInvoicesQuery(VendorInvoiceConstraints $constraints)
    {
        $this->vendorId = $constraints->vendorId;

        parent::makeRetrieveInvoiceQuery();

        $this->setVendorIdConstraint($constraints->vendorId);

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

        $this->setInvoiceHasVendorConstraint();

        static::addRelations();
    }

    /**
     * Set has vendor with id.
     *
     * @param int|null $vendorId
     * @return void
     */
    private function setVendorIdConstraint(int $vendorId)
    {
        $this->retrieveQuery->whereHas('vendorInvoice', function ($query) use ($vendorId) {
            $query->where('vendors_id', $vendorId);
        });
    }

    /**
     * Set invoice has vendor constraint.
     *
     * @return void
     */
    private function setInvoiceHasVendorConstraint()
    {
        $this->retrieveQuery->has('vendorInvoice');
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
     * @param int $invoiceType
     * @return void
     */
    private function setInvoiceTypeConstraint($invoiceType)
    {
        if (is_array($invoiceType)){

            $this->retrieveQuery->whereIn('invoice_types_id', $invoiceType);

        }elseif(is_int($invoiceType)){

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
        $this->retrieveQuery->whereHas('vendorInvoice', function ($query) use($direction) {
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
        $this->retrieveQuery->whereHas('vendorInvoice', function ($query) use($implemented) {
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
        $this->retrieveQuery->with('vendorInvoice', 'invoiceType', 'invoiceStatus');
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