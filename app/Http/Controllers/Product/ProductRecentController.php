<?php

/**
 * Add or Remove product in user favourite.
 */

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Price\ProductPrice;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
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
     * ProductFavouriteController constructor.
     *
     * @param Request $request
     * @param Product $product
     * @param ProductPrice $productPrice
     * @param ProductBadges $productBadges
     */
    public function __construct(Request $request, Product $product, ProductPrice $productPrice, ProductBadges $productBadges)
    {

        $this->request = $request;
        $this->product = $product;
        $this->productPrice = $productPrice;
        $this->productBadges = $productBadges;
    }

    /**
     * Show users' recent products.
     *
     * @return View
     */
    public function show()
    {
        return view('content.shop.recent.index')
            ->with('recentList', $this->getRecent())
            ->with('commonMetaData', [
                'title' => trans('meta.title.recent_products'),
            ]);
    }

    /**
     * Create user favourite products list.
     *
     * @return array
     */
    private function getRecent(): array
    {
        if (auth('web')->check()) {
            $products = $this->getRetrieveProductQuery()
                ->whereHas('recentProduct', function ($query) {
                    $query->where('users_id', auth('web')->user()->id);
                })
                ->get();

            return $this->formProductData($products);
        } else {
            return [];
        }
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
}
