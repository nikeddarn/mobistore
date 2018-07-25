<?php

/**
 * Add or Remove product in user favourite.
 */

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Price\ProductPrice;
use App\Models\FavouriteProduct;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductFavouriteController extends Controller
{
    /**
     * @var FavouriteProduct
     */
    private $favouriteProduct;

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
     * @param FavouriteProduct $favouriteProduct
     * @param Product $product
     * @param ProductPrice $productPrice
     * @param ProductBadges $productBadges
     */
    public function __construct(Request $request, FavouriteProduct $favouriteProduct, Product $product, ProductPrice $productPrice, ProductBadges $productBadges)
    {

        $this->favouriteProduct = $favouriteProduct;
        $this->request = $request;
        $this->product = $product;
        $this->productPrice = $productPrice;
        $this->productBadges = $productBadges;
    }

    /**
     * Show users' favourite products.
     *
     * @return View
     */
    public function show()
    {
        if ($this->request->ajax()) {
            // get part of mega menu
            $view = view('headers.common.bottom.parts.mega_menu.parts.favourites');
        }else{
            // get full view
            $view = view('content.shop.favourite.index')->with('commonMetaData', [
                'title' => trans('meta.title.favourite_products'),
            ]);
        }

        return $view->with('favouritesList', $this->getFavourites());
    }


    /**
     * Add product in user favourite.
     *
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToFavourite(int $productId)
    {
        if ($this->productExists($productId)) {

            $this->favouriteProduct->updateOrCreate([
                'products_id' => $productId,
                'users_id' => auth('web')->user()->id,
            ]);

            $successJson = json_encode([
                'message' => trans('shop.favourite.message.add'),
                'hrefReplace' => '/favourite/remove/' . $productId,
                'title' => trans('shop.favourite.title.remove'),
            ]);

            return $this->redirectBack($successJson);

        } else {
            return $this->redirectBack(false);
        }
    }

    /**
     * Remove product in user favourite.
     *
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function removeFromFavourite(int $productId)
    {
        if ($this->productExists($productId)) {

            $this->favouriteProduct->where([
                ['products_id', $productId],
                ['users_id', auth('web')->user()->id],
            ])->delete();

            $successJson = json_encode([
                'message' => trans('shop.favourite.message.remove'),
                'hrefReplace' => '/favourite/add/' . $productId,
                'title' => trans('shop.favourite.title.add'),
            ]);

            return $this->redirectBack($successJson);

        } else {
            return $this->redirectBack(false);
        }
    }

    /**
     * Is product with given number exist ?
     *
     * @param int $productId
     * @return bool
     */
    private function productExists(int $productId)
    {
        return (bool)$this->product->find($productId);
    }

    /**
     * Redirect back.
     *
     * @param $jsonMessage
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function redirectBack($jsonMessage)
    {
        if ($this->request->ajax()) {
            return response()->json($jsonMessage);
        } else {
            return redirect()->back();
        }
    }

    /**
     * Create user favourite products list.
     *
     * @return array
     */
    private function getFavourites(): array
    {
        if (auth('web')->check()) {
            $products = $this->getRetrieveProductQuery()
                ->whereHas('favouriteProduct', function ($query) {
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
