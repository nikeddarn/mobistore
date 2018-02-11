<?php
/**
 * Common interface for all invoices.
 */

namespace App\Contracts\Shop\Invoices\Handlers;


interface InvoiceHandlerInterface
{
    /**
     * Set is_committed flag of Invoice model to true.
     *
     * @return void
     */
    public function markInvoiceAsCommitted();

    /**
     * Get whole invoice sum.
     *
     * @return float|null
     */
    public function getInvoiceSum();

    /**
     * Get invoice title.
     *
     * @return string
     */
    public function getInvoiceType(): string;
}