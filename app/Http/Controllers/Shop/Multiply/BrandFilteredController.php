<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\Filters\BrandRouteFiltersGenerator;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\Quality;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BrandFilteredController extends CommonFilteredController
{
    /**
     * Filter generator for 'brand' route.
     *
     * @var BrandRouteFiltersGenerator
     */
    protected $brandRouteFiltersGenerator;

    public function __construct(Request $request, MetaData $metaData, Category $category, Brand $brand, DeviceModel $model, Product $product, Quality $quality, Color $color, BrandRouteFiltersGenerator $brandRouteFiltersGenerator)
    {
        parent::__construct($request, $metaData, $category, $brand, $model, $product, $quality, $color);

        $this->brandRouteFiltersGenerator = $brandRouteFiltersGenerator;
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

        return view('content.shop.by_brands.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with(['filters' => $this->getPossibleFilters()]);

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
//        foreach ($this->category->ancestorsAndSelf($this->selectedCategory->first()->id) as $item) {
//            $breadcrumbs[] = ['title' => $item->title, 'url' => $item->url];
//        }

        // add brand's breadcrumb if exists
//        if ($this->selectedBrand && $this->selectedBrand->count() === 1) {
//            $breadcrumbs[] = ['title' => $this->selectedBrand->first()->title, 'url' => $this->selectedCategory->first()->url . '/' . $this->selectedBrand->first()->url];

        // add model's breadcrumb if exists
//            if ($this->selectedModel && $this->selectedModel->count() === 1) {
//                $breadcrumbs[] = ['title' => $this->selectedModel->first()->title, 'url' => $this->selectedCategory->first()->url . '/' . $this->selectedModel->first()->url];
//            }
//        }

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

        if (!($this->selectedBrand && $this->selectedModel && $this->selectedBrand->count() === 1 && $this->selectedModel->count() === 1)) {
            abort(404);
        }
    }

    /**
     * Create possible user filters.
     *
     * @return array
     */
    private function getPossibleFilters(): array
    {
        $filters = [];

        $selectedItems = $this->prepareSelectedItems();

        $this->brandRouteFiltersGenerator->setCurrentSelectedItems($selectedItems);


        $categoryFilters = [];

        $rootCategory = $this->category->whereIsRoot()->first();
        $this->brandRouteFiltersGenerator->getFilterCreator(self::CATEGORY)->setAdditionalConstraints(function ($query) use ($rootCategory) {
            return $query->where('categories.parent_id', $rootCategory->id);
        });

        $rootCategoryFilter = $this->brandRouteFiltersGenerator->getFilter(self::CATEGORY);
        if ($rootCategoryFilter->count() > 1) {
            $categoryFilters[] = $rootCategoryFilter;
        }

        for ($depth = 1; $depth < config('shop.category_filters_depth'); $depth++) {
            $selectedItemsOnDepth = $this->getSelectedCategoriesIdByDepth($depth);
            $this->brandRouteFiltersGenerator->getFilterCreator(self::CATEGORY)->setAdditionalConstraints(function ($query) use ($selectedItemsOnDepth) {
                return $query->whereIn('categories.parent_id', $selectedItemsOnDepth);
            });
            $categoryFilter = $this->brandRouteFiltersGenerator->getFilter(self::CATEGORY);
            if ($categoryFilter->count() > 1){
                $categoryFilters[] = $categoryFilter;
            }
        }

        if (!empty($categoryFilters)) {
            $filters[self::CATEGORY] = $categoryFilters;
        }


        $qualityFilter = $this->brandRouteFiltersGenerator->getFilter(self::QUALITY);
        if ($qualityFilter->count() > 1) {
            $filters[self::QUALITY] = $qualityFilter;
        }

        $colorFilter = $this->brandRouteFiltersGenerator->getFilter(self::COLOR);
        if ($colorFilter->count() > 1) {
            $filters[self::COLOR] = $colorFilter;
        }
        return $filters;
    }

    /**
     * Define array of selected categories id which has received depth.
     * @param int $depth
     * @return array
     */
    private function getSelectedCategoriesIdByDepth(int $depth): array
    {
        return $this->selectedCategory
            ->filter(function (Category $category) use ($depth) {
                return $category->depth === $depth;
            })
            ->pluck('id')
            ->toArray();
    }

    /**
     * Collect selected categories for retrieve product constraint.
     * Collect all leaves of parent selected directory or only selected leaf.
     *
     * @param Collection $selectedCategories
     * @return Collection
     * @internal param $query
     */
    protected function collectProductConstraintsSelectedCategories(Collection $selectedCategories):Collection
    {
        $leavesSelectedCategories = collect();

        $selectedCategories->each(function (Category $category) use (&$leavesSelectedCategories) {
            if ($category->descendants && $category->descendants->count()) {
                if ($category->descendants->pluck('id')->intersect($this->selectedCategory->pluck('id'))->count()) {
                    $category->descendants->each(function ($leaf) use ($leavesSelectedCategories) {
                        if ($this->selectedCategory->pluck('id')->contains($leaf->id)) {
                            $leavesSelectedCategories->push($leaf);
                        }
                    });
                } else {
                    $leavesSelectedCategories = $leavesSelectedCategories->merge($category->descendants);
                }
            } else {
                $leavesSelectedCategories->push($category);
            }
        });

        return $leavesSelectedCategories;
    }
}
