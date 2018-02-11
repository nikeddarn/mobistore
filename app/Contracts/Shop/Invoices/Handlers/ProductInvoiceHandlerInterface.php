<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 06.01.18
 * Time: 18:52
 */

namespace App\Contracts\Shop\Invoices;


use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use Illuminate\Support\Collection;

interface ProductInvoiceHandlerInterface extends InvoiceHandlerInterface
{
    /**
     * Get products of invoice.
     *
     * @return Collection
     */
    public function getProducts(): Collection;

    /**
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return bool
     */
    public function addProducts(int $productId, float $price, int $quantity, int $warranty = null): bool;

    /**
     * Remove products from invoice by product's id.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function removeProducts(int $productId, int $quantity): bool;

    /**
     * Add collection of products
     *
     * @param array $products
     * @return bool
     */
//    public function addProductsCollection(array $products): bool;
}