<?php
/**
 * Create filter creators.
 * Generate filters with url on each item.
 */

namespace App\Http\Controllers\Shop\Filters\FilterGenerators;


use App\Contracts\Shop\Products\Filters\FilterCreatorInterface;
use App\Contracts\Shop\Products\Filters\FilterTypes;
use Closure;
use Exception;
use Illuminate\Support\Collection;

abstract class FiltersGenerator implements FilterTypes
{
    /**
     * Set of filters selected items.
     *
     * @var Collection
     */
    private $currentSelectedItems = null;

    /**
     * Set selected items for forming any filter.
     *
     * @param array $currentSelectedItems
     */
    public function setCurrentSelectedItems(array $currentSelectedItems)
    {
        $this->currentSelectedItems = $currentSelectedItems;
    }

    /**
     * Create filter items by given filter type with filter items urls depends on current selected items.
     *
     * @param string $type
     * @param array|null $currentSelectedItems
     * @return Collection
     * @throws Exception
     */
    public function getFilter(string $type, array $currentSelectedItems = null): Collection
    {
        if ($currentSelectedItems) {
            $this->currentSelectedItems = $currentSelectedItems;
        }

        // selected items on any filter must be defined (may be empty) for correct work of filter items route creator.
        if (!isset($this->currentSelectedItems[$type])) {
            throw new Exception('Current selected items on ' . $type . ' filter is undefined');
        }


        $filter = $this->getFilterCreator($type)->getFilterItems($this->getDefaultConstraints($type, $this->currentSelectedItems));

        return $this->createFilterItemsUrl($filter, $this->currentSelectedItems, $type);
    }

    /**
     * Get filter creator
     *
     * @param string $type
     * @return FilterCreatorInterface
     * @throws Exception
     */
    public function getFilterCreator(string $type): FilterCreatorInterface
    {
        $creatorName = $type . 'FilterCreator';

        if (property_exists($this, $creatorName)) {
            return $this->$creatorName;
        } else {
            throw new Exception('Undefined Filter Type');
        }
    }

    /**
     * Get base constraints for given filter type.
     *
     * @param string $type
     * @param array $currentSelectedItems
     * @return Closure
     */
    abstract protected function getDefaultConstraints(string $type, array $currentSelectedItems): Closure;

    /**
     * Create and add url to each filter item.
     *
     * @param Collection $filter
     * @param array $currentSelectedItems
     * @param string $type
     * @return Collection
     */
    protected function createFilterItemsUrl(Collection $filter, array $currentSelectedItems, string $type): Collection
    {
        foreach ($filter as $filterItem) {

            if ($this->isFilterItemSelected($filterItem, $currentSelectedItems[$type])) {
                $filterItem->selected = true;
                $shouldBeSelectedItemsOnThisFilter = $this->subtractSelectedItemWithDependentItems($filterItem, $currentSelectedItems, $type);
            } else {
                $filterItem->selected = false;
                $shouldBeSelectedItemsOnThisFilter = $this->addFilterItemToShouldBeSelectedItems($filterItem, $currentSelectedItems, $type);
            }

            $filterItem->filterUrl = $this->formFilterItemUrl($shouldBeSelectedItemsOnThisFilter);
        }

        return $filter;
    }

    /**
     * Is current filter item selected on this route?
     *
     * @param $item
     * @param Collection $currentSelectedItemsOnThisFilter
     * @return bool
     */
    private function isFilterItemSelected($item, Collection $currentSelectedItemsOnThisFilter): bool
    {
        return $currentSelectedItemsOnThisFilter->pluck('id')->contains($item->id);
    }

    /**
     * Add filter item to filter items collection that will be used as selected on click at this item.
     *
     * @param $addingFilterItem
     * @param array $currentSelectedItems
     * @param string $type
     * @return array
     * @internal param array $shouldBeSelectedItemsOnThisFilter
     */
    private function addFilterItemToShouldBeSelectedItems($addingFilterItem, array $currentSelectedItems, string $type):array
    {
        $shouldBeSelectedItemsOnThisFilter = clone $currentSelectedItems[$type];

        $shouldBeSelectedItemsOnThisFilter->push($addingFilterItem);

        $currentSelectedItems[$type] = $shouldBeSelectedItemsOnThisFilter;

        return $currentSelectedItems;
    }

    /**
     * Subtract filter item and its dependent items (if needing) from selected items that will be used on click at this filter.
     *
     * @param $subtractingFilterItem
     * @param array $currentSelectedItems
     * @param string $type
     * @return array
     * @internal param array $shouldBeSelectedItemsOnThisFilter
     */
    abstract protected function subtractSelectedItemWithDependentItems($subtractingFilterItem, array $currentSelectedItems, string $type): array;

    /**
     * Subtract filter item from selected items collection.
     *
     * @param $subtractingFilterItem
     * @param Collection $shouldBeSelectedItemsOnThisFilter
     * @return Collection
     */
    protected function subtractFilterItem($subtractingFilterItem, Collection $shouldBeSelectedItemsOnThisFilter): Collection
    {
        return $shouldBeSelectedItemsOnThisFilter->filter(function ($filterItem) use ($subtractingFilterItem) {
            return $filterItem->id !== $subtractingFilterItem->id;
        });
    }

    /**
     * Create filter item url depends on selected item that will be used at this url.
     *
     * @param array $shouldBeSelectedItems
     * @return string
     */
    private function formFilterItemUrl(array $shouldBeSelectedItems): string
    {
        if ($this->isMultiplyRoute($shouldBeSelectedItems)) {
            return $this->getMultiplyRoutePrefix() . $this->getMultiplyRoutePath($shouldBeSelectedItems);
        } else {
            return $this->getSingleRoutePrefix() . $this->getSingleRoutePath($shouldBeSelectedItems);
        }
    }

    /**
     * Is filter item route multiply ?
     *
     * @param array $shouldBeSelectedItems
     * @return bool
     */
    abstract protected function isMultiplyRoute(array $shouldBeSelectedItems): bool;

    /**
     * Prefix for "single" route.
     *
     * @return string
     */
    abstract protected function getSingleRoutePrefix(): string;

    /**
     * Prefix for "multiply" route.
     *
     * @return string
     */
    abstract protected function getMultiplyRoutePrefix(): string;

    /**
     * Create "single" route path part.
     *
     * @param array $shouldBeSelectedItems
     * @return string
     */
    abstract protected function getSingleRoutePath(array $shouldBeSelectedItems): string;

    /**
     * Create part of url from given selected items.
     *
     * @param Collection $shouldBeSelectedItems
     * @return string
     */
    protected function createUrlPart(Collection $shouldBeSelectedItems): string
    {
        if (isset($shouldBeSelectedItems) && $shouldBeSelectedItems->count()) {
            return '/' . $shouldBeSelectedItems->pluck('breadcrumb')->implode('/');
        } else {
            return '';
        }
    }

    /**
     * Create "multiply" route path part.
     *
     * @param array $shouldBeSelectedItems
     * @return string
     */
    private function getMultiplyRoutePath(array $shouldBeSelectedItems): string
    {
        $routePath = '';

        foreach ($shouldBeSelectedItems as $urlItemsGroup => $urlItems) {
            if ($urlItems->count()) {
                $routePath .= '/' . $urlItemsGroup . '=' . $urlItems->pluck('breadcrumb')->implode(',');
            }
        }

        return $routePath;
    }
}