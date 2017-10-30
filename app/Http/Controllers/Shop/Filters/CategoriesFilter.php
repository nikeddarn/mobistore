<?php

/**
 * Retrieve possible Brands depends on existing products.
 * Add Url for filter.
 */

namespace App\Http\Controllers\Shop\Filters;

use App\Models\Category;
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
        return $this->category->withDepth()->ancestorsAndSelf($this->selectedCategory->id)
            ->forget(0)// remove root node
            ->each(function ($ancestor) {
                $ancestor->siblings = $ancestor->parent->children()->get()->filter(function ($sibling) use ($ancestor) {
                    if($this->hasBranchProducts($sibling)){
                        $this->formFilterItemUrl($ancestor, $sibling);
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
        if ($this->selectedCategory) {
            return $this->selectedCategory->children->filter(function($child){
                if($this->hasBranchProducts($child)){
                    $child->filterUrl = '/brand/' . $this->selectedModel->url . '/' . $child->url;
                    return true;
                }
                return false;
            });
        } else {
            return $this->category->whereIsRoot()->first()->children->filter(function ($item){
                if($this->hasBranchProducts($item)){
                    $item->filterUrl = '/brand/' . $this->selectedModel->url . '/' . $item->url;
                    return true;
                }
                return false;
            });
        }
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

    private function formFilterItemUrl(Category $ancestor, Category $sibling)
    {
        if ($ancestor->id === $sibling->id) {
            $sibling->selected = true;
            $sibling->filterUrl = '/brand/' . $this->selectedModel->url . '/' . $ancestor->parent->url;
        } else {
            $sibling->selected = false;
            $sibling->filterUrl = '/filter/brand/brand=' . $this->selectedBrand->breadcrumb . '/model=' . $this->selectedModel->breadcrumb . '/category=' . $ancestor->breadcrumb . ',' . $sibling->breadcrumb;
        }
    }
}