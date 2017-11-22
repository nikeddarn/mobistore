<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\ShopController;
use App\Models\Category;
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

            $selectedModelName = 'selected' . ucfirst($model);

            if (isset($modelsData[$model])) {
                $this->$selectedModelName = $this->$model->whereIn('breadcrumb', explode(',', $modelsData[$model]))->get();
            } else {
                $this->$selectedModelName = collect();
            }
        }

        if (isset($modelsData['category'])) {
            $this->selectedCategory = $this->category->withDepth()->whereIn('breadcrumb', explode(',', $modelsData['category']))->get();
        } else {
            $this->selectedCategory = collect();
        }
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

    /**
     * Create array of selected items.
     *
     * @return array
     */
    protected function prepareSelectedItemsForFiltersCreator(): array
    {
        return [
            'brand' => $this->selectedBrand,
            'model' => $this->selectedModel,
            'category' => $this->selectedCategory,
            'color' => $this->selectedColor,
            'quality' => $this->selectedQuality
        ];
    }

}
