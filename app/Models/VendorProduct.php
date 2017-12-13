<?php

namespace App\Models;

use App\Models\Traits\CompositeKeys;
use Illuminate\Database\Eloquent\Model;

class VendorProduct extends Model
{
    use CompositeKeys;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendor_products';

    /**
     * Array of composite primary keys.
     *
     * @var array
     */
    protected $primaryKey = ['vendors_id', 'vendor_product_id'];

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

}
