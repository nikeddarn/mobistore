<?php

/**
 * Retrieve Brand models with constraints as filter items.
 */

namespace App\Http\Controllers\Shop\Filters\FilterCreators;

use App\Contracts\Shop\Products\Filters\FilterCreatorInterface;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;

class BrandFilterCreator extends FilterCreator implements FilterCreatorInterface
{
    /**
     * @var Brand
     */
    private $brand;

    /**
     * ColorFilterCreator constructor.
     * @param Brand $brand
     */
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Create query builder on current model.
     *
     * @return Builder
     */
    protected function createQuery(): Builder
    {
        return $this->brand->select();
    }

}