<?php

/**
 * Retrieve possible Colors depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Color;
use Illuminate\Support\Collection;

trait ColorMultiplyFilter{

    /**
     * Define possible quality levels for user filters.
     *
     * @return Collection
     */
    private function getPossibleColors()
    {
        return $this->color->whereHas('product', function ($query) {

            if($this->selectedCategory){
                $query->whereIn('categories_id', $this->selectedCategory->pluck('id'));
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
        return $possibleColors->each(function ($item) {

            $isItemSelected = (isset($this->selectedColor) && in_array($item->id, $this->selectedColor->pluck('id')->toArray())) ? true : false;

            if (isset($this->selectedColor)){

                $selectedColor = clone $this->selectedColor;

                if ($isItemSelected){

                    $selectedColor = $this->removeDeselectingColor($selectedColor, $item);

                    if($selectedColor->count() || $this->selectedQuality || (isset($this->selectedBrand) && $this->selectedBrand->count() > 1) || (isset($this->selectedModel) && $this->selectedModel->count() > 1)){
                        $item->filterUrl = $this->getFilteredUrl(null, null, null, $selectedColor);
                    }else{
                        $item->filterUrl = $this->getUnfilteredUrl();
                    }

                }else{
                    $item->filterUrl = $this->getFilteredUrl(null, null, null, $selectedColor->push($item));
                }

            }else{
                $item->filterUrl = $this->getFilteredUrl(null, null, null,  collect([0 => $item]));
            }

            $item->selected = $isItemSelected;
        });
    }

    private function removeDeselectingColor(Collection $colors, Color $deselecting)
    {
        return $colors->filter(function ($value) use ($deselecting){
            return $value->id !== $deselecting->id;
        });
    }
}