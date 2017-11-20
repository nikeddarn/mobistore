<?php
/**
 * Create filter creators.
 * Generate filters for "brand" route.
 */

namespace App\Http\Controllers\Shop\Filters;


use App\Http\Controllers\Shop\Filters\FilterCreators\CategoryFilterCreator;
use App\Http\Controllers\Shop\Filters\FilterCreators\ColorFilterCreator;
use App\Http\Controllers\Shop\Filters\FilterCreators\QualityFilterCreator;
use Closure;
use Exception;
use Illuminate\Support\Collection;

class BrandRouteFiltersGenerator extends FiltersGenerator
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
     * @var CategoryFilterCreator
     */
    protected $categoryFilterCreator;

    /**
     * BrandRouteFiltersGenerator constructor.
     *
     * @param ColorFilterCreator $colorFilterCreator
     * @param QualityFilterCreator $qualityFilterCreator
     * @param CategoryFilterCreator $categoryFilterCreator
     */
    public function __construct(ColorFilterCreator $colorFilterCreator, QualityFilterCreator $qualityFilterCreator, CategoryFilterCreator $categoryFilterCreator)
    {
        $this->colorFilterCreator = $colorFilterCreator;
        $this->qualityFilterCreator = $qualityFilterCreator;
        $this->categoryFilterCreator = $categoryFilterCreator;
    }

    /**
     * Get base constraints for given filter type.
     *
     * @param string $type
     * @param array $currentSelectedItems
     * @return Closure
     */
    protected function getDefaultConstraints(string $type, array $currentSelectedItems):Closure
    {
        if ($type === self::CATEGORY){
            return $this->categoriesFilterConstraints($currentSelectedItems);
        }else{
            return $this->commonFilterConstraints($currentSelectedItems);
        }
    }

    /**
     * Get base constraints for Color and Quality filter.
     *
     * @param array $currentSelectedItems
     * @return Closure
     */
    private function commonFilterConstraints(array $currentSelectedItems): closure
    {
        $selectedBrand = $currentSelectedItems[self::BRAND];
        $selectedModel = $currentSelectedItems[self::MODEL];

        return function ($query) use ($selectedBrand, $selectedModel) {
            return $query
                ->whereHas('product', function ($query) use ($selectedBrand) {

                    $query->where('brands_id', $selectedBrand->first()->id);
                })
                ->whereHas('product.deviceModel', function ($query) use ($selectedModel) {

                    $query->where('id', $selectedModel->first()->id);
                });
        };
    }

    /**
     * Get base constraints for Category filter.
     *
     * @param array $currentSelectedItems
     * @return Closure
     */
    private function categoriesFilterConstraints(array $currentSelectedItems): closure
    {
        $selectedBrand = $currentSelectedItems[self::BRAND];
        $selectedModel = $currentSelectedItems[self::MODEL];

        return function ($query) use ($selectedBrand, $selectedModel) {
            return $query
                ->join('categories as node', function ($join) {
                    $join->whereRaw('node._lft BETWEEN categories._lft AND categories._rgt');

                })
                ->join('products', function ($join) use ($selectedBrand){
                    $join->on('products.categories_id', '=', 'node.id')
                        ->where('brands_id', $selectedBrand->first()->id);

                })
                ->join('products_models_compatible', function ($join) use ($selectedModel){
                    $join->on('products.id', '=', 'products_models_compatible.products_id')
                        ->where('products_models_compatible.models_id', $selectedModel->first()->id);
                })
                ->distinct();
        };

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
        if ($type === self::CATEGORY) {
            $currentSelectedItems[$type] = $this->subtractCategoryFilterItemWithDescendantsFromShouldBeSelectedItems($subtractingFilterItem, clone $currentSelectedItems[$type]);
        }else{
            $currentSelectedItems[$type] =  $this->subtractFilterItem($subtractingFilterItem, clone $currentSelectedItems[$type]);
        }

        return $currentSelectedItems;
    }

    /**
     * Subtract filter item with its descendants from filter items collection that will be used as selected on click at this item.
     *
     * @param $subtractingFilterItem
     * @param Collection $shouldBeSelectedItemsOnThisFilter
     * @return Collection
     */
    private function subtractCategoryFilterItemWithDescendantsFromShouldBeSelectedItems($subtractingFilterItem, Collection $shouldBeSelectedItemsOnThisFilter): Collection
    {
        $subtractedShouldBeSelectedItemsOnThisFilter = $shouldBeSelectedItemsOnThisFilter->filter(function ($filterItem) use ($subtractingFilterItem) {
            if ($filterItem->id === $subtractingFilterItem->id) {
                return false;
            } else {
                $subtractingFilterItemDescendants = $subtractingFilterItem->descendants;
                if ($subtractingFilterItemDescendants && $subtractingFilterItemDescendants->count()) {
                    foreach ($subtractingFilterItem->descendants as $subtractingFilterItemDescendant) {
                        if ($subtractingFilterItemDescendant->id === $filterItem->id) {
                            return false;
                        }
                    }
                }
            }
            return true;
        });

        return $subtractedShouldBeSelectedItemsOnThisFilter;
    }

    /**
     * Is filter item route multiply ?
     *
     * @param array $shouldBeSelectedItems
     * @return bool
     */
    protected function isMultiplyRoute(array $shouldBeSelectedItems): bool
    {
        return (isset($shouldBeSelectedItems[self::QUALITY]) && $shouldBeSelectedItems[self::QUALITY]->count()) || (isset($shouldBeSelectedItems[self::COLOR]) && $shouldBeSelectedItems[self::COLOR]->count()) || (isset($shouldBeSelectedItems[self::CATEGORY]) && $shouldBeSelectedItems[self::CATEGORY]->count() > 1 && !$this->isCategoriesParentAndChild($shouldBeSelectedItems[self::CATEGORY]));
    }


    /**
     * Are given categories sequence nested ?
     *
     * @param Collection $categories
     * @return bool
     */
    private function isCategoriesParentAndChild(Collection $categories): bool
    {
        $sortedCategoriesDepths = $categories->sortBy('depth')->pluck('depth');

        for ($i = 0; $i < count($sortedCategoriesDepths); $i++) {
            if (isset($sortedCategoriesDepths[$i + 1]) && $sortedCategoriesDepths[$i] !== ($sortedCategoriesDepths[$i + 1] - 1)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Prefix for single brand route.
     *
     * @return string
     */
    protected function getSingleRoutePrefix(): string
    {
        return '/brand';
    }

    /**
     * Prefix for "multiply" route.
     *
     * @return string
     */
    protected function getMultiplyRoutePrefix(): string
    {
        return '/filter/brand';
    }

    /**
     * Create "single brand" route path part.
     *
     * @param array $shouldBeSelectedItems
     * @return string
     * @throws Exception
     */
    protected function getSingleRoutePath(array $shouldBeSelectedItems): string
    {
        $routePath = '';

        if ($shouldBeSelectedItems[self::BRAND] !== 1){
            throw new Exception('BRAND route component is missing or multiply in selected items on "single" route');
        }

        if ($shouldBeSelectedItems[self::MODEL] !== 1){
            throw new Exception('MODEL route component is missing or multiply in selected items on "single" route');
        }

        $routePath .= $this->createUrlPart($shouldBeSelectedItems[self::BRAND]) . $this->createUrlPart($shouldBeSelectedItems[self::MODEL]) . $this->createUrlPart($shouldBeSelectedItems[self::CATEGORY]->sortBy('depth'));

        return $routePath;
    }
}