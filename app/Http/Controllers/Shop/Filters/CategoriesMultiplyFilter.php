<?php

/**
 * Retrieve possible Brands depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Category;
use Illuminate\Support\Collection;

trait CategoriesMultiplyFilter
{
    /**
     * Categories that will be selected at request on this filter.
     *
     * @var Collection
     */
    protected $shouldBeSelectedCategories = null;

    /**
     * Categories with its ancestors that has products.
     *
     * @var Collection
     */
    protected $notEmptyCategories;

    /**
     * One of most deep selected categories.
     *
     * @var Category
     */
    protected $mostDeepSelectedCategory;

    /**
     * Define possible brands for user filters.
     *
     * @return Collection
     */
    private function getParentCategoriesFilters()
    {
        $this->notEmptyCategories = $this->notEmptyCategoriesMap();
        $this->mostDeepSelectedCategory = $this->mostDeepSelectedCategory();

        $parentFilters =  $this->category->withDepth()->ancestorsAndSelf($this->mostDeepSelectedCategory->id)
            ->forget(0)// remove root node
            ->each(function ($ancestor) {
                $ancestor->siblings = $ancestor->parent->children()->get()->filter(function ($sibling) use ($ancestor) {
                    $sibling->depth = $ancestor->depth;
                    if ($this->hasBranchProducts($sibling)) {
                        if ($this->isCategorySelected($sibling)) {
                            $sibling->selected = true;
                            $this->shouldBeSelectedCategories = $this->removeSelectedCategory($sibling);
                        } else {
                            $sibling->selected = false;
                            $this->shouldBeSelectedCategories = $this->addSelectedCategory($sibling);
                        }
                        $sibling->filterUrl = $this->getFilterItemUrl(['category' => $this->shouldBeSelectedCategories, 'color' => $this->selectedColor, 'quality' =>$this->selectedQuality]);
                        return true;
                    }
                    return false;
                });
            })
            ->filter(function ($filter) {
                return $filter->siblings->count() > 1;
            });

        return $parentFilters->count() ? $parentFilters : null;
    }



    private function getChildrenCategoriesFilter()
    {
        if ($this->selectedCategory) {
            $filterItems = collect();
            $this->selectedCategory->each(function ($category) use (&$filterItems){
                if ($category->depth === $this->mostDeepSelectedCategory->depth && $category->children && $category->children->count()) {
                    $filterItems = $filterItems->merge($category->children);
                }
            });
        } else {
            $filterItems = $this->category->whereIsRoot()->first()->children;
        }

        $childrenFilter =  $filterItems->filter(function ($child) {
            if ($this->hasBranchProducts($child)) {
                $this->shouldBeSelectedCategories = $this->addSelectedCategory($child);
                $child->filterUrl = $this->getFilterItemUrl(['category' => $this->shouldBeSelectedCategories, 'color' => $this->selectedColor, 'quality' =>$this->selectedQuality]);
                return true;
            }
            return false;
        });

        return $childrenFilter->count() > 1 ? $childrenFilter : null;
    }

    protected function mostDeepSelectedCategory()
    {
        return $this->selectedCategory->sortBy('depth')->last();
    }

    private function notEmptyCategoriesMap()
    {
        return $this->category
            ->whereHas('product.deviceModel', function ($query) {
                $query->where('id', $this->selectedModel->first()->id);
            })
            ->with('ancestors')
            ->get();
    }

    private function hasBranchProducts($item)
    {
        foreach ($this->notEmptyCategories as $notEmptyCategory) {
            if ($notEmptyCategory->id === $item->id) {
                return true;
            } else {
                foreach ($notEmptyCategory->ancestors as $notEmptyAncestor) {
                    if ($notEmptyAncestor->id === $item->id) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param Category $category
     * @return bool
     */
    private function isCategorySelected(Category $category)
    {
        foreach ($this->selectedCategory as $selectedCategory) {

            if ($selectedCategory->id === $category->id) {
                return true;
            }

            foreach ($selectedCategory->ancestors as $ancestor) {
                if ($ancestor->id === $category->id) {
                    return true;
                }
            }
        }
        return false;
    }

    private function removeSelectedCategory(Category $category)
    {
        $removingCategoriesId = $category->descendants->pluck('id')->push($category->id);
        $selectedCategory = clone $this->selectedCategory;

        return $selectedCategory->filter(function (Category $selectedCategory) use ($removingCategoriesId){
            return !$removingCategoriesId->contains($selectedCategory->id);
        });
    }

    private function addSelectedCategory(Category $addingCategory)
    {
        $selectedCategory = clone $this->selectedCategory;
        $selectedCategory->push($addingCategory);
        if ($addingCategory->children && $addingCategory->children->count()){
            $selectedCategory->filter(function (Category $category) use ($addingCategory){
                return $category->depth <= $addingCategory->depth;
            });
        }

        return $selectedCategory;
    }
}