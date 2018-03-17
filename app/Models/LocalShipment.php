<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalShipment extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'local_shipments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
