<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\ShopController;
use Illuminate\Support\Collection;

abstract class CommonUnfilteredController extends ShopController
{
    /**
     * Retrieve needing models by url.
     *
     * @param string $url
     * @return void
     */
    protected function getSelectedModels(string $url)
    {
        $this->selectedMetaData = $this->metaData->where('url', $url)
            ->with(['category', 'brand' => function ($query) {
                $query->select(['id', 'url', 'breadcrumb', 'title']);
            }, 'deviceModel' => function ($query) {
                $query->select(['id', 'url', 'breadcrumb', 'title', 'brands_id']);
            }])
            ->select(array_merge($this->metaData->transformAttributesByLocale(['categories_id', 'brands_id', 'models_id', 'page_title', 'meta_title', 'meta_description', 'meta_keywords', 'summary']), ['is_canonical', 'updated_at']))
            ->firstOrFail();

        // add selected category with ancestors without root category to collection.
        $this->selectedCategory = collect();
        if ($this->selectedMetaData->category) {
            $this->selectedCategory = $this->category->withDepth()->ancestorsAndSelf($this->selectedMetaData->category->id)->forget(0);
        }

        $this->selectedBrand = collect();
        if ($this->selectedMetaData->brand) {
            $this->selectedBrand->push($this->selectedMetaData->brand);
        }

        $this->selectedModel = collect();
        if ($this->selectedMetaData->deviceModel) {
            $this->selectedModel->push($this->selectedMetaData->deviceModel);
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
            'title' => $this->selectedMetaData->meta_title,
            'description' => $this->selectedMetaData->meta_description,
            'keywords' => $this->selectedMetaData->meta_keywords,

        ];
    }

    /**
     * Create array of data for meta and link tags.
     *
     * @return array
     */
    protected function createSpecialMetaData():array{
        $link = [];

        if ($this->isProductPageSingle) {
                if ( !$this->selectedMetaData->is_canonical && $this->selectedCategory->count() && $this->selectedCategory->sortBy('depth')->last()->isLeaf() && $this->selectedBrand->count() === 1 && $this->selectedModel->count() === 1){
                    $link[] = [
                        'rel' => 'canonical',
                        'href' => $this->createCanonicalUrl(),
                    ];
                }
            } else {
                $link[] = [
                    'rel' => 'canonical',
                    'href' => $this->request->url() . '?view=all',
                ];
                if ($this->products->previousPageUrl()) {
                    $link[] = [
                        'rel' => 'prev',
                        'href' => $this->products->previousPageUrl(),
                    ];
                }
                if ($this->products->nextPageUrl()) {
                    $link[] = [
                        'rel' => 'next',
                        'href' => $this->products->nextPageUrl(),
                    ];
                }
            }

        return [
            'link' => $link,
        ];
    }

    /**
     * Create canonical url for meta data.
     *
     * @return string
     */
    abstract protected function createCanonicalUrl():string;

    /**
     * Create page title, summary.
     *
     * @return array
     */
    protected function createPageData(): array
    {
        return [
            'pageTitle' => $this->selectedMetaData->page_title,
            'summary' => $this->selectedMetaData->summary,
        ];
    }

    /**
     * Get leaves of most deep selected categories.
     *
     * @param Collection $selectedCategory
     * @return Collection
     */
    protected function getLeavesOfMostDeepSelectedCategory(Collection $selectedCategory): Collection
    {
        $mostDeepSelectedCategory = $selectedCategory->sortBy('depth')->last();
        $leaves = collect();

        if ($mostDeepSelectedCategory->isLeaf()) {
            return $leaves->push($mostDeepSelectedCategory);
        } else {
            foreach ($mostDeepSelectedCategory->descendants as $descendant) {
                if ($descendant->isLeaf()) {
                    $leaves->push($descendant);
                }
            }
        }

        return $leaves;
    }

    /**
     * Create array of selected items for filters.
     *
     * @return array
     */
    protected function prepareSelectedItemsForFiltersCreator(): array
    {
        return [
            'brand' => $this->selectedBrand,
            'model' => $this->selectedModel,
            'category' => $this->selectedCategory,
            'color' => collect(),
            'quality' => collect(),
        ];
    }

    /**
     * Create response headers.
     *
     * @return array
     */
    protected function createHeaders():array
    {
        return [
            'Last-Modified' => date('D, d M Y H:i:s T', $this->selectedMetaData->updated_at->timestamp),
        ];
    }
}
