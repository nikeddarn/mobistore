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
     * Is invoice committed ?
     *
     * @return bool
     */
    public function isInvoiceCommitted(): bool;

    /**
     * Get invoice last update time.
     *
     * @return Carbon
     */
    public function getUpdateTime();

    /**
     * Set is_committed flag of Invoice model to true.
     *
     * @return bool
     */
    public function markInvoiceAsCommitted():bool ;

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
}