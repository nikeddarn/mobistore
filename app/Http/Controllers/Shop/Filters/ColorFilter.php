<?php

/**
 * Retrieve possible Colors depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use Illuminate\Support\Collection;

trait ColorFilter
{

    /**
     * Define possible quality levels for user filters.
     *
     * @return Collection
     */
    private function getPossibleColors()
    {
        return $this->color->whereHas('product', function ($query) {

            if ($this->selectedCategory) {
                $query->where('categories_id', $this->selectedCategory->id);
            }
        })
            ->get();
    }

    /**
     * Add to each color's filter its Url.
     *
     * @param Collection $possibleColors
     * @return Collection
     */
    private function formPossibleColorsUrl(Collection $possibleColors)
    {
        $routeStartPart = $this->getPersistentFilteredUrlPart();

        return $possibleColors->each(function ($item) use ($routeStartPart){
            $item->filterUrl = $routeStartPart . '/color=' . $item->breadcrumb;
        });
    }
}