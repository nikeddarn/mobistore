<?php

namespace App\Http\Controllers\Shop;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    private $locale;

    private $metaData;

    private $breadcrumbs;

    /**
     * Handle incoming url.
     * Show categories view or products view.
     *
     * @param Request $request
     * @param string $url
     * @return \Illuminate\View\View
     */
    public function index(Request $request, string $url = '')
    {
        $this->locale = App::getLocale();

        $this->metaData = $this->retrieveModels($url);

        $this->breadcrumbs = $this->defineBreadcrumbs();
        $request->session()->put('breadcrumbs', $this->breadcrumbs);

        if ($this->metaData->category->isLeaf()) {
            return view('content.shop.products.index')->with($this->productsViewData());
        } else {
            return view('content.shop.categories.index')->with($this->categoriesViewData());
        }
    }

    /**
     * Retrieve needing models by url.
     *
     * @param string $url
     * @return mixed
     */
    private function retrieveModels(string $url)
    {
        return MetaData::where('url', $url)
            ->with(['category', 'brand' => function ($query) {
                $query->select(['url', 'title']);
            }, 'deviceModel' => function ($query) {
                $query->select(['url', 'title']);
            }])
            ->first(['categories_id', 'brands_id', 'models_id', 'page_title_' . $this->locale, 'meta_title_' . $this->locale, 'meta_description_' . $this->locale, 'meta_keywords_' . $this->locale, 'summary_' . $this->locale,]);
    }


    /**
     * Define breadcrumbs by categories, brand and model.
     *
     * @return array
     */
    private function defineBreadcrumbs()
    {
        $breadcrumbs = [];
        $titleName = 'title_' . $this->locale;
        // add ancestors' breadcrumbs
        foreach (Category::ancestorsAndSelf($this->metaData->category->id) as $item) {
            $breadcrumbs[] = ['title' => $item->$titleName, 'url' => $item->url];
        }
        // add brand's breadcrumb if exists
        if ($this->metaData->brand) {
            $breadcrumbs[] = ['title' => $this->metaData->brand->title, 'url' => $this->metaData->category->url . $this->metaData->brand->url];
        }
        // add model's breadcrumb if exists
        if ($this->metaData->deviceModel) {
            $breadcrumbs[] = ['title' => $this->metaData->deviceModel->title, 'url' => $this->metaData->category->url . $this->metaData->deviceModel->url];
        }

        return $breadcrumbs;
    }

    /**
     * Create an array of view data for categories page.
     *
     * @return array
     */
    private function categoriesViewData()
    {
        return [
            'breadcrumbs' => $this->breadcrumbs,
            'categories' => $this->metaData->category->children,
            'metaData' => $this->metaData,
        ];
    }

    /**
     * Create an array of view data for products page.
     *
     * @return array
     */
    private function productsViewData()
    {
        return [
            'breadcrumbs' => $this->breadcrumbs,
            'metaData' => $this->metaData,
            'products' => $this->getProducts(),
            'productImagePathPrefix' => Storage::disk('public')->url('images/products/'),
            'brands' => $this->getBrands(),
            'models' => $this->getModels()
        ];
    }

    private function getProducts()
    {
        return Product::where(function($query){
            $query->where('categories_id', $this->metaData->category->id);
            if ($this->metaData->brand) {
                $query->where('brands_id', $this->metaData->brand->id);
            }
            if ($this->metaData->deviceModel) {
                $query->where('models_id', $this->metaData->deviceModel->id);
            }
        })->with('primaryImage')->get();
    }

    private function getBrands()
    {
        $categoryId = $this->metaData->category->id;

        return Brand::whereHas('product', function ($query) use ($categoryId){
            $query->where('categories_id', $categoryId)->orderBy('priority', 'asc');
        })->get();
    }

    private function getModels()
    {
        if($this->metaData->brand){
            return $this->metaData->brand->deviceModel;
        }else{
            return [];
        }
    }
}
