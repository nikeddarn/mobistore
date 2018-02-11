<?php
/**
 * Methods for base invoice handling.
 */

namespace App\Http\Support\Invoices\Handlers;


use App\Http\Support\Currency\ExchangeRates;
use App\Models\Invoice;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

abstract class InvoiceHandler
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
     * InvoiceHandler constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param ExchangeRates $exchangeRates
     */
    public function __construct(DatabaseManager $databaseManager, ExchangeRates $exchangeRates)
    {
        $this->exchangeRates = $exchangeRates;
        $this->databaseManager = $databaseManager;
    }

    public function bindInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Is invoice committed ?
     *
     * @return bool
     */
    public function isInvoiceCommitted()
    {
        return (bool)$this->invoice->is_committed;
    }

    /**
     * Set is_committed flag of Invoice model to true.
     *
     * @return bool
     */
    public function markInvoiceAsCommitted(): bool
    {
        $this->invoice->is_committed = true;
        return $this->invoice->save();
    }

    /**
     * Get total invoice sum.
     *
     * @return float|null
     */
    public function getInvoiceSum()
    {
        return $this->invoice->invoice_sum;
    }

    /**
     * Get total invoice sum in UAH.
     *
     * @return float
     */
    public function getInvoiceUahSum(): float
    {
        return $this->invoice->invoice_sum * $this->invoice->currencyRate->rate;
    }

    /**
     * Get invoice title.
     *
     * @return string
     */
    public function getInvoiceType(): string
    {
        if (!$this->invoice->invoiceType) {
            $this->invoice->load('invoiceType');
        }

        return $this->invoice->invoiceType;
    }

    /**
     * Update currency rate of invoice.
     *
     * @return void
     */
    public function updateInvoiceExchangeRate()
    {
        $this->invoice->currency_rates_id = $this->exchangeRates->getCurrencyRateModelId();
        $this->invoice->save();
    }

    /**
     * Increase invoice sum.
     *
     * @param float $sum
     */
    protected function increaseInvoiceSum(float $sum)
    {
        // increase total invoice sum
        $this->invoice->invoice_sum += $sum;
        $this->invoice->save();
    }

    /**
     * Decrease invoice sum.
     *
     * @param float $sum
     */
    protected function decreaseInvoiceSum(float $sum)
    {
        $this->invoice->invoice_sum -= $sum;
        $this->invoice->save();
    }
}