<?php
/**
 * Handler for complete user reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\ExchangeDefectProduct;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\StockHandlers\Product\StorageProductStockHandler;
use App\Models\UserDelivery;
use App\Models\UserInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class UserStorageReclamationProductInvoiceHandler extends StorageReclamationProductInvoiceHandler
{
    /**
     * @var StorageProductStockHandler
     */
    protected $storageProductStockHandler;

    /**
     * UserStorageReclamationProductInvoiceHandler constructor.
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

        $userInvoice->implemented = 1;
        return $userInvoice->save();
    }

    /**
     * Add existing reclamation product by storage reclamation.
     *
     * @param int $reclamationId
     * @return bool
     */
    protected function addProductByReclamationId(int $reclamationId): bool
    {
        if (parent::addProductByReclamationId($reclamationId)){
            $product = $this->invoice->reclamations()->where('id', $reclamationId)->with('product')->first()->product;

            return $this->reserveProductsOnStorage([$product->id => 1]);
        }

        return false;
    }

    /**
     * Remove existing reclamation product from invoice by InvoiceDefectProduct id.
     *
     * @param int $reclamationId
     * @return bool
     */
    protected function deleteProductFromInvoice(int $reclamationId): bool
    {
        if(parent::deleteProductFromInvoice($reclamationId)){
            $product = $this->invoice->reclamations()->where('id', $reclamationId)->with('product')->first()->product;

            return $this->removeReserveProductsOnStorage([$product->id => 1]);
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
     * @throws Exception
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
     * Get user invoice.
     *
     * @return UserInvoice
     */
    protected function getUserInvoice():UserInvoice
    {
        return $this->invoice->userInvoices->first();
    }
}