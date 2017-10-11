<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModelCompatible extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'products_models_compatible';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'products_id', 'models_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
