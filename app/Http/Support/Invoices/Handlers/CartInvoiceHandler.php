<?php
/**
 * Additional methods for handle cart data.
 */

namespace App\Http\Support\Invoices\Handlers;


use App\Models\InvoiceProduct;

final class CartInvoiceHandler extends ProductInvoiceHandler
{
    /**
     * Calculate sum of products cost by their id.
     *
     * @param array $productsId
     * @return float
     */
    public function calculateProductsSum(array $productsId):float
    {
        return $this->invoice->invoiceProduct->whereIn('products_id', $productsId)->sum(function (InvoiceProduct $invoiceProduct){
            return $invoiceProduct->quantity * $invoiceProduct->price;
        });
    }
}