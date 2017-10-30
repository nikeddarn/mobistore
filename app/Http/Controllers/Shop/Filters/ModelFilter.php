<?php

/**
 * Retrieve possible Models depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Brand;
use Illuminate\Support\Collection;

trait ModelFilter
{

    /**
     * Define possible models for user filters.
     *
     * @return Collection
     * @internal param Brand $brand
     */
    private function getPossibleModels()
    {
        return $this->model->whereHas('product', function ($query) {
            $query->where('categories_id', $this->selectedCategory->id);
        })
            ->where('brands_id', $this->usedInFiltersBrand->id)
            ->orderBy('title')
            ->get();
    }

    /**
     * Add to each model's filter its Url.
     *
     * @param Collection $possibleModels
     * @return Collection
     */
    private function formPossibleModelsUrl(Collection $possibleModels)
    {
        // Path prefix for 'category' route.
        $categoryPathPrefix = '/category/' . $this->selectedCategory->url . '/' . $this->usedInFiltersBrand->breadcrumb;
        // Path prefix for 'filtered category' route.
        $filterPathPrefix = '/filter/category/category=' . $this->selectedCategory->breadcrumb . '/brand=' . $this->usedInFiltersBrand->breadcrumb;

        return $possibleModels->each(function ($item) use ($categoryPathPrefix, $filterPathPrefix) {
            if ($this->selectedModel) {
                if ($item->id === $this->selectedModel->id) {
                    $item->filterUrl = $categoryPathPrefix;
                } else {
                    $item->filterUrl = $filterPathPrefix . '/model=' . $this->selectedModel->breadcrumb . ',' . $item->breadcrumb;
                }
            } else {
                $urlParts[] = $categoryPathPrefix;
                $item->filterUrl = $categoryPathPrefix . '/' . $item->breadcrumb;
            }

            $item->selected = ($this->selectedModel && $item->id === $this->selectedModel->id) ? true : false;
        });
    }
}