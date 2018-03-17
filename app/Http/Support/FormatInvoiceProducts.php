<?php
/**
 * Format invoice products data for view.
 */

namespace App\Http\Support;


use App\Models\InvoiceProduct;
use Illuminate\Support\Collection;

trait FormatInvoiceProducts
{
    /**
     * Format invoice products data for view.
     *
     * @param Collection $invoiceProducts
     * @param string|null $imageUrlPrefix
     * @return array
     */
    private function getFormattedInvoiceProductsData(Collection $invoiceProducts, string $imageUrlPrefix = null):array
    {
        $products = [];

        $invoiceProducts->each(function (InvoiceProduct $invoiceProduct) use ($imageUrlPrefix, &$products) {
            $products[] = [
                'id' => $invoiceProduct->product->id,
                'url' => $invoiceProduct->product->url,
                'title' => $invoiceProduct->product->page_title,
                'quantity' => $invoiceProduct->quantity,
                'price' => number_format($invoiceProduct->price, 2, '.', ','),
                'total' => number_format($invoiceProduct->price * $invoiceProduct->quantity, 2, '.', ','),
                'image' => $imageUrlPrefix ? $imageUrlPrefix . ($invoiceProduct->product->primaryImage ? $invoiceProduct->product->primaryImage->image : 'no_image.png') : null,
            ];
        });

        return $products;
    }
}