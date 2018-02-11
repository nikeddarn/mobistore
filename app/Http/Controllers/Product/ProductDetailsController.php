<?php

namespace App\Http\Controllers\Product;

use App\Breadcrumbs\CategoryRouteBreadcrumbsCreator;
use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Shop\Products\Filters\FilterTypes;
use App\Http\Controllers\Controller;
use App\Http\Support\Price\ProductPrice;
use App\Models\Product;
use App\Models\RecentProduct;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Storage as productStorage;
use Illuminate\Support\Str;

class ProductDetailsController extends Controller implements FilterTypes, CurrenciesInterface
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
     * Retrieved by url product.
     *
     * @var Product
     */
    private $selectedProduct;

    /**
     * @var float
     */
    private $price;

    /**
     * @var float
     */
    private $priceUah;

    /**
     * @var CategoryRouteBreadcrumbsCreator
     */
    private $categoryRouteBreadcrumbsCreator;

    /**
     * @var Str
     */
    private $str;

    /**
     * @var ProductPrice
     */
    private $productPrice;

    /**
     * @var productStorage
     */
    private $productStorage;

    /**
     * @var User
     */
    private $user;
    /**
     * @var RecentProduct
     */
    private $recentProduct;

    /**
     * ProductDetailsController constructor.
     * @param Request $request
     * @param Product $product
     * @param CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator
     * @param Str $str
     * @param ProductPrice $productPrice
     * @param productStorage $productStorage
     * @param RecentProduct $recentProduct
     */
    public function __construct(Request $request, Product $product, CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator, Str $str, ProductPrice $productPrice, productStorage $productStorage, RecentProduct $recentProduct)
    {
        $this->request = $request;
        $this->product = $product;
        $this->categoryRouteBreadcrumbsCreator = $categoryRouteBreadcrumbsCreator;
        $this->str = $str;
        $this->productPrice = $productPrice;
        $this->productStorage = $productStorage;

        $this->user = auth('web')->user();
        $this->recentProduct = $recentProduct;
    }

    /**
     * @param string $productUrl
     * @return mixed
     * @throws Exception
     */
    public function index(string $productUrl)
    {
        $this->selectedProduct = $this->retrieveProductData($productUrl);

        $this->price = $this->productPrice->getPriceByProductModel($this->selectedProduct);
        $this->priceUah = $this->price ? $this->price * $this->productPrice->getRate(self::USD) : null;

        $this->updateRecentProducts($this->selectedProduct->id);

        return response(
            view('content.product.product_details.index')
                ->with($this->productViewData())
                ->with($this->commonMetaData())
                ->with($this->breadcrumbs())
                ->with($this->commentsData())
        )
            ->withHeaders($this->createHeaders());
    }


    /**
     * Retrieve product with related models.
     *
     * @param string $productUrl
     * @return mixed
     */
    private function retrieveProductData(string $productUrl)
    {
        $query = $this->product->select()
            ->where('url', $productUrl)
            ->with('category', 'image', 'color', 'quality')
            ->with(['brand' => function ($query) {
                $query->select(['id', 'title', 'image', 'url']);
            }])
            ->with(['deviceModel' => function ($query) {
                $query->select(['id', 'url', 'title', 'series']);
            }])
            ->with('recentComment.user')
            ->with(['storageProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }])
            ->with(['vendorProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }]);

        if ($this->user) {
            $query->with(['favouriteProduct' => function ($query) {
                $query->where('id', $this->user->id);
            }]);
        }

        return $query->firstOrFail();
    }

    /**
     * Create product data for the view.
     *
     * @return array
     */
    private function productViewData(): array
    {
        $productData = [
            'images' => $this->getProductImages(),
            'title' => $this->selectedProduct->page_title,
            'summary' => $this->selectedProduct->summary,
            'id' => $this->selectedProduct->id,
            'price' => $this->price ? number_format($this->price, 2, '.', ',') : null,
            'priceUah' => $this->priceUah ? number_format($this->priceUah, 2, '.', ',') : null,
            'quality' => $this->selectedProduct->quality->title,
            'brand' => $this->selectedProduct->brand->title,
            'model' => $this->selectedProduct->deviceModel->implode('title', ', '),
            'color' => $this->selectedProduct->color->title,
            'category' => $this->selectedProduct->category->title,
            'stockStatus' => $this->selectedProduct->storageProduct->count() ? 1 : ($this->selectedProduct->vendorProduct->count() ? 0 : null),
            'stockLocations' => $this->getStoragesHasProduct($this->selectedProduct),
            'isFavourite' => $this->selectedProduct->favouriteProduct->count(),
        ];

        if ($this->selectedProduct->rating_count >= config('shop.min_rating_count_to_show')) {
            $productData['rating'] = ceil($this->selectedProduct->rating);
        }

        return [
            'product' => $productData,
        ];
    }

    /**
     * Retrieve product comments.
     *
     * @return array
     */
    private function commentsData()
    {
        return [
            'comments' => $this->selectedProduct->recentComment->map(function ($item) {
                return [
                    'comment' => $item->comment,
                    'rating' => $item->rating,
                    'userName' => isset($item->user) ? $item->user->name : $item->name,
                    'userImage' => isset($item->user) ? $item->user->image : null,
                ];
            })
        ];
    }

    /**
     * Create array of images urls.
     *
     * @return array
     */
    private function getProductImages(): array
    {
        $imagePathPrefix = Storage::disk('public')->url('images/products/big/');

        return $this->selectedProduct->image->sortByDesc('is_primary')->each(function ($image) use ($imagePathPrefix) {
            $image->image = $imagePathPrefix . $image->image;
        })->pluck('image')->toArray();
    }

    /**
     * Create array of titles of storages that has product.
     *
     * @param Product $product
     * @return array
     */
    private function getStoragesHasProduct(Product $product): array
    {
        return $this->productStorage->whereIn('id', $product->storageProduct->pluck('storages_id')->toArray())->get()->pluck('title')->toArray();
    }

    /**
     * Create meta data for the view.
     *
     * @return array
     */
    private function commonMetaData(): array
    {
        $description = $this->selectedProduct->meta_description . '. ';
        if (isset($this->priceUah)) {
            $description .= $this->str->ucfirst(trans('meta.phrases.buу_for_price', ['price' => $this->priceUah])) . '. ';

        } else {
            $description .= $this->str->ucfirst(trans('meta.phrases.bue')) . '. ';
        }
        $description .= $this->str->ucfirst(trans('meta.phrases.phones')) . '.';

        return [
            'commonMetaData' => [
                'title' => $this->selectedProduct->meta_title,
                'description' => $description,
                'keywords' => $this->selectedProduct->meta_keywords,
            ],
        ];
    }

    /**
     * Get breadcrumbs from session if exists or create breadcrumbs from product properties.
     *
     * @return array
     * @throws Exception
     */
    private function breadcrumbs(): array
    {
        if ($this->request->session()->has('breadcrumbs')) {
            $baseBreadcrumbs = $this->request->session()->get('breadcrumbs');
        } else {
            $baseBreadcrumbs = $this->categoryRouteBreadcrumbsCreator->createBreadcrumbs($this->getBreadcrumbCreatorItems());
        }

        return [
            'breadcrumbs' => array_merge($baseBreadcrumbs, $this->additionalBreadcrumbs()),
        ];
    }

    /**
     * Create product and comment breadcrumbs.
     *
     * @return array
     */
    private function additionalBreadcrumbs(): array
    {
        return [
            [
                'title' => $this->selectedProduct->breadcrumb ? $this->selectedProduct->breadcrumb : $this->selectedProduct->page_title,
            ],
        ];
    }

    /**
     * Create array of selected items for breadcrumb creator.
     *
     * @return array
     */
    private function getBreadcrumbCreatorItems(): array
    {
        $breadcrumbItems = [];

        $breadcrumbItems[self::CATEGORY] = ($this->selectedProduct->category->ancestors)->push($this->selectedProduct->category);

        if ($this->selectedProduct->brand) {
            $breadcrumbItems[self::BRAND] = collect()->push($this->selectedProduct->brand);
        }

        if ($this->selectedProduct->deviceModel && $this->selectedProduct->deviceModel->count() === 1) {
            $breadcrumbItems[self::MODEL] = $this->selectedProduct->deviceModel;
        }

        return $breadcrumbItems;
    }

    /**
     * Create response headers.
     *
     * @return array
     */
    protected function createHeaders(): array
    {
        return [
            'Last-Modified' => date('D, d M Y H:i:s T', $this->selectedProduct->updated_at->timestamp),
        ];
    }

    /**
     * Add to recent products
     *
     * @param int $productId
     */
    private function updateRecentProducts(int $productId)
    {
        if (auth('web')->check()) {
            $this->recentProduct->updateOrCreate([
                'products_id' => $productId,
                'users_id' => auth('web')->user()->id,
            ])->touch();
        }
    }
}
