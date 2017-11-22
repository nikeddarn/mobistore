<?php

namespace App\Http\Controllers\Shop;

use App\Contracts\Shop\Products\Filters\FilterTypes;
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
     * @var collection
     */
    protected $selectedBrand = null;

    /**
     * @var collection
     */
    protected $selectedModel = null;

    /**
     * @var bool
     */
    protected $isPaginable;
    /**
     * @var Request
     */
    private $request;


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
     */
    public function __construct(Request $request, MetaData $metaData, Category $category, Brand $brand, DeviceModel $model, Product $product, Quality $quality, Color $color)
    {

        $this->metaData = $metaData;
        $this->category = $category;
        $this->brand = $brand;
        $this->model = $model;
        $this->product = $product;
        $this->quality = $quality;
        $this->color = $color;
        $this->request = $request;

        $this->isPaginable = $request->has('view') ? false : true;
    }

    /**
     * Return common view data.
     *
     * @return array
     */
    protected function commonViewData()
    {
        return [
            'metaData' => $this->createMetaData(),
            'breadcrumbs' => $this->createBreadcrumbs(),
        ];
    }

    /**
     * Create an array of view data for products page.
     * @return array
     * @internal param Request $request
     */
    protected function productsViewData()
    {
        $products = $this->getProducts();

        return [
            'products' => $this->getProducts(),
            'productImagePathPrefix' => Storage::disk('public')->url('images/products/'),
            'viewAllUrl' => $this->isPaginable && $products->lastPage() > 1 ? $this->request->url() . '?view=all' : null,
        ];
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

        return $this->isPaginable ? $query->paginate(config('shop.products_per_page')) : $query->get();
    }

    /**
     * Categories that will constraint products retrieving.
     *
     * @param Collection $selectedCategories
     * @return Collection
     */
    abstract protected function productRetrieveConstraintCategories(Collection $selectedCategories):Collection;
}
