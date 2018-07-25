<?php
/**
 * Handler for complete user reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\DefectProduct;


use App\Models\UserDelivery;
use App\Models\UserInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class UserStorageReclamationProductInvoiceHandler extends StorageReclamationProductInvoiceHandler
{
    /**
     * UserStorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        parent::__construct($databaseManager);
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
            return parent::deleteHandlingInvoice();
        } else {
            return false;
        }
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
}