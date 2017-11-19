<?php

/**
 * Retrieve Quality models with constraints as filter items.
 */

namespace App\Http\Controllers\Shop\Filters\FilterCreators;

use App\Contracts\Shop\Products\Filters\FilterCreatorInterface;
use App\Models\Quality;
use Illuminate\Database\Eloquent\Builder;

class QualityFilterCreator extends FilterCreator implements FilterCreatorInterface
{
    /**
     * @var Quality
     */
    private $quality;

    /**
     * ColorFilterCreator constructor.
     * @param Quality $quality
     */
    public function __construct(Quality $quality)
    {
        $this->quality = $quality;
    }

    /**
     * Create query builder on this model.
     *
     * @return Builder
     */
    protected function createQuery(): Builder
    {
        return $this->quality->select();
    }

}