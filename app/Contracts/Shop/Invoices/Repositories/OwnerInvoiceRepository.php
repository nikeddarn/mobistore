<?php
/**
 * Methods for handle user's invoice or vendor invoice or storage invoice.
 */

namespace App\Contracts\Shop\Invoices\Repositories;


interface OwnerInvoiceRepository extends InvoiceRepositoryInterface
{
    /**
     * @param int $ownerId User's or vendor's or storage's id.
     * @param int|null $invoiceType
     * @param int $limit
     */
    public function getLastInvoices(int $ownerId, int $invoiceType = null, $limit = 1);
}