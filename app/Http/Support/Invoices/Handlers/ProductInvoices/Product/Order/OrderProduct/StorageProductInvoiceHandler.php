<?php
/**
 * Handler for product invoices that used storage.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProduct;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProductManager;
use App\Models\StorageDepartment;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class StorageProductInvoiceHandler extends OrderProductManager
{
    /**
     * UserStorageProductInvoiceHandler constructor.
     *
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
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return int Count of products that was added to invoice or subtracted from invoice.
     * @throws Exception
     */
    protected function addProductsToInvoice(int $productId, float $price, int $quantity = 1, int $warranty = null): int
    {
        return parent::addProductsToInvoice($productId, $price, $quantity, $warranty);
    }

    /**
     * Delete products from invoice by product's id.
     *
     * @param int $productId
     * @return int Products count that was subtracted from invoice.
     * @throws \Exception
     */
    protected function deleteProductsFromInvoice(int $productId): int
    {
        return parent::deleteProductsFromInvoice($productId);
    }

    /**
     * Decrease product count in invoice
     *
     * @param int $productId
     * @param int $decreasingQuantity
     * @return int
     * @throws \Exception
     */
    protected function decreaseInvoiceProductCount(int $productId, int $decreasingQuantity): int
    {
        return parent::decreaseInvoiceProductCount($productId, $decreasingQuantity);
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
     * Is storage invoice incoming?
     *
     * @return bool
     */
    protected function isStorageInvoiceIncoming()
    {
        return $this->getStorageInvoice()->direction === InvoiceDirections::INCOMING;
    }

    /**
     * Is storage invoice already implemented?
     *
     * @return bool
     */
    protected function isStorageInvoiceImplemented(): bool
    {
        return (bool)$this->getStorageInvoice()->implemented;
    }

    /**
     * Get outgoing storage department.
     *
     * @return StorageDepartment|null
     */
    protected function getOutgoingStorageDepartment()
    {
        return $this->invoice->outgoingStorageDepartment()->first();
    }

    /**
     * Get incoming storage department.
     *
     * @return StorageDepartment|null
     */
    protected function getIncomingStorageDepartment()
    {
        return $this->invoice->incomingStorageDepartment()->first();
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