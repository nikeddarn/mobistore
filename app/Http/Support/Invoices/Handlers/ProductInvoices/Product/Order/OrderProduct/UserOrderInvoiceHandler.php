<?php
/**
 * Handler for complete user storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProduct;


use App\Http\Support\Balance\UserBalance;
use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use App\Http\Support\Statistics\Product\StorageProductStatistics;
use App\Http\Support\Statistics\Product\UserProductStatistics;
use Illuminate\Database\DatabaseManager;

final class UserOrderInvoiceHandler extends UserStorageProductInvoiceHandler
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
     * @var UserProductStatistics
     */
    private $userProductStatistics;
    /**
     * @var UserBalance
     */
    private $userBalance;

    /**
     * StorageProductInvoiceHandler constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param StorageProductStockHandler $storageProductStockHandler
     * @param StorageProductStatistics $storageProductStatistics
     * @param UserProductStatistics $userProductStatistics
     * @param UserBalance $userBalance
     */
    public function __construct(DatabaseManager $databaseManager, StorageProductStockHandler $storageProductStockHandler, StorageProductStatistics $storageProductStatistics, UserProductStatistics $userProductStatistics, UserBalance $userBalance)
    {
        parent::__construct($databaseManager, $storageProductStockHandler);

        $this->storageProductStockHandler = $storageProductStockHandler;
        $this->storageProductStatistics = $storageProductStatistics;
        $this->userProductStatistics = $userProductStatistics;
        $this->userBalance = $userBalance;
    }

    /**
     * Complete storage invoice.
     *
     * @return bool
     */
    protected function completeStorageInvoice()
    {
        if (!parent::completeStorageInvoice()) {
            return false;
        }

        if ($this->isUserInvoiceImplemented() && $this->isStorageInvoiceIncoming()) {
            return $this->fixOutgoingUserInvoiceInBalance() && $this->fixIncomingStorageInvoiceInBalance() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Complete user invoice.
     *
     * @return bool
     */
    protected function completeUserInvoice()
    {
        if (!parent::completeUserInvoice()) {
            return false;
        }

        if ($this->isStorageInvoiceImplemented() && $this->isUserInvoiceIncoming()) {

            return $this->fixIncomingUserInvoiceInBalance() && $this->fixOutgoingStorageInvoiceInBalance() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Increase user balance on invoice sum.
     *
     *
     * @return bool
     */
    private function fixOutgoingUserInvoiceInBalance(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        // decrease user balance on invoice sum
        $this->userBalance->addToCreditBalance($user, $this->invoice->invoice_sum);
        // increase user balance on invoice delivery sum
        $this->userBalance->addToDebitBalance($user, $this->invoice->delivery_sum);

        // update user statistics
        $this->userProductStatistics->subtractReturnedInvoiceFromStatistic($user, $this->getProductsCount(), $this->getInvoiceSum());

        return true;
    }

    /**
     * Decrease user balance on invoice sum.
     *
     * @return bool
     */
    private function fixIncomingUserInvoiceInBalance(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        // increase user balance on invoice sum
        $this->userBalance->addToDebitBalance($user, $this->invoice->invoice_sum);
        // increase user balance on invoice delivery sum
        $this->userBalance->addToDebitBalance($user, $this->invoice->delivery_sum);

        // update user statistics
        $this->userProductStatistics->takeBoughtInvoiceToStatistic($user, $this->getProductsCount(), $this->getInvoiceSum());

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
            $this->storageProductStatistics->increaseSoldStorageProductCount($storageProduct, $invoiceProduct->quantity, $invoiceProduct->price);
            $this->storageProductStatistics->increaseTotalSoldProductCount($storageProduct, $invoiceProduct->quantity);

            // remove reserve
            $this->storageProductStockHandler->removeReserveCountOfStorageProduct($storageProduct, $invoiceProduct->quantity);
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
            $this->storageProductStatistics->decreaseSoldStorageProductCount($storageProduct, $invoiceProduct->quantity, $invoiceProduct->price);
            $this->storageProductStatistics->decreaseTotalSoldProductCount($storageProduct, $invoiceProduct->quantity);
        }

        return true;
    }
}