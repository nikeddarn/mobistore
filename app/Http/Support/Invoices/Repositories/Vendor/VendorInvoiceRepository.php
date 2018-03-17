<?php
/**
 * Vendor invoices retriever.
 */

namespace App\Http\Support\Invoices\Repositories\Vendor;

use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use App\Contracts\Shop\Invoices\Repositories\OwnerInvoiceRepository;

class VendorInvoiceRepository extends InvoiceRepository implements OwnerInvoiceRepository
{
    /**
     * @param int $ownerId Vendor's id
     * @param int|null $invoiceType
     * @param int $limit
     */
    public function getLastInvoices(int $ownerId, int $invoiceType = null, $limit = 1)
    {
        parent::buildRetrieveInvoiceQueryWithLimit($limit);
        $this->setVendorIdConstraint($ownerId);
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
        $this->retrieveQuery->with('vendorInvoice');
    }
}