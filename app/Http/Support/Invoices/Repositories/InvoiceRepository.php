<?php
/**
 * Common invoice repository.
 */

namespace App\Http\Support\Invoices\Repositories;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\Repositories\InvoiceRepositoryInterface;
use App\Http\Support\Currency\ExchangeRates;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Storage;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class InvoiceRepository implements InvoiceRepositoryInterface, InvoiceDirections
{
    /**
     * @var ExchangeRates
     */
    private $exchangeRates;

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
    private $databaseManager;

    /**
     * Invoice repository constructor.
     *
     * @param Invoice $invoice
     * @param ExchangeRates $exchangeRates
     * @param DatabaseManager $databaseManager
     */
    public function __construct(Invoice $invoice, ExchangeRates $exchangeRates, DatabaseManager $databaseManager)
    {
        $this->exchangeRates = $exchangeRates;
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
    public function getByInvoiceId(int $invoiceId): Model
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
     * Prepare query to retrieve invoice with limit.
     *
     * @param int $limit
     * @return void
     */
    protected function buildRetrieveInvoiceQueryWithLimit(int $limit = 1)
    {
        $this->makeRetrieveInvoiceQuery();
        $this->setCountLimitConstraint($limit);
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
     * Add limit constraint to invoice query.
     *
     * @param int $limit
     * @return void
     */
    private function setCountLimitConstraint(int $limit)
    {
        $this->retrieveQuery->limit($limit);
    }

    /**
     * Remove products reserve from storage. Destroy invoice.
     *
     * @param Invoice|Model $invoice
     * @return bool
     */
    protected function removeInvoiceData(Invoice $invoice)
    {
        try {
            $this->databaseManager->beginTransaction();

            // remove products reserve from storage if exists
            if ($invoice->invoiceProduct->count()){
                $this->removeReservedProductsOnStorage($invoice);
            }

            // delete invoice
            $invoice->delete();

            $this->databaseManager->commit();

            return true;
        } catch (Exception $e) {
            $this->databaseManager->rollback();
            return false;
        }
    }

    /**
     * Remove reserve for this invoice products from storage.
     *
     * @param Invoice $removingInvoice
     */
    protected function removeReservedProductsOnStorage(Invoice $removingInvoice)
    {
        $outgoingStorage = $this->getOutgoingStorage($removingInvoice);

        if ($outgoingStorage) {

            $storageProducts = $outgoingStorage->storageProduct->keyBy('products_id');

            $removingInvoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct) use ($storageProducts){
                $currentStorageProduct = $storageProducts->get($invoiceProduct->products_id);
                $currentStorageProduct->reserved_quantity = max(($currentStorageProduct->reserved_quantity - $invoiceProduct->quantity), 0);
                $currentStorageProduct->save();
            });
        }
    }

    /**
     * Get outgoing storage from invoice.
     *
     * @param Invoice $invoice
     * @return Storage|null
     */
    private function getOutgoingStorage(Invoice $invoice)
    {
        $outgoingStorageInvoice = $invoice->storageInvoice;

        if ($outgoingStorageInvoice->direction === self::OUTGOING){
            return $outgoingStorageInvoice->storage;
        }else{
            return null;
        }
    }
}