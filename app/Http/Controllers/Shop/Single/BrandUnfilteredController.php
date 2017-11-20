<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\Filters\BrandRouteFiltersGenerator;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\Quality;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandUnfilteredController extends CommonUnfilteredController
{
    /**
     * Filter generator for 'brand' route.
     *
     * @var BrandRouteFiltersGenerator
     */
    private $brandRouteFiltersGenerator;

    public function __construct(Request $request, MetaData $metaData, Category $category, Brand $brand, DeviceModel $model, Product $product, Quality $quality, Color $color, BrandRouteFiltersGenerator $brandRouteFiltersGenerator)
    {
        parent::__construct($request, $metaData, $category, $brand, $model, $product, $quality, $color);
        $this->brandRouteFiltersGenerator = $brandRouteFiltersGenerator;
    }

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

                return view('content.shop.by_brands.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with(['filters' => $this->getPossibleFilters()]);

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
        if ($this->selectedCategory) {
            $this->selectedCategory->ancestors->forget(0)->each(function ($ancestor) use (&$breadcrumbs){
                $breadcrumbs[] = ['title' => $ancestor->title, 'url' => $this->selectedModel->url . '/' . $ancestor->url];
            });
            $breadcrumbs[] = ['title' => $this->selectedCategory->title, 'url' => $this->selectedModel->url . '/' . $this->selectedCategory->url];
        }

        return $breadcrumbs;
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
        return function ($query) {
            if ($this->selectedCategory->descendants && $this->selectedCategory->descendants->count()) {
                return $query->whereIn('categories_id', $this->selectedCategory->descendants->pluck('id'));
            } else {
                return $query->where('categories_id', $this->selectedCategory->id);
            }
        };
    }
}
