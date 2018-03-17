<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserDelivery extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'user_deliveries';

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

    /**
     * Get the planned arrival.
     *
     * @param  string  $value
     * @return string
     */
    public function getPlannedArrivalAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }
}
