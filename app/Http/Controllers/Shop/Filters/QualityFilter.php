<?php

/**
 * Retrieve possible Quality levels depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use Illuminate\Support\Collection;

trait QualityFilter
{

    /**
     * Define possible quality levels for user filters.
     *
     * @return Collection
     */
    private function getPossibleQuality()
    {
        return $this->quality->whereHas('product', function ($query) {

            if ($this->selectedCategory) {
                $query->where('categories_id', $this->selectedCategory->id);
            }
        })
            ->get();
    }

    /**
     * Add to each quality filter its Url.
     *
     * @param Collection $possibleQuality
     * @return Collection
     */
    private function formPossibleQualityUrl(Collection $possibleQuality)
    {
        $routeStartPart = $this->getPersistentFilteredUrlPart();

        return $possibleQuality->each(function ($item) use ($routeStartPart){
            $item->filterUrl = $routeStartPart . '/quality=' . $item->breadcrumb;
        });
    }
}