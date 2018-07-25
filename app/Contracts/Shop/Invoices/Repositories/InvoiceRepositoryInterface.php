<?php
/**
 * Invoice repository interface.
 */

namespace App\Contracts\Shop\Invoices\Repositories;


use App\Models\Invoice;

interface InvoiceRepositoryInterface
{
    /**
     * Get user invoice by it's id.
     *
     * @param int $invoiceId
     * @return Invoice
     */
    public function getByInvoiceId(int $invoiceId): Invoice;
}