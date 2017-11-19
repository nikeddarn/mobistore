<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\Filters\CategoryRouteFiltersGenerator;
use App\Http\Controllers\Shop\Filters\ColorFilter;
use App\Http\Controllers\Shop\Filters\ModelFilter;
use App\Http\Controllers\Shop\Filters\QualityFilter;
use App\Http\Controllers\Shop\Filters\BrandFilter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\Quality;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryUnfilteredController extends CommonUnfilteredController
{
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

        if($this->selectedCategory->count() !== 1){
            abort(404);
        }

        if ($this->selectedCategory->first()->isLeaf()) {

            $this->getPossibleFilters();

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

    /**
     * Create possible user filters.
     *
     * @return array
     */
    private function getPossibleFilters():array
    {
        $filters = [];

        $selectedItems = $this->prepareSelectedItems();

        $this->categoryRouteFiltersGenerator->setCurrentSelectedItems($selectedItems);

        $brandFilter = $this->categoryRouteFiltersGenerator->getFilter(self::BRAND);
        if ($brandFilter->count() > 1) {
            $filters[self::BRAND] = $brandFilter;
        }

        $modelFilter = $this->categoryRouteFiltersGenerator->getFilter(self::MODEL);
        if ($modelFilter->count() > 1) {
            $filters[self::MODEL] = $modelFilter;
        }

        $qualityFilter = $this->categoryRouteFiltersGenerator->getFilter(self::QUALITY);
        if ($qualityFilter->count() > 1) {
            $filters[self::QUALITY] = $qualityFilter;
        }

        $colorFilter = $this->categoryRouteFiltersGenerator->getFilter(self::COLOR);
        if ($colorFilter->count() > 1) {
            $filters[self::COLOR] = $colorFilter;
        }
        return $filters;
    }

    /**
     * Add category query constraint to retrieve products query.
     *
     * @return Closure
     */
    protected function categoryHasProductsQueryBuilder()
    {
        return function ($query){
            return $query->where('categories_id', $this->selectedCategory->pluck('id'));
        };
    }
}
