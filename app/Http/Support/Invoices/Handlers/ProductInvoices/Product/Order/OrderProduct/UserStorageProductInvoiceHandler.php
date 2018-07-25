<?php
/**
 * Handler for product invoices that used storage with delivery.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProduct;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use App\Models\UserDelivery;
use App\Models\UserInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class UserStorageProductInvoiceHandler extends StorageProductInvoiceHandler
{
    /**
     * @var StorageProductStockHandler
     */
    private $storageProductStockHandler;

    /**
     * UserStorageProductInvoiceHandler constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param StorageProductStockHandler $storageProductStockHandler
     */
    public function __construct(DatabaseManager $databaseManager, StorageProductStockHandler $storageProductStockHandler)
    {
        parent::__construct($databaseManager);
        $this->storageProductStockHandler = $storageProductStockHandler;
    }

    /**
     * Get UserDelivery.
     *
     * @return UserDelivery|null
     */
    public function getUserDelivery()
    {
        return $this->getUserInvoice()->userDelivery;
    }

    /**
     * Set user invoice as implemented.
     *
     * @return bool
     */
    public function implementUserInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeUserInvoice()) {

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
     * Set invoice as not implemented if it's cancelled.
     *
     * @return bool
     */
    protected function completeRollbackStorageInvoice()
    {
        if (parent::completeRollbackStorageInvoice()) {
            // remove all products from storage reserve
            return $this->removeReserveProductsOnStorage($this->getArrayInvoiceProducts());
        }

        return false;
    }

    /**
     * Complete user invoice.
     *
     * @return bool
     */
    protected function completeUserInvoice()
    {
        if (!$this->isInvoiceProcessing()) {
            return false;
        }

        $userInvoice = $this->getUserInvoice();

        if ($userInvoice && !$userInvoice->implemented) {
            $userInvoice->implemented = 1;
            return $userInvoice->save();
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
        // invoice is completed
        if ($this->isInvoiceCompleted()) {
            return false;
        }

        $addedCount = parent::addProductsToInvoice($productId, $price, $quantity, $warranty);

        $this->reserveProductsOnStorage([$productId => $addedCount]);

        return $addedCount;
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
        // invoice is completed
        if ($this->isInvoiceCompleted()) {
            return false;
        }

        $deletedCount = parent::deleteProductsFromInvoice($productId);

        $this->removeReserveProductsOnStorage([$productId => $deletedCount]);

        return $deletedCount;
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
        // invoice is completed
        if ($this->isInvoiceCompleted()) {
            return false;
        }

        $deletedCount = parent::decreaseInvoiceProductCount($productId, $decreasingQuantity);

        $this->removeReserveProductsOnStorage([$productId => $deletedCount]);

        return $deletedCount;
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
        if (!$this->isUserInvoiceImplemented()) {
            return
                // remove all products from storage reserve
                $this->removeReserveProductsOnStorage($this->getArrayInvoiceProducts()) &&
                // delete invoice
                parent::deleteHandlingInvoice();
        } else {
            return false;
        }
    }

    /**
     * Reserve products on outgoing storage.
     *
     * @param array $reservingProducts
     * @return bool
     */
    private function reserveProductsOnStorage(array $reservingProducts): bool
    {
        // reserve products if user invoice is incoming
        if ($this->isUserInvoiceIncoming()) {
            $outgoingStorageDepartment = $this->getOutgoingStorageDepartment();

            return $this->storageProductStockHandler->reserveProductsOnStorage($outgoingStorageDepartment, $reservingProducts);
        }

        return true;
    }

    /**
     *  Remove reserve of products on outgoing storage.
     *
     * @param array $removingProducts
     * @return bool
     */
    private function removeReserveProductsOnStorage(array $removingProducts): bool
    {
        // remove reserve if user invoice is incoming
        if ($this->isUserInvoiceIncoming()) {
            $outgoingStorageDepartment = $this->getOutgoingStorageDepartment();

            return $this->storageProductStockHandler->removeReserveProductsFromStorage($outgoingStorageDepartment, $removingProducts);
        }

        return true;
    }

    /**
     * Is user invoice incoming?
     *
     * @return bool
     */
    protected function isUserInvoiceIncoming()
    {
        return $this->getUserInvoice()->direction === InvoiceDirections::INCOMING;
    }

    /**
     * Is user invoice already implemented?
     *
     * @return bool
     */
    protected function isUserInvoiceImplemented(): bool
    {
        return (bool)$this->getUserInvoice()->implemented;
    }

    /**
     * Is invoice completed yet?
     *
     * @return bool
     */
    private function isInvoiceCompleted(): bool
    {
        return $this->isStorageInvoiceImplemented() && $this->isUserInvoiceImplemented();
    }

    /**
     * Get user invoice.
     *
     * @return UserInvoice
     */
    protected function getUserInvoice():UserInvoice
    {
        return $this->invoice->userInvoices->first();
    }
}