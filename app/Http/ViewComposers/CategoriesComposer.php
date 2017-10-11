<?php

namespace App\Http\ViewComposers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\View\View;

class CategoriesComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('allCategories', $this->getCategoriesTree())->with('allBrands', $this->getBrands());
    }

    /**
     * Retrieve root's children.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getCategoriesTree()
    {
        $categories = Category::withDepth()->get()->toTree();

        return $categories->count() ? $categories[0]->children : [];
    }

    private function getBrands()
    {
        return Brand::orderBy('priority', 'asc')->get();
    }
}