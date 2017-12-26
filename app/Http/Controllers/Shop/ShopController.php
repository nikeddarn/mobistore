<?php

namespace App\Http\Controllers\Shop;

use App\Contracts\Shop\Products\Filters\FilterTypes;
use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Price\ProductPrice;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\Quality;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

abstract class ShopController extends Controller implements FilterTypes
{
    /**
     * @var MetaData
     */
    protected $metaData;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Brand
     */
    protected $brand;
    /**
     * @var DeviceModel
     */
    protected $model;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Quality
     */
    protected $quality;

    /**
     * @var Color
     */
    protected $color;

    /**
     * @var MetaData
     */
    protected $selectedMetaData = null;

    /**
     * @var collection
     */
    protected $selectedCategory = null;

    /**
     * Root category with depth = 0.
     *
     * @var Category
     */
    protected $rootCategory = null;

    /**
     * @var collection
     */
    protected $selectedBrand = null;

    /**
     * @var collection
     */
    protected $selectedModel = null;

    /**
     * @var Collection|LengthAwarePaginator
     */
    protected $products;

    /**
     * False if there is get parameter view = 'all' (can't paginate this page)
     *
     * @var bool
     */
    protected $isPaginable = null;

    /**
     * True if page is not paginable or product list can be splitted only to 1 page.
     *
     * @var bool
     */
    protected $isProductPageSingle = null;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ProductPrice
     */
    private $productPrice;

    /**
     * @var ProductBadges
     */
    private $productBadges;

    /**
     * @var User
     */
    private $user;


    /**
     * CategoryUnfilteredController constructor.
     * @param Request $request
     * @param MetaData $metaData
     * @param Category $category
     * @param Brand $brand
     * @param DeviceModel $model
     * @param Product $product
     * @param Quality $quality
     * @param Color $color
     * @param ProductPrice $productPrice
     * @param ProductBadges $productBadges
     */
    public function __construct(Request $request, MetaData $metaData, Category $category, Brand $brand, DeviceModel $model, Product $product, Quality $quality, Color $color, ProductPrice $productPrice, ProductBadges $productBadges)
    {
        $this->user = auth('web')->user();

        $this->metaData = $metaData;
        $this->category = $category;
        $this->brand = $brand;
        $this->model = $model;
        $this->product = $product;
        $this->quality = $quality;
        $this->color = $color;
        $this->request = $request;
        $this->productPrice = $productPrice;
        $this->productBadges = $productBadges;

        $this->isPaginable = $request->has('view') && $request->get('view') === 'all' ? false : true;

        $this->rootCategory = $this->category->withDepth()->whereIsRoot()->first();
    }

    /**
     * Return common view data.
     *
     * @return array
     */
    protected function commonViewData(): array
    {
        return [
            'commonMetaData' => $this->createCommonMetaData(),
            'breadcrumbs' => $this->getBreadcrumbs(),
            'pageData' => $this->createPageData(),
        ];
    }


    /**
     * Create breadcrumbs.
     * Store it in flash session for 'product' route.
     *
     * @return array
     */
    private function getBreadcrumbs(): array
    {
        $breadcrumbs = $this->createBreadcrumbs();

        $this->request->session()->put('breadcrumbs', $breadcrumbs);

        return $breadcrumbs;
    }

    /**
     * Create special meta data
     *
     * @return array
     */
    protected function specialMetaData(): array
    {
        return [
            'specialMetaData' => $this->createSpecialMetaData(),
        ];
    }

    /**
     * Create title, description, keywords
     *
     * @return array
     */
    abstract protected function createCommonMetaData(): array;

    /**
     * Create array of data for meta and link tags.
     *
     * @return array
     */
    abstract protected function createSpecialMetaData(): array;

    /**
     * Create breadcrumbs.
     *
     * @return array
     */
    abstract protected function createBreadcrumbs(): array;

    /**
     * Create page title, summary.
     *
     * @return array
     */
    abstract protected function createPageData(): array;

    /**
     * Create an array of view data for products page.
     * @return array
     * @internal param Request $request
     */
    protected function productsViewData()
    {
        $productsData = [
            'products' => $this->formProductData(),
            'isPageFirstOrSingle' => !$this->isProductPageSingle && $this->products->currentPage() === 1,
        ];

        if (!$this->isProductPageSingle) {
            $productsData['viewAllUrl'] = $this->request->url() . '?view=all';
            $productsData['productsPagesLinks'] = $this->products->links();
        }

        return $productsData;
    }

    /**
     * Prepare data for each product
     */
    private function formProductData()
    {
        $rate = $this->productPrice->getRate();

        $productImagePathPrefix = Storage::disk('public')->url('images/products/small/');

        return $this->products->each(function ($product) use ($rate, $productImagePathPrefix) {

            $product->image = $product->primaryImage ? $productImagePathPrefix . $product->primaryImage->image : null;

            $price = $this->productPrice->getPrice($product);

            $product->price = $price ? number_format($price, 2, '.', ',') : null;
            $product->priceUah = $price && $rate ? number_format($price * $rate, 2, '.', ',') : null;

            $product->stockStatus = $product->storageProduct->count() ? 1 : ($product->vendorProduct->count() ? 0 : null);

            $product->badges = $this->productBadges->createBadges($product->productBadge);

            $product->isFavourite = $product->favouriteProduct->count();
        });

    }

    /**
     * Retrieve products.
     * Define whether products page is single.
     *
     * @return void
     */
    protected function retrieveProducts()
    {
        $this->products = $this->getProducts();
        $this->isProductPageSingle = !($this->isPaginable && $this->products instanceof LengthAwarePaginator && $this->products->hasPages());
    }

    /**
     * Get products with constraints that defined by selected items.
     *
     * @return LengthAwarePaginator|Collection
     */
    private function getProducts()
    {
        $query = $this->product->select();

        if (isset($this->selectedCategory) && $this->selectedCategory->count()) {
            $query->whereIn('categories_id', $this->productRetrieveConstraintCategories($this->selectedCategory)->pluck('id'));
        }

        if (isset($this->selectedBrand) && $this->selectedBrand->count()) {
            $query->whereIn('brands_id', $this->selectedBrand->pluck('id'));
        }

        if (isset($this->selectedModel) && $this->selectedModel->count()) {
            $query->whereHas('deviceModel', function ($query) {
                $query->whereIn('id', $this->selectedModel->pluck('id'));
            });
        }

        if (isset($this->selectedColor) && $this->selectedColor->count()) {
            $query->where(function ($query) {
                $query->whereIn('colors_id', $this->selectedColor->pluck('id'))
                    ->orWhereNull('colors_id');
            });
        }

        if (isset($this->selectedQuality) && $this->selectedQuality->count()) {
            $query->whereIn('quality_id', $this->selectedQuality->pluck('id'));
        }

        $query->with('primaryImage');

        $query->with(['storageProduct' => function ($query) {
            $query->where('stock_quantity', '>', 0);
        }]);

        $query->with(['vendorProduct' => function ($query) {
            $query->where('stock_quantity', '>', 0);
        }]);

        $query->with('productBadge.badge');

        if ($this->user){
            $query->with(['favouriteProduct' => function($query){
                $query->where('id', $this->user->id);
            }]);
        }

        return $this->isPaginable ? $query->paginate(config('shop.products_per_page')) : $query->get();
    }

    /**
     * Categories that will constraint products retrieving.
     *
     * @param Collection $selectedCategories
     * @return Collection
     */
    abstract protected function productRetrieveConstraintCategories(Collection $selectedCategories): Collection;
}
