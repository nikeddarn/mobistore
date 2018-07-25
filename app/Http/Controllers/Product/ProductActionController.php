<?php

/**
 * Add or Remove product in user favourite.
 */

namespace App\Http\Controllers\Product;

use App\Contracts\Shop\Badges\ProductBadgesInterface;
use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Price\ProductPrice;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductActionController extends Controller implements ProductBadgesInterface
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
     * Show users' action's products.
     *
     * @return View
     */
    public function show()
    {
        if ($this->request->ajax()) {
            // get part of mega menu
            $view = view('headers.common.bottom.parts.mega_menu.parts.action');
        }else{
            // get full view
            $view = view('content.shop.action.index')->with('commonMetaData', [
                'title' => trans('meta.title.action_products'),
                'description' => trans('meta.description.action_products'),
                'keywords' => trans('meta.keywords.action_products'),
            ]);
        }

        return $view->with('actionList', $this->getActionProducts());
    }

    /**
     * Create user favourite products list.
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
}
