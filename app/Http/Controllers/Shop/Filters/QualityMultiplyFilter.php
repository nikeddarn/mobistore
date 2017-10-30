<?php

/**
 * Retrieve possible Quality levels depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Quality;
use Illuminate\Support\Collection;

trait QualityMultiplyFilter
{

    /**
     * Define possible quality levels for user filters.
     *
     * @return Collection
     */
    private function getPossibleQuality()
    {
        return $this->quality->whereHas('product', function ($query) {

            if (isset($this->selectedCategory) && $this->selectedCategory->count()) {
                $query->whereIn('categories_id', $this->selectedCategory->pluck('id'));
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
        return $possibleQuality->each(function ($item) {

            $isItemSelected = (isset($this->selectedQuality) && in_array($item->id, $this->selectedQuality->pluck('id')->toArray())) ? true : false;

            if (isset($this->selectedQuality)){

                $selectedQuality = clone $this->selectedQuality;

                if ($isItemSelected){

                    $selectedQuality = $this->removeDeselectingQuality($selectedQuality, $item);

                    if($this->selectedColor || $selectedQuality->count() || (isset($this->selectedBrand) && $this->selectedBrand->count() > 1) || (isset($this->selectedModel) && $this->selectedModel->count() > 1)){
                        $item->filterUrl = $this->getFilteredUrl(null, null, $selectedQuality);
                    }else{
                        $item->filterUrl = $this->getUnfilteredUrl();
                    }

                }else{
                    $item->filterUrl = $this->getFilteredUrl(null, null, $selectedQuality->push($item));
                }

            }else{
                $item->filterUrl = $this->getFilteredUrl(null, null, collect([0 => $item]));
            }

            $item->selected = $isItemSelected;
        });
    }

    private function removeDeselectingQuality(Collection $quality, Quality $deselecting)
    {
        return $quality->filter(function ($value) use ($deselecting){
            return $value->id !== $deselecting->id;
        });
    }
}