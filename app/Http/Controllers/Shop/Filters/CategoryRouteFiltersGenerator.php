<?php
/**
 * Create filter creators.
 * Generate filters for "category" route.
 */

namespace App\Http\Controllers\Shop\Filters;


use App\Http\Controllers\Shop\Filters\FilterCreators\BrandFilterCreator;
use App\Http\Controllers\Shop\Filters\FilterCreators\ColorFilterCreator;
use App\Http\Controllers\Shop\Filters\FilterCreators\ModelFilterCreator;
use App\Http\Controllers\Shop\Filters\FilterCreators\QualityFilterCreator;
use App\Models\Category;
use App\Models\DeviceModel;
use Closure;
use Exception;
use Illuminate\Support\Collection;

class CategoryRouteFiltersGenerator extends FiltersGenerator
{
    /**
     * @var ColorFilterCreator
     */
    protected $colorFilterCreator;

    /**
     * @var QualityFilterCreator
     */
    protected $qualityFilterCreator;

    /**
     * @var BrandFilterCreator
     */
    protected $brandFilterCreator;

    /**
     * @var ModelFilterCreator
     */
    protected $modelFilterCreator;


    /**
     * BrandRouteFiltersGenerator constructor.
     *
     * @param ColorFilterCreator $colorFilterCreator
     * @param QualityFilterCreator $qualityFilterCreator
     * @param BrandFilterCreator $brandFilterCreator
     * @param ModelFilterCreator $modelFilterCreator
     */
    public function __construct(ColorFilterCreator $colorFilterCreator, QualityFilterCreator $qualityFilterCreator, BrandFilterCreator $brandFilterCreator, ModelFilterCreator $modelFilterCreator)
    {
        $this->colorFilterCreator = $colorFilterCreator;
        $this->qualityFilterCreator = $qualityFilterCreator;
        $this->brandFilterCreator = $brandFilterCreator;
        $this->modelFilterCreator = $modelFilterCreator;
    }

    /**
     * Get base constraints for given filter type.
     *
     * @param string $type
     * @param array $currentSelectedItems
     * @return Closure
     */
    protected function getDefaultConstraints(string $type, array $currentSelectedItems): Closure
    {
        if ($type === self::MODEL) {
            return $this->modelFilterConstraints($currentSelectedItems);
        } else {
            return $this->commonFilterConstraints($currentSelectedItems);
        }
    }

    /**
     * Get base constraints for filter.
     *
     * @param array $currentSelectedItems
     * @return Closure
     */
    private function commonFilterConstraints(array $currentSelectedItems): closure
    {
        $leavesOfSelectedCategories = $this->getLeavesOfSelectedCategories($currentSelectedItems[self::CATEGORY]);

        return function ($query) use ($leavesOfSelectedCategories) {
            return $query
                ->whereHas('product', function ($query) use ($leavesOfSelectedCategories) {

                    $query->whereIn('categories_id', $leavesOfSelectedCategories->pluck('id'));
                });
        };
    }

    private function modelFilterConstraints(array $currentSelectedItems): Closure
    {
        $leavesOfSelectedCategories = $this->getLeavesOfSelectedCategories($currentSelectedItems[self::CATEGORY]);

        return function ($query) use ($leavesOfSelectedCategories, $currentSelectedItems) {
            return $query
                ->whereHas('product', function ($query) use ($leavesOfSelectedCategories) {

                    $query->whereIn('categories_id', $leavesOfSelectedCategories->pluck('id'));
                })
                ->whereHas('brand', function ($query) use ($currentSelectedItems) {
                    $query->whereIn('id', $currentSelectedItems[self::BRAND]->pluck('id'));
                });
        };
    }

    /**
     * Find and collect leaves of selected categories.
     *
     * @param Collection $selectedCategories
     * @return Collection
     */
    private function getLeavesOfSelectedCategories(Collection $selectedCategories): Collection
    {
        $leavesOfSelectedCategories = collect();

        $selectedCategories->each(function (Category $selectedCategory) use (&$leavesOfSelectedCategories) {
            if ($selectedCategory->isLeaf()) {
                $leavesOfSelectedCategories->push($selectedCategory);
            } else {
                $selectedCategoryDescendants = $selectedCategory->descendants;
                if ($selectedCategoryDescendants && $selectedCategoryDescendants->count()) {
                    $selectedCategoryDescendants->each(function ($descendant) use (&$leavesOfSelectedCategories) {
                        if ($descendant->isLeaf()) {
                            $leavesOfSelectedCategories->push($descendant);
                        }
                    });
                }
            }
        });

        return $leavesOfSelectedCategories;
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
    protected function subtractSelectedItemWithDependentItems($subtractingFilterItem, array $currentSelectedItems, string $type): array
    {
        if ($type === self::BRAND) {
            return $this->subtractBrandFilterItemWithDependedDeviceModels($subtractingFilterItem, $currentSelectedItems);
        }else{
            $currentSelectedItems[$type] = $this->subtractFilterItem($subtractingFilterItem, clone $currentSelectedItems[$type]);
            return $currentSelectedItems;
        }
    }

    /**
     * Subtract filter item from selected brands collection.
     * Subtract models of this brand from selected models collection.
     *
     * @param $subtractingFilterItem
     * @param array $currentSelectedItems
     * @return array
     * @internal param array $shouldBeSelectedItemsOnThisFilter
     */
    private function subtractBrandFilterItemWithDependedDeviceModels($subtractingFilterItem, array $currentSelectedItems)
    {
        $currentSelectedItems[self::BRAND] = $this->subtractFilterItem($subtractingFilterItem, clone $currentSelectedItems[self::BRAND]);

        $currentSelectedItems[self::MODEL] = clone $currentSelectedItems[self::MODEL]->filter(function (DeviceModel $model) use ($subtractingFilterItem){
            return $model->brands_id !== $subtractingFilterItem->id;
        });

        return $currentSelectedItems;
    }

    /**
     * Is filter item route multiply ?
     *
     * @param array $shouldBeSelectedItems
     * @return bool
     */
    protected function isMultiplyRoute(array $shouldBeSelectedItems): bool
    {
        return (isset($shouldBeSelectedItems[self::QUALITY]) && $shouldBeSelectedItems[self::QUALITY]->count()) || (isset($shouldBeSelectedItems[self::COLOR]) && $shouldBeSelectedItems[self::COLOR]->count()) || (isset($shouldBeSelectedItems[self::BRAND]) && $shouldBeSelectedItems[self::BRAND]->count() > 1) || (isset($shouldBeSelectedItems[self::MODEL]) && $shouldBeSelectedItems[self::MODEL]->count() > 1);
    }

    /**
     * Prefix for single brand route.
     *
     * @return string
     */
    protected function getSingleRoutePrefix(): string
    {
        return '/category';
    }

    /**
     * Prefix for "multiply" route.
     *
     * @return string
     */
    protected function getMultiplyRoutePrefix(): string
    {
        return '/filter/category';
    }

    /**
     * Create "single category" route path part.
     *
     * @param array $shouldBeSelectedItems
     * @return string
     * @throws Exception
     */
    protected function getSingleRoutePath(array $shouldBeSelectedItems): string
    {
        $routePath = '';

        if (!$shouldBeSelectedItems[self::CATEGORY]->count()) {
            throw new Exception('CATEGORY route component is missing on "single" route');
        }

        $routePath .= $this->createUrlPart($shouldBeSelectedItems[self::CATEGORY]->sortBy('depth')) . $this->createUrlPart($shouldBeSelectedItems[self::BRAND]) . $this->createUrlPart($shouldBeSelectedItems[self::MODEL]);

        return $routePath;
    }
}