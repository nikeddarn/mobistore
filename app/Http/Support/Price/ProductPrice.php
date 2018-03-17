<?php

/**
 * Calculate product price.
 */

namespace App\Http\Support\Price;

use App\Contracts\Currency\CurrenciesInterface;
use App\Http\Support\Currency\ExchangeRates;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductPrice implements CurrenciesInterface
{
    /**
     * @var ExchangeRates
     */
    private $exchangeRates;

    /**
     * @var Product
     */
    private $product;


    /**
     * ProductPrice constructor.
     * Retrieve user if exist.
     * @param ExchangeRates $exchangeRates
     * @param Product $product
     */
    public function __construct(ExchangeRates $exchangeRates, Product $product)
    {
        $this->exchangeRates = $exchangeRates;
        $this->product = $product;
    }

    /**
     * Get user product price by product model.
     *
     * @param Product $product
     * @return float|null
     */
    public function getUserPriceByProductModel(Product $product)
    {
        return $this->defineProductPrice($product);
    }

    /**
     * Get user product price by product id.
     *
     * @param int $productId
     * @return float|null
     */
    public function getUserPriceByProductId(int $productId)
    {
        $product = $this->retrieveProductById($productId);
        return $this->defineProductPrice($product);
    }

    /**
     * Get vendor product price by product model.
     *
     * @param Product $product
     * @param int $vendorId
     * @return float|null
     */
    public function getVendorPriceByProductModel(Product $product, int $vendorId)
    {
        return $this->getVendorPurchasePrice($product, $vendorId);
    }

    /**
     * Get vendor product price by product id.
     *
     * @param int $productId
     * @param int $vendorId
     * @return float|null
     */
    public function getVendorPriceByProductId(int $productId, int $vendorId)
    {
        $product = $this->retrieveProductById($productId);

        return $this->getVendorPurchasePrice($product, $vendorId);
    }

    /**
     * Get currency rate.
     *
     * @param string $currency
     * @return float
     */
    public function getRate($currency = self::USD)
    {
        return $this->exchangeRates->getRate($currency);
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
     * @return float|null
     */
    private function defineProductPrice(Product $product)
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