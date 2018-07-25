<?php
/**
 * Common invoice repository.
 */

namespace App\Http\Support\Invoices\Repositories;

use App\Contracts\Shop\Invoices\Repositories\InvoiceRepositoryInterface;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * Invoice repository constructor.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get invoice by it's id.
     *
     * @param int $invoiceId
     * @return Invoice|Model
     */
    public function getByInvoiceId(int $invoiceId): Invoice
    {
        return $this->invoice->newQuery()->where('id', $invoiceId)->first();
    }

    /**
     * Make invoice query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeQuery(): Builder
    {
        return $this->invoice->newQuery();
    }
}