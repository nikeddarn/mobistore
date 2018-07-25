<?php
/**
 * Vendor product statistics handler.
 */

namespace App\Http\Support\Statistics\Product;


use App\Models\Vendor;

class VendorProductStatistics
{
    /**
     * Take bought invoice in statistic.
     *
     * @param Vendor $vendor
     * @param int $invoiceProductsCount
     * @param float $invoiceSum
     * @return bool
     */
    public function takeBoughtInvoiceToStatistic(Vendor $vendor, int $invoiceProductsCount, float $invoiceSum):bool
    {
        // increase count of purchases
        $vendor->puchases_count += 1;

        // increase purchased products count
        $vendor->purchased_products_count += $invoiceProductsCount;

        // increase total sum of purchased products
        $vendor->purchosed_products_sum += $invoiceSum;

        return $vendor->save();
    }

    /**
     * Subtract returned invoice from statistic.
     *
     * @param Vendor $vendor
     * @param int $invoiceProductsCount
     * @param float $invoiceSum
     * @return bool
     */
    public function subtractReturnedInvoiceFromStatistic(Vendor $vendor, int $invoiceProductsCount, float $invoiceSum):bool
    {
        // increase count of purchases
        $vendor->puchases_count -= 1;

        // increase purchased products count
        $vendor->purchased_products_count -= $invoiceProductsCount;

        // increase total sum of purchased products
        $vendor->purchosed_products_sum -= $invoiceSum;

        return $vendor->save();
    }
}