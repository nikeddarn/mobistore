<?php
/**
 * User product statistics handler.
 */

namespace App\Http\Support\Statistics\Product;


use App\Models\User;

class UserProductStatistics
{
    /**
     * Take bought invoice in statistic.
     *
     * @param User $user
     * @param int $invoiceProductsCount
     * @param float $invoiceSum
     * @return bool
     */
    public function takeBoughtInvoiceToStatistic(User $user, int $invoiceProductsCount, float $invoiceSum):bool
    {
        // increase count of purchases
        $user->puchases_count += 1;

        // increase purchased products count
        $user->purchased_products_count += $invoiceProductsCount;

        // increase total sum of purchased products
        $user->purchosed_products_sum += $invoiceSum;

        return $user->save();
    }

    /**
     * Subtract returned invoice from statistic.
     *
     * @param User $user
     * @param int $invoiceProductsCount
     * @param float $invoiceSum
     * @return bool
     */
    public function subtractReturnedInvoiceFromStatistic(User $user, int $invoiceProductsCount, float $invoiceSum):bool
    {
        // decrease count of purchases
        $user->puchases_count -= 1;

        // decrease purchased products count
        $user->purchased_products_count -= $invoiceProductsCount;

        // decrease total sum of purchased products
        $user->purchosed_products_sum -= $invoiceSum;

        return $user->save();
    }
}