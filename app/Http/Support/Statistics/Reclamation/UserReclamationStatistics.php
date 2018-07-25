<?php
/**
 * User product statistics handler.
 */

namespace App\Http\Support\Statistics\Product;


use App\Models\User;

class UserReclamationStatistics
{
    /**
     * Take accepted reclamation invoice in statistic.
     *
     * @param User $user
     * @param int $invoiceProductsCount
     * @return bool
     */
    public function takeAcceptedReclamationInvoiceToStatistic(User $user, int $invoiceProductsCount):bool
    {
        // increase reclamation products count
        $user->reclamation_products_count += $invoiceProductsCount;

        return $user->save();
    }

    /**
     * Subtract returned invoice from statistic.
     *
     * @param User $user
     * @param int $invoiceProductsCount
     * @return bool
     */
    public function takeRejectedReclamationInvoiceToStatistic(User $user, int $invoiceProductsCount):bool
    {
        // increase rejected reclamation products count
        $user->rejected_reclamation_products_count += $invoiceProductsCount;

        return $user->save();
    }
}