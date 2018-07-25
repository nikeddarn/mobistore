<?php
/**
 * Methods for base invoice handling.
 */

namespace App\Http\Support\Invoices\Handlers;


use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceHandler implements InvoiceHandlerInterface
{
    /**
     * @var Invoice
     */
    protected $invoice;

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
     * Get handling invoice.
     *
     * @return Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
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
     * Get invoice creation time.
     *
     * @return Carbon
     */
    public function getCreateTime()
    {
        return $this->invoice->created_at;
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
     * Get total invoice sum.
     *
     * @return float
     */
    public function getInvoiceSum(): float
    {
        return $this->invoice->invoice_sum;
    }

    /**
     * Get total invoice sum in local cash.
     *
     * @return float
     */
    public function getInvoiceLocalSum(): float
    {
        return $this->invoice->invoice_sum * $this->invoice->rate;
    }

    /**
     * Get total invoice sum.
     *
     * @return float
     */
    public function getInvoiceDeliverySum(): float
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
     * Get invoice title.
     *
     * @return string
     */
    public function getInvoiceType(): string
    {
        return $this->invoice->invoiceType;
    }

    /**
     * Get invoice status.
     *
     * @return string
     */
    public function getInvoiceStatus(): string
    {
        return $this->invoice->invoiceStatus;
    }

    /**
     * Get exchange rate.
     *
     * @return float
     */
    public function getExchangeRate():float
    {
        return $this->invoice->rate;
    }
}