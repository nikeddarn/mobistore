<?php
/**
 * Handle payment vendor storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\PaymentInvoices\Cost;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Balance\VendorBalance;
use App\Http\Support\Invoices\Handlers\PaymentInvoices\PaymentInvoiceManager;
use App\Models\VendorInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class VendorCostInvoiceHandler extends PaymentInvoiceManager
{
    /**
     * @var VendorBalance
     */
    private $vendorBalance;

    /**
     * CompletedVendorStoragePaymentInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param VendorBalance $vendorBalance
     */
    public function __construct(DatabaseManager $databaseManager, VendorBalance $vendorBalance)
    {
        parent::__construct($databaseManager);
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
        if ($this->isVendorInvoiceIncoming()) {
            return $this->fixIncomingVendorInvoiceInBalance() && $this->setInvoiceFinished();
        }

        // fix outgoing vendor invoice
        if ($this->isVendorInvoiceOutgoing()) {
            return $this->fixOutgoingVendorInvoiceInBalance() && $this->setInvoiceFinished();
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
    protected function setInvoiceCancelled():bool
    {
        if ($this->isVendorInvoiceImplemented()){
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
    protected function deleteHandlingInvoice():bool
    {
        if ($this->isVendorInvoiceImplemented()){
            return false;
        }

        return parent::deleteHandlingInvoice();
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
    private function isVendorInvoiceIncoming()
    {
        return $this->getVendorInvoice()->direction === InvoiceDirections::INCOMING;
    }

    /**
     * Is vendor invoice incoming?
     *
     * @return bool
     */
    private function isVendorInvoiceOutgoing()
    {
        return $this->getVendorInvoice()->direction === InvoiceDirections::OUTGOING;
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
     * Get vendor invoice.
     *
     * @return VendorInvoice
     */
    private function getVendorInvoice():VendorInvoice
    {
        return $this->invoice->vendorInvoices->first();
    }
}