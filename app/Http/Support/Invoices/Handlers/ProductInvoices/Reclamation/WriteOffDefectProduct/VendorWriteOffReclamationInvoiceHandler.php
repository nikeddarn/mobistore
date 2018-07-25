<?php
/**
 * Handler for complete vendor reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\WriteOffDefectProduct;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Balance\VendorBalance;
use App\Models\VendorInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class VendorWriteOffReclamationInvoiceHandler extends WriteOffDefectProductManager
{
    /**
     * @var VendorBalance
     */
    private $vendorBalance;

    /**
     * VendorStorageReclamationProductInvoiceHandler constructor.
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

            if (static::completeVendorInvoice()){

                $this->databaseManager->commit();

                return true;
            }else{
                $this->databaseManager->rollback();

                return false;
            }
        } catch (Exception $e) {
            $this->databaseManager->rollback();

            return false;
        }
    }

    /**
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled(): bool
    {
        if (!$this->isVendorInvoiceImplemented()) {
            return parent::setInvoiceCancelled();
        } else {
            return false;
        }
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
     * Complete user invoice.
     *
     * @return bool
     */
    private function completeVendorInvoice()
    {
        if (!$this->setVendorInvoiceImplemented()){
            return false;
        }

        if ($this->isVendorInvoiceIncoming()){
            return $this->fixVendorInvoice() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Complete vendor invoice.
     *
     * @return bool
     */
    private function setVendorInvoiceImplemented()
    {
        if (!$this->isInvoiceProcessing()){
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
     * Increase vendor balance on invoice sum.
     *
     * @return bool
     */
    private function fixVendorInvoice():bool
    {
        // retrieve vendor
        $vendor = $this->getVendorInvoice()->vendor;

        // increase vendor balance on invoice sum
        $this->vendorBalance->addToDebitBalance($vendor, $this->invoice->invoice_sum);

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active vendor reclamation
            $vendor->reclamation()->detach($reclamation->id);
        }

        return true;
    }

    /**
     * Is vendor invoice already implemented?
     *
     * @return bool
     */
    private function isVendorInvoiceImplemented(): bool
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
     * Get vendor invoice.
     *
     * @return VendorInvoice
     */
    private function getVendorInvoice():VendorInvoice
    {
        return $this->invoice->vendorInvoices->first();
    }
}