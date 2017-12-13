<?php

namespace App\Http\ViewComposers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\View\View;

class CategoriesComposer
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var Brand
     */
    private $brand;

    /**
     * CategoriesComposer constructor.
     * @param Category $category
     * @param Brand $brand
     */
    public function __construct(Category $category, Brand $brand)
    {

        $this->category = $category;
        $this->brand = $brand;
    }
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
        $categories = $this->category->withDepth()->get()->toTree();

        return $categories->count() ? $categories[0]->children : collect();
    }

    private function getBrands()
    {
        return $this->brand->orderBy('priority', 'asc')->get();
    }
}