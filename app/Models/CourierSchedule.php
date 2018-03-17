<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierSchedule extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'courier_schedules';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo('App\Models\Courier', 'couriers_id', 'id');
    }
}
