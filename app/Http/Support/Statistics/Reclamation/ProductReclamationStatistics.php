<?php
/**
 * Vendor product statistics handler.
 */

namespace App\Http\Support\Statistics\Product;


use App\Models\Reclamation;

class ProductReclamationStatistics
{
    /**
     * Take accepted reclamation invoice in statistic.
     *
     * @param Reclamation $reclamation
     * @return bool
     */
    public function takeAcceptedReclamationInvoiceToStatistic(Reclamation $reclamation):bool
    {
        $product = $reclamation->product()->first();

        // increase reclamation products count
        $product->defect_quantity += 1;

        return $product->save();
    }

    /**
     * Subtract returned invoice from statistic.
     *
     * @param Reclamation $reclamation
     * @return bool
     */
    public function takeRejectedReclamationInvoiceToStatistic(Reclamation $reclamation):bool
    {
        $product = $reclamation->product()->first();

        // decrease reclamation products count
        $product->defect_quantity -= 1;

        return $product->save();
    }
}