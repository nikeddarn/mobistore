<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\ShopController;
use App\Models\MetaData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class CommonFilteredController extends ShopController
{
    /**
     * @var Collection
     */
    protected $selectedColor = null;

    /**
     * @var Collection
     */
    protected $selectedQuality = null;

    /**
     * Create associative array of filtered models.
     *
     * @param string $url
     * @return array
     */
    protected function parseUrl(string $url)
    {
        $modelsData = [];
        foreach (explode('/', $url) as $urlPart) {
            $modelData = explode('=', $urlPart);
            $modelsData[$modelData[0]] = $modelData[1];
        }
        return $modelsData;
    }


    /**
     * Retrieve models.
     *
     * @param string $url
     * @return void
     */
    protected function retrieveModels(string $url)
    {
        $modelsData = $this->parseUrl($url);

        foreach (['brand', 'model', 'color', 'quality'] as $model) {
            if (isset($modelsData[$model])) {
                $selectedModelName = 'selected' . ucfirst($model);
                $this->$selectedModelName = $this->$model->whereIn('breadcrumb', explode(',', $modelsData[$model]))->get();
            }
        }
        if (isset($modelsData['category'])) {
            $this->selectedCategory = $this->category->withDepth()->whereIn('breadcrumb', explode(',', $modelsData['category']))->get();
        }
    }

    /**
     * Collection of products that relevant the conditions.
     *
     * @return Collection|LengthAwarePaginator
     */
    protected function getProducts()
    {
        $query = $this->product->select();

        if ($this->selectedCategory) {
            $this->categoryHasProductsQueryBuilder($query);
        }

        if (isset($this->selectedBrand) && $this->selectedBrand->count()) {
            $query->whereIn('brands_id', $this->selectedBrand->pluck('id'));
        }

        if (isset($this->selectedModel) && $this->selectedModel->count()) {
            $query->whereHas('deviceModel', function ($query) {
                $query->whereIn('id', $this->selectedModel->pluck('id'));
            });
        }

        if (isset($this->selectedColor) && $this->selectedColor->count()) {
            $query->whereIn('colors_id', $this->selectedColor->pluck('id'));
        }

        if (isset($this->selectedQuality) && $this->selectedQuality->count()) {
            $query->whereIn('quality_id', $this->selectedQuality->pluck('id'));
        }

        return $this->isPaginable ? $query->paginate(config('shop.products_per_page')) : $query->get();
    }

    /**
     * @return MetaData
     */
    protected function createMetaData()
    {
        $pageTitleParts = [];

        if (isset($this->selectedCategory)) {
            $pageTitleParts[] = $this->selectedCategory->implode('title', ', ');
        } else {
            $pageTitleParts[] = $this->category->whereIsRoot()->first()->title;
        }

        foreach (['brand', 'model', 'color', 'quality'] as $model) {

            $selectedModelName = 'selected' . ucfirst($model);

            if (isset($this->$selectedModelName)) {
                $pageTitleParts[] = $this->$selectedModelName->implode('title', ', ');
            }
        }

        $metaData = $this->metaData;
        $metaData->page_title = implode('. ', $pageTitleParts);

        return $metaData;
    }

}
