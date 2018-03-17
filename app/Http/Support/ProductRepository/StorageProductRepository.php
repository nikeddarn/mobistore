<?php
/**
 * Methods for find products on storages.
 */

namespace App\Http\Support\ProductRepository;


use App\Models\Product;

class StorageProductRepository
{
    /**
     * @var Product
     */
    private $product;

    /**
     * StorageProductRepository constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get available count of product in total at all storages.
     *
     * @param int $productId
     * @return int
     */
    public function getAvailableProductCount(int $productId):int
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