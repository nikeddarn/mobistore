<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\Filters\BrandMultiplyFilter;
use App\Http\Controllers\Shop\Filters\ColorMultiplyFilter;
use App\Http\Controllers\Shop\Filters\ModelMultiplyFilter;
use App\Http\Controllers\Shop\Filters\QualityMultiplyFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryFilteredController extends CommonFilteredController
{
    use BrandMultiplyFilter;
    use ModelMultiplyFilter;
    use QualityMultiplyFilter;
    use ColorMultiplyFilter;

    /**
     * @var Collection
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
        if (!$url) {
            abort(404);
        }

        $this->getSelectedModels($url);

        $this->getPossibleFilters();

        return view('content.shop.by_categories.products.index')->with($this->commonViewData())->with($this->productsViewData($request))->with($this->filtersViewData());

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

        if (!($this->selectedCategory && $this->selectedCategory->count() === 1)) {
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
            'filtersAvailable' => $this->possibleBrands || $this->possibleModels || $this->possibleQuality || $this->possibleColors,
            'possibleBrands' => $this->possibleBrands,
            'possibleModels' => $this->possibleModels,
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
        $possibleBrands = $this->getPossibleBrands();
        if ($possibleBrands->count() > 1) {
            $this->possibleBrands = $this->formPossibleBrandsUrl($possibleBrands);
        }


        $this->usedInFiltersBrand = $this->selectedBrand ? $this->selectedBrand : ((isset($possibleBrands) && $possibleBrands->count() === 1) ? $possibleBrands : null);

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

    protected function getFilteredUrl(Collection $selectedBrands = null, Collection $selectedModels = null, Collection $selectedQuality = null, Collection $selectedColor = null)
    {
        $url = '/filter/category/category=' . $this->selectedCategory->first()->breadcrumb;

        if (!$selectedBrands) {
            $selectedBrands = $this->selectedBrand;
        }

        if (!$selectedModels) {
            $selectedModels = $this->selectedModel;
        }

        if (!$selectedQuality) {
            $selectedQuality = $this->selectedQuality;
        }

        if (!$selectedColor) {
            $selectedColor = $this->selectedColor;
        }

        if ($selectedBrands && $selectedBrands->count()) {
            $url .= '/brand=' . $selectedBrands->implode('breadcrumb', ',');
        }

        if ($selectedModels && $selectedModels->count()) {
            $url .= '/model=' . $selectedModels->implode('breadcrumb', ',');
        }

        if ($selectedQuality && $selectedQuality->count()) {
            $url .= '/quality=' . $selectedQuality->implode('breadcrumb', ',');
        }

        if ($selectedColor && $selectedColor->count()) {
            $url .= '/color=' . $selectedColor->implode('breadcrumb', ',');
        }

        return $url;
    }

    protected function getUnfilteredUrl(Collection $selectedBrand = null, Collection $selectedModel = null)
    {
        $url = '/category/' . $this->selectedCategory->first()->url;

        if ($selectedModel && $selectedModel->count()) {
            $url .= '/' . $selectedModel->first()->url;
        } elseif ($selectedBrand && $selectedBrand->count()) {
            $url .= '/' . $selectedBrand->first()->url;
        } else {
            if (isset($this->selectedModel) && $this->selectedModel->count()) {
                $url .= '/' . $this->selectedModel->first()->url;
            } elseif (isset($this->selectedBrand) && $this->selectedBrand->count()) {
                $url .= '/' . $this->selectedBrand->first()->url;
            }
        }

        return $url;
    }
}
