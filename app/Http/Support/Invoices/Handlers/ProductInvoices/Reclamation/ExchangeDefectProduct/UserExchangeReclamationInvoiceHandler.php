<?php
/**
 * Handler for complete user reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\ExchangeDefectProduct;


use App\Http\Support\Balance\UserBalance;
use App\Http\Support\Statistics\Product\ProductReclamationStatistics;
use App\Http\Support\Statistics\Product\UserReclamationStatistics;
use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use Illuminate\Database\DatabaseManager;

final class UserExchangeReclamationInvoiceHandler extends UserStorageReclamationProductInvoiceHandler
{
    /**
     * @var ProductReclamationStatistics
     */
    private $productReclamationStatistics;
    /**
     * @var UserReclamationStatistics
     */
    private $userReclamationStatistics;
    /**
     * @var UserBalance
     */
    private $userBalance;

    /**
     * UserStorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param ProductReclamationStatistics $productReclamationStatistics
     * @param UserReclamationStatistics $userReclamationStatistics
     * @param StorageProductStockHandler $storageProductStockHandler
     * @param UserBalance $userBalance
     */
    public function __construct(DatabaseManager $databaseManager, ProductReclamationStatistics $productReclamationStatistics, UserReclamationStatistics $userReclamationStatistics, StorageProductStockHandler $storageProductStockHandler, UserBalance $userBalance)
    {
        parent::__construct($databaseManager, $storageProductStockHandler);
        $this->productReclamationStatistics = $productReclamationStatistics;
        $this->userReclamationStatistics = $userReclamationStatistics;
        $this->userBalance = $userBalance;
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

            return $this->fixOutgoingStorageInvoice() && $this->fixIncomingUserInvoice() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Add outgoing storage data to statistics.
     *
     * @return bool
     */
    private function fixOutgoingStorageInvoice(): bool
    {
        // get storage department
        $storageDepartment = $this->invoice->outgoingStorageDepartment()->first();

        foreach ($this->getInvoiceProducts() as $reclamation) {

            // get storage product
            $storageProduct = $this->storageProductStockHandler->getStorageProduct($storageDepartment, $reclamation->products_id);

            // product not available
            if (!$storageProduct || $storageProduct->stock_quantity < 1) {
                return false;
            }

            // update stock quantity
            $this->storageProductStockHandler->decreaseProductStock($storageProduct, 1);
        }

        return true;
    }

    /**
     * Add incoming user data to statistics.
     *
     * @return bool
     */
    private function fixIncomingUserInvoice(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        // increase user balance on invoice delivery sum
        $this->userBalance->addToDebitBalance($user, $this->invoice->delivery_sum);

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active storage reclamation
            $user->reclamation()->detach($reclamation->id);
        }

        return true;
    }
}