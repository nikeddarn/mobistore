<?php
/**
 * Cart invoice viewer.
 */

namespace App\Http\Support\Invoices\Viewers\Product;


use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use App\Http\Support\Invoices\Viewers\InvoiceViewer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final class CartInvoiceViewer extends InvoiceViewer
{
    const HEADER_CART_IMAGES_PATH = 'images/products/small/';

    const FULL_CART_IMAGES_PATH = 'images/products/big/';

    /**
     * Get data for header cart.
     *
     * @param InvoiceHandlerInterface $invoiceHandler
     * @return array
     */
    public function getHeaderCartData(InvoiceHandlerInterface $invoiceHandler)
    {
        return [
            'productsCount' => $invoiceHandler->getProductsCount(),
            'totalSum' => $this->formatUsdPrice($invoiceHandler->getInvoiceSum()),
            'products' => $this->getHeaderCartProductsData($invoiceHandler->getInvoiceProducts()),
        ];
    }

    /**
     * Get data for full cart.
     *
     * @param InvoiceHandlerInterface $invoiceHandler
     * @return array
     */
    public function getFullCartData(InvoiceHandlerInterface $invoiceHandler)
    {
        return [
            'productsCount' => $invoiceHandler->getProductsCount(),
            'totalSum' => $this->formatUsdPrice($invoiceHandler->getInvoiceSum()),
            'totalLocalSum' => $this->formatLocalPrice($invoiceHandler->getInvoiceLocalSum()),
            'products' => $this->getFullCartProductsData($invoiceHandler->getInvoiceProducts()),
        ];
    }

    /**
     * Get product data for header cart.
     *
     * @param Collection $invoiceProducts
     * @return array
     */
    private function getHeaderCartProductsData(Collection $invoiceProducts)
    {
        $productsData = [];

        $imagePathPrefix = Storage::disk('public')->url(self::HEADER_CART_IMAGES_PATH);

        foreach ($invoiceProducts as $invoiceProduct){
            $productsData[] = [
                'id' => $invoiceProduct->product->id,
                'url' => $invoiceProduct->product->url,
                'title' => $invoiceProduct->product->page_title,
                'quantity' => $invoiceProduct->quantity,
                'price' => $this->formatUsdPrice($invoiceProduct->price),
                'image' => $invoiceProduct->product->primaryImage ? $imagePathPrefix . $invoiceProduct->product->primaryImage : null,
            ];
        }

        return $productsData;
    }

    /**
     * Get product data for full cart.
     *
     * @param Collection $invoiceProducts
     * @return array
     */
    private function getFullCartProductsData(Collection $invoiceProducts)
    {
        $productsData = [];

        $imagePathPrefix = Storage::disk('public')->url(self::FULL_CART_IMAGES_PATH);

        foreach ($invoiceProducts as $invoiceProduct){
            $productsData[] = [
                'id' => $invoiceProduct->product->id,
                'url' => $invoiceProduct->product->url,
                'title' => $invoiceProduct->product->page_title,
                'quantity' => $invoiceProduct->quantity,
                'price' => $this->formatUsdPrice($invoiceProduct->price),
                'total' => $this->formatUsdPrice($invoiceProduct->price * $invoiceProduct->quantity),
                'image' => $invoiceProduct->product->primaryImage ? $imagePathPrefix . $invoiceProduct->product->primaryImage : null,
            ];
        }

        return $productsData;
    }
}