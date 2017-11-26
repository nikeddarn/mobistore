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
        if (!$url){
            $this->getSelectedModels('category');

            $responseContent = view('content.shop.by_categories.categories.index')
                ->with($this->subcategoriesList());
        }else{
            $this->getSelectedModels($url);

            if(!$this->selectedCategory->count()){
                abort(404);
            }

            if ($this->selectedCategory->last()->isLeaf()) {
                $this->categoryRouteFiltersGenerator = $categoryRouteFiltersGenerator;
                $this->retrieveProducts();

                $responseContent = view('content.shop.by_categories.products.index')
                        ->with($this->productsViewData())
                        ->with($this->specialMetaData())
                        ->with(['filters' => $this->getPossibleFilters()]);
            } else {
                $responseContent = view('content.shop.by_categories.categories.index')
                    ->with($this->subcategoriesList());
            }
        }

        return response(
            $responseContent
                ->with($this->commonViewData())
        )
            ->withHeaders($this->createHeaders());
    }

    /**
     * Create an array of view data for categories page.
     *
     * @return array
     */
    private function subcategoriesList()
    {
        $categories = $this->selectedCategory->count() ? $this->selectedCategory->last()->children : $this->category->whereIsRoot()->first()->children;

        return [
            'categories' => $categories,
        ];
    }

    /**
     * Define breadcrumbs by categories, brand and model.
     *
     * @return array
     */
    protected function createBreadcrumbs():array
    {
        return array_merge($this->categoryBreadcrumbPart(true), $this->brandBreadcrumbPart(false, true), $this->modelBreadcrumbPart(true));
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

    /**
     * Create canonical url for meta data.
     *
     * @return string
     */
    protected function createCanonicalUrl():string
    {
        return '/brand/' . $this->selectedModel->first()->url . '/' . $this->selectedCategory->sortBy('depth')->last()->url;
    }
}
