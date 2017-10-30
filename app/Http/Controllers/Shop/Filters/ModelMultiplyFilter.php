<?php

/**
 * Retrieve possible Models depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\DeviceModel;
use Illuminate\Support\Collection;

trait ModelMultiplyFilter
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
            $query->whereIn('categories_id', $this->selectedCategory->pluck('id'));
        })
            ->whereIn('brands_id', $this->usedInFiltersBrand->pluck('id'))
            ->orderBy('title')
            ->with('brand')
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
        return $possibleModels->each(function ($item){

            $isItemSelected = (isset($this->selectedModel) && in_array($item->id, $this->selectedModel->pluck('id')->toArray())) ? true : false;

            if (isset($this->selectedModel)){

                $selectedModel = clone $this->selectedModel;

                if ($isItemSelected){

                    $selectedModel = $this->removeDeselectingModel($selectedModel, $item);

                    if($this->selectedColor || $this->selectedQuality || (isset($this->selectedBrand) && $this->selectedBrand->count() > 1) || (isset($selectedModel) && $selectedModel->count() > 1)){
                        $item->filterUrl = $this->getFilteredUrl(null, $selectedModel);
                    }else{
                        $item->filterUrl = $this->getUnfilteredUrl(null, $selectedModel);
                    }

                }else{
                    $item->filterUrl = $this->getFilteredUrl(null, $selectedModel->push($item));
                }

            }else{
                $item->filterUrl = $this->getFilteredUrl(null, collect([0 => $item]));
            }

            $item->selected = $isItemSelected;
        });
    }

    private function removeDeselectingModel(Collection $models, DeviceModel $deselecting)
    {
        return $models->filter(function ($value) use ($deselecting){
            return $value->id !== $deselecting->id;
        });
    }
}