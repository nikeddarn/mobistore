<?php

namespace App\Http\Controllers\Shop\Single;

use App\Http\Controllers\Shop\ShopController;
use App\Models\MetaData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

        $this->selectedCategory = collect();
        if ($this->selectedMetaData->category){
            $this->selectedCategory->push($this->selectedMetaData->category);
        }

        $this->selectedBrand = collect();
        if ($this->selectedMetaData->brand){
            $this->selectedBrand->push($this->selectedMetaData->brand);
        }

        $this->selectedModel = collect();
        if ($this->selectedMetaData->deviceModel){
            $this->selectedModel->push($this->selectedMetaData->deviceModel);
        }
    }

    /**
     * @return MetaData
     */
    protected function createMetaData(){
        return $this->selectedMetaData;
    }


    /**
     * Form start part of path to filtered route
     *
     * @return string
     */
    protected function getPersistentFilteredUrlPart(){

        $filterPath = $this->filteredRoutePrefix();

        if($this->selectedCategory){
            $filterPath .= '/category=' . $this->selectedCategory->breadcrumb;
        }

        if($this->selectedBrand){
            $filterPath .= '/brand=' . $this->selectedBrand->breadcrumb;
        }

        if ($this->selectedModel) {
            $filterPath .= '/model=' . $this->selectedModel->breadcrumb;
        }

        return $filterPath;
    }

    /**
     * Collection of products that relevant the conditions.
     *
     * @return Collection|LengthAwarePaginator
     */
    protected function getProducts()
    {
        $query = $this->product->where(function ($query) {
            if ($this->selectedCategory->count()) {
                $this->categoryHasProductsQueryBuilder()($query);
            }
            if ($this->selectedBrand->count()) {
                $query->where('brands_id', $this->selectedBrand->first()->id);
            }
            if ($this->selectedModel->count()) {
                $query->whereHas('deviceModel', function ($query) {
                    $query->where('id', $this->selectedModel->first()->id);
                });
            }
        })
            ->with('primaryImage');

        return $this->isPaginable ? $query->paginate(config('shop.products_per_page')) : $query->get();
    }

    /**
     * Create array of selected items.
     *
     * @return array
     */
    protected function prepareSelectedItems(): array
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
