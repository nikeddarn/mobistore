<?php
/**
 * Handler for complete storage reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\DefectProduct;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class StorageReclamationProductInvoiceHandler extends DefectProductManager
{
    /**
     * StorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        parent::__construct($databaseManager);
    }

    /**
     * Set storage invoice as implemented.
     *
     * @return bool
     */
    public function implementStorageInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeStorageInvoice()) {

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
     * Set storage invoice as not implemented.
     *
     * @return bool
     */
    public function rollbackStorageInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeRollbackStorageInvoice()) {

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
        if (!$this->isInvoiceProcessing()) {
            return false;
        }

        $storageInvoice = $this->getStorageInvoice();

        if ($storageInvoice && !$storageInvoice->implemented) {
            $storageInvoice->implemented = 1;
            return $storageInvoice->save();
        }

        return false;
    }

    /**
     * Set invoice as not implemented if it's cancelled.
     *
     * @return bool
     */
    protected function completeRollbackStorageInvoice()
    {
        if (!$this->isInvoiceCancelled()) {
            return false;
        }

        $storageInvoice = $this->getStorageInvoice();

        if ($storageInvoice && $storageInvoice->implemented) {
            $storageInvoice->implemented = 0;
            return $storageInvoice->save();
        }

        return false;
    }

    /**
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled(): bool
    {
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
        if (!$this->isStorageInvoiceImplemented()) {
            return parent::deleteHandlingInvoice();
        } else {
            return false;
        }
    }

    /**
     * Is storage invoice already implemented?
     *
     * @return bool
     */
    protected function isStorageInvoiceImplemented(): bool
    {
        // storage invoice is not exist or implemented
        return (bool)$this->getStorageInvoice()->implemented;
    }

    /**
     * Is storage invoice incoming?
     *
     * @return bool
     */
    protected function isStorageInvoiceIncoming()
    {
        return $this->getStorageInvoice()->direction === InvoiceDirections::INCOMING;
    }

    /**
     * Get storage invoice.
     *
     * @return StorageInvoice
     */
    protected function getStorageInvoice():StorageInvoice
    {
        return $this->invoice->storageInvoices->first();
    }
}