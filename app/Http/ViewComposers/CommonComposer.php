<?php

namespace App\Http\ViewComposers;

use App\Contracts\Shop\Badges\BadgeTypes;
use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Invoice\Repository\CartRepository;
use App\Http\Support\Invoices\Handlers\ProductInvoiceHandler;
use App\Http\Support\Price\ProductPrice;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CommonComposer implements BadgeTypes
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
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @var ProductInvoiceHandler
     */
    private $invoiceHandler;


    /**
     * CommonComposer constructor.
     * @param Request $request
     * @param Category $category
     * @param Brand $brand
     * @param Product $product
     * @param ProductPrice $productPrice
     * @param ProductBadges $productBadges
     * @param CartRepository $cartRepository
     * @param ProductInvoiceHandler $invoiceHandler
     */
    public function __construct(Request $request, Category $category, Brand $brand, Product $product, ProductPrice $productPrice, ProductBadges $productBadges, CartRepository $cartRepository, ProductInvoiceHandler $invoiceHandler)
    {
        $this->request = $request;
        $this->category = $category;
        $this->brand = $brand;
        $this->product = $product;
        $this->productPrice = $productPrice;
        $this->productBadges = $productBadges;
        $this->cartRepository = $cartRepository;
        $this->invoiceHandler = $invoiceHandler;
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
        $view
            ->with('categoriesList', $this->getCategoriesTree())
            ->with('brandsList', $this->getBrands())
            ->with('cartProducts', $this->getCartProducts())
            ->with('actionList', $this->getActionProducts());

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
                    $query->where('users_id', auth('web')->user()->id);
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
                    $query->where('users_id', auth('web')->user()->id);
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

            $price = $this->productPrice->getPriceByProductModel($product);

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
        $userCart = $this->getUserCart();
        if ($userCart) {
            $this->invoiceHandler->bindInvoice($userCart);

            if (!$this->invoiceHandler->isInvoiceCommitted() && $userCart->updated_at->timestamp <= Carbon::now()->subDays(config('shop.invoice_exchange_rate_ttl'))->timestamp) {
                $this->invoiceHandler->updateInvoiceExchangeRate();
            }

            $productImagePathPrefix = Storage::disk('public')->url('images/products/small/');

            $productsCount = $this->invoiceHandler->getProductsCount();

            return [
                'productsCount' => $productsCount . '&nbsp;' . trans_choice('shop.products', $this->invoiceHandler->getProductsCount()),
                'products' => $this->invoiceHandler->getFormattedProducts($productImagePathPrefix),
                'totalSum' => $this->invoiceHandler->getInvoiceSum(),
            ];
        } else {
            return [];
        }
    }

    /**
     * Retrieve or create user cart.
     *
     * @return Invoice|\Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    private function getUserCart()
    {
        if (auth('web')->check()) {
            $user = auth('web')->user();
            $userCart = $this->cartRepository->getByUserId($user->id);
        } elseif ($this->request->hasCookie(self::CART_COOKIE_NAME)) {
            $userCart = $this->cartRepository->getByUserCookie($this->request->cookie(self::CART_COOKIE_NAME));
        } else {
            $userCart = null;
        }

        return ($userCart && $userCart->updated_at->timestamp > Carbon::now()->subDays(config('shop.user_cart_ttl'))->timestamp) ? $userCart : null;
    }

    private function getUserData()
    {
        $user = auth('web')->user();

        return [
            'userName' => explode(' ', $user->name)[0],
            'userBadges' => [
                'count' => 2,
                'badges' => [],
            ]
        ];
    }
}