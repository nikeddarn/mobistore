<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class DeliveryType extends Model
{
    use Translatable;
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'delivery_types';

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be selected depends on locale from JSON type field.
     *
     * @var array
     */
    public $translatable = ['title'];
}
