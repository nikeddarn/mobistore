<?php

namespace App\Models;

use App\Models\Traits\CompositeKeys;
use Illuminate\Database\Eloquent\Model;

class ProductBadge extends Model
{
    use CompositeKeys;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'product_badges';

    /**
     * Array of composite primary keys.
     *
     * @var array
     */
    protected $primaryKey = ['badges_id', 'products_id'];

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
        'badges_id', 'products_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function badge()
    {
        return $this->belongsTo('App\Models\Badge', 'badges_id', 'id');
    }
}
