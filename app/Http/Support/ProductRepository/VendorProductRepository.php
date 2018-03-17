<?php
/**
 * Methods for find products on vendors
 */

namespace App\Http\Support\ProductRepository;


use App\Models\Product;

class VendorProductRepository
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
     * Get available count of product in total at all vendors.
     *
     * @param int $productId
     * @return int
     */
    public function getAvailableProductCount(int $productId):int
    {
        return (int)$this->product
            ->join('vendor_products', 'products.id', '=', 'vendor_products.products_id')
            ->where('id', $productId)
            ->groupBy('products.id')
            ->selectRaw('SUM(vendor_products.stock_quantity) AS vendor_product_stock')
            ->first()->vendor_product_stock;
    }

    /**
     * Get keyed by products' id array of products count of all storages.
     *
     * @param array $productIds
     * @return array
     */
    public function getAvailableProductsCountById(array $productIds): array
    {
        return $this->product
            ->join('vendor_products', 'products.id', '=', 'vendor_products.products_id')
            ->whereIn('products.id', $productIds)
            ->groupBy('products.id')
            ->selectRaw('products.id AS id, SUM(vendor_products.stock_quantity) AS vendor_product_stock')
            ->having('vendor_product_stock', '>', 0)
            ->get()
            ->pluck('vendor_product_stock', 'id')
            ->toArray();
    }
}