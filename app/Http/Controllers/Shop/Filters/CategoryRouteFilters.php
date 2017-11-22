<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 22.11.17
 * Time: 18:11
 */

namespace App\Http\Controllers\Shop\Filters;


trait CategoryRouteFilters
{
    /**
     * Create possible user filters.
     *
     * @return array
     */
    private function getPossibleFilters()
    {
        $filters = [];

        $selectedItems = $this->prepareSelectedItemsForFiltersCreator();

        $this->categoryRouteFiltersGenerator->setCurrentSelectedItems($selectedItems);

        $brandFilter = $this->categoryRouteFiltersGenerator->getFilter(self::BRAND);
        if ($brandFilter->count() > 1) {
            $filters[self::BRAND] = $brandFilter;
        }

        $modelFilter = $this->categoryRouteFiltersGenerator->getFilter(self::MODEL);
        if ($modelFilter->count() > 1) {
            $filters[self::MODEL] = $modelFilter;
        }

        $qualityFilter = $this->categoryRouteFiltersGenerator->getFilter(self::QUALITY);
        if ($qualityFilter->count() > 1) {
            $filters[self::QUALITY] = $qualityFilter;
        }

        $colorFilter = $this->categoryRouteFiltersGenerator->getFilter(self::COLOR);
        if ($colorFilter->count() > 1) {
            $filters[self::COLOR] = $colorFilter;
        }
        return $filters;
    }
}