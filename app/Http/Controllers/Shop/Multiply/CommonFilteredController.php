<?php

namespace App\Http\Controllers\Shop\Multiply;

use App\Http\Controllers\Shop\ShopController;
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
     * Create title, description, keywords
     *
     * @return array
     */
    protected function createCommonMetaData(): array
    {
        return [
            'title' => $this->createPageTitle(),
            'description' => null,
            'keywords' => null,

        ];
    }

    /**
     * Create array of data for meta and link tags.
     *
     * @return array
     */
    protected function createSpecialMetaData(): array
    {
        return [
            'meta' => [
                [
                    'name' => 'robots',
                    'content' => 'noindex,nofollow',
                ],
            ]
        ];
    }

    /**
     * Create page title, summary.
     *
     * @return array
     */
    protected function createPageData(): array
    {
        return [
            'pageTitle' => $this->createPageTitle(),
            'summary' => null,
        ];
    }

    protected function createPageTitle()
    {
        $pageTitle = '';

        if ($this->selectedCategory->count()) {
            $pageTitle .= $this->categoryPageTitlePart();
        }

        if ($this->selectedBrand->count()) {
            $pageTitle .= ' ' . $this->selectedBrand->implode('title', ', ');
        }

        if ($this->selectedModel->count()) {
            $pageTitle .= ' ' . $this->selectedModel->implode('title', ', ');
        }

        $pageTitle .= '.';

        if ($this->selectedQuality->count()) {
            $pageTitle .= ' ' . $this->selectedQuality->implode('title', ', ') . '.';
        }

        if ($this->selectedColor->count()) {
            $pageTitle .= ' ' . $this->selectedColor->implode('title', ', ') . '.';
        }

        return $pageTitle;
    }

    /**
     * Create category page title part.
     *
     * @return string
     */
    protected function categoryPageTitlePart(): string
    {
        if ($this->isCategoriesParentAndChild($this->selectedCategory)) {
            return $this->selectedCategory->sortBy('depth')->last()->title;
        } else {
            return $this->selectedCategory->sortBy('depth')->implode('title', ', ');
        }
    }

    /**
     * Are given categories sequence nested ?
     *
     * @param Collection $categories
     * @return bool
     */
    private function isCategoriesParentAndChild(Collection $categories): bool
    {
        $sortedCategoriesDepths = $categories->sortBy('depth')->pluck('depth');

        for ($i = 0; $i < count($sortedCategoriesDepths); $i++) {
            if (isset($sortedCategoriesDepths[$i + 1]) && $sortedCategoriesDepths[$i] !== ($sortedCategoriesDepths[$i + 1] - 1)) {
                return false;
            }
        }

        return true;
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
