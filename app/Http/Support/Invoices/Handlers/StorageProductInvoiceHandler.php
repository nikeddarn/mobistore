<?php
/**
 * Handler for product invoices that used storage.
 */

namespace App\Http\Support\Invoices\Handlers;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Models\Storage;
use App\Models\StorageProduct;

class StorageProductInvoiceHandler extends ProductInvoiceHandler implements InvoiceDirections
{
    /**
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return int Count of products that was added to invoice or subtracted from invoice.
     */
    protected function addProductsToInvoice(int $productId, float $price, int $quantity = 1, int $warranty = null): int
    {
        $addedCount = parent::addProductsToInvoice($productId, $price, $quantity, $warranty);

        return $addedCount;
    }

    /**
     * Delete products from invoice by product's id.
     *
     * @param int $productId
     * @return int Products count that was subtracted from invoice.
     * @throws \Exception
     */
    protected function deleteProductsFromInvoice(int $productId): int
    {
        $deletedCount = parent::deleteProductsFromInvoice($productId);

        return $deletedCount;
    }

    /**
     * Decrease product count in invoice
     *
     * @param int $productId
     * @param int $decreasingQuantity
     * @return int
     * @throws \Exception
     */
    protected function decreaseInvoiceProductCount(int $productId, int $decreasingQuantity):int
    {
        $deletedCount = parent::decreaseInvoiceProductCount($productId, $decreasingQuantity);

        return $deletedCount;
    }

    /**
     * @param Storage $storage
     * @param int $productId
     * @return StorageProduct|\Illuminate\Database\Eloquent\Model
     */
    protected function createStorageProduct(Storage $storage, int $productId)
    {
        return $storage->storageProduct()->create([
            'storages_id' => $storage->id,
            'products_id' => $productId,
        ]);
    }
}