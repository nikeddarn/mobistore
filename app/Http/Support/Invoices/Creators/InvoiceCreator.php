<?php
/**
 * Invoice creator.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Currency\ExchangeRates;
use App\Models\Invoice;
use Illuminate\Database\DatabaseManager;

class InvoiceCreator implements InvoiceTypes, InvoiceDirections
{
    /**
     * @var Invoice
     */
    protected $createdInvoice;

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
     * Make base invoice.
     *
     * @param int $invoiceType
     * @return void
     */
    protected function makeInvoice(int $invoiceType)
    {
        $this->createdInvoice = $this->invoice->create([
            'invoice_types_id' => $invoiceType,
            'rate' => $this->exchangeRates->getRate(),
            'invoice_status_id' => InvoiceStatusInterface::PROCESSING,
        ]);
    }
}