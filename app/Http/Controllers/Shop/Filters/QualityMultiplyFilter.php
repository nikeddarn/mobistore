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
     * Quality that will be selected at request on this filter.
     *
     * @var Collection
     */
    protected $shouldBeSelectedQuality;

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
            if ($this->isQualitySelected($item)){
                $item->selected = true;
                $item->filterUrl = $this->getDeselectingUrl($item);
            }else{
                $item->selected = false;
                $item->filterUrl = $this->getSelectingUrl($item);
            }

//            if (isset($this->selectedQuality)){
//
//                $selectedQuality = clone $this->selectedQuality;
//
//                if ($isItemSelected){
//
//                    $selectedQuality = $this->removeSelectedQuality($selectedQuality, $item);
//
//                    if($this->selectedColor || $selectedQuality->count() || (isset($this->selectedBrand) && $this->selectedBrand->count() > 1) || (isset($this->selectedModel) && $this->selectedModel->count() > 1)){
//                        $item->filterUrl = $this->getFilteredUrl(null, null, $selectedQuality);
//                    }else{
//                        $item->filterUrl = $this->getUnfilteredUrl();
//                    }
//
//                }else{
//                    $item->filterUrl = $this->getFilteredUrl(null, null, $selectedQuality->push($item));
//                }
//
//            }else{
//                $item->filterUrl = $this->getFilteredUrl(null, null, collect([0 => $item]));
//            }


        });
    }

    private function isQualitySelected(Quality $quality)
    {
        return $this->selectedQuality->pluck('id')->contains($quality->id);
    }

    private function getDeselectingUrl(Quality $quality)
    {
        $this->shouldBeSelectedQuality = $this->removeSelectedCategory($quality);

        if (($this->selectedQuality && $this->selectedQuality->count()) || ($this->selectedColor && $this->selectedColor->count()) || ($this->shouldBeSelectedCategories->count() > 2)) {
            return $this->getFilteredUrl();
        } else {
            if ($this->shouldBeSelectedCategories->count() < 2){
                return $this->getUnfilteredUrl();
            }else{
                if ($this->shouldBeSelectedCategories->first()->id === $this->shouldBeSelectedCategories->last()->parent->id || $this->shouldBeSelectedCategories->first()->parent->id === $this->shouldBeSelectedCategories->last()->id){
                    return $this->getUnfilteredUrl();
                }else{
                    return $this->getFilteredUrl();
                }
            }
        }
    }

    private function getSelectingUrl(Quality $quality)
    {
        $this->shouldBeSelectedQuality = $this->addSelectedQuality($quality);
        return $this->getFilteredUrl();
    }

    private function removeSelectedQuality(Quality $quality)
    {
        $selectedQuality = clone $this->selectedQuality;

        return $selectedQuality->filter(function ($item) use ($quality){
            return $item->id !== $quality->id;
        });
    }

    private function addSelectedQuality(Quality $quality)
    {
        $selectedQuality = clone $this->selectedQuality;
        return $selectedQuality->push($quality);
    }

//    private function getFilteredUrl(Collection $selectedBrands = null, Collection $selectedModels = null, Collection $selectedQuality = null, Collection $selectedColor = null)
//    {
//        $url = '/filter/category/category=' . $this->selectedCategory->first()->breadcrumb;
//
//        if (!$selectedBrands) {
//            $selectedBrands = $this->selectedBrand;
//        }
//
//        if (!$selectedModels) {
//            $selectedModels = $this->selectedModel;
//        }
//
//        if (!$selectedQuality) {
//            $selectedQuality = $this->selectedQuality;
//        }
//
//        if (!$selectedColor) {
//            $selectedColor = $this->selectedColor;
//        }
//
//        if ($selectedBrands && $selectedBrands->count()) {
//            $url .= '/brand=' . $selectedBrands->implode('breadcrumb', ',');
//        }
//
//        if ($selectedModels && $selectedModels->count()) {
//            $url .= '/model=' . $selectedModels->implode('breadcrumb', ',');
//        }
//
//        if ($selectedQuality && $selectedQuality->count()) {
//            $url .= '/quality=' . $selectedQuality->implode('breadcrumb', ',');
//        }
//
//        if ($selectedColor && $selectedColor->count()) {
//            $url .= '/color=' . $selectedColor->implode('breadcrumb', ',');
//        }
//
//        return $url;
//    }

//    private function getUnfilteredUrl(Collection $selectedBrand = null, Collection $selectedModel = null)
//    {
//        $url = '/category/' . $this->selectedCategory->first()->url;
//
//        if ($selectedModel && $selectedModel->count()) {
//            $url .= '/' . $selectedModel->first()->url;
//        } elseif ($selectedBrand && $selectedBrand->count()) {
//            $url .= '/' . $selectedBrand->first()->url;
//        } else {
//            if (isset($this->selectedModel) && $this->selectedModel->count()) {
//                $url .= '/' . $this->selectedModel->first()->url;
//            } elseif (isset($this->selectedBrand) && $this->selectedBrand->count()) {
//                $url .= '/' . $this->selectedBrand->first()->url;
//            }
//        }
//
//        return $url;
//    }
}