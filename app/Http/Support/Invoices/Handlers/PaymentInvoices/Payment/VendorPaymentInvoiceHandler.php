<?php
/**
 * Handle payment vendor storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\PaymentInvoices\Payment;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Balance\StorageBalance;
use App\Http\Support\Balance\VendorBalance;
use App\Models\VendorInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class VendorPaymentInvoiceHandler extends StoragePaymentInvoiceHandler
{
    /**
     * @var VendorBalance
     */
    private $vendorBalance;

    /**
     * VendorPaymentInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param StorageBalance $storageBalance
     * @param VendorBalance $vendorBalance
     */
    public function __construct(DatabaseManager $databaseManager, StorageBalance $storageBalance, VendorBalance $vendorBalance)
    {
        parent::__construct($databaseManager, $storageBalance);
        $this->vendorBalance = $vendorBalance;
    }

    /**
     * Set vendor invoice as implemented.
     *
     * @return bool
     * @throws \Exception
     */
    public function implementVendorInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeVendorInvoice()) {

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
        if (!parent::completeStorageInvoice()){
            return false;
        }

        // fix incoming storage invoice
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
        if (!$this->setVendorInvoiceImplemented()){
            return false;
        }

        // fix incoming vendor invoice
        if ($this->isStorageInvoiceImplemented() && $this->isVendorInvoiceIncoming()) {
            return $this->fixIncomingVendorInvoiceInBalance() && $this->fixOutgoingStorageInvoiceInBalance() && $this->setInvoiceFinished();
        }

        return false;
    }

    /**
     * Complete vendor invoice.
     *
     * @return bool
     */
    protected function setVendorInvoiceImplemented()
    {
        if (!$this->isInvoiceProcessing()) {
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
     * Is vendor invoice incoming?
     *
     * @return bool
     */
    private function isVendorInvoiceIncoming()
    {
        return $this->getVendorInvoice()->direction === InvoiceDirections::INCOMING;
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
     * Is invoice completed yet?
     *
     * @return bool
     */
    private function isInvoiceCompleted(): bool
    {
        return $this->isStorageInvoiceImplemented() && $this->isVendorInvoiceImplemented();
    }

    /**
     * Decrease vendor balance on invoice sum.
     *
     * @return bool
     */
    private function fixOutgoingVendorInvoiceInBalance(): bool
    {
        // retrieve vendor
        $vendor = $this->getVendorInvoice()->vendor;

        return $this->vendorBalance->addToCreditBalance($vendor, $this->getInvoiceSum());
    }

    /**
     * Increase vendor balance on invoice sum.
     *
     * @return bool
     */
    private function fixIncomingVendorInvoiceInBalance(): bool
    {
        // retrieve vendor
        $vendor = $this->getVendorInvoice()->vendor;

        return $this->vendorBalance->addToDebitBalance($vendor, $this->getInvoiceSum());
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