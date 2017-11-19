<?php

/**
 * Filter Creator Interface.
 */

namespace App\Contracts\Shop\Products\Filters;

use Closure;
use Illuminate\Support\Collection;

interface FilterCreatorInterface
{
    /**
     * Retrieve filter items with given constraints
     *
     * @param Closure $filterConstraints
     * @return Collection Filter items.
     */
    public function getFilterItems(Closure $filterConstraints): Collection;

    /**
     * Set additional constraints to apply in the filter.
     *
     * @param Closure $additionalConstraints
     * @return void
     */
    public function setAdditionalConstraints(Closure $additionalConstraints);
}