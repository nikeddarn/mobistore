<?php
/**
 * Invoice repository interface.
 */

namespace App\Contracts\Shop\Invoices\Repositories;


use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

interface InvoiceRepositoryInterface
{
    /**
     * Is invoice with given id exist ?
     *
     * @param int $invoiceId
     * @return bool
     */
    public function exists(int $invoiceId): bool;

    /**
     * Get retrieved invoice.
     *
     * @return Invoice
     */
    public function getRetrievedInvoice();

    /**
     * Get user invoice by its id.
     *
     * @param int $invoiceId
     * @return Model
     */
    public function getByInvoiceId(int $invoiceId): Invoice;

    /**
     * Delete user's basket.
     *
     * @param int $invoiceId
     * @return void
     */
    public function deleteByInvoiceId(int $invoiceId);

    /**
     * Delete invoice.
     *
     * @param Invoice $invoice
     * @return bool|null
     * @throws \Exception
     */
    public function deleteInvoice(Invoice $invoice);

    /**
     * Delete invoice that was retrieved by this class.
     *
     * @return bool
     */
    public function deleteRetrievedInvoice();
}