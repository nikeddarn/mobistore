<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\Filters\CategoriesMultiplyFilter;
use App\Http\Controllers\Shop\Filters\ColorByBrandMultiplyFilter;
use App\Http\Controllers\Shop\Filters\QualityByBrandMultiplyFilter;
use App\Models\Category;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BrandFilteredController extends CommonFilteredController
{
    use CategoriesMultiplyFilter;
    use QualityByBrandMultiplyFilter;
    use ColorByBrandMultiplyFilter;

    /**
     * @var Collection
     */
    private $parentCategoriesFilters = null;

    /**
     * @var Collection
     */
    private $childrenCategoriesFilter = null;

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

        return view('content.shop.by_brands.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with($this->filtersViewData());

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
     * Data for user filters.
     *
     * @return array
     */
    private function filtersViewData()
    {
        return [
            'filtersAvailable' => $this->parentCategoriesFilters || $this->childrenCategoriesFilter || $this->possibleQuality || $this->possibleColors,
            'parentCategoriesFilters' => $this->parentCategoriesFilters,
            'childrenCategoriesFilter' => $this->childrenCategoriesFilter,
            'possibleQuality' => $this->possibleQuality,
            'possibleColors' => $this->possibleColors,
        ];
    }

    /**
     * Define possible data for user filters.
     *
     * @return void
     */
    private function getPossibleFilters()
    {
        if ($this->selectedCategory) {
            $this->parentCategoriesFilters = $this->getParentCategoriesFilters();
        }

        $this->childrenCategoriesFilter = $this->getChildrenCategoriesFilter();

        $this->possibleQuality = $this->getPossibleQuality();

        $this->possibleColors = $this->getPossibleColors();
    }


    /**
     * Add category query constraint to retrieve products query.
     *
     * @param $query
     * @return Closure
     */
    protected function categoryHasProductsQueryBuilder($query)
    {
        $leavesSelectedCategories = collect();
        $this->selectedCategory->each(function (Category $category) use (&$leavesSelectedCategories) {
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

        return $query->whereIn('categories_id', $leavesSelectedCategories->pluck('id'));
    }

    protected function getFilterItemUrl(array $urlParts)
    {
        if ((isset($urlParts['quality']) && $urlParts['quality']->count()) || (isset($urlParts['color']) && $urlParts['color']->count()) || (isset($urlParts['category']) && $urlParts['category']->count() > 2)) {
            return $this->getFilteredUrl($urlParts);
        }

        if (isset($urlParts['category']) && $urlParts['category']->count() === 2 && $urlParts['category']->first()->id !== $urlParts['category']->last()->parent->id && $urlParts['category']->last()->id !== $urlParts['category']->first()->parent->id) {
            return $this->getFilteredUrl($urlParts);
        }

        return $this->getUnfilteredUrl($urlParts);
    }

    protected function getFilteredUrl(array $urlParts)
    {
        $url = '/filter/brand/brand=' . $this->selectedBrand->first()->breadcrumb . '/model=' . $this->selectedModel->first()->breadcrumb;

        foreach ($urlParts as $partName => $part) {
            if (isset($part) && $part->count()) {
                $url .= '/' . $partName . '=' . $part->implode('breadcrumb', ',');
            }
        }

        return $url;
    }

    protected function getUnfilteredUrl(array $urlParts)
    {
        $url = '/brand/' . $this->selectedModel->first()->url . '/';

        $url .= ($urlParts['category']->sortBy('depth'))->implode('breadcrumb', '/');

        return $url;
    }
}
