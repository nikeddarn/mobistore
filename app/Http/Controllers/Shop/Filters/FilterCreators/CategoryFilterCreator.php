<?php

/**
 * Retrieve Category models with constraints as filter items.
 */

namespace App\Http\Controllers\Shop\Filters\FilterCreators;

use App\Contracts\Shop\Products\Filters\FilterCreatorInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoryFilterCreator extends FilterCreator implements FilterCreatorInterface
{
    /**
     * @var Category
     */
    private $category;

    /**
     * ColorFilterCreator constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Create query builder on this model.
     *
     * @return Builder
     */
    protected function createQuery(): Builder
    {
        return $this->category->select('categories.*');
    }

}