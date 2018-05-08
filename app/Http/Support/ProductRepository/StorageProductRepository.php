<?php
/**
 * Methods for find products on storages.
 */

namespace App\Http\Support\ProductRepository;


use App\Models\Product;
use App\Models\Storage;
use App\Models\StorageProduct;

class StorageProductRepository
{
    /**
     * @var StorageProduct
     */
    private $storageProduct;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Product
     */
    private $product;

    /**
     * StorageProductRepository constructor.
     * @param Storage $storage
     * @param StorageProduct $storageProduct
     * @param Product $product
     */
    public function __construct(Storage $storage, StorageProduct $storageProduct, Product $product)
    {
        $this->storageProduct = $storageProduct;
        $this->storage = $storage;
        $this->product = $product;
    }

    /**
     * Get count of products keyed by storage id.
     *
     * @param int $productId
     * @return array
     */
    public function getProductsCountKeyedByStorageId(int $productId): array
    {
        return $this->storageProduct
            ->select(['storages_id', 'stock_quantity'])
            ->where('products_id', $productId)
            ->orderByDesc('stock_quantity')
            ->get()
            ->pluck('stock_quantity', 'storages_id')
            ->toArray();
    }

    /**
     * Get array of storages id that have all needing products.
     *
     * @param array $products
     * @return array
     */
    public function getStoragesHaveAllProducts(array $products): array
    {
        $productsId = array_keys($products);

        return
            // retrieve storages that has all products
            $this->storage
                ->whereHas('storageProduct', function ($query) use ($productsId) {
                    $query->whereIn('products_id', $productsId);
                })
                ->with(['storageProduct' => function ($query) use ($productsId) {
                    $query->whereIn('products_id', $productsId);
                }])
                ->get()
                // filter storages that have needing product quantity
                ->filter(function (Storage $storage) use ($products) {
                    foreach ($storage->storageProduct->pluck('stock_quantity', 'products_id')->toArray() as $productId => $productStockQuantity) {
                        // needing product quantity more than storage product quantity
                        if ($products[$productId] > $productStockQuantity) {
                            // remove storage from collection
                            return false;
                        }
                    }
                    // stay storage in collection
                    return true;
                })
                ->pluck('id')
                ->toArray();
    }

    /**
     * Get available count of product in total at all storages.
     *
     * @param int $productId
     * @return int
     */
    public function getAvailableProductCount(int $productId): int
    {
        return (int)$this->product
            ->join('storage_products', 'products.id', '=', 'storage_products.products_id')
            ->where('id', $productId)
            ->groupBy('products.id')
            ->selectRaw('SUM(CAST(storage_products.stock_quantity AS SIGNED) - CAST(storage_products.reserved_quantity AS SIGNED)) AS product_available_quantity')
            ->first()->product_available_quantity;
    }

    /**
     * Get keyed by product id array of available count of products  in total at all storages.
     *
     * @param array $productIds
     * @return array
     */
    public function getAvailableProductsCountById(array $productIds): array
    {
        return $this->product
            ->join('storage_products', 'products.id', '=', 'storage_products.products_id')
            ->whereIn('products.id', $productIds)
            ->groupBy('products.id')
            ->selectRaw('products.id AS id, SUM(CAST(storage_products.stock_quantity AS SIGNED) - CAST(storage_products.reserved_quantity AS SIGNED)) AS product_available_quantity')
            ->having('product_available_quantity', '>', 0)
            ->get()
            ->pluck('product_available_quantity', 'id')
            ->toArray();
    }
}