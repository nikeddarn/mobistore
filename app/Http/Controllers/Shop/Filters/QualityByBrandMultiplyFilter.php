<?php

/**
 * Retrieve possible Quality levels depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Quality;
use Illuminate\Support\Collection;

trait QualityByBrandMultiplyFilter
{
    /**
     * Quality that will be selected at request on this filter.
     *
     * @var Collection
     */
    protected $shouldBeSelectedQuality = null;

    /**
     * Define possible quality levels for user filters.
     *
     * @return Collection
     */
    private function getPossibleQuality()
    {
        $qualityFilter = $this->quality
            ->whereHas('product', function ($query) {

                $query->where('brands_id', $this->selectedBrand->first()->id);
            })
            ->whereHas('product.deviceModel', function ($query) {

                $query->where('id', $this->selectedModel->first()->id);
            })
            ->get()
            ->each(function ($item) {
                $this->formPossibleQualityUrl($item);
            });

        return $qualityFilter->count() ? $qualityFilter : null;
    }

    /**
     * Add to each quality filter its Url.
     *
     * @param Quality $quality
     * @return void
     */
    private function formPossibleQualityUrl(Quality $quality)
    {
        if ($this->isQualitySelected($quality)) {
            $quality->selected = true;
            $this->shouldBeSelectedQuality = $this->removeSelectedQuality($quality);
        } else {
            $quality->selected = false;
            $this->shouldBeSelectedQuality = $this->addSelectedQuality($quality);
        }

        $quality->filterUrl = $this->getFilterItemUrl(['category' => $this->selectedCategory, 'color' => $this->selectedColor, 'quality' => $this->shouldBeSelectedQuality]);
    }

    private function isQualitySelected(Quality $quality)
    {
        return isset($this->selectedQuality) && $this->selectedQuality->pluck('id')->contains($quality->id);
    }


    private function removeSelectedQuality(Quality $quality)
    {
        $selectedQuality = clone $this->selectedQuality;

        return $selectedQuality->filter(function ($item) use ($quality) {
            return $item->id !== $quality->id;
        });
    }

    private function addSelectedQuality(Quality $quality)
    {
        if (isset($this->selectedQuality)) {
            $selectedQuality = clone $this->selectedQuality;
        } else {
            $selectedQuality = collect();
        }

        return $selectedQuality->push($quality);
    }
}