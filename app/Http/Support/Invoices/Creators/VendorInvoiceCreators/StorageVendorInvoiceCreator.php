<?php
/**
 * Vendor invoice creator with storage invoice.
 */

namespace App\Http\Support\Invoices\Creators\VendorInvoiceCreators;

use App\Models\Invoice;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\Eloquent\Model;

abstract class StorageVendorInvoiceCreator extends VendorInvoiceCreator
{
    /**
     * @var int
     */
    protected $storageDepartmentId;

    /**
     * Create user invoice model by vendor's id.
     *
     * @param int $vendorId
     * @param int|null $storageDepartmentId
     * @return Invoice
     * @throws \Exception
     */
    public function createInvoice(int $vendorId, int $storageDepartmentId = null): Invoice
    {
        try {
            $this->databaseManager->beginTransaction();

            $this->vendorId = $vendorId;
            $this->storageDepartmentId = $storageDepartmentId;

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

        return $invoice->setRelation('storageInvoices', collect()
            ->push($this->makeStorageInvoice($invoice))
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
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getVendorInvoiceData(): array
    {
        return parent::getVendorInvoiceData();
    }

    /**
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getStorageInvoiceData(): array
    {
        return [
            'storage_departments_id' => $this->storageDepartmentId,
        ];
    }

    /**
     * Make UserInvoice.
     *
     * @param Invoice $invoice
     * @return StorageInvoice|Model
     */
    private function makeStorageInvoice(Invoice $invoice): StorageInvoice
    {
        return $invoice->storageInvoices()->create(static::getStorageInvoiceData());
    }
}