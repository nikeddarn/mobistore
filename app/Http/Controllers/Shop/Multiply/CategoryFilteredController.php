<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\Filters\CategoryRouteFiltersGenerator;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\Quality;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryFilteredController extends CommonFilteredController
{
    /**
     * Filter generator for 'category' route.
     *
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
        if (!$url) {
            abort(404);
        }

        $this->getSelectedModels($url);

        $this->getPossibleFilters();

        return view('content.shop.by_categories.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with(['filters' => $this->getPossibleFilters()]);

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
        if ($this->selectedBrand && $this->selectedBrand->count() === 1) {
            $breadcrumbs[] = ['title' => $this->selectedBrand->first()->title, 'url' => $this->selectedCategory->first()->url . '/' . $this->selectedBrand->first()->url];

            // add model's breadcrumb if exists
            if ($this->selectedModel && $this->selectedModel->count() === 1) {
                $breadcrumbs[] = ['title' => $this->selectedModel->first()->title, 'url' => $this->selectedCategory->first()->url . '/' . $this->selectedModel->first()->url];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Retrieve needing models by url.
     *
     * @param string $url
     * @return void
     */
    protected function getSelectedModels(string $url)
    {
        $this->retrieveModels($url);

        if (!($this->selectedCategory && $this->selectedCategory->count() === 1)) {
            abort(404);
        }
    }

    /**
     * Create possible user filters.
     *
     * @return array
     */
    private function getPossibleFilters()
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
     * Collect leaf of selected categories for retrieve product constraint.
     *
     * @param Collection $selectedCategories
     * @return Collection
     * @internal param $query
     */
    protected function collectProductConstraintsSelectedCategories(Collection $selectedCategories):Collection
    {
        return collect()->push($selectedCategories->sortBy('depth')->last());
    }

}
