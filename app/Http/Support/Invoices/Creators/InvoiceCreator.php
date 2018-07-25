<?php
/**
 * Invoice creator.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Http\Support\Currency\ExchangeRates;
use App\Models\Invoice;
use Illuminate\Database\DatabaseManager;

abstract class InvoiceCreator
{
    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var ExchangeRates
     */
    private $exchangeRates;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * Invoice creator constructor.
     *
     * @param Invoice $invoice
     * @param ExchangeRates $exchangeRates
     * @param DatabaseManager $databaseManager
     */
    public function __construct(Invoice $invoice, ExchangeRates $exchangeRates, DatabaseManager $databaseManager)
    {
        $this->invoice = $invoice;
        $this->exchangeRates = $exchangeRates;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Make invoice.
     *
     * @return Invoice
     */
    protected function makeInvoice():Invoice
    {
        return $this->invoice->create(static::getInvoiceData());
    }

    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return [
            'rate' => $this->exchangeRates->getRate(),
            'invoice_status_id' => InvoiceStatusInterface::PROCESSING,
        ];
    }
}