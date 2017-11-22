<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\Filters\BrandRouteFilters;
use App\Http\Controllers\Shop\Filters\FilterGenerators\BrandRouteFiltersGenerator;
use App\Models\Category;
use Illuminate\Support\Collection;

class BrandFilteredController extends CommonFilteredController
{
    use BrandRouteFilters;

    /**
     * Filter generator for 'brand' route.
     *
     * @var BrandRouteFiltersGenerator
     */
    protected $brandRouteFiltersGenerator;


    /**
     * Handle incoming url.
     * Show categories view or products by category view.
     *
     * @param string $url
     * @param BrandRouteFiltersGenerator $brandRouteFiltersGenerator
     * @return \Illuminate\View\View
     * @internal param Request $request
     */
    public function index(string $url = '', BrandRouteFiltersGenerator $brandRouteFiltersGenerator)
    {
        $this->brandRouteFiltersGenerator = $brandRouteFiltersGenerator;

        if (!$url) {
            abort(404);
        }

        $this->getSelectedModels($url);

        return view('content.shop.by_brands.products.index')->with($this->commonViewData())->with($this->productsViewData())->with(['filters' => $this->getPossibleFilters()]);

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
     * Categories that will constraint products retrieving.
     *
     * @param Collection $selectedCategories
     * @return Collection
     */
    protected function productRetrieveConstraintCategories(Collection $selectedCategories): Collection
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
