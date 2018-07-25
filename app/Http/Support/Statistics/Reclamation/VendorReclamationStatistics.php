<?php
/**
 * Vendor product statistics handler.
 */

namespace App\Http\Support\Statistics\Product;


use App\Models\Vendor;

class VendorReclamationStatistics
{
    /**
     * Take accepted reclamation invoice in statistic.
     *
     * @param Vendor $vendor
     * @param int $invoiceProductsCount
     * @return bool
     */
    public function takeAcceptedReclamationInvoiceToStatistic(Vendor $vendor, int $invoiceProductsCount):bool
    {
        // increase reclamation products count
        $vendor->reclamation_products_count += $invoiceProductsCount;

        return $vendor->save();
    }

    /**
     * Subtract returned invoice from statistic.
     *
     * @param Vendor $vendor
     * @param int $invoiceProductsCount
     * @return bool
     */
    public function takeRejectedReclamationInvoiceToStatistic(Vendor $vendor, int $invoiceProductsCount):bool
    {
        // increase rejected reclamation products count
        $vendor->rejected_reclamation_products_count += $invoiceProductsCount;

        return $vendor->save();
    }
}