<?php
/**
 * Handler for complete vendor reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\DefectProduct;


use App\Models\VendorInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class VendorStorageReclamationProductInvoiceHandler extends StorageReclamationProductInvoiceHandler
{
    /**
     * VendorStorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        parent::__construct($databaseManager);
    }
    /**
     * Set vendor invoice as implemented.
     *
     * @return bool
     */
    public function implementVendorInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeVendorInvoice()) {

                $this->databaseManager->commit();

                return true;
            } else {
                $this->databaseManager->rollback();

                return false;
            }
        } catch (Exception $e) {
            $this->databaseManager->rollback();

            return false;
        }
    }

    /**
     * Complete storage invoice.
     *
     * @return bool
     */
    protected function completeStorageInvoice()
    {
        return parent::completeStorageInvoice();
    }

    /**
     * Complete user invoice.
     *
     * @return bool
     */
    protected function completeVendorInvoice()
    {
        if (!$this->isInvoiceProcessing()){
            return false;
        }

        $vendorInvoice = $this->getVendorInvoice();

        $vendorInvoice->implemented = 1;
        return $vendorInvoice->save();
    }

    /**
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled(): bool
    {
        // invoice is completed
        if ($this->isInvoiceCompleted()) {
            return false;
        }

        return parent::setInvoiceCancelled();
    }

    /**
     * Delete current invoice.
     *
     * @return bool
     * @throws \Exception
     */
    protected function deleteHandlingInvoice(): bool
    {
        if (!$this->isVendorInvoiceImplemented()) {
            return parent::deleteHandlingInvoice();
        } else {
            return false;
        }
    }

    /**
     * Get vendor invoice.
     *
     * @return VendorInvoice
     */
    protected function getVendorInvoice():VendorInvoice
    {
        return $this->invoice->vendorInvoices->first();
    }

    /**
     * Is vendor invoice already implemented?
     *
     * @return bool
     */
    protected function isVendorInvoiceImplemented(): bool
    {
        return (bool)$this->getVendorInvoice()->implemented;
    }

    /**
     * Is invoice completed yet?
     *
     * @return bool
     */
    private function isInvoiceCompleted(): bool
    {
        return $this->isStorageInvoiceImplemented() && $this->isVendorInvoiceImplemented();
    }
}