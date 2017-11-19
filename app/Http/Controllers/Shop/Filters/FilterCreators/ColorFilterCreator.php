<?php

/**
 * Retrieve Color models with constraints as filter items.
 */

namespace App\Http\Controllers\Shop\Filters\FilterCreators;

use App\Contracts\Shop\Products\Filters\FilterCreatorInterface;
use App\Models\Color;
use Illuminate\Database\Eloquent\Builder;

class ColorFilterCreator extends FilterCreator implements FilterCreatorInterface
{
    /**
     * @var Color
     */
    private $color;

    /**
     * ColorFilterCreator constructor.
     * @param Color $color
     */
    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    /**
     * Create query builder on current model.
     *
     * @return Builder
     */
    protected function createQuery(): Builder
    {
        return $this->color->select();
    }

}