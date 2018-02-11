<?php
/**
 * Common invoice repository.
 */

namespace App\Http\Support\Invoice\Repository;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Currency\ExchangeRates;
use App\Models\Invoice;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InvoiceRepository implements InvoiceTypes, InvoiceDirections
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var ExchangeRates
     */
    private $exchangeRates;

    /**
     * Invoice repository constructor.
     *
     * @param Request $request
     * @param Invoice $invoice
     * @param DatabaseManager $databaseManager
     * @param ExchangeRates $exchangeRates
     */
    public function __construct(Request $request, Invoice $invoice, DatabaseManager $databaseManager, ExchangeRates $exchangeRates)
    {

        $this->request = $request;
        $this->invoice = $invoice;
        $this->databaseManager = $databaseManager;
        $this->exchangeRates = $exchangeRates;
    }

    /**
     * Is invoice with given id exist ?
     *
     * @param int $invoiceId
     * @return bool
     */
    public function exists(int $invoiceId): bool
    {
        return (bool)self::getRetrieveQueryByInvoiceId($invoiceId)->first();
    }

    /**
     * Get user invoice by its id.
     *
     * @param int $invoiceId
     * @return Model
     */
    public function getByInvoiceId(int $invoiceId): Model
    {
        $query = static::getRetrieveQueryByInvoiceId($invoiceId);

        return $query->first();
    }


    /**
     * Delete user's basket.
     *
     * @param int $invoiceId
     * @return void
     */
    public function deleteByInvoiceId(int $invoiceId)
    {
        self::getRetrieveQueryByInvoiceId($invoiceId)->delete();
    }

    /**
     * Delete invoice.
     *
     * @param Invoice $invoice
     * @return bool|null
     * @throws \Exception
     */
    public function deleteInvoice(Invoice $invoice)
    {
        return $invoice->delete();
    }

    /**
     * Prepare query to retrieve invoice by its id.
     *
     * @param int $invoiceId
     * @return Builder
     */
    protected function getRetrieveQueryByInvoiceId(int $invoiceId):Builder
    {
        $query = $this->makeRetrieveInvoiceQuery();
        $query = $this->setInvoiceIdConstraint($query, $invoiceId);

        return $query;
    }

    /**
     * Prepare query to retrieve invoice with limit.
     *
     * @param int $limit
     * @return Builder
     */
    protected function getRetrieveQueryWithLimit(int $limit = 1):Builder
    {
        $query = $this->makeRetrieveInvoiceQuery();
        $query = $this->setCountLimitConstraint($query, $limit);

        return $query;
    }

    /**
     * Make base invoice.
     *
     * @param int $invoiceType
     * @param string $invoiceDirection
     * @return Invoice
     */
    protected function makeInvoice(int $invoiceType, string $invoiceDirection):Invoice
    {
        return $this->invoice->create([
            'invoice_types_id' => $invoiceType,
            'direction' => $invoiceDirection,
            'currency_rates_id' => $this->exchangeRates->getCurrencyRateModelId(),
        ]);
    }

    /**
     * Make base retrieve invoice query.
     *
     * @return Builder
     */
    private function makeRetrieveInvoiceQuery(): Builder
    {
        return $this->invoice->select();
    }

    /**
     * Add where (invoice id) constraint to invoice query.
     *
     * @param Builder $query
     * @param int $invoiceId
     * @return Builder
     */
    private function setInvoiceIdConstraint(Builder $query, int $invoiceId):Builder
    {
        return $query->where('id', $invoiceId);
    }

    /**
     * Add limit constraint to invoice query.
     *
     * @param Builder $query
     * @param int $limit
     * @return Builder
     */
    private function setCountLimitConstraint(Builder $query, int $limit): Builder
    {
        return $query->limit($limit);
    }
}