<?php

/**
 * Retrieve possible Brands depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Brand;
use Illuminate\Support\Collection;

trait BrandMultiplyFilter {

    /**
     * Define possible brands for user filters.
     *
     * @return Collection
     */
    private function getPossibleBrands()
    {
        return $this->brand->whereHas('product', function ($query) {
            $query->whereIn('categories_id', $this->selectedCategory->pluck('id'));
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
        return $possibleBrands->each(function ($item){

            $isItemSelected = (isset($this->selectedBrand) && in_array($item->id, $this->selectedBrand->pluck('id')->toArray())) ? true : false;

            if (isset($this->selectedBrand)){

                $selectedBrand = clone $this->selectedBrand;

                if ($isItemSelected){

                    $selectedBrand = $this->removeDeselectingBrand($selectedBrand, $item);
                    $selectedModel = isset($this->selectedModel) ? $this->removeModelsOfDeselectingBrand(clone $this->selectedModel, $item) : null;
                    if($this->selectedColor || $this->selectedQuality || (isset($selectedBrand) && $selectedBrand->count() > 1) || (isset($selectedModel) && $selectedModel->count() > 1)){
                        $item->filterUrl = $this->getFilteredUrl($selectedBrand, $selectedModel);
                    }else{
                        $item->filterUrl = $this->getUnfilteredUrl($selectedBrand, $selectedModel);
                    }

                }else{
                    $item->filterUrl = $this->getFilteredUrl($selectedBrand->push($item), collect());
                }

            }else{
                $item->filterUrl = $this->getFilteredUrl(collect([0 => $item]));
            }

            $item->selected = $isItemSelected;
        });
    }

    private function removeDeselectingBrand(Collection $brands, Brand $deselecting)
    {
        return $brands->filter(function ($value) use ($deselecting){
            return $value->id !== $deselecting->id;
        });
    }

    private function removeModelsOfDeselectingBrand(Collection $models, Brand $brand)
    {
        return $models->filter(function($model) use ($brand){
            return $model->brand->id !== $brand->id;
        });
    }
}