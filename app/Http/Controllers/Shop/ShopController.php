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
     * @var Collection
     */
    protected $possibleQuality = null;

    /**
     * @var Collection
     */
    protected $possibleColors = null;

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
     * @var Brand
     */
    protected $usedInFiltersBrand = null;

    /**
     * @var collection
     */
    protected $selectedModel = null;

    /**
     * @var bool
     */
    protected $isPaginable;

    /**
     * @var Collection|LengthAwarePaginator
     */
    protected $products;


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
     *
     * @param Request $request
     * @return array
     */
    protected function productsViewData(Request $request)
    {
        $this->products = $this->getProducts();

        return [
            'products' => $this->products,
            'productImagePathPrefix' => Storage::disk('public')->url('images/products/'),
            'viewAllUrl' => $this->isPaginable && $this->products->lastPage() > 1 ? $request->url() . '?view=all' : null,
        ];
    }
}
