<?php

/**
 * Retrieve possible Quality levels depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Color;
use Illuminate\Support\Collection;

trait ColorByBrandMultiplyFilter
{
    /**
     * Quality that will be selected at request on this filter.
     *
     * @var Collection
     */
    protected $shouldBeSelectedColor = null;

    /**
     * Define possible quality levels for user filters.
     *
     * @return Collection
     */
    private function getPossibleColors()
    {
        $colorFilter = $this->color
            ->whereHas('product', function ($query) {

                $query->where('brands_id', $this->selectedBrand->first()->id);
            })
            ->whereHas('product.deviceModel', function ($query) {

                $query->where('id', $this->selectedModel->first()->id);
            })
            ->get()
            ->each(function ($item) {
                $this->formPossibleColorsUrl($item);
            });

        return $colorFilter->count() ? $colorFilter : null;
    }

    /**
     * Add to each quality filter its Url.
     *
     * @param Color $color
     * @return void
     */
    private function formPossibleColorsUrl(Color $color)
    {
            if ($this->isColorSelected($color)) {
                $color->selected = true;
                $this->shouldBeSelectedColor = $this->removeSelectedColor($color);

            } else {
                $color->selected = false;
                $this->shouldBeSelectedColor = $this->addSelectedColor($color);
            }

            $color->filterUrl = $this->getFilterItemUrl(['category' => $this->selectedCategory, 'color' => $this->shouldBeSelectedColor, 'quality' => $this->selectedQuality]);
    }

    private function isColorSelected(Color $color)
    {
        return isset($this->selectedColor) && $this->selectedColor->pluck('id')->contains($color->id);
    }


    private function removeSelectedColor(Color $color)
    {
        $selectedColor = clone $this->selectedColor;

        return $selectedColor->filter(function ($item) use ($color) {
            return $item->id !== $color->id;
        });
    }

    private function addSelectedColor(Color $color)
    {
        if (isset($this->selectedColor)) {
            $selectedColor = clone $this->selectedColor;
        } else {
            $selectedColor = collect();
        }

        return $selectedColor->push($color);
    }
}