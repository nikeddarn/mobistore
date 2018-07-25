<?php
/**
 * Handler for complete vendor reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\ExchangeDefectProduct;


use App\Http\Support\Balance\VendorBalance;
use App\Http\Support\Statistics\Product\VendorReclamationStatistics;
use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use Illuminate\Database\DatabaseManager;

final class VendorExchangeReclamationInvoiceHandler extends VendorStorageReclamationProductInvoiceHandler
{
    /**
     * @var VendorReclamationStatistics
     */
    private $vendorReclamationStatistics;
    /**
     * @var StorageProductStockHandler
     */
    private $storageProductStockHandler;
    /**
     * @var VendorBalance
     */
    private $vendorBalance;

    /**
     * VendorStorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param VendorReclamationStatistics $vendorReclamationStatistics
     * @param StorageProductStockHandler $storageProductStockHandler
     * @param VendorBalance $vendorBalance
     */
    public function __construct(DatabaseManager $databaseManager, VendorReclamationStatistics $vendorReclamationStatistics, StorageProductStockHandler $storageProductStockHandler, VendorBalance $vendorBalance)
    {
        parent::__construct($databaseManager);
        $this->vendorReclamationStatistics = $vendorReclamationStatistics;
        $this->storageProductStockHandler = $storageProductStockHandler;
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
            return $this->fixIncomingStorageInvoice() && $this->fixOutgoingVendorInvoice() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Add incoming storage data to statistics.
     *
     * @return bool
     */
    private function fixIncomingStorageInvoice(): bool
    {
        // get storage department
        $storageDepartment = $this->invoice->incomingStorageDepartment()->first();

        foreach ($this->getInvoiceProducts() as $reclamation) {

            // get storage product
            $storageProduct = $this->storageProductStockHandler->getOrCreateStorageProduct($storageDepartment, $reclamation->products_id);

            // update stock quantity
            $this->storageProductStockHandler->increaseProductStock($storageProduct, 1);
        }

        return true;
    }

    /**
     * Add outgoing vendor data to statistics.
     *
     * @return bool
     */
    private function fixOutgoingVendorInvoice(): bool
    {
        // retrieve vendor
        $vendor = $this->getVendorInvoice()->vendor;

        // increase user balance on invoice delivery sum
        $this->vendorBalance->addToDebitBalance($vendor, $this->invoice->delivery_sum);

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active storage reclamation
            $vendor->reclamation()->detach($reclamation->id);
        }

        return $this->vendorReclamationStatistics->takeRejectedReclamationInvoiceToStatistic($vendor, $this->getProductsCount());
    }
}