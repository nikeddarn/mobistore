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
        $view->with('categories', $this->getCategories())->with('brands', $this->getBrands());
    }

    /**
     * Retrieve root's children.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getCategories()
    {
        return Category::whereIsRoot()->first()->children()->get();
    }

    private function getBrands()
    {
        return Brand::orderBy('priority', 'asc')->get();
    }
}