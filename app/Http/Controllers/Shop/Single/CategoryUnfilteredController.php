<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\Filters\CategoryRouteFilters;
use App\Http\Controllers\Shop\Filters\FilterGenerators\CategoryRouteFiltersGenerator;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\Quality;
use Closure;
use Illuminate\Http\Request;

class CategoryUnfilteredController extends CommonUnfilteredController
{
    use CategoryRouteFilters;

    /**
     * @var CategoryRouteFiltersGenerator
     */
    private $categoryRouteFiltersGenerator;

    public function __construct(Request $request, MetaData $metaData, Category $category, Brand $brand, DeviceModel $model, Product $product, Quality $quality, Color $color, CategoryRouteFiltersGenerator $categoryRouteFiltersGenerator)
    {
        parent::__construct($request, $metaData, $category, $brand, $model, $product, $quality, $color);
        $this->categoryRouteFiltersGenerator = $categoryRouteFiltersGenerator;
    }

    /**
     * Handle incoming url.
     * Show categories view or products by category view.
     *
     * @param string $url
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(string $url = '', Request $request)
    {
        $this->getSelectedModels($url);

        if(!$this->selectedCategory->count()){
            abort(404);
        }

        if ($this->selectedCategory->last()->isLeaf()) {

            return view('content.shop.by_categories.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with(['filters' => $this->getPossibleFilters()]);

        } else {

            return view('content.shop.by_categories.categories.index')->with($this->commonViewData())->with($this->categoriesViewData());

        }
    }

    /**
     * Create an array of view data for categories page.
     *
     * @return array
     */
    private function categoriesViewData()
    {
        return [
            'categories' => $this->selectedCategory->first()->children,
        ];
    }

    /**
     * Define breadcrumbs by categories, brand and model.
     *
     * @return array
     */
    protected function createBreadcrumbs()
    {
        $breadcrumbs = [];

        // add ancestors' breadcrumbs
        foreach ($this->category->ancestorsAndSelf($this->selectedCategory->first()->id) as $item) {
            $breadcrumbs[] = ['title' => $item->title, 'url' => $item->url];
        }

        // add brand's breadcrumb if exists
        if ($this->selectedBrand->count()) {
            $breadcrumbs[] = ['title' => $this->selectedBrand->first()->title, 'url' => $this->selectedCategory->first()->url . '/' . $this->selectedBrand->first()->url];
        }

        // add model's breadcrumb if exists
        if ($this->selectedModel->count()) {
            $breadcrumbs[] = ['title' => $this->selectedModel->first()->title, 'url' => $this->selectedCategory->first()->url . '/' . $this->selectedModel->first()->url];
        }

        return $breadcrumbs;
    }
}
