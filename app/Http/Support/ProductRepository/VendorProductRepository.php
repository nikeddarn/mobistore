<?php
/**
 * Methods for find products on vendors
 */

namespace App\Http\Support\ProductRepository;


use App\Models\Product;
use App\Models\VendorProduct;

class VendorProductRepository
{
    /**
     * @var VendorProduct
     */
    private $vendorProduct;

    /**
     * @var Product
     */
    private $product;

    /**
     * StorageProductRepository constructor.
     * @param VendorProduct $vendorProduct
     * @param Product $product
     */
    public function __construct(VendorProduct $vendorProduct, Product $product)
    {
        $this->vendorProduct = $vendorProduct;
        $this->product = $product;
    }

    /**
     * Get count of products keyed by storage id.
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
     * @param int $productId
     * @return array
     */
    public function getProductsCountKeyedByVendorId(int $productId): array
    {
        return $this->vendorProduct
            ->select(['vendors_id', 'stock_quantity'])
            ->where('products_id', $productId)
            ->orderByDesc('stock_quantity')
            ->get()
            ->pluck('stock_quantity', 'vendors_id')
            ->toArray();
    }

    /**
     * Get keyed by product id array of available count of products  in total at all vendors.
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
            ->selectRaw('products.id AS id, SUM(vendor_products.stock_quantity) AS product_available_quantity')
            ->having('product_available_quantity', '>', 0)
            ->get()
            ->pluck('product_available_quantity', 'id')
            ->toArray();
    }
}