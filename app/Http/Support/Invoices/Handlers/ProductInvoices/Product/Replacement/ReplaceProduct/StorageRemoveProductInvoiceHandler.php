<?php
/**
 * Handler for complete product storage replacement invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Replacement\ReplaceProduct;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Replacement\StorageReplaceProductManager;
use App\Http\Support\Statistics\Product\StorageProductStatistics;
use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use App\Models\InvoiceProduct;
use App\Models\StorageDepartment;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class StorageRemoveProductInvoiceHandler extends StorageReplaceProductManager
{
    /**
     * @var StorageProductStatistics
     */
    private $storageProductStatistics;
    /**
     * @var StorageProductStockHandler
     */
    private $storageProductStockHandler;

    /**
     * StorageRemoveProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param StorageProductStockHandler $storageProductStockHandler
     * @param StorageProductStatistics $storageProductStatistics
     */
    public function __construct(DatabaseManager $databaseManager, StorageProductStockHandler $storageProductStockHandler, StorageProductStatistics $storageProductStatistics)
    {
        parent::__construct($databaseManager);
        $this->storageProductStatistics = $storageProductStatistics;
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
        $storageInvoice = $this->getOutgoingStorageInvoice();

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
        $outgoingStorageInvoice = $this->getOutgoingStorageInvoice();

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
        $incomingStorageInvoice = $this->getIncomingStorageInvoice();

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

        foreach ($this->getInvoiceProducts() as $invoiceProduct) {
            // get last incoming product price for statistics
            $lastIncomingProductPrice = $this->getLastIncomingProductPrice($outgoingStorageDepartment, $invoiceProduct);

            if (!($this->fixOutgoingStorageInvoiceInBalance($outgoingStorageDepartment, $invoiceProduct, $lastIncomingProductPrice) && $this->fixIncomingStorageInvoiceInBalance($incomingStorageDepartment, $invoiceProduct, $lastIncomingProductPrice))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove products of invoice from storage.
     *
     * @param StorageDepartment $storageDepartment
     * @param InvoiceProduct $invoiceProduct
     * @param float $lastIncomingProductPrice
     * @return bool
     */
    private function fixOutgoingStorageInvoiceInBalance(StorageDepartment $storageDepartment, InvoiceProduct $invoiceProduct, float $lastIncomingProductPrice): bool
    {
        // get storage product
        $storageProduct =$this->storageProductStockHandler->getStorageProduct($storageDepartment, $invoiceProduct->products_id);

        // product not available
        if (!$storageProduct || $storageProduct->stock_quantity < $invoiceProduct->quantity) {
            return false;
        }

        // update stock quantity
        $this->storageProductStockHandler->decreaseProductStock($storageProduct, $invoiceProduct->quantity);

        // update statistics
        $this->storageProductStatistics->decreasePurchasedStorageProductCount($storageProduct, $invoiceProduct->quantity, $lastIncomingProductPrice);

        return true;
    }

    /**
     * Add products of invoice on storage.
     *
     * @param StorageDepartment $storageDepartment
     * @param InvoiceProduct $invoiceProduct
     * @param float $lastIncomingProductPrice
     * @return bool
     */
    private function fixIncomingStorageInvoiceInBalance(StorageDepartment $storageDepartment, InvoiceProduct $invoiceProduct, float $lastIncomingProductPrice): bool
    {
        // get storage product
        $storageProduct =$this->storageProductStockHandler->getOrCreateStorageProduct($storageDepartment, $invoiceProduct->products_id);

        // update stock quantity
        $this->storageProductStockHandler->increaseProductStock($storageProduct, $invoiceProduct->quantity);

        // update statistics
        $this->storageProductStatistics->increasePurchasedStorageProductCount($storageProduct, $invoiceProduct->quantity, $lastIncomingProductPrice);

        return true;
    }

    /**
     * Get last incoming product price for given storage.
     *
     * @param StorageDepartment $storageDepartment
     * @param InvoiceProduct $invoiceProduct
     * @return float
     */
    private function getLastIncomingProductPrice(StorageDepartment $storageDepartment, InvoiceProduct $invoiceProduct): float
    {
        return $this->invoice->newQuery()
            ->whereHas('invoiceProduct', function ($query) use ($invoiceProduct) {
                $query->where('products_id', $invoiceProduct->products_id);
            })
            ->whereHas('storageInvoices', function ($query) use ($storageDepartment) {
                $query->where('storages_id', $storageDepartment->id)
                    ->where('direction', InvoiceDirections::INCOMING);
            })
            ->whereIn('invoice_types_id', [
                InvoiceTypes::USER_ORDER, InvoiceTypes::USER_PRE_ORDER,
            ])
            ->orderByDesc('created_at')
            ->with('invoiceProduct')
            ->first()
            ->invoiceProduct
            ->keyBy('products_id')
            ->get($invoiceProduct->products_id)
            ->price;
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

    /**
     * Is outgoing storage implemented ?
     *
     * @return bool
     */
    private function isOutgoingStorageInvoiceImplemented(): bool
    {
        return (bool)$this->getOutgoingStorageInvoice()->implemented;
    }

    /**
     * Is incoming storage implemented ?
     *
     * @return bool
     */
    private function isIncomingStorageInvoiceImplemented(): bool
    {
        return (bool)$this->getIncomingStorageInvoice()->implemented;
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
     * Get outgoing storage invoice.
     *
     * @return StorageInvoice
     */
    private function getOutgoingStorageInvoice():StorageInvoice
    {
        return $this->invoice->outgoingStorageInvoice;
    }

    /**
     * Get incoming storage invoice.
     *
     * @return StorageInvoice
     */
    private function getIncomingStorageInvoice():StorageInvoice
    {
        return $this->invoice->incomingStorageInvoice;
    }
}