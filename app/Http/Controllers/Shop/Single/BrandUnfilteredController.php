<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\Filters\BrandRouteFilters;
use App\Http\Controllers\Shop\Filters\FilterGenerators\BrandRouteFiltersGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BrandUnfilteredController extends CommonUnfilteredController
{
    use BrandRouteFilters;
    /**
     * Filter generator for 'brand' route.
     *
     * @var BrandRouteFiltersGenerator
     */
    private $brandRouteFiltersGenerator;

    /**
     * Handle incoming url.
     * Show brands view or products by brand view.
     *
     * @param string $url
     * @param BrandRouteFiltersGenerator $brandRouteFiltersGenerator
     * @return \Illuminate\View\View
     * @internal param Request $request
     */
    public function index(string $url = '', BrandRouteFiltersGenerator $brandRouteFiltersGenerator)
    {
        $this->brandRouteFiltersGenerator = $brandRouteFiltersGenerator;

        if ($url === '') {

            $this->getSelectedModels('brand');

            return view('content.shop.by_brands.brands.index')->with($this->commonViewData())->with($this->supportedBrands());

        } else {

            $this->getSelectedModels($url);

            if (!$this->selectedBrand->count()) {
                abort(404);
            }

            if ($this->selectedModel->count()) {

                return view('content.shop.by_brands.products.index')->with($this->commonViewData())->with($this->productsViewData())->with(['filters' => $this->getPossibleFilters()]);

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
        $modelsBySeries = $this->selectedBrand->first()->deviceModel()->select(DB::raw('series, GROUP_CONCAT(JSON_OBJECT("title", title, "url", url, "image", image) ORDER BY title) as models'))->groupBy('series')->orderBy('series')->get();

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

//        $breadcrumbs[] = ['title' => $this->metaData->where('url', 'brand')->first()->page_title, 'url' => ''];
//
//        // add brand's breadcrumb if exists
//        if ($this->selectedBrand->count()) {
//            $breadcrumbs[] = ['title' => $this->selectedBrand->first()->title, 'url' => $this->selectedBrand->first()->url];
//        }
//
//        // add model's breadcrumb if exists
//        if ($this->selectedModel->count()) {
//            $breadcrumbs[] = ['title' => $this->selectedModel->first()->title, 'url' => $this->selectedModel->first()->url];
//        }
//
//        // add category's breadcrumb if exists
//        if ($this->selectedCategory->count()) {
//            $this->selectedCategory->ancestors->forget(0)->each(function ($ancestor) use (&$breadcrumbs){
//                $breadcrumbs[] = ['title' => $ancestor->title, 'url' => $this->selectedModel->url . '/' . $ancestor->url];
//            });
//            $breadcrumbs[] = ['title' => $this->selectedCategory->title, 'url' => $this->selectedModel->url . '/' . $this->selectedCategory->url];
//        }

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
