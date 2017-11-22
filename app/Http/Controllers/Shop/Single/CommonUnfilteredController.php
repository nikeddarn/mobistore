<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\ShopController;
use App\Models\MetaData;
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
                $query->select(['id', 'url', 'breadcrumb', 'title']);
            }])
            ->select($this->metaData->transformAttributesByLocale(['categories_id', 'brands_id', 'models_id', 'page_title', 'meta_title', 'meta_description', 'meta_keywords', 'summary']))
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
     * @return MetaData
     */
    protected function createMetaData()
    {
        return $this->selectedMetaData;
    }

    protected function getLeavesOfMostDeepSelectedCategory(Collection $selectedCategory):Collection
    {
        $mostDeepSelectedCategory = $selectedCategory->sortBy('depth')->last();
        $leaves = collect();

        if ($mostDeepSelectedCategory->isLeaf()){
            return $leaves->push($mostDeepSelectedCategory);
        }else{
            foreach ($mostDeepSelectedCategory->descendants as $descendant){
                if ($descendant->isLeaf()){
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
}
