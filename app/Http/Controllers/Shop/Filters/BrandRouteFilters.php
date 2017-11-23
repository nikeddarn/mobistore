<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 20.11.17
 * Time: 19:14
 */

namespace App\Http\Controllers\Shop\Filters;


use App\Models\Category;
use Illuminate\Support\Collection;

trait BrandRouteFilters
{
    /**
     * Create possible user filters.
     *
     * @return array
     */
    private function getPossibleFilters(): array
    {
        $filters = [];

        // set current selected items to filters generator

        $selectedItems = $this->prepareSelectedItemsForFiltersCreator();

        $this->brandRouteFiltersGenerator->setCurrentSelectedItems($selectedItems);

        // create categories filters

            $categoryFilters = [];

            $selectedCategoriesWithRoot = (clone $this->selectedCategory)->push($this->rootCategory);
            $maxSelectedCategoriesDepth = $this->maxSelectedCategoriesDepth($selectedCategoriesWithRoot);

            for ($depth = 0; $depth < config('shop.category_filters_depth') && $depth < ($maxSelectedCategoriesDepth + 1); $depth++) {

                $selectedItemsOnDepth = $this->getSelectedCategoriesIdByDepth($selectedCategoriesWithRoot, $depth);

                $this->brandRouteFiltersGenerator->getFilterCreator(self::CATEGORY)->setAdditionalConstraints(function ($query) use ($selectedItemsOnDepth) {
                    return $query->whereIn('categories.parent_id', $selectedItemsOnDepth);
                });

                $categoryFilter = $this->brandRouteFiltersGenerator->getFilter(self::CATEGORY);

                if ($categoryFilter->count() > 1) {
                    $categoryFilters[] = $categoryFilter;
                }
            }

            if (!empty($categoryFilters)) {
                $filters[self::CATEGORY] = $categoryFilters;
            }

        // create quality filter

        $qualityFilter = $this->brandRouteFiltersGenerator->getFilter(self::QUALITY);
        if ($qualityFilter->count() > 1) {
            $filters[self::QUALITY] = $qualityFilter;
        }


        // create color filters

        $colorFilter = $this->brandRouteFiltersGenerator->getFilter(self::COLOR);
        if ($colorFilter->count() > 1) {
            $filters[self::COLOR] = $colorFilter;
        }
        return $filters;
    }

    /**
     * Define array of selected categories id which has received depth.
     * @param Collection $selectedCategoriesWithRoot
     * @param int $depth
     * @return array
     */
    private function getSelectedCategoriesIdByDepth(Collection $selectedCategoriesWithRoot, int $depth): array
    {
        return $selectedCategoriesWithRoot
            ->filter(function (Category $category) use ($depth) {
                return $category->depth === $depth;
            })
            ->pluck('id')
            ->toArray();
    }

    /**
     * Define max depth of selected items.
     *
     * @param Collection $selectedCategories
     * @return int
     */
    private function maxSelectedCategoriesDepth(Collection $selectedCategories): int
    {
        return $selectedCategories->sortBy('depth')->last()->depth;
    }
}