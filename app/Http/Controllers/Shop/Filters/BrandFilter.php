<?php

/**
 * Retrieve possible Brands depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use Illuminate\Support\Collection;

trait BrandFilter {

    /**
     * Define possible brands for user filters.
     *
     * @return Collection
     */
    private function getPossibleBrands()
    {
        return $this->brand->whereHas('product', function ($query) {
            $query->where('categories_id', $this->selectedCategory->id);
        })
            ->orderBy('priority', 'asc')
            ->get();
    }

    /**
     * Add to each brand's filter its Url.
     *
     * @param Collection $possibleBrands
     * @return Collection
     */
    private function formPossibleBrandsUrl(Collection $possibleBrands)
    {
        // Path prefix for 'category' route.
        $categoryPathPrefix = '/category/' . $this->selectedCategory->url;
        // Path prefix for 'filtered category' route.
        $filterPathPrefix = '/filter/category/category=' . $this->selectedCategory->breadcrumb;

        return $possibleBrands->each(function ($item) use ($categoryPathPrefix, $filterPathPrefix){
            if ($this->selectedBrand) {
                if ($item->id === $this->selectedBrand->id) {
                    $item->filterUrl = $categoryPathPrefix;
                } else {
                    $item->filterUrl =  $filterPathPrefix . '/brand=' . $this->selectedBrand->breadcrumb . ',' . $item->breadcrumb;
                }
            } else {
                $item->filterUrl =  $categoryPathPrefix . '/' . $item->breadcrumb;
            }
            $item->selected = ($this->selectedBrand && $item->id === $this->selectedBrand->id) ? true : false;
        });
    }
}