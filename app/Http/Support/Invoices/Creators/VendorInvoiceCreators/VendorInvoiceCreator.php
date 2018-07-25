<?php
/**
 * Vendor invoice creator.
 */

namespace App\Http\Support\Invoices\Creators\VendorInvoiceCreators;

use App\Http\Support\Invoices\Creators\InvoiceCreator;
use App\Models\Invoice;
use App\Models\VendorInvoice;
use Exception;
use Illuminate\Database\Eloquent\Model;

abstract class VendorInvoiceCreator extends InvoiceCreator
{
    /**
     * @var int
     */
    protected $vendorId;

    /**
     * Create user invoice model by vendor's id.
     *
     * @param int $vendorId
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $vendorId): Invoice
    {
        try {
            $this->databaseManager->beginTransaction();

            $this->vendorId = $vendorId;

            $invoice = $this->makeInvoice();

            $this->databaseManager->commit();

            return $invoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Make invoice.
     *
     * @return Invoice
     */
    protected function makeInvoice(): Invoice
    {
        $invoice = parent::makeInvoice();

        return $invoice->setRelation('vendorInvoices', collect()
            ->push($this->makeVendorInvoice($invoice))
        );
    }

    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData(): array
    {
        return parent::getInvoiceData();
    }

    /**
     * Get array of data for create VendorInvoice model.
     *
     * @return array
     */
    protected function getVendorInvoiceData(): array
    {
        return [
            'vendors_id' => $this->vendorId,
        ];
    }

    /**
     * Make VendorInvoice.
     *
     * @param Invoice $invoice
     * @return VendorInvoice|Model
     */
    private function makeVendorInvoice(Invoice $invoice): VendorInvoice
    {
        return $invoice->vendorInvoices()->create(static::getVendorInvoiceData());
    }
}