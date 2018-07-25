<?php
/**
 * Handler for complete vendor reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\DefectProduct;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Statistics\Product\VendorReclamationStatistics;
use Illuminate\Database\DatabaseManager;

final class VendorReclamationInvoiceHandler extends VendorStorageReclamationProductInvoiceHandler
{
    /**
     * @var VendorReclamationStatistics
     */
    private $vendorReclamationStatistics;

    /**
     * VendorStorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param VendorReclamationStatistics $vendorReclamationStatistics
     */
    public function __construct(DatabaseManager $databaseManager, VendorReclamationStatistics $vendorReclamationStatistics)
    {
        parent::__construct($databaseManager);
        $this->vendorReclamationStatistics = $vendorReclamationStatistics;
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
     * Complete user invoice.
     *
     * @return bool
     */
    protected function completeVendorInvoice()
    {
        if (!parent::completeVendorInvoice()){
            return false;
        }

        if ($this->isStorageInvoiceImplemented() && $this->isVendorInvoiceIncoming()){
            return $this->fixIncomingVendorInvoice() && $this->fixOutgoingStorageInvoice() && $this->setInvoiceFinished();
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
            // add from active storage reclamation
            $storageDepartment->reclamation()->attach($reclamation->id);
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
            // remove from active storage reclamation
            $storageDepartment->reclamation()->detach($reclamation->id);
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

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active storage reclamation
            $vendor->reclamation()->detach($reclamation->id);
        }

        return $this->vendorReclamationStatistics->takeRejectedReclamationInvoiceToStatistic($vendor, $this->getProductsCount());
    }

    /**
     * Add incoming vendor data to statistics.
     *
     * @return bool
     */
    private function fixIncomingVendorInvoice(): bool
    {
        // retrieve vendor
        $vendor = $this->getVendorInvoice()->vendor;

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active storage reclamation
            $vendor->reclamation()->attach($reclamation->id);
        }

        return $this->vendorReclamationStatistics->takeAcceptedReclamationInvoiceToStatistic($vendor, $this->getProductsCount());
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
}