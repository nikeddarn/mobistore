<?php
/**
 * Handler for vendor storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers;

use App\Models\Invoice;
use App\Models\Vendor;

class VendorStorageProductInvoiceHandler extends StorageProductInvoiceHandler
{
    /**
     * Set storage invoice as implemented.
     *
     * @return bool
     */
    public function setStorageInvoiceImplemented():bool
    {
        $storageInvoice = $this->invoice->storageInvoice->first();

        if ($storageInvoice){
            $storageInvoice->implemented = 1;
            $storageInvoice->save();
            return true;
        }
        return false;
    }

    /**
     * Set vendor invoice as implemented.
     *
     * @return bool
     */
    public function setVendorInvoiceImplemented():bool
    {
        $vendorInvoice = $this->invoice->vendorInvoice;

        if ($vendorInvoice){
            $vendorInvoice->implemented = 1;
            $vendorInvoice->save();
            return true;
        }
        return false;
    }

    /**
     * Get vendor.
     *
     * @return Vendor
     */
    public function getVendor():Vendor
    {
        return $this->invoice->vendorInvoice->vendor;
    }

    /**
     * Get vendor id.
     *
     * @return int
     */
    public function getVendorId():int
    {
        return $this->invoice->vendorInvoice->vendors_id;
    }

    /**
     * Get related user invoice.
     *
     * @return Invoice|null
     */
    public function getRelatedUserInvoice()
    {
        $relatedUserInvoice = $this->invoice->vendorInvoice->userInvoice->first();

        if ($relatedUserInvoice){
            return $relatedUserInvoice->invoice()->with('userInvoice')->first();
        }

        return null;
    }
}