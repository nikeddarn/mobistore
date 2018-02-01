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
     * Get product price by product model.
     *
     * @param Product $product
     * @return float|null
     */
    public function getPriceByProductModel(Product $product)
    {
        return $this->defineProductPrice($product);
    }

    /**
     * Get product price by product id.
     *
     * @param int $productId
     * @return float|null
     */
    public function getPriceByProductId(int $productId)
    {
        $product = $this->retrieveProductById($productId);
        return $this->defineProductPrice($product);
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
            return $this->getVendorPrice($product);
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
                $query->where('stock_quantity', '>', 0);
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
    private function getVendorPrice(Product $product)
    {
        $userPriceColumn = $this->getUserPriceColumn();

        $minVendorColumnPrice = $product->vendorProduct->min('price' . $userPriceColumn);

        return $minVendorColumnPrice ? ($minVendorColumnPrice + $product->delivery_price) : null;
    }
}