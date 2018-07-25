<?php
/**
 * Methods for handling any invoice.
 */

namespace App\Contracts\Shop\Invoices\Handlers;


use App\Models\Invoice;
use Carbon\Carbon;

interface InvoiceHandlerInterface
{
    /**
     * Bind given invoice to this handler.
     *
     * @param Invoice $invoice
     * @return $this
     */
    public function bindInvoice(Invoice $invoice);

    /**
     * Get handling invoice.
     *
     * @return Invoice
     */
    public function getInvoice(): Invoice;

    /**
     * Get invoice id.
     *
     * @return int
     */
    public function getInvoiceId();

    /**
     * Get invoice creation time.
     *
     * @return Carbon
     */
    public function getCreateTime();

    /**
     * Get invoice last update time.
     *
     * @return Carbon
     */
    public function getUpdateTime();

    /**
     * Get whole invoice sum.
     *
     * @return float
     */
    public function getInvoiceSum(): float;

    /**
     * Get total invoice sum in local cash.
     *
     * @return float
     */
    public function getInvoiceLocalSum(): float;

    /**
     * Get total invoice sum.
     *
     * @return float
     */
    public function getInvoiceDeliverySum(): float;

    /**
     * Get total invoice sum in UAH.
     *
     * @return float
     */
    public function getInvoiceDeliveryUahSum(): float;

    /**
     * Get invoice title.
     *
     * @return string
     */
    public function getInvoiceType(): string;

    /**
     * Get invoice status.
     *
     * @return string
     */
    public function getInvoiceStatus(): string;
}