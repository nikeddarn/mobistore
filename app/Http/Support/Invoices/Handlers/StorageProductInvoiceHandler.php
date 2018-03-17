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

        $this->reserveProductsOnStorage($productId, $addedCount);

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

        $this->reserveProductsOnStorage($productId, $deletedCount * -1);

        return $deletedCount;
    }

    /**
     * Reserve products on outgoing store. If $reservingCount is negative, products will be unreserved by this count.
     *
     * @param int $productId
     * @param int $reservingCount
     */
    private function reserveProductsOnStorage(int $productId, int $reservingCount)
    {
        $outgoingStorage = $this->getOutgoingStorage();

        if ($outgoingStorage) {

            $storageProduct = $outgoingStorage->storageProduct->keyBy('products_id')->get($productId);

            if (!$storageProduct) {
                $storageProduct = $this->createStorageProduct($outgoingStorage, $productId);
                $outgoingStorage->storageProduct->push($storageProduct);
            }

            $storageProduct->reserved_quantity = max($storageProduct->reserved_quantity + $reservingCount, 0);
            $storageProduct->save();
        }
    }

    /**
     * Get outgoing storage from invoice.
     *
     * @return Storage|null
     */
    private function getOutgoingStorage()
    {
        $outgoingStorageInvoice = $this->invoice->storageInvoice;

        if ($outgoingStorageInvoice->direction === self::OUTGOING){
            return $outgoingStorageInvoice->storage;
        }else{
            return null;
        }
    }

    /**
     * @param Storage $storage
     * @param int $productId
     * @return StorageProduct|\Illuminate\Database\Eloquent\Model
     */
    private function createStorageProduct(Storage $storage, int $productId)
    {
        return $storage->storageProduct()->create([
            'storages_id' => $storage->id,
            'products_id' => $productId,
        ]);
    }
}