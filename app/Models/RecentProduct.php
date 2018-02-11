<?php

namespace App\Models;

use App\Models\Traits\CompositeKeys;
use Illuminate\Database\Eloquent\Model;

class RecentProduct extends Model
{
    use CompositeKeys;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'recent_products';

    /**
     * Array of composite primary keys.
     *
     * @var array
     */
    protected $primaryKey = ['users_id', 'products_id'];

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_id', 'products_id',
    ];
}
