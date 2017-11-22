<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\Filters\CategoryRouteFilters;
use App\Http\Controllers\Shop\Filters\FilterGenerators\CategoryRouteFiltersGenerator;
use Illuminate\Support\Collection;

class CategoryUnfilteredController extends CommonUnfilteredController
{
    use CategoryRouteFilters;

    /**
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

        $this->getSelectedModels($url);

        if(!$this->selectedCategory->count()){
            abort(404);
        }

        if ($this->selectedCategory->last()->isLeaf()) {

            return view('content.shop.by_categories.products.index')->with($this->commonViewData())->with($this->productsViewData())->with(['filters' => $this->getPossibleFilters()]);

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
     * Categories that will constraint products retrieving.
     *
     * @param Collection $selectedCategories
     * @return Collection
     */
    protected function productRetrieveConstraintCategories(Collection $selectedCategories): Collection
    {
        return $this->getLeavesOfMostDeepSelectedCategory($this->selectedCategory);
    }
}
