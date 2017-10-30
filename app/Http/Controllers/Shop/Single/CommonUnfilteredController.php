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

        $this->selectedCategory = $this->selectedMetaData->category;
        $this->selectedBrand = $this->selectedMetaData->brand;
        $this->selectedModel = $this->selectedMetaData->deviceModel;
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
            if ($this->selectedCategory) {
                $this->categoryHasProductsQueryBuilder()($query);
            }
            if ($this->selectedBrand) {
                $query->where('brands_id', $this->selectedBrand->id);
            }
            if ($this->selectedModel) {
                $query->whereHas('deviceModel', function ($query) {
                    $query->where('id', $this->selectedModel->id);
                });
            }
        })
            ->with('primaryImage');

        return $this->isPaginable ? $query->paginate(config('shop.products_per_page')) : $query->get();
    }
}
