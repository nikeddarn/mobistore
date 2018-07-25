<?php

namespace App\Http\ViewComposers;

use App\Contracts\Shop\Badges\ProductBadgesInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Badges\UserBadges;
use App\Http\Support\Currency\ExchangeRates;
use App\Http\Support\Invoices\Fabrics\User\Product\CartInvoiceFabric;
use App\Http\Support\Price\DeliveryPrice;
use App\Models\Brand;
use App\Models\Category;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CommonComposer implements ProductBadgesInterface, InvoiceDirections
{
    /**
     * @var Authenticatable
     */
    private $user;
    /**
     * @var Category
     */
    private $category;
    /**
     * @var Brand
     */
    private $brand;
    /**
     * @var CartInvoiceFabric
     */
    private $cartInvoiceFabric;
    /**
     * @var DeliveryPrice
     */
    private $deliveryPrice;

    /**
     * @var ExchangeRates
     */
    private $exchangeRates;
    /**
     * @var UserBadges
     */
    private $userBadges;


    /**
     * CommonComposer constructor.
     *
     * @param Category $category
     * @param Brand $brand
     * @param CartInvoiceFabric $cartInvoiceFabric
     * @param DeliveryPrice $deliveryPrice
     * @param ExchangeRates $exchangeRates
     * @param UserBadges $userBadges
     */
    public function __construct(Category $category, Brand $brand, CartInvoiceFabric $cartInvoiceFabric, DeliveryPrice $deliveryPrice, ExchangeRates $exchangeRates, UserBadges $userBadges)
    {
        $this->category = $category;
        $this->brand = $brand;
        $this->cartInvoiceFabric = $cartInvoiceFabric;
        $this->deliveryPrice = $deliveryPrice;
        $this->exchangeRates = $exchangeRates;
        $this->userBadges = $userBadges;
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     * @throws \Exception
     */
    public function compose(View $view)
    {
        $this->user = auth('web')->user();

        $view->with('categoriesList', $this->getCategories())
            ->with('brandsList', $this->getBrands())
            ->with('headerCartData', $this->getCartProductsData())
            ->with('featuresData', $this->getFeaturesData());

        if (auth('web')->check()) {
            $view->with('userData', $this->getUserData());
        }
    }

    /**
     * Retrieve root's children.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getCategories(): Collection
    {
        $rootCategory = $this->category->whereIsRoot()->first();

        return $rootCategory ? $rootCategory->children : null;
    }

    /**
     * Retrieve brands
     *
     * @return \Illuminate\Support\Collection
     */
    private function getBrands(): Collection
    {
        return $this->brand->orderBy('priority', 'asc')->get();
    }

    /**
     * Get formatted cart products property.
     *
     * @return array
     * @throws Exception
     */
    private function getCartProductsData()
    {

        $userCart = $this->cartInvoiceFabric->getRepository()->getCart($this->user, cookie('cart'));

        if ($userCart) {
            $cartHandler = $this->cartInvoiceFabric->bindInvoiceToHandler($userCart);

            if ($cartHandler->isUserCartExpired(config('shop.cart.cart_expire_days'))) {
                return [];
            }

            if ($cartHandler->isUserCartExpired(config('shop.cart.cart_product_price_expire_days'))) {
                $cartHandler->updateExchangeRate();
                $cartHandler->updateProductsPrices();
            }

            return $this->cartInvoiceFabric->getInvoiceViewer()->getHeaderCartData($cartHandler);
        } else {
            return [];
        }
    }

    /**
     * Get user data.
     *
     * @return array
     */
    private function getUserData()
    {
        $userBadges = $this->userBadges->getUserBadges($this->user);

        return [
            'userName' => explode(' ', $this->user->name)[0],
            'userBadges' => [
                'count' => array_sum($userBadges),
                'badges' => $userBadges,
            ]
        ];
    }

    /**
     * Get features panel data.
     *
     * @return array
     */
    private function getFeaturesData()
    {
        $minFreeDeliveringInvoiceSum = ceil($this->deliveryPrice->getFreeDeliveryMinSum($this->user) * $this->exchangeRates->getRate() / 10) * 10;

        return [
            'free_delivery_from' => trans('shop.delivery.price.paid', [
                'sum' => number_format($minFreeDeliveringInvoiceSum),
            ]),
            'payment' => trans('shop.payment.on_delivered'),
        ];
    }
}