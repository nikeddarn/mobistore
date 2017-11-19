<?php

/**
 * Retrieve DeviceModel models with constraints as filter items.
 */

namespace App\Http\Controllers\Shop\Filters\FilterCreators;

use App\Contracts\Shop\Products\Filters\FilterCreatorInterface;
use App\Models\DeviceModel;
use Illuminate\Database\Eloquent\Builder;

class ModelFilterCreator extends FilterCreator implements FilterCreatorInterface
{
    /**
     * @var DeviceModel
     */
    private $model;

    /**
     * ColorFilterCreator constructor.
     * @param DeviceModel $model
     */
    public function __construct(DeviceModel $model)
    {
        $this->model = $model;
    }

    /**
     * Create query builder on current model.
     *
     * @return Builder
     */
    protected function createQuery(): Builder
    {
        return $this->model->select();
    }

}