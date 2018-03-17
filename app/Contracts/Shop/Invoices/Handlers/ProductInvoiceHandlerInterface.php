<?php
/**
 * Methods for handling product invoice.
 */

namespace App\Contracts\Shop\Invoices\Handlers;

use Illuminate\Support\Collection;

interface ProductInvoiceHandlerInterface extends InvoiceHandlerInterface
{
    /**
     * Get products of invoice.
     *
     * @return Collection
     */
    public function getInvoiceProducts(): Collection;

    /**
     * Create array of products data for view.
     *
     * @param string|null $imageUrlPrefix
     * @return array
     */
    public function getFormattedProducts(string $imageUrlPrefix = null): array;

    /**
     * Is product with given id already in cart ?
     *
     * @param int $productId
     * @return bool
     */
    public function productExists(int $productId):bool ;

    /**
     * Get total count of products of invoice
     *
     * @return int
     */
    public function getProductsCount():int;

    /**
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return int Added products count.
     */
    public function appendProducts(int $productId, float $price, int $quantity = 1, int $warranty = null): int ;

    /**
     * Remove all products from invoice by product's id.
     *
     * @param int $productId
     * @return int Deleted products count.
     */
    public function deleteProducts(int $productId): int ;
}