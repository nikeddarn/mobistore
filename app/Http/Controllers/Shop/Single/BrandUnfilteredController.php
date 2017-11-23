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
            return view('content.shop.by_brands.brands.index')->with($this->commonViewData())->with($this->brandsList());
        } else {
            $this->getSelectedModels($url);

            if ($this->selectedBrand->count() !== 1) {
                abort(404);
            }

            if ($this->selectedModel->count() === 1) {
                return view('content.shop.by_brands.products.index')->with($this->commonViewData())->with($this->productsViewData())->with(['filters' => $this->getPossibleFilters()]);
            } else {
                return view('content.shop.by_brands.models.index')->with($this->commonViewData())->with($this->modelsList());
            }
        }
    }

    /**
     * Create brands list for view.
     *
     * @return array
     */
    private function brandsList():array
    {
        return [
            'brands' => $this->brand->orderBy('priority')->get()
        ];
    }

    /**
     * Create array of model's series with its models.
     *
     * @return array
     */
    private function modelsList():array
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
        return array_merge($this->brandBreadcrumbPart(true), $this->modelBreadcrumbPart(), $this->categoryBreadcrumbPart(false, true));
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
