<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\Filters\CategoriesFilter;
use App\Http\Controllers\Shop\Filters\ColorFilter;
use App\Http\Controllers\Shop\Filters\QualityFilter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BrandUnfilteredController extends CommonUnfilteredController
{
    use CategoriesFilter;
    use QualityFilter;
    use ColorFilter;

    /**
     * @var Collection
     */
    private $parentCategoriesFilters = null;

    /**
     * @var Collection
     */
    private $childrenCategoriesFilter = null;

    /**
     * Categories with its ancestors that has products.
     *
     * @var Collection
     */
    protected $notEmptyCategories;

    /**
     * Handle incoming url.
     * Show brands view or products by brand view.
     *
     * @param string $url
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(string $url = '', Request $request)
    {
        if ($url === '') {

            $this->getSelectedModels('brand');

            return view('content.shop.by_brands.brands.index')->with($this->commonViewData())->with($this->supportedBrands());

        } else {

            $this->getSelectedModels($url);

            if (!$this->selectedBrand) {
                abort(404);
            }

            if ($this->selectedModel) {

                $this->getPossibleFilters();

                return view('content.shop.by_brands.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with($this->filtersViewData());

            } else {

                return view('content.shop.by_brands.models.index')->with($this->commonViewData())->with($this->modelsOfBrand());

            }
        }
    }

    private function supportedBrands()
    {
        return [
            'brands' => $this->brand->orderBy('priority')->get()
        ];
    }

    private function modelsOfBrand()
    {
        $modelsBySeries = $this->selectedBrand->deviceModel()->select(DB::raw('series, GROUP_CONCAT(JSON_OBJECT("title", title, "url", url, "image", image) ORDER BY title) as models'))->groupBy('series')->orderBy('series')->get();

        foreach ($modelsBySeries as $item) {
            $item->models = json_decode('[' . $item->models . ']');
        }

        return [
            'modelsBySeries' => $modelsBySeries,
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

        $breadcrumbs[] = ['title' => $this->metaData->where('url', 'brand')->first()->page_title, 'url' => ''];

        // add brand's breadcrumb if exists
        if ($this->selectedBrand) {
            $breadcrumbs[] = ['title' => $this->selectedBrand->title, 'url' => $this->selectedBrand->url];
        }

        // add model's breadcrumb if exists
        if ($this->selectedModel) {
            $breadcrumbs[] = ['title' => $this->selectedModel->title, 'url' => $this->selectedModel->url];
        }

        // add category's breadcrumb if exists
        if($this->selectedCategory){
            $breadcrumbs[] = ['title' => $this->selectedCategory->title, 'url' => $this->selectedCategory->url];
        }

        return $breadcrumbs;
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
        $this->notEmptyCategories = $this->notEmptyCategoriesMap();

        if($this->selectedCategory){
            $parentCategoriesFilters = $this->getParentCategoriesFilters();

            if ($parentCategoriesFilters->count()) {
                $this->parentCategoriesFilters = $parentCategoriesFilters;
            }
        }

        $childrenCategoriesFilter = $this->getChildrenCategoriesFilter();

        if ($childrenCategoriesFilter && $childrenCategoriesFilter->count() > 1){
            $this->childrenCategoriesFilter = $childrenCategoriesFilter;
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
     * Route prefix to multiply brand controller
     * @return string
     */
    protected function filteredRoutePrefix()
    {
        return '/filter/brand';
    }

    /**
     * Add category query constraint to retrieve products query.
     *
     * @return Closure
     */
    protected function categoryHasProductsQueryBuilder()
    {
        return function ($query){
            if($this->selectedCategory->descendants && $this->selectedCategory->descendants->count()){
                return $query->whereIn('categories_id', $this->selectedCategory->descendants->pluck('id'));
            }else{
                return $query->where('categories_id', $this->selectedCategory->id);
            }
        };
    }
}
