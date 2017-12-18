<?php

/**
 * Calculate product price.
 */

namespace App\Http\Support\Price;

use App\Contracts\Currency\CurrenciesInterface;
use App\Http\Support\Currency\ExchangeRates;
use App\Models\Product;

class ProductPrice implements CurrenciesInterface
{
    /**
     * Column name for retrieve price for this user.
     *
     * @var string
     */
    private $userPriceColumn;

    /**
     * Column name for retrieve markup of product for this user.
     *
     * @var string
     */
    private $userMarkupColumn;

    /**
     * @var ExchangeRates
     */
    private $exchangeRates;


    /**
     * ProductPrice constructor.
     * Retrieve user if exist.
     * @param ExchangeRates $exchangeRates
     */
    public function __construct(ExchangeRates $exchangeRates)
    {
        $this->defineUserPriceColumns();
        $this->exchangeRates = $exchangeRates;
    }

    public function getPrice(Product $product)
    {
        if (config('shop.can_use_vendor_price') && !$product->storageProduct->count()) {

            if ($product->vendorProduct->count()) {
                return $this->getMinimumVendorPrice($product);
            } else {
                return $product->{$this->userPriceColumn};
            }
        } else {
            return $product->{$this->userPriceColumn};
        }
    }

    public function getRate($currency = self::USD)
    {
        return $this->exchangeRates->getRate($currency);
    }

    private function getMinimumVendorPrice(Product $product)
    {
        $minPrice = null;

        $productMarkup = $product->{$this->userMarkupColumn};

        $product->vendorProduct->each(function ($vendorProduct) use (&$minPrice, $productMarkup) {
            if (!(empty($productMarkup) || empty($vendorProduct->offer_price) || empty($vendorProduct->delivery_price))) {
                $vendorOfferPrice = ($vendorProduct->offer_price + $vendorProduct->delivery_price) * $productMarkup;
                if (!isset($minPrice) || $vendorOfferPrice < $minPrice) {
                    $minPrice = $vendorOfferPrice;
                }
            }
            if (!empty($vendorProduct->{$this->userPriceColumn})) {
                if (!isset($minPrice) || $vendorProduct->{$this->userPriceColumn} < $minPrice) {
                    $minPrice = $vendorProduct->{$this->userPriceColumn};
                }
            }
        });

        return $minPrice;
    }

    /**
     * Define price column number from user price group or set retail price column.
     *
     * @return void
     */
    private function defineUserPriceColumns()
    {
        $user = auth('web')->user();

        $this->userPriceColumn = isset($user) ? 'price' . $user->price_group : 'price' . 1;
        $this->userMarkupColumn = isset($user) ? 'markup' . $user->price_group : 'markup' . 1;
    }
}