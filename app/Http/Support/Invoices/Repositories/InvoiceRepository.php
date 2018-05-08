<?php
/**
 * Common invoice repository.
 */

namespace App\Http\Support\Invoices\Repositories;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\Repositories\InvoiceRepositoryInterface;
use App\Models\Invoice;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class InvoiceRepository implements InvoiceRepositoryInterface, InvoiceDirections
{
    /**
     * @var Invoice
     */
    protected $retrievedInvoice = null;

    /**
     * @var Builder
     */
    protected $retrieveQuery;

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * Invoice repository constructor.
     *
     * @param Invoice $invoice
     * @param DatabaseManager $databaseManager
     */
    public function __construct(Invoice $invoice, DatabaseManager $databaseManager)
    {
        $this->invoice = $invoice;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Is invoice with given id exist ?
     *
     * @param int $invoiceId
     * @return bool
     */
    public function exists(int $invoiceId): bool
    {
        static::buildRetrieveQueryByInvoiceId($invoiceId);

        $this->retrievedInvoice = $this->retrieveQuery->first();

        return (bool)$this->retrievedInvoice;
    }

    /**
     * Get retrieved invoice.
     *
     * @return Invoice
     */
    public function getRetrievedInvoice()
    {
        return $this->retrievedInvoice;
    }

    /**
     * Get user invoice by its id.
     *
     * @param int $invoiceId
     * @return Model
     */
    public function getByInvoiceId(int $invoiceId):Invoice
    {
        if (isset($this->retrievedInvoice)) {
            return $this->retrievedInvoice;
        } else {
            static::buildRetrieveQueryByInvoiceId($invoiceId);

            $this->retrievedInvoice = $this->retrieveQuery->first();

            return $this->retrievedInvoice;
        }
    }

    /**
     * Delete invoice by its id.
     *
     * @param int $invoiceId
     * @return bool
     * @throws Exception
     */
    public function deleteByInvoiceId(int $invoiceId)
    {
        assert($invoiceId > 0, 'Invoice id must be positive integer');

        static::buildRetrieveQueryByInvoiceId($invoiceId);

        $invoice = $this->retrieveQuery->first();

        return static::removeInvoiceData($invoice);
    }

    /**
     * Delete given invoice.
     *
     * @param Invoice $invoice
     * @return bool|null
     * @throws Exception
     */
    public function deleteInvoice(Invoice $invoice)
    {
        assert($invoice instanceof Invoice, 'Deleting object must be instance of App\Models\Invoice');

        return static::removeInvoiceData($invoice);
    }

    /**
     * Delete invoice that was retrieved by this class.
     *
     * @return bool
     * @throws Exception
     */
    public function deleteRetrievedInvoice()
    {
        assert($this->retrievedInvoice instanceof Invoice, 'Retrieved invoice does not exist');

        return static::removeInvoiceData($this->retrievedInvoice);
    }

    /**
     * Prepare query to retrieve invoice by its id.
     *
     * @param int $invoiceId
     * @return void
     */
    protected function buildRetrieveQueryByInvoiceId(int $invoiceId)
    {
        $this->makeRetrieveInvoiceQuery();
        $this->setInvoiceIdConstraint($invoiceId);
    }

    /**
     * Make base retrieve invoice query.
     *
     * @return void
     */
    protected function makeRetrieveInvoiceQuery()
    {
        $this->retrieveQuery = $this->invoice->select();
    }

    /**
     * Add where (invoice id) constraint to invoice query.
     *
     * @param int $invoiceId
     * @return void
     */
    private function setInvoiceIdConstraint(int $invoiceId)
    {
        $this->retrieveQuery->where('id', $invoiceId);
    }

    /**
     * Remove products reserve from storage. Destroy invoice.
     *
     * @param Invoice|Model $invoice
     * @return bool
     * @throws Exception
     */
    protected function removeInvoiceData(Invoice $invoice)
    {
        return (bool)$invoice->delete();
    }
}