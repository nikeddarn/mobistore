<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\Filters\ColorFilter;
use App\Http\Controllers\Shop\Filters\ModelFilter;
use App\Http\Controllers\Shop\Filters\QualityFilter;
use App\Http\Controllers\Shop\Filters\BrandFilter;
use App\Models\Brand;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryUnfilteredController extends CommonUnfilteredController
{
    use BrandFilter;
    use ModelFilter;
    use QualityFilter;
    use ColorFilter;

    /**
     * @var Brand
     */
    protected $usedInFiltersBrand = null;

    /**
     * @var Collection
     */
    private $possibleBrands = null;

    /**
     * @var Collection
     */
    private $possibleModels = null;

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

        if(!$this->selectedCategory){
            abort(404);
        }

        if ($this->selectedCategory->isLeaf()) {

            $this->getPossibleFilters();

            return view('content.shop.by_categories.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with($this->filtersViewData());

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
            'categories' => $this->selectedCategory->children,
        ];
    }

    /**
     * Data for user filters.
     *
     * @return array
     */
    private function filtersViewData()
    {
        return [
            'filtersAvailable' => $this->possibleBrands || $this->possibleModels || $this->possibleQuality || $this->possibleColors,
            'possibleBrands' => $this->possibleBrands,
            'possibleModels' => $this->possibleModels,
            'possibleQuality' => $this->possibleQuality,
            'possibleColors' => $this->possibleColors,
            'selectedBrand' => $this->selectedBrand,
            'selectedModel' => $this->selectedModel,
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
        foreach ($this->category->ancestorsAndSelf($this->selectedCategory->id) as $item) {
            $breadcrumbs[] = ['title' => $item->title, 'url' => $item->url];
        }

        // add brand's breadcrumb if exists
        if ($this->selectedBrand) {
            $breadcrumbs[] = ['title' => $this->selectedBrand->title, 'url' => $this->selectedCategory->url . '/' . $this->selectedBrand->url];
        }

        // add model's breadcrumb if exists
        if ($this->selectedModel) {
            $breadcrumbs[] = ['title' => $this->selectedModel->title, 'url' => $this->selectedCategory->url . '/' . $this->selectedModel->url];
        }

        return $breadcrumbs;
    }

    /**
     * Define possible data for user filters.
     *
     * @return void
     */
    private function getPossibleFilters()
    {
        $possibleBrands = $this->getPossibleBrands();
        if ($possibleBrands->count() > 1) {
            $this->possibleBrands = $this->formPossibleBrandsUrl($possibleBrands);
        }

        $this->usedInFiltersBrand = $this->selectedBrand ? $this->selectedBrand : ((isset($possibleBrands) && $possibleBrands->count() === 1) ? $possibleBrands->first() : null);

        if ($this->usedInFiltersBrand) {
            $possibleModels = $this->getPossibleModels();
            if ($possibleModels->count() > 1) {
                $this->possibleModels = $this->formPossibleModelsUrl($possibleModels);
            }
        }

        $possibleQuality = $this->getPossibleQuality();
        if ($possibleQuality->count() > 1) {
            $this->possibleQuality = $this->formPossibleQualityUrl($possibleQuality);
        }

        $possibleColors = $this->getPossibleColors();
        if ($possibleColors->count() > 1) {
            $this->possibleColors = $this->formPossibleColorsUrl($possibleColors);
        }
    }

    /**
     * Route prefix to multiply category controller
     * @return string
     */
    protected function filteredRoutePrefix()
    {
        return '/filter/category';
    }

    /**
     * Add category query constraint to retrieve products query.
     *
     * @return Closure
     */
    protected function categoryHasProductsQueryBuilder()
    {
        return function ($query){
            return $query->where('categories_id', $this->selectedCategory->id);
        };
    }
}
