<?php

/**
 * Calculate product price.
 */

namespace App\Http\Support\Price;

use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Shop\Prices\UserPriceGroups;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Collection;

class ProductPrice implements CurrenciesInterface
{
    /**
     * @var Product
     */
    private $product;


    /**
     * ProductPrice constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get user product price by product model.
     *
     * @param User|Authenticatable $user
     * @param Product $product
     * @return float|null
     */
    public function getUserPriceByProductModel(Product $product, $user = null)
    {
        $userPriceGroup = $user ? $user->price_group : UserPriceGroups::RETAIL;

        return $this->defineProductPriceForUser($product, $userPriceGroup);
    }

    /**
     * Get user product price by product id.
     *
     * @param User|Authenticatable $user
     * @param int $productId
     * @return float|null
     */
    public function getUserPriceByProductId(int $productId, $user = null)
    {
        $product = $this->retrieveProductById($productId);

        if (!$product){
            return null;
        }

        $userPriceGroup = $user ? $user->price_group : UserPriceGroups::RETAIL;

        return $this->defineProductPriceForUser($product, $userPriceGroup);
    }

    /**
     * Retrieve product by id.
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Model|Product|null
     */
    private function retrieveProductById(int $productId)
    {
        return $this->product
            ->where('id', $productId)
            ->with(['storageProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }])
            ->with(['vendorProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }])->first();
    }

    /**
     * Define price of product by its model.
     *
     * @param Product $product
     * @param int $userPriceGroup
     * @return float|null
     */
    private function defineProductPriceForUser(Product $product, int $userPriceGroup)
    {
        if ($this->hasStorageProduct($product) || !(config('shop.can_use_vendor_price') && $this->hasVendorProduct($product))) {
            return $product->{'price' . $this->getUserPriceColumn()};
        } else {
            return $this->getVendorSalePrice($product);
        }
    }

    /**
     * Get user price column from user model (if exists) or set retail column.
     *
     * @return int
     */
    private function getUserPriceColumn(): int
    {
        if (auth('web')->check()) {
            return auth('web')->user()->price_group;
        } else {
            return 1;
        }
    }

    /**
     * Has any storage product with given id ?
     *
     * @param Product $product
     * @return bool
     */
    private function hasStorageProduct(Product $product): bool
    {
        if (!$product->storageProduct instanceof Collection) {
            $product->load(['storageProduct' => function ($query) {
                $query->whereRaw('(stock_quantity - reserved_quantity) > 0');
            }]);
        }

        return (bool)$product->storageProduct->count();
    }

    /**
     * Has any vendor product with given id ?
     *
     * @param Product $product
     * @return bool
     */
    private function hasVendorProduct(Product $product): bool
    {
        if (!$product->vendorProduct instanceof Collection) {
            $product->load(['vendorProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }]);
        }

        return (bool)$product->vendorProduct->count();
    }

    /**
     * Get min price of vendors.
     *
     * @param Product $product
     * @return float|null
     */
    private function getVendorSalePrice(Product $product)
    {
        $userPriceColumn = $this->getUserPriceColumn();

        $minVendorColumnPrice = $product->vendorProduct->min('price' . $userPriceColumn);

        return $minVendorColumnPrice ? ($minVendorColumnPrice + $product->delivery_price) : null;
    }

    /**
     * Get vendor price.
     *
     * @param Product $product
     * @param int $vendorId
     * @return float
     */
    private function getVendorPurchasePrice(Product $product, int $vendorId):float
    {
        if (!$product->vendorProduct instanceof Collection) {
            $product->load('vendorProduct');
        }

        $vendorProduct = $product->vendorProduct->where('vendors_id', $vendorId)->first();

        return $vendorProduct->offer_price ? $vendorProduct->offer_price : $vendorProduct->price5;
    }
}