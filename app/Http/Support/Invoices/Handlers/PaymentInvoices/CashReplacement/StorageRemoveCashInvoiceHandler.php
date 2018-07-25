<?php
/**
 * Handler for complete storage to storage cash replacement invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Replacement\ReplaceProduct;


use App\Http\Support\Balance\StorageBalance;
use App\Http\Support\Invoices\Handlers\PaymentInvoices\PaymentInvoiceManager;
use App\Models\StorageDepartment;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class StorageRemoveCashInvoiceHandler extends PaymentInvoiceManager
{
    /**
     * @var StorageBalance
     */
    private $storageBalance;

    /**
     * StorageRemoveProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param StorageBalance $storageBalance
     */
    public function __construct(DatabaseManager $databaseManager, StorageBalance $storageBalance)
    {
        parent::__construct($databaseManager);
        $this->storageBalance = $storageBalance;
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

        return $this->storageBalance->addToDebitBalance($incomingStorageDepartment, $this->getInvoiceSum()) && $this->storageBalance->addToCreditBalance($outgoingStorageDepartment, $this->getInvoiceSum());
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