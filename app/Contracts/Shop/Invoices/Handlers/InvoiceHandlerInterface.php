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
    public function getInvoice():Invoice;

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
    public function getInvoiceSum():float ;

    /**
     * Get total invoice sum in UAH.
     *
     * @return float
     */
    public function getInvoiceUahSum(): float;

    /**
     * Get invoice title.
     *
     * @return string
     */
    public function getInvoiceType(): string;

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
     * Set invoice delivery sum.
     *
     * @param float $deliverySum
     */
    public function setInvoiceDeliverySum(float $deliverySum);
}