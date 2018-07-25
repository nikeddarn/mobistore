<?php
/**
 * Storage product reservation.
 */

namespace App\Http\Support\StockHandlers\Product;


use App\Models\StorageDepartment;
use App\Models\StorageProduct;

class StorageProductStockHandler
{
    /**
     * Reserve products on storage.
     *
     * @param StorageDepartment $storageDepartment
     * @param array $products
     * @return bool
     */
    public function reserveProductsOnStorage(StorageDepartment $storageDepartment, array $products): bool
    {
        foreach ($products as $productId => $reservingCount) {
            // get storage product
            $storageProduct = $this->getOrCreateStorageProduct($storageDepartment, $productId);

            // change reserved count
            $storageProduct->reserved_quantity += abs($reservingCount);

            $storageProduct->save();
        }

        return true;
    }

    /**
     *  Remove reserve of products on outgoing storage.
     *
     * @param StorageDepartment $storageDepartment
     * @param array $products
     * @return bool
     */
    public function removeReserveProductsFromStorage(StorageDepartment $storageDepartment, array $products): bool
    {
        foreach ($products as $productId => $reserveRemovingCount) {
            // get storage product
            $storageProduct = $this->getStorageProduct($storageDepartment, $productId);

            // change reserved count
            if ($storageProduct) {
                $storageProduct->reserved_quantity = max($storageProduct->reserved_quantity - abs($reserveRemovingCount), 0);

                $storageProduct->save();
            }
        }

        return true;
    }

    /**
     * Reserve  given count of product with given id.
     *
     * @param StorageDepartment $storageDepartment
     * @param int $productId
     * @param int $productCount
     * @return bool
     */
    public function reserveProductOnStorage(StorageDepartment $storageDepartment, int $productId, int $productCount):bool
    {
        $storageProduct = $this->getStorageProduct($storageDepartment, $productId);

        if (!$storageProduct){
            return false;
        }

        return $this->reserveCountOfStorageProduct($storageProduct, $productCount);
    }

    /**
     * Remove reserve given count of product with given id.
     *
     * @param StorageDepartment $storageDepartment
     * @param int $productId
     * @param int $productCount
     * @return bool
     */
    public function removeReserveProductOnStorage(StorageDepartment $storageDepartment, int $productId, int $productCount):bool
    {
        $storageProduct = $this->getStorageProduct($storageDepartment, $productId);

        if (!$storageProduct){
            return false;
        }

        return $this->removeReserveCountOfStorageProduct($storageProduct, $productCount);
    }

    /**
     * Reserve given count of StorageProduct.
     *
     * @param StorageProduct $storageProduct
     * @param int $reservingCount
     * @return bool
     */
    public function reserveCountOfStorageProduct(StorageProduct $storageProduct, int $reservingCount):bool
    {
        $storageProduct->reserved_quantity += abs($reservingCount);
        return $storageProduct->save();
    }

    /**
     * Remove given count of StorageProduct from storage reserve.
     *
     * @param StorageProduct $storageProduct
     * @param int $reserveRemovingCount
     * @return bool
     */
    public function removeReserveCountOfStorageProduct(StorageProduct $storageProduct, int $reserveRemovingCount):bool
    {
        $storageProduct->reserved_quantity = max($storageProduct->reserved_quantity - abs($reserveRemovingCount), 0);
        return $storageProduct->save();
    }

    /**
     * Get storage product.
     *
     * @param StorageDepartment $storageDepartment
     * @param int $productId
     * @return StorageProduct|\Illuminate\Database\Eloquent\Model|null
     */
    public function getStorageProduct(StorageDepartment $storageDepartment, int $productId)
    {
        return $storageDepartment->storageProduct()
            ->where([
                'storages_id' => $storageDepartment->id,
                'products_id' => $productId,
            ])
            ->first();
    }

    /**
     * Get or create storage product.
     *
     * @param StorageDepartment $storageDepartment
     * @param int $productId
     * @return StorageProduct|\Illuminate\Database\Eloquent\Model
     */
    public function getOrCreateStorageProduct(StorageDepartment $storageDepartment, int $productId): StorageProduct
    {
        return $storageDepartment->storageProduct()
            ->firstOrNew([
                'storages_id' => $storageDepartment->id,
                'products_id' => $productId,
            ]);
    }

    /**
     * Increase product stock count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @return bool
     */
    public function increaseProductStock(StorageProduct $storageProduct, int $quantity):bool
    {
        $storageProduct->stock_quantity += $quantity;

        return $storageProduct->save();
    }

    /**
     * Decrease product stock count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @return bool
     */
    public function decreaseProductStock(StorageProduct $storageProduct, int $quantity):bool
    {
        $storageProduct->stock_quantity -= $quantity;

        return $storageProduct->save();
    }
}