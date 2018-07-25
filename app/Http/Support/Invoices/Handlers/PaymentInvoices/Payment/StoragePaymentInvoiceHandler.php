<?php
/**
 * Handle payment storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\PaymentInvoices\Payment;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Balance\StorageBalance;
use App\Http\Support\Invoices\Handlers\PaymentInvoices\PaymentInvoiceManager;
use App\Models\StorageDepartment;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

class StoragePaymentInvoiceHandler extends PaymentInvoiceManager
{
    /**
     * @var StorageBalance
     */
    private $storageBalance;

    /**
     * StoragePaymentInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param StorageBalance $storageBalance
     */
    public function __construct(DatabaseManager $databaseManager, StorageBalance $storageBalance)
    {
        parent::__construct($databaseManager);
        $this->storageBalance = $storageBalance;
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
     * @throws \Exception
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
     * Is storage invoice already implemented?
     *
     * @return bool
     */
    protected function isStorageInvoiceImplemented(): bool
    {
        return (bool)$this->getStorageInvoice()->implemented;
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
     * Subtract invoice sum from storage balance.
     *
     * @return bool
     */
    protected function fixOutgoingStorageInvoiceInBalance(): bool
    {
        $outgoingStorageDepartment = $this->getOutgoingStorageDepartment();

        if ($this->storageBalance->getBalance($outgoingStorageDepartment) < $this->getInvoiceSum()){
            return false;
        }

        return $this->storageBalance->addToCreditBalance($outgoingStorageDepartment, $this->getInvoiceSum());
    }

    /**
     * Add invoice sum to storage balance.
     *
     * @return bool
     */
    protected function fixIncomingStorageInvoiceInBalance(): bool
    {
        $incomingStorageDepartment = $this->getIncomingStorageDepartment();

        return $this->storageBalance->addToDebitBalance($incomingStorageDepartment, $this->getInvoiceSum());
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