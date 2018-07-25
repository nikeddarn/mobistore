<?php
/**
 * Handler for complete vendor storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProduct;


use App\Http\Support\Balance\VendorBalance;
use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use App\Http\Support\Statistics\Product\StorageProductStatistics;
use App\Http\Support\Statistics\Product\VendorProductStatistics;
use Illuminate\Database\DatabaseManager;

final class VendorOrderInvoiceHandler extends VendorStorageProductInvoiceHandler
{
    /**
     * @var StorageProductStockHandler
     */
    protected $storageProductStockHandler;

    /**
     * @var StorageProductStatistics
     */
    protected $storageProductStatistics;
    /**
     * @var VendorProductStatistics
     */
    private $vendorProductStatistics;
    /**
     * @var VendorBalance
     */
    private $vendorBalance;

    /**
     * StorageProductInvoiceHandler constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param StorageProductStockHandler $storageProductStockHandler
     * @param StorageProductStatistics $storageProductStatistics
     * @param VendorProductStatistics $vendorProductStatistics
     * @param VendorBalance $vendorBalance
     */
    public function __construct(DatabaseManager $databaseManager, StorageProductStockHandler $storageProductStockHandler, StorageProductStatistics $storageProductStatistics, VendorProductStatistics $vendorProductStatistics, VendorBalance $vendorBalance)
    {
        parent::__construct($databaseManager);

        $this->storageProductStockHandler = $storageProductStockHandler;
        $this->storageProductStatistics = $storageProductStatistics;
        $this->vendorProductStatistics = $vendorProductStatistics;
        $this->vendorBalance = $vendorBalance;
    }

    /**
     * Complete storage invoice.
     *
     * @return bool
     */
    protected function completeStorageInvoice()
    {
        if (!parent::completeStorageInvoice()){
            return false;
        }

        if ($this->isVendorInvoiceImplemented() && $this->isStorageInvoiceIncoming()){
            return $this->fixOutgoingVendorInvoiceInBalance() && $this->fixIncomingStorageInvoiceInBalance() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Complete user invoice.
     *
     * @return bool
     */
    protected function completeVendorInvoice()
    {
        if (!parent::completeVendorInvoice()){
            return false;
        }

        if ($this->isStorageInvoiceImplemented() && $this->isVendorInvoiceIncoming()){
            return $this->fixIncomingVendorInvoiceInBalance() && $this->fixOutgoingStorageInvoiceInBalance() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Decrease vendor balance on invoice sum.
     *
     * @return bool
     */
    private function fixOutgoingVendorInvoiceInBalance():bool
    {
        // retrieve vendor
        $vendor = $this->getVendorInvoice()->vendor;

        // decrease vendor balance on invoice sum
        $this->vendorBalance->addToCreditBalance($vendor, $this->invoice->invoice_sum);
        // increase vendor balance on invoice delivery sum
        $this->vendorBalance->addToDebitBalance($vendor, $this->invoice->delivery_sum);

        // update vendor statistics
        $this->vendorProductStatistics->takeBoughtInvoiceToStatistic($vendor, $this->getProductsCount(), $this->getInvoiceSum());

        return true;
    }

    /**
     * Increase vendor balance on invoice sum.
     *
     * @return bool
     */
    private function fixIncomingVendorInvoiceInBalance():bool
    {
        // retrieve vendor
        $vendor = $this->getVendorInvoice()->vendor;

        // increase vendor balance on invoice sum
        $this->vendorBalance->addToDebitBalance($vendor, $this->invoice->invoice_sum);
        // increase vendor balance on invoice delivery sum
        $this->vendorBalance->addToDebitBalance($vendor, $this->invoice->delivery_sum);

        // update user statistics
        $this->vendorProductStatistics->subtractReturnedInvoiceFromStatistic($vendor, $this->getProductsCount(), $this->getInvoiceSum());

        return true;
    }

    /**
     * Remove products of invoice from storage.
     *
     * @return bool
     */
    protected function fixOutgoingStorageInvoiceInBalance(): bool
    {
        $outgoingStorageDepartment = $this->getOutgoingStorageDepartment();

        foreach ($this->getInvoiceProducts() as $invoiceProduct) {
            // get storage product
            $storageProduct = $this->storageProductStockHandler->getStorageProduct($outgoingStorageDepartment, $invoiceProduct->products_id);

            // product not available
            if (!$storageProduct || $storageProduct->stock_quantity < $invoiceProduct->quantity) {
                return false;
            }

            // update stock quantity
            $this->storageProductStockHandler->decreaseProductStock($storageProduct, $invoiceProduct->quantity);

            //update statistics
            $this->storageProductStatistics->decreasePurchasedStorageProductCount($storageProduct, $invoiceProduct->quantity, $invoiceProduct->price);
        }

        return true;
    }

    /**
     * Add products of invoice on storage.
     *
     * @return bool
     */
    protected function fixIncomingStorageInvoiceInBalance(): bool
    {
        $incomingStorageDepartment = $this->getIncomingStorageDepartment();

        foreach ($this->getInvoiceProducts() as $invoiceProduct) {
            // get storage product
            $storageProduct = $this->storageProductStockHandler->getOrCreateStorageProduct($incomingStorageDepartment, $invoiceProduct->products_id);

            // update stock quantity
            $this->storageProductStockHandler->increaseProductStock($storageProduct, $invoiceProduct->quantity);

            //update statistics
            $this->storageProductStatistics->increasePurchasedStorageProductCount($storageProduct, $invoiceProduct->quantity, $invoiceProduct->price);
        }

        return true;
    }
}