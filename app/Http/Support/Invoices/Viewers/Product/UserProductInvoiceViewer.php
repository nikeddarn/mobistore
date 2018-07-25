<?php
/**
 * User product invoice viewer.
 */

namespace App\Http\Support\Invoices\Viewers\Product;


use App\Http\Support\Invoices\Viewers\InvoiceViewer;
use App\Models\InvoiceProduct;
use Illuminate\Support\Collection;

class UserProductInvoiceViewer extends InvoiceViewer
{
    /**
     * Create array of products data for view.
     *
     * @param Collection $invoiceProducts
     * @param string|null $imageUrlPrefix
     * @return array
     */
    public function getFormattedProducts(Collection $invoiceProducts, string $imageUrlPrefix = null):array
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

    /**
     * Get prepared invoices for view.
     *
     * @param Collection $invoices
     * @return array
     */
    public function prepareInvoices(Collection $invoices): array
    {
        // TODO: Implement prepareInvoices() method.
    }
}