<?php
/**
 * Handler for complete storage reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\ReplaceDefectToProduct;


use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use App\Models\StorageDepartment;
use Exception;
use Illuminate\Database\DatabaseManager;

final class RemoveDefectToProductInvoiceHandler extends ReplaceDefectToProductManager
{
    /**
     * @var StorageProductStockHandler
     */
    private $storageProductStockHandler;

    /**
     * StorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param StorageProductStockHandler $storageProductStockHandler
     */
    public function __construct(DatabaseManager $databaseManager, StorageProductStockHandler $storageProductStockHandler)
    {
        parent::__construct($databaseManager);
        $this->storageProductStockHandler = $storageProductStockHandler;
    }

    /**
     * Set outgoing storage invoice as implemented.
     *
     * @return bool
     * @throws \Exception
     */
    public function implementOutgoingStorageInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeOutgoingStorageInvoice()) {

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
     * Set incoming storage invoice as implemented.
     *
     * @return bool
     * @throws \Exception
     */
    public function implementIncomingStorageInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeIncomingStorageInvoice()) {

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
     * Complete outgoing storage replacement invoice.
     *
     * @return bool
     */
    protected function completeOutgoingStorageInvoice()
    {
        if (!$this->isInvoiceProcessing()) {
            return false;
        }

        return $this->setOutgoingStorageInvoiceImplemented();
    }

    /**
     * Complete incoming storage replacement invoice.
     *
     * @return bool
     */
    protected function completeIncomingStorageInvoice()
    {
        if (!($this->isInvoiceProcessing() && $this->isOutgoingStorageInvoiceImplemented())) {
            return false;
        }

        return $this->setIncomingStorageInvoiceImplemented() && $this->fixInvoiceInBalance() && $this->setInvoiceFinished();
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

        // get outgoing storage invoice
        $storageInvoice = $this->invoice->outgoingStorageInvoice->first();

        if ($storageInvoice && $storageInvoice->implemented) {
            $storageInvoice->implemented = 0;
            return $storageInvoice->save();
        }

        return false;
    }

    /**
     * Set outgoing storage invoice as implemented.
     *
     * @return bool
     */
    private function setOutgoingStorageInvoiceImplemented()
    {
        $outgoingStorageInvoice = $this->invoice->outgoingStorageInvoice->first();

        if ($outgoingStorageInvoice && !$outgoingStorageInvoice->implemented) {
            $outgoingStorageInvoice->implemented = 1;
            return $outgoingStorageInvoice->save();
        }

        return false;
    }

    /**
     * Set incoming storage invoice as implemented.
     *
     * @return bool
     */
    private function setIncomingStorageInvoiceImplemented()
    {
        $incomingStorageInvoice = $this->invoice->incomingStorageInvoice->first();

        if ($incomingStorageInvoice && !$incomingStorageInvoice->implemented) {
            $incomingStorageInvoice->implemented = 1;
            return $incomingStorageInvoice->save();
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
        if ($this->isOutgoingStorageInvoiceImplemented() || $this->isIncomingStorageInvoiceImplemented()){
            return false;
        }

        return parent::deleteHandlingInvoice();
    }

    /**
     * Fix invoice data in balance.
     *
     * @return bool
     */
    private function fixInvoiceInBalance(): bool
    {
        $outgoingStorageDepartment = $this->getOutgoingStorageDepartment();
        $incomingStorageDepartment = $this->getIncomingStorageDepartment();

        foreach ($this->getInvoiceProducts() as $reclamation) {

            // get storage product
            $storageProduct = $this->storageProductStockHandler->getOrCreateStorageProduct($incomingStorageDepartment, $reclamation->products_id);

            // update stock quantity
            $this->storageProductStockHandler->increaseProductStock($storageProduct, 1);

            // add to active incoming storage reclamation
            $outgoingStorageDepartment->reclamation()->detach($reclamation->id);
        }

        return true;
    }

    /**
     * Is outgoing storage implemented ?
     *
     * @return bool
     */
    private function isOutgoingStorageInvoiceImplemented(): bool
    {
        return (bool)$this->invoice->outgoingStorageInvoice->implemented;
    }

    /**
     * Is incoming storage implemented ?
     *
     * @return bool
     */
    private function isIncomingStorageInvoiceImplemented(): bool
    {
        return (bool)$this->invoice->incomingStorageInvoice->implemented;
    }

    /**
     * Is invoice completed yet?
     *
     * @return bool
     */
    private function isInvoiceCompleted(): bool
    {
        return $this->isOutgoingStorageInvoiceImplemented() && $this->isIncomingStorageInvoiceImplemented();
    }

    /**
     * Get outgoing storage department.
     *
     * @return StorageDepartment|null
     */
    private function getOutgoingStorageDepartment()
    {
        return $this->invoice->outgoingStorageDepartment()->first();
    }

    /**
     * Get incoming storage department.
     *
     * @return StorageDepartment|null
     */
    private function getIncomingStorageDepartment()
    {
        return $this->invoice->incomingStorageDepartment()->first();
    }
}