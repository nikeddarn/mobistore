<?php
/**
 * Methods for base invoice handling.
 */

namespace App\Http\Support\Invoices\Handlers;


use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use App\Http\Support\Price\ProductPrice;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;

abstract class InvoiceHandler implements InvoiceHandlerInterface
{
    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var ProductPrice
     */
    protected $productPrice;

    /**
     * InvoiceHandler constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param ProductPrice $productPrice
     */
    public function __construct(DatabaseManager $databaseManager, ProductPrice $productPrice )
    {
        $this->databaseManager = $databaseManager;
        $this->productPrice = $productPrice;
    }

    /**
     * Bind given invoice to this handler.
     *
     * @param Invoice $invoice
     * @return $this
     */
    public function bindInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice id.
     *
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->invoice->id;
    }

    /**
     * Is invoice committed ?
     *
     * @return bool
     */
    public function isInvoiceCommitted(): bool
    {
        return (bool)$this->invoice->implemented;
    }

    /**
     * Get invoice last update time.
     *
     * @return Carbon
     */
    public function getUpdateTime()
    {
        return $this->invoice->updated_at;
    }

    /**
     * Set is_committed flag of Invoice model to true.
     *
     * @return bool
     */
    public function markInvoiceAsCommitted(): bool
    {
        $this->invoice->implemented = true;
        return $this->invoice->save();
    }

    /**
     * Get total invoice sum.
     *
     * @return float
     */
    public function getInvoiceSum():float
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
        return $this->invoice->invoice_sum * $this->invoice->rate;
    }

    /**
     * Get total invoice sum.
     *
     * @return float
     */
    public function getInvoiceDeliverySum():float
    {
        return $this->invoice->delivery_sum;
    }

    /**
     * Get total invoice sum in UAH.
     *
     * @return float
     */
    public function getInvoiceDeliveryUahSum(): float
    {
        return $this->invoice->delivery_sum * $this->invoice->rate;
    }

    /**
     * Set invoice delivery sum.
     *
     * @param float $deliverySum
     */
    public function setInvoiceDeliverySum(float $deliverySum)
    {
        $this->invoice->delivery_sum = $deliverySum;
        $this->invoice->save();
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
        $this->invoice->rate = $this->productPrice->getRate();
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