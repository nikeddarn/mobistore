<?php
/**
 * Methods for invoice notification only.
 */

namespace App\Notifications\Invoices;


trait InvoiceNotification
{
    /**
     * Get invoice id.
     *
     * @return int
     */
    public function getInvoiceId():int
    {
        return $this->invoice->id;
    }
}