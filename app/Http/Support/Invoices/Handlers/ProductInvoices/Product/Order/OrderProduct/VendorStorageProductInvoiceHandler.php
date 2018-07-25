<?php
/**
 * Handler for vendor storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProduct;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Models\Vendor;
use App\Models\VendorInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class VendorStorageProductInvoiceHandler extends StorageProductInvoiceHandler
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
     * Set vendor invoice as implemented.
     *
     * @return bool
     */
    public function implementVendorInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeVendorInvoice()){

                $this->databaseManager->commit();

                return true;
            }else{
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
        return parent::completeRollbackStorageInvoice();
    }

    /**
     * Complete vendor invoice.
     *
     * @return bool
     */
    protected function completeVendorInvoice()
    {
        if (!$this->isInvoiceProcessing()){
            return false;
        }

        $vendorInvoice = $this->getVendorInvoice();

        if ($vendorInvoice && !$vendorInvoice->implemented) {
            $vendorInvoice->implemented = 1;
            return $vendorInvoice->save();
        }

        return false;
    }

    /**
     * Get vendor.
     *
     * @return Vendor
     */
    public function getVendor(): Vendor
    {
        return $this->getVendorInvoice()->vendor;
    }

    /**
     * Get vendor id.
     *
     * @return int
     */
    public function getVendorId(): int
    {
        return $this->getVendorInvoice()->vendors_id;
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
        // invoice is completed
        if ($this->isInvoiceCompleted()) {
            return false;
        }

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
        // invoice is completed
        if ($this->isInvoiceCompleted()) {
            return false;
        }

        return parent::decreaseInvoiceProductCount($productId, $decreasingQuantity);
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
     * Is vendor invoice already implemented?
     *
     * @return bool
     */
    protected function isVendorInvoiceImplemented(): bool
    {
        return (bool)$this->getVendorInvoice()->implemented;
    }

    /**
     * Is vendor invoice incoming?
     *
     * @return bool
     */
    protected function isVendorInvoiceIncoming()
    {
        return $this->getVendorInvoice()->direction === InvoiceDirections::INCOMING;
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

    /**
     * Get vendor invoice.
     *
     * @return VendorInvoice
     */
    protected function getVendorInvoice():VendorInvoice
    {
        return $this->invoice->vendorInvoices->first();
    }
}