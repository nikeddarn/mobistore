<?php

/**
 * Retrieve possible Brands depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use Illuminate\Support\Collection;

trait CategoriesFilter
{
    /**
     * Define possible brands for user filters.
     *
     * @return Collection
     */
    private function getParentCategoriesFilters()
    {
        $unfilteredPathPrefix = '/brand/' . $this->selectedModel->url;
        $filteredPathPrefix = '/filter/brand/brand=' . $this->selectedBrand->breadcrumb . '/model=' . $this->selectedModel->breadcrumb . '/category=';

        return $this->category->ancestorsAndSelf($this->selectedCategory->id)
            ->forget(0)// remove root node
            ->each(function ($ancestor) use ($unfilteredPathPrefix, $filteredPathPrefix){
                $ancestor->siblings = $ancestor->parent->children()->get()->filter(function ($sibling) use ($ancestor, $unfilteredPathPrefix, $filteredPathPrefix) {
                    if($this->hasBranchProducts($sibling)){
                        if ($ancestor->id === $sibling->id) {
                            $sibling->selected = true;
                            $sibling->filterUrl = $unfilteredPathPrefix . '/' . $ancestor->parent->url;
                        } else {
                            $sibling->selected = false;
                            $sibling->filterUrl = $filteredPathPrefix . $ancestor->breadcrumb . ',' . $sibling->breadcrumb;
                        }
                        return true;
                    }
                    return false;
                });
            })
            ->filter(function($filter){
                return $filter->siblings->count() > 1;
            });
    }

    private function getChildrenCategoriesFilter()
    {
        $unfilteredPathPrefix = '/brand/' . $this->selectedModel->url;
        if ($this->selectedCategory) {
            $filterItems = $this->selectedCategory->children;
        }else{
            $filterItems = $this->category->whereIsRoot()->first()->children;
        }

        return $filterItems->filter(function($child) use ($unfilteredPathPrefix){
                if($this->hasBranchProducts($child)){
                    $child->filterUrl = $unfilteredPathPrefix . '/' . $child->url;
                    return true;
                }
                return false;
            });
    }

    private function notEmptyCategoriesMap()
    {
        return $this->category
            ->whereHas('product.deviceModel', function ($query) {
                $query->where('id', $this->selectedModel->id);
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
}