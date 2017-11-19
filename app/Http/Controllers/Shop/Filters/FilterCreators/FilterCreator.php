<?php

/**
 * Retrieve filter models with constraints as filter items.
 */

namespace App\Http\Controllers\Shop\Filters\FilterCreators;


use App\Contracts\Shop\Products\Filters\FilterTypes;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class FilterCreator implements FilterTypes
{
    /**
     * Array of additional filter constraints.
     *
     * @var Closure
     */
    private $additionalRetrieveConstraints = null;

    /**
     * Push given constraint to array of filter constraints.
     *
     * @param Closure $additionalConstraints
     */
    public function setAdditionalConstraints(Closure $additionalConstraints)
    {
        $this->additionalRetrieveConstraints = $additionalConstraints;
    }

    /**
     * Retrieve Color Filter items From App\Models\Color with given constraint.
     *
     * @param Closure $filterConstraints Closure that contains Illuminate\Database\Eloquent\Builder constraint to retrieve filter items.
     * @return Collection Filter items.
     */
    public function getFilterItems(Closure $filterConstraints): Collection
    {
        $query = $this->createQuery();

        $constraints = $this->createConstraints($filterConstraints);

        $constraints($query);

        return $query->get();
    }

    /**
     * Apply received constraints to current filter.
     *
     * @param Closure $constraints
     * @return Closure
     */
    protected function createConstraints(Closure $constraints):Closure
    {
        return function ($query) use ($constraints){

            if ($constraints instanceof Closure){
                $constraints($query);
            }

            if ($this->additionalRetrieveConstraints instanceof Closure){
                $additionalConstraints = $this->additionalRetrieveConstraints;
                $additionalConstraints($query);
            }
        };

    }

    /**
     * Create query builder on current model.
     *
     * @return Builder
     */
    abstract protected function createQuery(): Builder;

}