<?php

/**
 * Add or Remove product in user favourite.
 */

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Price\ProductPrice;
use App\Http\Controllers\Controller;
use App\Http\Support\ProductRepository\UserRecentProductRepository;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductRecentController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ProductPrice
     */
    private $productPrice;

    /**
     * @var ProductBadges
     */
    private $productBadges;

    /**
     * @var UserRecentProductRepository
     */
    private $recentProductRepository;

    /**
     * ProductFavouriteController constructor.
     *
     * @param Request $request
     * @param UserRecentProductRepository $recentProductRepository
     * @param ProductPrice $productPrice
     * @param ProductBadges $productBadges
     */
    public function __construct(Request $request, UserRecentProductRepository $recentProductRepository, ProductPrice $productPrice, ProductBadges $productBadges)
    {
        $this->request = $request;
        $this->productPrice = $productPrice;
        $this->productBadges = $productBadges;
        $this->recentProductRepository = $recentProductRepository;
    }

    /**
     * Show users' recent products.
     *
     * @return View
     */
    public function show()
    {
        if ($this->request->ajax()) {
            // get part of mega menu
            $view = view('headers.common.bottom.parts.mega_menu.parts.recent');
        }else{
            // get full view
            $view = view('content.shop.recent.index')->with('commonMetaData', [
                'title' => trans('meta.title.recent_products'),
            ]);
        }

        return $view->with('recentList', $this->getRecentProducts());
    }

    /**
     * Create user favourite products list.
     *
     * @return array
     */
    private function getRecentProducts(): array
    {
        if (auth('web')->check()) {
            $products = $this->recentProductRepository->getUserRecentProducts(auth('web')->user());

            return $this->formProductData($products);
        } else {
            return [];
        }
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
}
