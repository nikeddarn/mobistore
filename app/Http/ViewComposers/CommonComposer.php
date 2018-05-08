<?php

namespace App\Http\ViewComposers;

use App\Contracts\Shop\Badges\BadgeTypes;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Currency\ExchangeRates;
use App\Http\Support\Invoices\Fabrics\CartInvoiceFabric;
use App\Http\Support\Price\DeliveryPrice;
use App\Http\Support\Price\ProductPrice;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\UserInvoice;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CommonComposer implements BadgeTypes, InvoiceDirections
{
    /**
     * @var string
     */
    const CART_COOKIE_NAME = 'cart';

    /**
     * @var Category
     */
    private $category;

    /**
     * @var Brand
     */
    private $brand;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductPrice
     */
    private $productPrice;

    /**
     * @var ProductBadges
     */
    private $productBadges;

    /**
     * @var Request
     */
    private $request;

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
     * @var Authenticatable
     */
    private $user;
    /**
     * @var UserInvoice
     */
    private $userInvoice;


    /**
     * CommonComposer constructor.
     * @param Request $request
     * @param Category $category
     * @param Brand $brand
     * @param Product $product
     * @param ProductPrice $productPrice
     * @param ProductBadges $productBadges
     * @param CartInvoiceFabric $cartInvoiceFabric
     * @param DeliveryPrice $deliveryPrice
     * @param ExchangeRates $exchangeRates
     * @param UserInvoice $userInvoice
     */
    public function __construct(
        Request $request, Category $category,
        Brand $brand,
        Product $product,
        ProductPrice $productPrice,
        ProductBadges $productBadges,
        CartInvoiceFabric $cartInvoiceFabric,
        DeliveryPrice $deliveryPrice,
        ExchangeRates $exchangeRates,
        UserInvoice $userInvoice
    )
    {
        $this->request = $request;
        $this->category = $category;
        $this->brand = $brand;
        $this->product = $product;
        $this->productPrice = $productPrice;
        $this->productBadges = $productBadges;
        $this->cartInvoiceFabric = $cartInvoiceFabric;
        $this->deliveryPrice = $deliveryPrice;
        $this->exchangeRates = $exchangeRates;
        $this->userInvoice = $userInvoice;
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

        $view
            ->with('categoriesList', $this->getCategoriesTree())
            ->with('brandsList', $this->getBrands())
            ->with('cartProducts', $this->getCartProducts())
            ->with('actionList', $this->getActionProducts())
            ->with('featuresData', $this->getFeaturesData());

        if (auth('web')->check()) {
            $view
                ->with('favouritesList', $this->getFavourites())
                ->with('recentList', $this->getRecentProducts())
                ->with('userData', $this->getUserData());
        }
    }

    /**
     * Retrieve root's children.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getCategoriesTree(): Collection
    {
        $categories = $this->category->withDepth()->get()->toTree();

        return $categories->count() ? $categories[0]->children : collect();
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
     * Create user favourite products list.
     *
     * @return array
     */
    private function getFavourites(): array
    {
        $products = $this->getRetrieveProductQuery()
            ->whereHas('favouriteProduct', function ($query) {
                $query->where('users_id', $this->user->id);
            })
            ->get();

        return $this->formProductData($products);
    }

    /**
     * Create action products list.
     *
     * @return array
     */
    private function getActionProducts(): array
    {
        $products = $this->getRetrieveProductQuery()
            ->whereHas('productBadge', function ($query) {
                $query->where([
                    ['badges_id', '=', self::PRICE_DOWN],
                    ['updated_at', '>=', Carbon::now()->subDays(config('shop.badges')[self::PRICE_DOWN]['ttl'])],
                ])
                    ->orWhere('badges_id', self::ACTION);
            })
            ->get();

        return $this->formProductData($products);
    }

    /**
     * Create user recent products list.
     *
     * @return array
     */
    private function getRecentProducts()
    {
        $products = $this->getRetrieveProductQuery()
            ->whereHas('recentProduct', function ($query) {
                $query->where('users_id', $this->user->id);
            })
            ->join('recent_products', 'recent_products.products_id', '=', 'products.id')
            ->orderByDesc('recent_products.updated_at')
            ->limit(config('shop.recent_products_show'))
            ->get();

        return $this->formProductData($products);
    }

    /**
     * Create retrieve products builder.
     *
     * @return Builder
     */
    private function getRetrieveProductQuery(): Builder
    {
        return $this->product
            ->with('primaryImage')
            ->with(['storageProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }])
            ->with(['vendorProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }])
            ->with('productBadge.badge');
    }

    /**
     * Prepare data for each product
     *
     * @param Collection $products
     * @return array
     */
    private function formProductData(Collection $products): array
    {
        $productsData = [];

        $rate = $this->productPrice->getRate();

        $imageUrlPrefix = Storage::disk('public')->url('images/products/small/');

        $products->each(function (Product $product) use ($rate, $imageUrlPrefix, &$productsData) {

            $price = $this->productPrice->getUserPriceByProductModel($product);

            $productsData[] = [
                'id' => $product->id,
                'url' => $product->url,
                'image' => $imageUrlPrefix . ($product->primaryImage ? $product->primaryImage->image : 'no_image.png'),
                'title' => $product->page_title,
                'price' => $price ? number_format($price, 2, '.', ',') : null,
                'priceUah' => $price && $rate ? number_format($price * $rate, 2, '.', ',') : null,
                'stockStatus' => $product->storageProduct->count() ? 1 : ($product->vendorProduct->count() ? 0 : null),
                'badges' => $this->productBadges->createBadges($product->productBadge),
                'isFavourite' => true,
            ];
        });

        return $productsData;
    }

    /**
     * Get formatted cart products property.
     *
     * @return array
     * @throws \Exception
     */
    private function getCartProducts()
    {
        $cartRepository = $this->cartInvoiceFabric->getRepository();

        if ($cartRepository->cartExists()) {
            $handleableCart = $this->cartInvoiceFabric->getHandler();
            $handleableCart->bindInvoice($cartRepository->getRetrievedInvoice());

            if ($handleableCart->getUpdateTime() < Carbon::today()->subDays(1)) {
                $handleableCart->updateProductsPrices();
            }

            $productImagePathPrefix = Storage::disk('public')->url('images/products/small/');

            $productsCount = $handleableCart->getProductsCount();

            return [
                'productsCount' => $productsCount . '&nbsp;' . trans_choice('shop.products', $productsCount),
                'products' => $handleableCart->getFormattedProducts($handleableCart->getInvoiceProducts(), $productImagePathPrefix),
                'totalSum' => $handleableCart->getInvoiceSum(),
            ];
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
        $userBadges = $this->getUserBadges();

        return [
            'userName' => explode(' ', $this->user->name)[0],
            'userBadges' => [
                'count' => array_sum($userBadges),
                'badges' => $userBadges,
            ]
        ];
    }

    /**
     * Get user badges
     *
     * @return array
     */
    private function getUserBadges()
    {
        $userBadges = [];

        $notifications = $this->user->unreadNotifications()->where('created_at', '>', Carbon::now()->subDays(config('shop.show_unread_message_days')))->count();
        if ($notifications) {
            $userBadges['message'] = $notifications;
        }

        $deliveries = $this->userInvoice
            ->where([
                ['users_id', $this->user->id],
                ['implemented', 0],
                ['direction', self::INCOMING]
            ])
            ->whereHas('invoice', function ($query){
                $query->whereIn('invoice_types_id', [
                    InvoiceTypes::ORDER,
                    InvoiceTypes::PRE_ORDER,
                    InvoiceTypes::EXCHANGE_RECLAMATION,
                    InvoiceTypes::RETURN_RECLAMATION,
                ]);
                $query->where('invoice_status_id', InvoiceStatusInterface::PROCESSING);
            })
            ->count();
        if ($deliveries) {
            $userBadges['delivery'] = $deliveries;
        }

        return $userBadges;
    }

    /**
     * Get features panel data.
     *
     * @return array
     */
    private function getFeaturesData()
    {
        return [
            'free_delivery_from' => trans('shop.delivery.price.paid', [
                'sum' => ceil($this->deliveryPrice->getFreeDeliveryMinSum($this->user) * $this->exchangeRates->getRate() / 10) * 10,
            ]),
            'payment' => trans('shop.payment.on_delivered'),
        ];
    }

}