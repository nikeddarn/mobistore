<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'courier';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courierSchedule()
    {
        return $this->hasMany('App\Models\CourierSchedule', 'couriers_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorShipment()
    {
        return $this->hasMany('App\Models\VendorShipment', 'couriers_id', 'id');
    }
}
