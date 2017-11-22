<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\Filters\CategoryRouteFilters;
use App\Http\Controllers\Shop\Filters\FilterGenerators\CategoryRouteFiltersGenerator;
use Illuminate\Support\Collection;

class CategoryFilteredController extends CommonFilteredController
{
    use CategoryRouteFilters;

    /**
     * Filter generator for 'category' route.
     *
     * @var CategoryRouteFiltersGenerator
     */
    private $categoryRouteFiltersGenerator;

    /**
     * Handle incoming url.
     * Show categories view or products by category view.
     *
     * @param string $url
     * @param CategoryRouteFiltersGenerator $categoryRouteFiltersGenerator
     * @return \Illuminate\View\View
     * @internal param Request $request
     */
    public function index(string $url = '', CategoryRouteFiltersGenerator $categoryRouteFiltersGenerator)
    {
        $this->categoryRouteFiltersGenerator = $categoryRouteFiltersGenerator;

        if (!$url) {
            abort(404);
        }

        $this->getSelectedModels($url);

        $this->getPossibleFilters();

        return view('content.shop.by_categories.products.index')->with($this->commonViewData())->with($this->productsViewData())->with(['filters' => $this->getPossibleFilters()]);

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

        if (!$this->selectedCategory->count()) {
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
        return collect()->push($selectedCategories->sortBy('depth')->last());
    }
}
